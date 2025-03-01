<?php

namespace Modules\Questionnaire\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Delivery\Http\Requests\StoreDataRequest;
use Modules\Questionnaire\Entities\Questionnaire;
use Modules\Questionnaire\Entities\QuestionnaireResult;
use Modules\Questionnaire\Entities\Schedule;
use Modules\Questionnaire\Entities\TemporaryAnswers;
use Modules\Questionnaire\Http\Requests\QuestResultStoreRequest;
use Modules\Questionnaire\Services\QuestionnaireService;
use Modules\Questionnaire\Transformers\EmployeeResource;
use Modules\Questionnaire\Transformers\QuestionnaireResource;
use Modules\Questionnaire\Transformers\QuestionsResource;
use Modules\Questionnaire\Transformers\RoleResource;
use Modules\Vpanel\Entities\Role;
use Modules\Vpanel\Entities\User;
use Modules\Vpanel\Entities\UserRating;

class QuestionnaireController extends Controller
{
    public function index()
    {
        return view('questionnaire::index');
    }

    public function roles(): array
    {
        return RoleResource::collection(Role::all())->resolve();
    }

    public function getEmployees(Role $role): array
    {
        $employees = User::whereJsonContains('role', $role->name)->get();
        return EmployeeResource::collection($employees)->resolve();
    }

    public function actualQuestionnaires(string $roleId): array
    {
        $questionnaires = Questionnaire::actual()->where('respondent_role_id', $roleId)->get();
        return QuestionnaireResource::collection($questionnaires)->resolve();
    }

    public function getQuestions(string $questionnaireId)
    {

      if (QuestionnaireService::timeIsOver($questionnaireId)) {
        return response()->json(['message' => 'Время истекло']);
      }
      $temporaryAnswers = null;

      $data = Questionnaire::with('blocks.questions.answers')
        ->where('id', $questionnaireId)
        ->first();

      // Проверяем сколько времени прошло с последнего заполнения
      $activeQuestionnaire = QuestionnaireResult::where('questionnaire_id', $questionnaireId)
        ->where('controller_id', Auth::user()->id)
        ->where('created_at', '>=', Carbon::now()->subHours(6))
        ->where('done', 'false')
        ->first();

      // Если анкета была создана недавно (прошло не более 6 часов), то берем ответы из промежуточного состояния
      if($activeQuestionnaire) {
        $temporaryAnswers = TemporaryAnswers::where('questionnaire_id', $questionnaireId)->orderBy('id', 'desc')->first();
        if ($temporaryAnswers) {
          $temporaryAnswers = json_decode($temporaryAnswers->answers, true);
        }
      }

      $totalQuestions = 0;

      // Скрываем поля в связанных моделях
      foreach ($data->blocks as $block) {
        $block->makeHidden(['created_at', 'updated_at', 'deleted_at']); // Поля, которые нужно скрыть в Block
        foreach ($block->questions as $question) {
          $totalQuestions++;
          $question->makeHidden(['created_at', 'updated_at', 'deleted_at']); // Поля, которые нужно скрыть в Question
          foreach ($question->answers as $answer) {
            $answer->makeHidden(['created_at', 'updated_at', 'deleted_at', 'next_question_id']); // Поля, которые нужно скрыть в Answer
          }
        }
      }

      return response()->json([
        'data' => $data['blocks'],
        'answers' => $temporaryAnswers,
        'totalQuestions' => $totalQuestions,
      ]);
    }

    public function store(QuestResultStoreRequest $request)
    {
      if (QuestionnaireService::timeIsOver($request->questionnaire)) {
        return response()->json(['message' => 'Время истекло']);
      }

      $processedAnswers = QuestionnaireService::prepareNew($request);

      $answers = $request->answers;
      $employeeId = $request->employee;

      $total = 0;
      foreach($answers as $answer) {
        $answer = json_decode($answer, true);
        $total += $answer['score'];
      }

      $newRating = $this->getNewRating($employeeId, $total, $answers);

      // Проверяем сколько времени прошло с последнего заполнения
      $activeQuestionnaire = QuestionnaireResult::where('questionnaire_id', $request->questionnaire)
        ->where('employee_id', $request->employee)
        ->where('created_at', '>=', Carbon::now()->subHours(6))
        ->where('done', 'false')
        ->first();

      // Если анкета заполнена и отправлена, удаляем все временные сохраненные ответы у этой анкеты
      if ($request->done == 'true') {
        TemporaryAnswers::where('questionnaire_id', $request->questionnaire)
          ->where('employee_id', $employeeId)
          ->delete();
      }

      // Если анкета была создана недавно (прошло не более 6 часов) и заполнена не до конца, то обновляем ее, иначе создаем новую
      if($activeQuestionnaire) {

        // Обновляем ответы в опроснике
        $activeQuestionnaire->update([
          'questionnaire_id' => $request->questionnaire,
          'controller_id' => Auth::user()->id,
          'employee_id' => $employeeId,
          'answers' => $processedAnswers,
          'total' => $total,
          'done' => $request->done,
        ]);

        // Обновляем запись о рейтинге
        $activeRating = UserRating::where('vpanel_user_id', $employeeId)
          ->where('created_at', '>=', Carbon::now()->subHours(8))
          ->first();

        $activeRating->update([
          'rating' => $newRating
        ]);

        return response()->json(['message' => 'Ответы успешно обновлены']);

      } else {

        QuestionnaireResult::create([
          'questionnaire_id' => $request->questionnaire,
          'controller_id' => Auth::user()->id,
          'employee_id' => $employeeId,
          'answers' => $processedAnswers,
          'total' => $total,
          'done' => $request->done,
        ]);

        UserRating::create([
          'vpanel_user_id' => $employeeId,
          'rating' => $newRating
        ]);

        return response()->json(['message' => 'Ответы успешно сохранены']);
      }

    }

    /**
     * @param mixed $employeeId
     * @param mixed $total
     * @param array $processedAnswers
     * @return float|int
     */
    public function getNewRating(mixed $employeeId, float $total, array $processedAnswers): int|float
    {
        $rating = User::find($employeeId)->rating->rating;

        $newRating = $rating + (($total - count($processedAnswers) * 3) / 1000);
        $newRating = min($newRating, 5);
        $newRating = max($newRating, 1);
        return $newRating;

    }

}
