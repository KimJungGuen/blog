<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\NoticeBoard;
use Carbon\Carbon;


class UserController extends Controller
{
    //삭제된 유저 복구 *softdelete삭제 복구용
    //$model->withTrashed()->restore();
 
    public function index(Request $request) 
    {
       //dd($request);
        $searchData = $this->searchData($request);
        $usersData = $this->usersData($searchData);
        $averageData = $this->averageData(); 
        // dd($searchData);
        return view('userList', array(
          'searchData' => $searchData,
          'users' => $usersData['users'],
          'userStatus' => $usersData['userStatus'],
          'averageData' => $averageData
        ));
    }

    public function searchData(Request $request)
    {
        //휴면,가입 유저상태 조회 값 3 전체, 2휴면, 1사용
        $searchUserStatus = '';
        $searchUserAll = $request->input('searchUserAll', null);
        $searchUserActive = $request->input('searchUserActive', null);
        $searchUserSleep = $request->input('searchUserSleep', null);


        //유저 상태 조회가 사용 또는 휴먼 둘중 하나일경우 또는 다일경우
        if (isset($searchUserAll) || isset($searchUserActive) && isset($searchUserSleep)) {
          $searchUserStatus = 'all';
        } else if (isset($searchUserActive)) {
          $searchUserStatus = 'active';
        } else if (isset($searchUserSleep)) {
          $searchUserStatus = 'sleep';
        }

        $searchData = [
          'filterFirst' => $request->input('filterFirst', null), //첫번째 검색필터
          'filterSecond' => $request->input('filterSecond', null), //두번째 검색 필터
          'searchFirstWord' => $request->input('searchFirstWord', null), //첫번째 검색필터 필드 값
          'searchSecondWord' => $request->input('searchSecondWord', null), //두번째 검색필터 필드 값
          'searchDateFirst' => $request->input('searchDateFirst', Carbon::now()->addMonth(-1)->format('Y-m-d')), //가입일 첫번째 기준
          'searchDateSecond' => $request->input('searchDateSecond', Carbon::now()->format('Y-m-d')), //가입일 두번째 기준
          'gender' => $request->input('gender', 'all'), //성별
          'userStatus' => $searchUserStatus, //유저 상태
          'sort' => $request->input('sort', 'index'), //정렬 기준 검색 searchForm
          'orderBy' => $request->input('orderBy', 'asc'),  //정렬 방식 searchForm
          'page' => $request->input('page', 1),
          'searchPageLimit' => $request->input('searchPageLimit', 5)
        ]; 

        return $searchData;
    }    

    public function usersData(array $searchData)
    {
        $userModel = new NoticeBoard();
        $users = $userModel->getUserList($searchData);
        $userStatus = array();

        foreach ($users as $index => $user) {
          $user->join_date = str::substr($user->join_date, 0, 10);
          $user->gender = ($user->gender == 1) ? '남' : '여';
          $userStatus[$index] = (empty($user->deleted_at)) ? '사용' : '휴면';
          $user->accumulated = number_format($user->accumulated);
        }

        $usersData = collect([
          'users' => $users,
          'userStatus' => $userStatus
        ]);

        return $usersData;
    }

    public function averageData()
    {
        $userModel = new NoticeBoard();
        $aveData = $userModel->averageData();
        $aveData['totalAccumulated'] = number_format($aveData['totalAccumulated']);

        return $aveData;
    }
}
