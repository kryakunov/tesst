<?php

namespace App\Http\Controllers;

use App\Http\Resources\GetPostsResource;
use App\Models\Anketa;
use App\Models\Post;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index() {
        return GetPostsResource::collection(Test::all());
    }
    public function asd() {
        return view('test');
    }
    public function upload(Request $request) {
        dd($request->image);
        dd($request->image->storeAs('images', 'test.png'));
    }
    public function test() {
        return inertia('Post/test');
    }
    public function send(Request $request) {
        dd($request->all());
        return 'ok';
    }

    public function getQuestions() {
        $json = json_decode(file_get_contents('file.txt', ), true);

        $anketaId = 1; // Замените на ID нужной анкеты
        $anketa = Anketa::with('blocks.questions') // Загружаем блоки и вопросы
        ->where('id', $anketaId)
            ->first(); // Получаем первую анкету с указанным ID

// Теперь вы можете вернуть данные в формате JSON
        return response()->json($json);
    }
}
