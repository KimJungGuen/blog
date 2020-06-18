<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\History;

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

// Route::get('/', function () {
//     return view('welcome');
// });

//메인페이지
Route::get('/users','UserController@index');
//유저등록 페이지
Route::get('/user',function(){
    return view('RegisterPage', array('registerCheck' => false));
});
//유저등록 요청
Route::post('/users','UserController@userRegister')->name('register');
//유저 id 중복검사 요청 ajax
Route::post('/userIdCheck', 'UserController@userIdCheck')->name('userIdCheck');
//유저 pw 체크요청 ajax
Route::post('/userPwCheck/{userIndex}', 'UserController@userPwCheck')->name('userPwCheck');
//유저 Update페이지
Route::get('/userUpdate/{userIndex}', 'UserController@userUpdatePage')->middleware(History::class);
//유저 업데이트 요청
Route::put('/userUpdate/{userIndex}', 'UserController@userUpdate')->name('update');
//유저 삭제
Route::delete('/userDelete', 'UserController@userDelete');