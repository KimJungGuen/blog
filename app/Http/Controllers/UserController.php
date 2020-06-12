<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\NoticeBoard;
use Carbon\Carbon;


class UserController extends Controller
{
    //삭제된 유저 복구 *softdelete삭제 복구용
    //$model->withTrashed()->restore();
 
    /**
     * 
     */
    public function index(Request $request) 
    {
    
        $searchData = $this->searchData($request);
        $usersData = $this->usersData($searchData);
        $averageData = $this->averageData(); 
       
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
          $userStatus[$index] = (is_null($user->deleted_at)) ? '사용' : '휴면';
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

    public function userIdCheck(UserRequest $request)
    {
        //
        $msg = '사용가능한 ID입니다.';
        return response()->json(array('check' => true, 'msg' =>$msg));
    }


    public function userPwCheck(Request $request, int $userIndex)
    {
        $userModel = new Noticeboard();

        //해당 유저 인덱스로 세션 생성
        $userPw = md5($request->input('userPw'));

        $result = $userModel->getUserPw($userIndex, $userPw);
    
        
        if ($result) {
          $pwcCheck = true;
          $request->session()->put('userIndex', $userIndex);
        } else {
          $pwcCheck = false;
        }
      
        return response()->json(array('pwCheck' => $pwcCheck));
    }

    public function userRegister(UserRequest $request)
    {
        $msg = '';
        $result = false;
        try{
            if ($request->file('file')) {
                $path = $request->file('file')->store('userFile');
            } else {
                $path = null;
            }

            $userModel = new NoticeBoard();
            //들어온 전화번호를 -문자를 를 삽입한다.

            $tel = preg_replace('/([0-9]{3})([0-9]{4})([0-9]{4})/', '$1-$2-$3', $request->input('tel'));

            $email = $request->input('email') . '@' . $request->input('emailDomain');

            $addressNum = $request->input('addressNum');
            $addressRoad = $request->input('addressRoad');
            $addressDetail = $request->input('addressDetail');
            $userId = $request->input('userId');
            $userPw = md5($request->input('userPw'));
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
                'join_date' => $join_date,
                'marry' => $marry,
                'tel' => $tel,
                'file' => $path
            ];

            $result = $userModel->userInsert($user);

            if (isset($result)) {
                $msg = '회원등록에 성공했습니다.';
            }
        } catch (\Exception $e) {
            $msg = '회원등록에 실패했습니다.';
        }

        return response()->json(array('msg' => $msg, 'result' => $result));
    }

    //유저 업데이트 페이지에 해당 PK의 유저정보 표시
    public function userUpdatePage(Request $request, int $userIndex)
    {
        //세션이 있는지 체크
        if ($request->session()->has('userIndex')) {
            //세션 헤제
            $request->session()->forget('userIndex');
            $userModel = new NoticeBoard();
            $user = $userModel->getUser($userIndex);

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
        $msg = '';
        $result = 0;
        try{
            $userModel = new NoticeBoard();
            $userIndex = $request->input('userIndex');
            $user = $userModel->getUser($userIndex);
            $fileExt = $request->allFiles();
            $etc = $request->input('etc');
            $accumulated = $request->input('accumulated');

            $inputUserPw = $request->input('userPw', null);

            if (is_null($inputUserPw)) {
                $user = $userModel->getUser($userIndex);
                $userPw = $user->user_pw;
            } else {
                $inputUserPwLength = Str::of($inputUserPw)->length();

                $user = $userModel->getUser($userIndex);
    
                if($inputUserPwLength < 5 || $inputUserPwLength > 20) {
                    throw new \Exception('비밀번호는 5자에서 20자 사이로 입력해주세요.');
                }

                $userPw = md5($inputUserPw);
            }

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

            $result = $userModel->userUpdate($userData);

            if (isset($result)) {
                $msg = '업데이트에 성공했습니다.';
            } else {
                throw new \Exception('업데이트에 실패했습니다.');
            }

        } catch (\Exception $e) {
            $msg = $e->getMessage();
        }

        return response()->json(array('msg' => $msg, 'result' => $result));
    }
}
