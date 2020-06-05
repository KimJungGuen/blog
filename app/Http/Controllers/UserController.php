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
   //검색 & 유저 리스트 페이지

   public function average() 
   {
    $model = new NoticeBoard();

    $users = $model->getUserAll();
    $total = count($users);
    $active = 0;
    $male = 0;
    $young = 0;
    $adult = 0;
    $old = 0;
    $minAccumulated = 0;
    $middleAccumulated = 0;
    $hightAccumulated = 0;
    $totalAccumulated = 0;

    foreach($users as $user) {

      if (!$user->deleted_at) {
        $active += 1;
      }
      if ($user->gender == 1) {
        $male += 1;
      }

      if ($user->age > 19) {
        if($user->age < 40) {
          $young += 1;
        } else if ($user->age >= 40 && $user->age < 60) {
          $adult += 1;
        } else if ($user->age >= 60) {
          $old += 1;
        }
      }

      if ($user->accumulated > 1000) {
        if ($user->accumulated >= 1000 && $user->accumulated <= 9999) {
          $middleAccumulated += 1;
        } else if ($user->accumulated >= 10000) {
          $hightAccumulated += 1;
        }
      }

      $totalAccumulated += $user->accumulated;
    }

    $sleep = $total - $active;
    $female = $total - $male;
    $children = $total - ($young + $adult + $old);
    $minAccumulated = $total - ($middleAccumulated + $hightAccumulated);

    $avgData = array('total' => $total,
                      'active' => $active,
                      'sleep' => $sleep,
                      'male' => $male,
                      'female' => $female,
                      'children' => $children,
                      'young' => $young,
                      'adult' => $adult,
                      'old' => $old,
                      'minAccumulated' => $minAccumulated,
                      'middleAccumulated' => $middleAccumulated,
                      'hightAccumulated' => $hightAccumulated,
                      'totalAccumulated' => $totalAccumulated
    );

    $test = (String)$users[0]->accumulated;

    $aa = preg_replace('/([0-9]{3})([0-9]{3})/', '$1,$2', $test);
    //dd($aa);
    
    return $avgData;
   }


   public function page(array $page)
   {
    $model = new NoticeBoard();
    $userStatus = array();
    $index = 0;
    //현재페이지에 뿌려질 유저의 수
    $perPage = $page['users']->perPage();
    //조회된 건수
    $total = $page['users']->total();
    //$average = $model->average();
    $avgData = $this->average();
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
    
    return view('userList', array('users' => $page['users'],
                                  'pageView' => $pageView,
                                  'userStatus' => $userStatus,
                                  'searchData' => $page['searchData'],
                                  'avgData' => $avgData
                ));
   }
   //메인 페이지
   public function users(Request $request)
   {
     $model = new NoticeBoard();
     //처음 페이지에 들어갈떄 표시할 유저의 수는 5로 지정
     $pageLimit = $request->input('pageLimit', 5);
     //페이지 조건에 의한 유저 페이징
     $searchData = $this->searchData($request);
     //dd($searchData);
     $users = $model->getList($pageLimit, $searchData);
     //$model->withTrashed()->restore();
     $page = ['users' => $users,
              'pageLimit' => $pageLimit,
              'searchData' => $searchData
            ];    

     return $this->page($page);
   }
   //ID중복체크 요청처리 ajax json타입으로 반환
   public function userIdCheck(Request $request)
   {
     $model = new NoticeBoard();
     $userId = $request->input('userId', null);
     //해당 유저의 id로 검색
     if($userId) {
       $dbUserId = $model->getUserId($userId);
       $userIdLength = Str::length($userId);

       $msg = 'Complete ID';
       $check = 1;

       if($dbUserId == $userId) {
         $msg = 'Duplicate ID';
         $check = 0;
       }

       if ($userIdLength <= 4 || $userIdLength >= 21) {
           $msg = 'not less than 5 but not more than 20 characters';
           $check = 0;
       }
     } else {
       $msg = 'a blank space ID';
       $check = 0;
     }

     return response()->json(array('msg' => $msg, 'check' => $check));
   }
   //유저등록
   public function userRegister(UserRequest $request)
   {
     $msg = '회원등록이 되었습니다.';

     if ($request->file('file')) {
       $path = $request->file('file')->store('userFile');
     } else {
       $path = null;
     }

     $model = new NoticeBoard();
     //들어온 전화번호를 - 구분자를 삽입한다.
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
        $msg = '회원등록에 실패했습니다.';
      }

     return response()->json(array('msg' => $msg, 'result' => $result));
   }
   //유저 비밀번호 확인
   public function userPwCheck(Request $request)
   {
     $model = new NoticeBoard();

     $userIndex = $request->input('userIndex', 0);
     $userPw = $request->input('userPw', null);
     $msg = '비밀번호가 일치합니다.';
     $pwCheck = true;

     //해당 유저를 조회
     $dbUserPw = $model->getUserPw($userIndex);
     //조회한 유저와 입력한 비밀번호가 맞는지 체크
     $pwCheck = Str::of($dbUserPw)->exactly($userPw);

     if (!$pwCheck) { //비밀번호가 틀릴경우
       $msg = '비밀번호가 틀렸습니다.';
     } else {
       //비밀번호가 맞을경우 해당 usreIndex session생성
       $request->session()->put('userIndex', $userIndex);
     }

     return response()->json(array('pwCheck' => $pwCheck, 'msg' => $msg));
   }
   //유저 업데이트 페이지에 해당 PK의 유저정보 표시
   public function userUpdatePage(Request $request,$userIndex)
   {
     $this->middleware('auth');
     if ($request->session()->has('userIndex')) {
       $request->session()->forget('userIndex');
       $model = new NoticeBoard();
       $user = $model->getUser($userIndex);
       
       $userPw = $user['user_pw'];
       $etc = $user['etc'];
       $accumulated = $user['accumulated'];
       $addressNum = $user['address_num'];
       $addressRoad = $user['address_road'];
       $addressDetail = $user['address_detail'];
       //email 표시를 위해 @기준으로 다시나눔
       $email = Str::of($user['email'])->before('@');
       $emailDomain = Str::of($user['email'])->after('@');
       //tel표시를 위해 다시 숫자만 보이게 변환
       $tel =  preg_replace('/-/', '',$user['tel']);

       $path = $user['file'];
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
   public function userUpdate(UserRequest $request)
   {
     $model = new NoticeBoard();
     $userIndex = $request->input('userIndex');
     $user = $model->getUser($userIndex);
     $fileExt = $request->allFiles();
     $etc = $request->input('etc');
     $accumulated = $request->input('accumulated');
     $userPw = $request->input('userPw');
     $msg = '회원정보 업데이트 성공';

     if ($fileExt) {
       $path = $request->file('file')->store('userFile' , 'local');
     } else {
       $path = $user['file'];
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
      $msg = $e->getMessage();
     }

     return response()->json(array('msg' => $msg, 'result' => $result));
   }
   //유저 검색 필드 값 가공
   public function searchData(Request $request)
   {
     $currentUrl = url()->current();
     //검색필터를 지정안했으면 false로 초기화
     $filterFir = $request->input('filterFir', false); //첫번쨰 검색어 필터
     $filterSec = $request->input('filterSec', false); //두번쨰 검색어 필터
     //성별 구문 1남 2여 3전체
     $gender = $request->input('gender', 'all');
     //검색 폼 정렬방식 기본 오름차순
     $orderBy = $request->input('orderBy', 'asc');
     //검색 폼 정렬 기준 필드
     $sort = $request->input('sort', 'index');
     //날짜 사이의 데이터 조회
     $searchDateFir = $request->input('searchDateFir', Carbon::now()->addMonth(-1)->format('Y-m-d'));
     $searchDateSec = $request->input('searchDateSec', Carbon::now()->format('Y-m-d'));
     //휴면,가입 유저상태 조회 값 3 전체, 2휴면, 1사용
     $searchUserStatus = 'all';
     $searchUserAll = $request->input('searchUserAll', null);
     $searchUserActive = $request->input('searchUserActive', null);
     $searchUserSleep = $request->input('searchUserSleep', null);
     //검색 키워드
     $searchTextFir = $request->input('searchFirWord');
     $searchTextSec = $request->input('searchSecWord');

     $listOrderBy = $request->input('listOrderBy', 'asc');
     $listSort = $request->input('listSort', 'index');
      
     //유저 상태 조회가 사용 또는 휴먼 둘중 하나일경우 또는 다일경우
     if (!empty($searchUserAll) || !empty($searchUserActive) && !empty($searchUserSleep)) {
        $searchUserStatus = 'all';
     } else if (!empty($searchUserActive)) {
        $searchUserStatus = 'active';
     } else if (!empty($searchUserSleep)) {
        $searchUserStatus = 'sleep';
     }
 
     $searchData = ['filterFir' => $filterFir, //첫번째 검색필터
                    'filterSec' => $filterSec, //두번째 검색 필터
                    'searchTextFir' => $searchTextFir, //첫번째 검색필터 필드 값
                    'searchTextSec' => $searchTextSec, //두번째 검색필터 필드 값
                    'searchDateFir' => $searchDateFir, //가입일 첫번째 기준
                    'searchDateSec' => $searchDateSec, //가입일 두번째 기준
                    'gender' => $gender, //성별
                    'userStatus' => $searchUserStatus, //유저 상태
                    'sort' => $sort, //정렬 기준 검색 form
                    'orderBy' => $orderBy,
                    'listSort' => $listSort,
                    'listOrderBy' => $listOrderBy]; // 정렬 방식 검색 form

    //dd($searchData);
     return $searchData;
   }
   //유저 검색
   public function userSearch(Request $request)
   {
     $model = new NoticeBoard();
     //요청된 검색 필드 값 정리
     $searchData = $this->searchData($request);
     $pageLimit = $request->input('searchPageLimit', 5);
    //dd(1);
     //유저 상태에 따른 검색
     $users = $model->searchUsers(collect($searchData), $pageLimit);

     $page = ['users' => $users,
              'pageLimit' => $pageLimit,
              'searchData' => $searchData
            ];

      return $this->page($page);
   }
   //유저 삭제
   public function userDelete(Request $request)
   {
     $model = new NoticeBoard();
     $userIndex = $request->input('userIndex', false);
     $msg = '선택한 유저가 탈퇴됐습니다.';
     if (!empty($userIndex)) {
         try{
           $deleteRow = $model->deleteUser($userIndex);
         } catch(\Exception $e) {
           $msg = '회원 탈퇴가 실패했습니다.';
         }
     }
     return response()->json(array('msg' => $msg,
                                   'deleteRow' => $deleteRow));
   }

}
