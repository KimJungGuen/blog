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

  //통계창 데이터 전체 데이터를 가져와서 가공
  public function average() 
  {
    $noticeBoardModel = new NoticeBoard();

    //전체 유저 데이터
    $users = $noticeBoardModel->getUserAll();
    //총 계정
    $total = count($users);
    //사용 계정
    $active = 0;
    //남자
    $male = 0;
    //20~30대
    $young = 0;
    //40~50대
    $adult = 0;
    //60대이상
    $old = 0;
    //적립금 1,000원 이상 9,999이하
    $middleAccumulated = 0;
    //적립금 10,000이상
    $hightAccumulated = 0;
    //총 적립금
    $totalAccumulated = 0;

    foreach($users as $user) {
      //휴면 유저가 아닐 경우
      if (!$user->deleted_at) {
        $active += 1;
      }

      //남성일 경우
      if ($user->gender == 1) {
        $male += 1;
      }

      /**
       * 1번째 if 19세초과 일 경우
       * 2번쨰 if 40세 미만
       * 1번째 elseif 40세 이상 60세 미만
       * 2번째 elseif 60세 이상
       */
      if ($user->age > 19) {
        if($user->age < 40) {
          $young += 1;
        } else if ($user->age >= 40 && $user->age < 60) {
          $adult += 1;
        } else if ($user->age >= 60) {
          $old += 1;
        }
      }

      /**
       * 1번째 if 적립금 1,000원 이상
       * 2번째 if 9,999원 이하
       * elseif 10,000원 이상
       */
      if ($user->accumulated >= 1000) {
        if ($user->accumulated <= 9999) {
          $middleAccumulated += 1;
        } else if ($user->accumulated >= 10000) {
          $hightAccumulated += 1;
        }
      }
      $totalAccumulated += $user->accumulated;
    }

    //휴면 계정
    $sleep = $total - $active;
    //여성
    $female = $total - $male;
    //19세 이하
    $children = $total - ($young + $adult + $old);
    //적립금 1,000원 미만
    $minAccumulated = $total - ($middleAccumulated + $hightAccumulated);
    //총 적립금 숫자 포맷 
    $totalAccumulated = number_format($totalAccumulated); 

    $aveData = array(
      'total' => $total, //총계정
      'active' => $active, //사용계정
      'sleep' => $sleep, //휴면계정
      'male' => $male, //남
      'female' => $female, //여
      'children' => $children, //19세이하
      'young' => $young, //20~30대
      'adult' => $adult, //40~50대
      'old' => $old, //60대 이상
      'minAccumulated' => $minAccumulated, //적립금 1,000원 미만
      'middleAccumulated' => $middleAccumulated,//적림금 1,000원 이상 9999원 미만
      'hightAccumulated' => $hightAccumulated, //적립금 10,000원 이상
      'totalAccumulated' => $totalAccumulated //총 적립금
    );

    return $aveData;
  }

  //통계장 데이터 가져올때 가공
  public function sqlAverage()
  {
    $noticeBoardModel = new NoticeBoard();
    $aveData = $noticeBoardModel->average();
    $aveData['totalAccumulated'] = number_format($aveData['totalAccumulated']);
    return $aveData;
  }

  //검색 & 유저 리스트 페이지
  public function page(array $page)
  {
    
    $noticeBoardModel = new NoticeBoard();
    $userStatus = array();
    //현재페이지에 뿌려질 유저의 수
    $perPage = $page['users']->perPage();
    //조회된 건수
    $total = $page['users']->total();
    //통계 계산
    $aveData = $this->average();
    $aveData = $this->sqlAverage();
    //view에 표시될 데이터 포맷 처리
    foreach ($page['users'] as $index => $user) {
      $user->join_date =  str::substr($user->join_date, 0, 10);
      $user->gender = ($user->gender == 1) ? '남' : '여';
      $userStatus[$index] = ($user->deleted_at) ? '휴면' : '사용';
      $user->accumulated = number_format($user->accumulated);
    }

    $pageView = [
      'pageLimit' => $page['pageLimit'],
      'perPage' => $perPage,
      'total' => $total
    ];

    return view('userList', array(
      'users' => $page['users'],
      'pageView' => $pageView,
      'userStatus' => $userStatus,
      'searchData' => $page['searchData'],
      'avgData' => $aveData
    ));
  }
  
  //메인 페이지
  public function users(Request $request)
  {
    $noticeBoardModel = new NoticeBoard();
    
    //처음 페이지에 들어갈떄 표시할 유저의 수는 5로 지정
    $pageLimit = $request->input('pageLimit', 5);
    //페이지 조건에 의한 유저 페이징
    $searchData = $this->searchData($request);
    $users = $noticeBoardModel->getList($pageLimit, $searchData);

    $page = [
      'users' => $users,
      'pageLimit' => $pageLimit,
      'searchData' => $searchData
    ];    

    return $this->page($page);
  }

  //ID중복체크 요청처리 ajax json타입으로 반환
  public function userIdCheck(UserRequest $request)
  {
    $msg = '사용가능한 ID입니다.';
    return response()->json(array('check' => true, 'msg' =>$msg));
  }

  //유저등록
  public function userRegister(UserRequest $request)
  {
    $msg = '회원등록이 되었습니다.';
    $result = null;
    try{
      if ($request->file('file')) {
        $path = $request->file('file')->store('userFile');
      } else {
        $path = null;
      }

      $noticeBoardModel = new NoticeBoard();
      //들어온 전화번호를 -문자를 를 삽입한다.
      $tel = preg_replace('/([0-9]{3})([0-9]{4})([0-9]{4})/', '$1-$2-$3', $request->input('tel'));

      $email = $request->input('email') . '@' . $request->input('emailDomain');

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

      $user = [
        'userId' => $userId,
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
        'file' => $path
      ];
      
      $result = $noticeBoardModel->userInsert($user);

    } catch (\Exception $e) {
      $msg = '회원등록에 실패했습니다.';
    }

    return response()->json(array('msg' => $msg, 'result' => $result));
  }

  //유저 비밀번호 확인
  public function userPwCheck(UserRequest $request, int $userIndex)
  {
    //해당 유저 인덱스로 세션 생성
    $request->session()->put('userIndex', $userIndex);
 
    return response()->json(array('pwCheck' => true));
  }

  //유저 업데이트 페이지에 해당 PK의 유저정보 표시
  public function userUpdatePage(Request $request, int $userIndex)
  {
    //세션이 있는지 체크
    if ($request->session()->has('userIndex')) {
      //세션 헤제
      $request->session()->forget('userIndex');
      $noticeBoardModel = new NoticeBoard();
      $user = $noticeBoardModel->getUser($userIndex);

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

      //img표시를 위해 파일의 위치 저장
      $path = $user['file'];
      $imgUrl = Storage::url($path);

      $userData = [
        'userIndex' => $userIndex,
        'userPw' => $userPw,
        'email' => $email,
        'emailDomain' => $emailDomain,
        'tel' => $tel,
        'addressNum' => $addressNum,
        'addressRoad' => $addressRoad,
        'addressDetail' => $addressDetail,
        'etc' => $etc,
        'accumulated' => $accumulated,
        'imgUrl' => $imgUrl
      ];

      return view('userUpdate',['userData' => $userData]);
    } else {
      return redirect('/users');
    }
  }

  //유저 Update
  public function userUpdate(UserRequest $request)
  {
    $msg = '회원정보 업데이트 성공';
    $result = 0;

    try{
      $noticeBoardModel = new NoticeBoard();
      $userIndex = $request->input('userIndex');
      $user = $noticeBoardModel->getUser($userIndex);
      $fileExt = $request->allFiles();
      $etc = $request->input('etc');
      $accumulated = $request->input('accumulated');
      $userPw = $request->input('userPw');

      if ($fileExt) {
        $path = $request->file('file')->store('userFile' , 'local');
      } else {
        $path = $user['file'];
      }

      //email 문자열 합산
      $email = $request->input('email') . '@' . $request->input('emailDomain');

      //전화번호 포멧으로 문자열 재배치 수정
      $tel = preg_replace('/([0-9]{3})([0-9]{4})([0-9]{4})/', '$1-$2-$3', $request->input('tel'));

      $addressNum = $request->input('addressNum');
      $addressRoad = $request->input('addressRoad');
      $addressDetail = $request->input('addressDetail');

      $userData = [
        'userIndex' => $userIndex,
        'userPw' => $userPw,
        'email' => $email,
        'accumulated' => $accumulated,
        'addressNum' => $addressNum,
        'addressRoad' => $addressRoad,
        'addressDetail' => $addressDetail,
        'tel' => $tel,
        'file' => $path,
        'etc' => $etc
      ];

      $result = $noticeBoardModel->userUpdate($userData);

    } catch (\Exception $e) {
      $msg = '업데이트에 실패했습니다.';
    }

    return response()->json(array('msg' => $msg, 'result' => $result));
  }

  //유저 검색 필드 값 가공
  public function searchData(Request $request)
  {
    //현재 요청이들어온 url을 확인한다.
    $currentUrl = url()->current();
    // 들어온 url에 userSearch가 있을 경우 true
    $urlFind = Str::contains($currentUrl, 'userSearch');

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

    //정렬 userList hidden 값
    $listOrderBy = $request->input('listOrderBy', 'asc');
    $listSort = $request->input('listSort', 'index');

    //users url에서 요청이 왔을경우 검색폼 유지를 위한 할당
    if (!$urlFind) {
      $sort = $listSort;
      $orderBy = $listOrderBy;
    }

    //유저 상태 조회가 사용 또는 휴먼 둘중 하나일경우 또는 다일경우
    if (!empty($searchUserAll) || !empty($searchUserActive) && !empty($searchUserSleep)) {
      $searchUserStatus = 'all';
    } else if (!empty($searchUserActive)) {
      $searchUserStatus = 'active';
    } else if (!empty($searchUserSleep)) {
      $searchUserStatus = 'sleep';
    }

    $searchData = [
      'filterFir' => $filterFir, //첫번째 검색필터
      'filterSec' => $filterSec, //두번째 검색 필터
      'searchTextFir' => $searchTextFir, //첫번째 검색필터 필드 값
      'searchTextSec' => $searchTextSec, //두번째 검색필터 필드 값
      'searchDateFir' => $searchDateFir, //가입일 첫번째 기준
      'searchDateSec' => $searchDateSec, //가입일 두번째 기준
      'gender' => $gender, //성별
      'userStatus' => $searchUserStatus, //유저 상태
      'sort' => $sort, //정렬 기준 검색 searchForm
      'orderBy' => $orderBy,  //정렬 방식 searchForm
      'listSort' => $listSort, //정렬 기준 검색 listForm
      'listOrderBy' => $listOrderBy
    ]; // 정렬 방식 검색 listForm

    return $searchData;
  }

  //유저 검색
  public function userSearch(UserRequest $request)
  {
    $noticeBoardModel = new NoticeBoard();
    //요청된 검색 필드 값 정리
    $searchData = $this->searchData($request);
    $pageLimit = $request->input('searchPageLimit', 5);
  
    //검색조건이 있을 경우
    $users = $noticeBoardModel->searchUsers($searchData, $pageLimit);

    //필터 선택과 검색어 필드가 비었을 때 검색 버튼을 눌렀을 경우
    if (!$searchData['filterFir'] && !$searchData['filterSec'] || !$searchData['searchTextFir'] && !$searchData['searchTextSec']) {
      $users = $noticeBoardModel->getUserAllPaginate($searchData, $pageLimit);
    }

    $page = [
      'users' => $users,
      'pageLimit' => $pageLimit,
      'searchData' => $searchData
    ];

    return $this->page($page);
  }

  //유저 삭제
  public function userDelete(Request $request)
  {
    $msg = '선택한 유저가 탈퇴됐습니다.';
    $deleteRow = 0;
    $deleteCheck = 0;
    try{

      $noticeBoardModel = new NoticeBoard();
      $userIndex = $request->input('userIndex', false);
    
      if (!empty($userIndex)) {
        //이미 삭제된 유저가 있을 경우 유저의 수 반환
        $deleteCheck = $noticeBoardModel->deleteCheck($userIndex);
        
        //이미 삭제된 유저가 없을경우에만 실행
        if (!$deleteCheck) {
          $deleteRow = $noticeBoardModel->deleteUser($userIndex);
        }
      } 
    } catch(\Exception $e) {
      $msg = '회원탈퇴가 실패했습니다.';
    }

    return response()->json(array('msg' => $msg, 'deleteRow' => $deleteRow
    ));
  }
}
