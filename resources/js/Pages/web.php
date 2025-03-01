<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\Questionnaire\Http\Controllers\QuestionnaireController;

Route::group(['prefix' => 'questionnaire', 'middleware' => 'auth'],function() {
    Route::get('/', [QuestionnaireController::class,'index'])->name('questionnaire.index');
    Route::get('/roles', [QuestionnaireController::class,'roles']);
    Route::get('/employees/{role}', [QuestionnaireController::class,'getEmployees']);
    Route::post('/store', [QuestionnaireController::class,'store'])->name('questionnaire.store');
    Route::get('/{roleId}', [QuestionnaireController::class,'actualQuestionnaires']);
    Route::get('/questionnaire/{questionnaireId}', [QuestionnaireController::class,'getQuestions']);
    Route::get('/{questionId}', [QuestionnaireController::class,'getAnswers']);
    Route::get('/test', [QuestionnaireController::class,'test']);
});
