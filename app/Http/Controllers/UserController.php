<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\NoticeBoard;
use App\Http\Requests\UserRequest;


class UserController extends Controller
{
  //검색 & 유저 리스트 페이지
   public function page($page)
   {
    $userStatus = array();
    $index = 0;
    //현재페이지에 뿌려질 유저의 수
    $perPage = $page['users']->perPage();
    //조회된 건수
    $total = $page['users']->total();

    //view에 표시될 데이터 포맷 처리
    foreach ($page['users'] as $user) {
      $user->join_date =  str::substr($user->join_date, 0, 10);
      $user->gender = ($user->gender == 1) ? '남' : '여';
      $userStatus[$index] = ($user->deleted_at) ? '휴면' : '사용';
      $index++;
    }

     $pageView = ['pageLimit' => $page['pageLimit'],
                  'perPage' => $perPage,
                  'total' => $total];

    return view('userList', [
      'users' => $page['users'],
      'pageView' => $pageView,
      'searchData' => $page['searchValue'],
      'userStatus' => $userStatus,
      'filters' => $page['filters']
    ]);
   }
   //메인 페이지
   public function users(Request $request)
   {
     $model = new NoticeBoard();
     $searchValue = false;
     $filters = false;
    //처음 페이지에 들어갈떄 표시할 유저의 수는 5로 지정
     $pageLimit = $request->input('pageLimit', 5);
    //페이지 조건에 의한 유저 페이징
     $users = $model->getList($pageLimit);

     $page = ['users' => $users,
              'pageLimit' => $pageLimit,
              'searchValue' => $searchValue,
              'filters' => $filters];

     return $this->page($page);
   }
   //ID중복체크 요청처리 ajax json타입으로 반환
   public function userIdCheck(Request $request)
   {
     $model = new NoticeBoard();
     $userId = $request->input('userId', null);
     //해당 유저의 id로 검색
     if($userId) {
       $users = $model->getUserId($userId);
       $userIdLength = Str::length($userId);

       $msg = 'Complete ID';
       $check = 1;

       foreach ($users as $user) {
           if($user->user_id == $userId) {
             $msg = 'Duplicate ID';
             $check = 0;
           }
         }

       if ($userIdLength <= 4 || $userIdLength >= 21) {
           $msg = 'not less than 5 but not more than 20 characters';
           $check = 0;
       }
     } else {
       $msg = 'a blank space ID';
       $check = 0;
     }

     return response()->json(['msg' => $msg, 'check' => $check]);
   }
   //유저등록
   public function userRegister(UserRequest $request)
   {

     $validated = $request->validated();

     if ($request->file('file')) {
       $path = $request->file('file')->store('userFile');
     } else {
       $path = null;
     }

     $model = new NoticeBoard();
     //들어온 전화번호를 보기편하게 - 구분자를 삽입한다.
     $tel = preg_replace('/([0-9]{3})([0-9]{4})([0-9]{4})/',
                         '$1-$2-$3',
                         $request->input('tel'));

     $email = $request->input('email')
            . '@'
            . $request->input('emailDomain');

     $addressNum = $request->input('addressNum');
     $addressRoad = $request->input('addressRoad');
     $addressDetail = $request->input('addressDetail');
     $userId = $request->input('userId');
     $userPw = $request->input('userPw');
     $name = $request->input('name');
     $gender = $request->input('gender');
     $age = $request->input('age');
     $accumulated = $request->input('accumulated');
     $etc = $request->input('etc');
     $join_date = now();
     $marry = $request->input('marry');

     $user = ['userId' => $userId,
              'userPw' => $userPw,
              'name' => $name,
              'gender' => $gender,
              'age' => $age,
              'accumulated' => $accumulated,
              'email' => $email,
              'addressNum' => $addressNum,
              'addressRoad' => $addressRoad,
              'addressDetail' => $addressDetail,
              'etc' => $etc,
              'join_date' => now(),
              'marry' => $marry,
              'tel' => $tel,
              'file' => $path];

      try {
        $result = $model->userInsert($user);
      } catch (\Exception $e) {
        return view('error');
      }
     return redirect('/users');
   }
   //유저 비밀번호 확인
   public function userPwCheck(Request $request)
   {
     $model = new NoticeBoard();
     $userIndex = $request->input('userIndex', 0);
     $userPw = $request->input('userPw', null);
     $msg = '비밀번호가 일치합니다.';
     $result = null;
     $pwCheck = true;

     //해당 유저를 조회
     $user = $model->getUser($userIndex);
     //조회한 유저와 입력한 비밀번호가 맞는지 체크
     $pwCheck = Str::of($user[0]->user_pw)->exactly($userPw);

     if (!$pwCheck) { //비밀번호가 틀릴경우
       $msg = '비밀번호가 틀렸습니다.';
     } else {
       //비밀번호가 맞을경우 해당 usreIndex session생성
       $request->session()->put('userIndex', $user[0]->index);
     }

     return response()->json(['pwCheck' => $pwCheck, 'msg' => $msg]);
   }
   //유저 업데이트 페이지에 해당 PK의 유저정보 표시
   public function userUpdatePage(Request $request,$userIndex)
   {
     if($request->session()->has('userIndex')){
       $request->session()->forget('userIndex');
       //dd(session()->all());
       $model = new NoticeBoard();
       $user = $model->getUser($userIndex);

       $userPw = $user[0]->user_pw;
       $etc = $user[0]->etc;
       $accumulated = $user[0]->accumulated;
       //email 표시를 위해 @기준으로 다시나눔
       $email = Str::of($user[0]->email)->before('@');
       $emailDomain = Str::of($user[0]->email)->after('@');
       //tel표시를 위해 다시 숫자만 보이게 변환
       $tel =  preg_replace('/-/', '',$user[0]->tel);
       //우편번호를 / 기준으로 다시 분할 표시
       $addressNum = $user[0]->address_num;
       $addressRoad = $user[0]->address_road;
       $addressDetail = $user[0]->address_detail;


       $path = $user[0]->file;
       $imgUrl = Storage::url($path);

       $userData = ['userIndex' => $userIndex,
                    'userPw' => $userPw,
                    'email' => $email,
                    'emailDomain' => $emailDomain,
                    'tel' => $tel,
                    'addressNum' => $addressNum,
                    'addressRoad' => $addressRoad,
                    'addressDetail' => $addressDetail,
                    'etc' => $etc,
                    'accumulated' => $accumulated,
                    'imgUrl' => $imgUrl];

       return view('userUpdate',['userData' => $userData]);
     } else {
       return redirect('/users');
     }
   }
   //유저 Update
   public function userUpdate(Request $request)
   {
     $validate = $request->validate([
                           'userPw' => 'required|same:userPwCheck',
                           'tel' => 'required|max:13',
                           'accumulated' => 'required|integer',
                           'addressNum' => 'required|max:5',
                           'addressRoad' => 'required',
                           'email' => 'required'
                         ]);

     $model = new NoticeBoard();
     $user = $model->getUser($request->input('userIndex'));
     $userIndex = $request->input('userIndex');
     $fileExt = $request->allFiles();
     $etc = $request->input('etc');
     $accumulated = $request->input('accumulated');
     $userPw = $request->input('userPw');

     if ($fileExt) {
       $path = $request->file('file')->store('userFile' , 'local');
     } else {
       $path = $user[0]->file;
     }

     //email 문자열 합산
     $email = $request->input('email')
            . '@'
            . $request->input('emailDomain');
     //전화번호 포멧으로 문자열 재배치 수정
     $tel = preg_replace('/([0-9]{3})([0-9]{4})([0-9]{4})/',
                         '$1-$2-$3',
                         $request->input('tel'));

     $addressNum = $request->input('addressNum');
     $addressRoad = $request->input('addressRoad');
     $addressDetail = $request->input('addressDetail');

     $userData = ['userIndex' => $userIndex,
                  'userPw' => $userPw,
                  'email' => $email,
                  'accumulated' => $accumulated,
                  'addressNum' => $addressNum,
                  'addressRoad' => $addressRoad,
                  'addressDetail' => $addressDetail,
                  'tel' => $tel,
                  'file' => $path,
                  'etc' => $etc];


     try {
       $result = $model->userUpdate($userData);
     } catch (\Exception $e) {
       return view('error');
     }

     return redirect('/users');
   }
   //검색 필드 값 가공
   public function searchData($request)
   {
     //검색필터를 지정안했으면 false로 초기화
     $filterFir = $request->input('filterFir', false); //첫번쨰 검색어 필터
     $filterSec = $request->input('filterSec', false); //두번쨰 검색어 필터
     //성별 구문 1남 2여 3전체
     $gender = $request->input('gender', 3);
     //정렬방식 기본 오름차순
     $orderBy = $request->input('orderBy', 'asc');
     //정렬 기준 필드
     $sort = $request->input('sort', 'index');
     $sortOrder = ['sort' => $sort, 'orderBy' => $orderBy];
     //날짜 사이의 데이터 조회
     $searchDateFir = $request->input('searchDateFir', now()->format('Y-m-d'));
     $searchDateSec = $request->input('searchDateSec', now()->format('Y-m-d'));
     //휴면,가입 유저상태 조회 값 3 전체, 2휴면, 1사용
     $searchUserStatus = $request->input('searchUserAll', null);
     //검색필드
     $searchTextFir = $request->input('searchFirWord');
     $searchTextSec = $request->input('searchSecWord');

     //유저 상태 조회가 사용 또는 휴먼 둘중 하나일경우
     if (!$searchUserStatus) {
       if ($request->input('searchUserActive', null)) { //가입자 조회만 체크할경우
         $searchUserStatus = $request->input('searchUserActive');
         if($request->input('searchUserSleep', null)) { //사용과 휴면 둘다 체크했을경우
           $searchUserStatus = 3;
         }
       } else { //나머지 경우는 휴면 유저 조회밖에 없으므로 else처리
         $searchUserStatus = $request->input('searchUserSleep', null);
       }
     }

     //성별이 체크된 경우 성별 검색을 위해 값을 넣어줌  1=남 2=여
     $gender = ($gender == 3) ? '%'
               : (($gender == 1) ? 1
               : 2);

     $searchData = ['filterFir' => $filterFir, //첫번째 검색필터
                    'filterSec' => $filterSec, //두번째 검색 필터
                    'searchTextFir' => $searchTextFir, //첫번째 검색필터 필드 값
                    'searchTextSec' => $searchTextSec, //두번째 검색필터 필드 값
                    'searchDateFir' => $searchDateFir, //가입일 첫번째 기준
                    'searchDateSec' => $searchDateSec, //가입일 두번째 기준
                    'gender' => $gender,
                    'userStatus' => $searchUserStatus,
                    'sortOrder' => $sortOrder]; //유저의 성별



     $filters = $this->filter($searchData);

     return array($searchData, $filters);
   }
   //이전 필터값 유지를 위해 필터 값 재 가공
   public function filter($searchData)
   {
     //필터 값에 따라 표시될 문자를 넣음 *비 효율적이므로 스위치로 개정 필요
     $filterFir = ($searchData['filterFir'] == 'user_id')
                              ? 'ID'
                              : (($searchData['filterFir'] == 'name')
                              ? '이름'
                              : 'email');

     $filterSec = ($searchData['filterSec'] == 'user_id')
                              ? 'ID'
                              : (($searchData['filterSec'] == 'name')
                              ? '이름'
                              : 'email');

     $sortValue = $searchData['sortOrder']['sort'];
     $sort = ($searchData['sortOrder']['sort'] == 'index')
           ? '번호'
           : (($searchData['sortOrder']['sort'] == 'accumulated')
           ? '적립금'
           : '나이');

     $orderByValue = $searchData['sortOrder']['orderBy'];
     $orderBy = ($searchData['sortOrder']['orderBy'] == 'asc') ? '오름차순' : '내림차순';

     $gender = ($searchData['gender'] == '%')
             ? 3
             : (($searchData['gender'] == 1)
             ? 1
             : 2);

     $filters = ['filterFir' => $filterFir,
                 'filterSec' => $filterSec,
                 'orderBy' => $orderBy,
                 'orderByValue' => $orderByValue,
                 'sort' => $sort,
                 'sortValue' => $sortValue,
                 'gender' => $gender];

     return $filters;

   }
   //유저 검색
   public function userSearch(Request $request)
   {
     $model = new NoticeBoard();
     //요청된 검색 필드 값 가공 처리
     $search = $this->searchData($request);
     //검색 가공값
     $searchValue = $search[0];
     //필터 가공값
     $filters = $search[1];
     //dd($searchData);
     $pageLimit = $request->input('searchPageLimit', 5);
     //dd($searchData['sortOrder']);
     //유저 상태에 따른 검색
     $users = ($searchValue['userStatus'] == 3)
            ? $model->searchFullUser($searchValue, $searchValue['sortOrder'], $pageLimit)
            : (($searchValue['userStatus'] == 1)
            ? $model->searchActiveUser($searchValue, $searchValue['sortOrder'], $pageLimit)
            : $model->searchSleepUser($searchValue, $searchValue['sortOrder'], $pageLimit));


     $page = ['users' => $users,
              'pageLimit' => $pageLimit,
              'searchValue' => $searchValue,
              'filters' => $filters];

      return $this->page($page);
   }
   //유저 삭제
   public function userDelete(Request $request)
   {
     $model = new NoticeBoard();
     $userIndex = $request->input('userIndex', null);
     $msg = '';

     if ($userIndex) {
       foreach ($userIndex as $index) {
         try{
           $deleteRow = $model->deleteUser($index);
         } catch(\Exception $e) {
           $msg = '삭제에 실패했습니다.'
         }
         $msg = '선택한 유저가 삭제됐습니다.';
       }
     } else {
       $msg = '선택한 유저가 없습니다.';
     }
     //$delete = $result;
     return response()->json(['msg' => $msg, 'deleteRow' => $deleteRow]);
   }

}
