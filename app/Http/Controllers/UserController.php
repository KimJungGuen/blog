<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NoticeBoard;
use Illuminate\Support\Str;

class UserController extends Controller
{
   //메인 페이지
   public function users(Request $request){
     $model = new NoticeBoard();
     //처음 페이지에 들어갈떄 표시할 유저의 수는 5로 지정
     $pageLimit = $request->input('pageLimit', 5);
     //페이지 조건에 의한 유저 페이징
     $users = $model->getList($pageLimit);
     //현재페이지에 뿌려질 유저의 수
     $perPage = $users->perPage();
     //조회된 건수
     $total = $users->total();
     //view에 표시될 데이터 포맷 처리
     foreach ($users as $user) {
       $user->join_date =  str::substr($user->join_date, 0, 10);
       if ($user->gender == 1) {
         $user->gender = '남';
       } else if ($user->gender == 2) {
        $user->gender = '여';
       }
     }

      $pageView = ['pageLimit' => $pageLimit,
                   'perPage' => $perPage,
                   'total' => $total];

     return view('userList', [
       'users' => $users,
       'pageView' => $pageView
     ]);
   }
   //ID중복체크 요청처리 ajax json타입으로 반환
   public function userIdCheck(Request $request){
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
   public function userRegister(Request $request){

     /*
     name notNull, 문자만, 2자에서 5자 사이
     userId notNull, 5자에서 20자 사이, user db조회 id중복 안될때만
     userPw notNull,
     userPwCheck notNull, 숫자만
     gender notNull, 숫자만
     age notNull, 숫자만
     tel notNull, 숫자만, 최소 11자이상
     email notNull,
     emailDomain notNull,
     accumulated notNull, 숫자만
     marry notNull, 숫자만
     accumulated notNull, 숫자만
     addressNum notNull, ,숫자만
     addressRoad notNull,
     */
     $validate = $request->validate([
                           'name' => 'required|alpha|between:2,5',
                           'userId' => 'required|between:5,20|unique:user,user_id',
                           'age' => 'required|integer',
                           'userPw' => 'required|same:userPwCheck',
                           'tel' => 'required',
                           'gender' => 'required|numeric',
                           'accumulated' => 'required|integer',
                           'addressNum' => 'required|max:5',
                           'addressRoad' => 'required',
                           'marry' => 'required',
                           'email' => 'required'
                         ]);

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

     //최초 가입은 상태가 없기떄문에 초기값으로 가입을 넣어준다.
     $userStatus = '가입';
     //들어온 우편번호와 주소를 구분자를 삽입하여 통합
     $address = $request->input('addressNum')
              .'#*'
              . $request->input('addressRoad')
              .'#@'
              . $request->input('addressDetail');

     $email = $request->input('email')
            . '@'
            . $request->input('emailDomain');

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
              'address' => $address,
              'etc' => $etc,
              'join_date' => now(),
              'marry' => $marry,
              'tel' => $tel,
              'userStatus' => $userStatus,
              'file' => $path];

     $result = $model->userInsert($user);

     return redirect('/users');
   }
   //유저 비밀번호 확인
   public function userPwCheck(Request $request){
     $model = new NoticeBoard();
     $userIndex = $request->input('userIndex', 0);
     $userPw = $request->input('userPw', null);
     $msg = '비밀번호가 일치합니다.';
     $result = null;
     $pwCheck = true;

     //해당 유저를 조회
     $result = $model->getUser($userIndex);
     //조회한 유저와 입력한 비밀번호가 맞는지 체크
     $pwCheck = Str::of($result[0]->user_pw)->exactly($userPw);
     //비밀번호가 틀릴경우
     if (!$pwCheck) {
       $msg = '비밀번호가 틀렸습니다.';
     }

     return response()->json(['pwCheck' => $pwCheck, 'msg' => $msg]);
   }
   //유저 업데이트 페이지에 해당 PK의 유저정보 표시
   public function userUpdatePage(Request $request,$userIndex){
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
     $addressNum = Str::of($user[0]->address)->before('#*');
     $addressRoad = Str::of($user[0]->address)->between('#*' ,'#@');
     $addressDetail = Str::of($user[0]->address)->after('#@');

     $userData = ['userIndex' => $userIndex,
                  'userPw' => $userPw,
                  'email' => $email,
                  'emailDomain' => $emailDomain,
                  'tel' => $tel,
                  'addressNum' => $addressNum,
                  'addressRoad' => $addressRoad,
                  'addressDetail' => $addressDetail,
                  'etc' => $etc,
                  'accumulated' => $accumulated];

     return view('userUpdate',['userData' => $userData]);
   }
   //유저 Update
   public function userUpdate(Request $request){
     $validate = $request->validate([
                        //   'userPw' => 'required|same:userPwCheck',
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
       $path = $request->file('file')->store('userFile');
     } else {
       $path = $user[0]->file;
     }
     //주소 문자열 합산
     $address = $request->input('addressNum')
              . '#*'
              . $request->input('addressRoad')
              . '#@'
              . $request->input('addressDetail');
     //email 문자열 합산
     $email = $request->input('email')
            . '@'
            . $request->input('emailDomain');
     //전화번호 포멧으로 문자열 재배치 수정
     $tel = preg_replace('/([0-9]{3})([0-9]{4})([0-9]{4})/',
                         '$1-$2-$3',
                         $request->input('tel'));

     $userData = ['userIndex' => $userIndex,
                  'userPw' => $userPw,
                  'email' => $email,
                  'accumulated' => $accumulated,
                  'address' => $address,
                  'tel' => $tel,
                  'file' => $path,
                  'etc' => $etc];

     $result = $model->userUpdate($userData);

     return redirect('/users');
   }
   //유저 검색
   public function userSearch(Request $request){
     $model = new NoticeBoard();

     $pageLimit = $request->input('searchPageLimit', 5);
     //검색필터를 지정안했으면 false로 초기화
     $filterFir = $request->input('filterFir', false); //첫번쨰 검색어 필터
     $filterSec = $request->input('filterSec', false); //두번쨰 검색어 필터
     //성별 구문 1남 2여 3전체
     $gender = $request->input('gender', '[1-2]');
     //정렬방식 기본 오름차순
     $orderBy = $request->input('orderBy', 'asc');
     //정렬 기준 필드
     $sort = $request->input('sort', 'no');
     $sortOrder = ['sort' => $sort, 'orderBy' => $orderBy];
     //날짜 사이의 데이터 조회 초기값 2000~2050
     $searchDateFir = $request->input('searchDateFir');
     $searchDateSec = $request->input('searchDateSec');
     //휴면,가입 유저상태 조회 값
     $searchUserStatus = $request->input('searchUserAll', null);

     //유저 상태 조회가 사용 또는 휴먼 둘중 하나일경우
     if (!$searchUserStatus) {
       if ($request->input('searchUserActive', null)) { //가입자 조회만 체크할경우
         $searchUserStatus = $request->input('searchUserActive', null);
       } else { //나머지 경우는 휴면 유저 조회밖에 없으므로 else처리
         $searchUserStatus = $request->input('searchUserSleep', null);
       }
     }
     //성별이 체크된 경우 성별 검색을 위해 값을 넣어줌  1=남 2=여
     if ($request->input('gender') == 1) {
       $gender = 1;
     } else if ($request->input('gender') == 2) {
       $gender = 2;
     }

     if ($filterFir && $filterSec) { //검색지정 필터가 2개 다 있을경우
       $searchTextFir = $request->input('searchFirWord'); //첫번쨰 검색필드의 텍스트
       $searchTextSec = $request->input('searchSecWord'); //두번째 검색필드의 텍스트
       $search = ['filterFir' => $filterFir, //첫번째 검색필터
                 'filterSec' => $filterSec, //두번째 검색 필터
                 'searchTextFir' => $searchTextFir, //첫번째 검색필터 필드 값
                 'searchTextSec' => $searchTextSec, //두번째 검색필터 필드 값
                 'searchDateFir' => $searchDateFir, //가입일 첫번째 기준
                 'searchDateSec' => $searchDateSec, //가입일 두번째 기준
                 'searchUserStatus' => $searchUserStatus, //조회할 유저의 상태
                 'gender' => $gender]; //유저의 성별
       $users = $model->searchFullFilter($search, $sortOrder, $pageLimit); //전체조건 조회
     }
     //페이지 조건에 의한 유저 페이징
     $users = $model->getList($pageLimit);
     //현재페이지에 뿌려질 유저의 수
     $perPage = $users->perPage();
     //조회된 건수
     $total = $users->total();
     //view에 표시될 데이터 포맷 처리
     foreach ($users as $user) {
       $user->join_date =  str::substr($user->join_date, 0, 10);
       if ($user->gender == 1) {
        $user->gender = '남';
       } else if ($user->gender == 2) {
        $user->gender = '여';
       }
     }

      $pageView = ['pageLimit' => $pageLimit,
                  'perPage' => $perPage,
                  'total' => $total];

     return view('userList', [
       'users' => $users,
       'pageView' => $pageView
     ]);

   }
}
