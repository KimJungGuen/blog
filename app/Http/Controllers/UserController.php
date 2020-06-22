<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\NoticeBoard;
use Carbon\Carbon;

/**
 * @file    UserController.php
 * @brief   유저관리 컨트롤러 클래스
*/
class UserController extends Controller
{

    /**
     * @brief   유저 관리 메인
     * @param   Request $request
     * @return  view
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

     /**
     * @brief   유저 관리 > 유저 수정 화면
     * @param   int $userIndex : 해당 유저의 고유번호
     * @return  view(array $userData)
    */
    public function userDetail(int $userIndex)
    {

        //수정 요청을 한 유저의 데이터 표시
        $userModel = new NoticeBoard();
        $user = $userModel->getUserInfo($userIndex);
        $path = $user['file'];
        $imgUrl = Storage::url($path);
        $userData = [
            'userIndex' => $userIndex,
            'email' => Str::of($user['email'])->before('@'),
            'emailDomain' => Str::of($user['email'])->after('@'),
            'tel' => preg_replace('/-/', '',$user['tel']),
            'addressNum' => $user['address_num'],
            'addressRoad' => $user['address_road'],
            'addressDetail' => $user['address_detail'],
            'etc' => $user['etc'],
            'accumulated' => $user['accumulated'],
            'imgUrl' => $imgUrl
        ];

        return view('userUpdate',array(
            'userData' => $userData
        ));
    
    }

    /**
     * @brief   유저 관리 > 유저 등록
     * @param   UserRequest $request : 입력 데이터
     * @return  redirect /user
     * @throws  Exception
    */
    public function userRegister(UserRequest $request)
    {   

        $userModel = new NoticeBoard();
        
        $msg = '';
        $result = false;
        $multipleCount = $request->input('multipleCount');
        $path = array();
        $marry = array();
        $userOrder = array();

        try{
            for ($index = 0; $index < $multipleCount; $index++) {
                
                if ($request->file('file_' . $index)) {
                    $path[$index] = $request->file('file_' . $index)->store('userFile');
                } else {
                    $path[$index] = null;
                }

                //전화번호 및 이메일 포맷 변경
                $tel[$index] = $this->phoneNumberFormat($request->input('tel')[$index]);
                $email[$index] = $request->input('email')[$index] . '@' . $request->input('emailDomain')[$index];
                $userPw[$index] = md5($request->input('userPw')[$index]);
                $marry[$index] = $request->input('marry_' . $index);
                $userOrder[$index] = $userModel->getUserLastOrder() + $index + 1;
            }
            
            $user = [
                'userId' => $request->input('userId'),
                'userPw' => $userPw,
                'name' => $request->input('name'),
                'gender' => $request->input('gender'),
                'age' => $request->input('age'),
                'accumulated' => $request->input('accumulated'),
                'email' => $email,
                'addressNum' => $request->input('addressNum'),
                'addressRoad' => $request->input('addressRoad'),
                'addressDetail' => $request->input('addressDetail'),
                'etc' => $request->input('etc'),
                'join_date' => Carbon::now(),
                'marry' => $marry,
                'tel' => $tel,
                'file' => $path,
                'userOrder' => $userOrder,
                'multipleCount' => $multipleCount,
            ];
            

            $userModel = new NoticeBoard();

            $result = $userModel->userInsert($user);
            if ($result) {
                $msg = '회원등록에 성공했습니다.';
                return redirect('/user')->with('msg', $msg);
            }
        } catch (\Exception $e) {
            $msg = '회원등록에 실패했습니다.';
            dd($e->getMessage());
            return redirect('/user')->withErrors(array($msg));;
        }
    }




    /**
     * @brief   유저 관리 > 유저 수정
     * @param   UserRequest $request : 입력 데이터, 
     *          int $userIndex : 해당 유저의 고유번호
     * @return  redirect userUpdate
     * @throws  Exception 
    */
    public function userUpdate(UserRequest $request, int $userIndex)
    {
        $msg = '';
        $result = 0;
        try{
            $userModel = new NoticeBoard();
            $user = $userModel->getUserInfo($userIndex);
            $fileExt = $request->allFiles();
            $inputUserPw = $request->input('userPw');
            $inputUserPwCheck = $request->input('userPwCheck');
            $inputUserPwSpecialCharacter = preg_match('/[`~!@#$%^&\*\(\)_=\+\[\]\{\};:\'"<>,\.\/\?\|\\\-]/', $inputUserPw);
            $inputUserPwCharacter = preg_match('/[a-z]/i', $inputUserPw);
            $inputUserPwNumber = preg_match('/[0-9]/', $inputUserPw);
            

            

            //입력된 유저의 비밀번호가 없을 경우 기존 비밀번호 사용
            if (is_null($inputUserPw)) {
                $userPw = $user->user_pw;
            } else {
                $inputUserPwLength = Str::of($inputUserPw)->length();

                if ($inputUserPwLength < 5 || $inputUserPwLength > 20) {
                    throw new \Exception('비밀번호는 5자에서 20자 사이로 입력해주세요.');
                } else if ($inputUserPw != $inputUserPwCheck) {
                    throw new \Exception('비밀번호와 비밀번호 확인이 일치하지않습니다.');
                } else if (empty($inputUserPwSpecialCharacter) || empty($inputUserPwCharacter) || empty($inputUserPwNumber)) {
                    throw new \Exception('비밀번호는 영문, 숫자, 특수문자 혼용으로 입력해주세요.');
                }

                $userPw = md5($inputUserPw);
            }

            $path = $user['file'];
            
            //새로운 파일이 있을 경우 기존 이미지 삭제 및 새 이미지 등록
            if ($fileExt) {
                Storage::disk('local')->delete($path);
                $path = $request->file('file')->store('userFile' , 'local');
            }

            //전화번호 및 이메일 포맷 변경
            $tel = $this->phoneNumberFormat($request->input('tel'));
            $email = $request->input('email') . '@' . $request->input('emailDomain');

            $userData = [
                'userIndex' => $userIndex,
                'userPw' => $userPw,
                'email' => $email,
                'accumulated' => $request->input('accumulated'),
                'addressNum' => $request->input('addressNum'),
                'addressRoad' => $request->input('addressRoad'),
                'addressDetail' => $request->input('addressDetail'),
                'tel' => $tel,
                'file' => $path,
                'etc' => $request->input('etc')
            ];

            try {
                $result = $userModel->userUpdate($userData);
                $msg = '업데이트에 성공했습니다.';
                return redirect('/userDetail/' . $userIndex)->with('msg', $msg);
            } catch (\Exception $e) {
                $msg = '업데이트에 실패했습니다.';
                return redirect('/userDetail/' . $userIndex)->withErrors(array($msg));
            }

        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return redirect('/userDetail/' . $userIndex)->withErrors(array($msg));
        }
    }

    /**
     * @brief   유저 리스트 > 유저 휴면 (softDelete)
     * @param   Request $request : 유저의 고유 번호
     * @return  json 'msg' : $msg, 
     *               'deleteRow' : $deleteRow
     * @throws  Exception
    */
    public function userSleep(Request $request)
    {
        $msg = '';
        $deleteRow = 0;
        $deleteCheck = 0;
        try{
            $userModel = new NoticeBoard();
            $userIndex = $request->input('userIndex', null);

            if (isset($userIndex)) {
                //삭제된 유저 확인
                $deleteCheck = $userModel->sleepUserCheck($userIndex);

                //이미 삭제된 유저가 없을 경우에 만 실행
                if (!$deleteCheck) {
                    $deleteRow = $userModel->sleepUser($userIndex);
                    for ($index = 0; $index < count($userIndex); $index++) {
                        $orderResult = $userModel->sleepUserOrderChange($userIndex[$index]);
                        if (empty($orderResult)) {
                            $msg = '휴면 유저 순번조정이 실패했습니다.';
                        }
                    }
                    $msg = '선택한 유저가 탈퇴됐습니다.';
                }
            } 
        } catch(\Exception $e) {
            dd($e->getMessage());
            $msg = '회원탈퇴가 실패했습니다.';
        }

        return response()->json(array('msg' => $msg, 'deleteRow' => $deleteRow));
    }


    /**
     * @brief   유저 리스트 > 검색 데이터 처리
     * @param   Request $request : 페이지 전체 데이터
     * @return  array $searchData
    */
    public function searchData(Request $request)
    {
        $searchUserStatus = 'active';
        $searchUserAll = $request->input('searchUserAll', null);
        $searchUserActive = $request->input('searchUserActive', null);
        $searchUserSleep = $request->input('searchUserSleep', null);


        //유저 상태 체크에 대한 값 설정
        if (isset($searchUserAll) || isset($searchUserActive) && isset($searchUserSleep)) {
            $searchUserStatus = 'all';
        } else if (isset($searchUserActive)) {
            $searchUserStatus = 'active';
        } else if (isset($searchUserSleep)) {
            $searchUserStatus = 'sleep';
        }
        $defaultStartDay = Carbon::now()->addMonth(-1)->format('Y-m-d');
        $defaultEndDay = Carbon::now()->format('Y-m-d');
        
        $searchData = [
            'filterFirst' => $request->input('filterFirst', null), //첫 번째 검색필터
            'filterSecond' => $request->input('filterSecond', null), //두 번째 검색 필터
            'searchFirstWord' => $request->input('searchFirstWord', null), //첫 번째 검색어
            'searchSecondWord' => $request->input('searchSecondWord', null), //두 번째 검색어
            'searchDateFirst' => $request->input('searchDateFirst', $defaultStartDay), //날짜 검색 시작일
            'searchDateSecond' => $request->input('searchDateSecond', $defaultEndDay), //날짜 검색 종료일
            'gender' => $request->input('gender', 'all'), //성별
            'userStatus' => $searchUserStatus, //유저 상태
            'sortIndex' => $request->input('sortIndex', 'no'), //정렬 기준
            'orderBy' => $request->input('orderBy', 'asc'),  //정렬 방식
            'page' => $request->input('page', 1),
            'searchPageLimit' => $request->input('searchPageLimit', 5)
        ];  

        if(
            ((isset($searchData['filterFirst']) && isset($searchData['searchFirstWord']))
            || (isset($searchData['filterSecond']) && isset($searchData['searchSecondWord']))
            || ($searchData['searchDateFirst'] != $defaultStartDay)
            || ($searchData['searchDateSecond'] != $defaultEndDay)
            || ($searchData['gender'] != 'all'))
            && ($searchData['sortIndex'] == 'no')
        ) {
            $searchData['sortIndex'] = 'index';
        }
        return $searchData;
    }    

    /**
     * @brief   유저 리스트 > 유저 리스트
     * @param   array $searchData : 검색 조건
     * @return  array $usersData
    */
    public function usersData(array $searchData)
    {
        $userModel = new NoticeBoard();
        $users = $userModel->getUserList($searchData);
        $userStatus = array();
        //유저 데이터 포맷 변경
        foreach ($users as $index => $user) {
            $user->join_date = str::substr($user->join_date, 0, 10);
            $user->gender = ($user->gender == 'M') ? '남' : '여';
            $userStatus[$index] = (is_null($user->deleted_at)) ? '사용' : '휴면';
            $user->accumulated = number_format($user->accumulated);
        }
        //$userModel->withTrashed()->restore();
        $usersData = [
            'users' => $users,
            'userStatus' => $userStatus
        ];

        return $usersData;
    }

    /**
     * @brief   유저 리스트 > 유저 통계 데이터
     * @param   void
     * @return  array $aveData
    */
    public static function averageData()
    {
        $userModel = new NoticeBoard();
        $aveData = $userModel->averageData();
        
        //통계 데이터 포맷 변경
        foreach($aveData as $index => $data) {
            $aveData[$index] = number_format($data);
        }

        return $aveData;
    }

    /**
     * @brief   유저 리스트 > 유저 비밀번호 확인
     * @param   Request $request : 입력 데이터
     *                  int $userIndex : 해당 유저의 고유번호
     * @return  json 'pwCheck' : $pwCheck, 'userIndex' : $userIndex
    */
    public function userPwCheck(Request $request, int $userIndex)
    {
        $userModel = new Noticeboard();

        $userPw = md5($request->input('userPw'));
     
        //유저 비밀번호 확인
        $result = $userModel->userPwCheck($userIndex, $userPw);
        
        if ($result) {
            $pwCheck = true;
            return response()->json(array(
                'pwCheck' => $pwCheck,
                'userIndex' => $userIndex
            ));
        } else {
            $pwCheck = false;
            return response()->json(array(
                'pwCheck' => $pwCheck,
                'userIndex' => null
            ));
        }
    }
    
    /**
     * @brief   유저 등록 > 유저 아이디 중복 확인
     * @param   Request $request : 입력 데이터
     * @return  json 'msg' : $msg, 'check' : $check
    */
    public function userIdCheck(UserRequest $request)
    { 
        $userModel = new NoticeBoard();
        $inputUserId = $request->input('userId');
        $userId = $userModel->getUserId($inputUserId);
        $userIdLength = Str::of($inputUserId)->length();
        $msg = "사용가능한 ID입니다.";
        $check = true;
  
        //입력된 아이디와 기존 아이디를 비교
        if($userId == $inputUserId) {
            $msg = "중복된 ID입니다.";
            $check = false;
        }

        //입력 상태 및 길이 확인
        if($userIdLength == 0) {
            $msg = "ID를 입력해주세요.";
            $check = false;
        } else if($userIdLength <= 4 || $userIdLength >= 21) {
            $msg = "ID를 5자에서 20자 사이로 입력해주세요.";
            $check = false;
        }

        return response()->json(array('check' => $check, 'msg' =>$msg));
    }

    /**
     * @brief   전화번호 포맷 변환
     * @param   String $telNumber : 전화번호
     * @return  String $tel
    */
    public function phoneNumberFormat(String $telNumber)
    {
        //전화번호 길이
        $telLength = Str::of($telNumber)->length();
        $tel = $telNumber;

        /**
         * 전화번호 패턴
         * 11
         * *** - **** - ****
         * 
         * 10
         * 01* - *** - ****
         * 02 - **** - ****
         * 0** - *** - ****
         * 00* - *** - ****
         * 
         * 9
         * ***** - ****
         * 
         * 8
         * **** - ****
         * 00* - * - ****
        */
        //전화번호 맨 앞자리 패턴을 기준으로 - 삽입 위치를 조정
        // i,g,      * + . ? 
        switch ($telLength) {
            case 11:
                $tel = preg_replace('/([0-9]{3})([0-9]{4})([0-9]{4})/', '$1-$2-$3', $telNumber);
                break;
            case 10:
                if (preg_match('/^(02)/', $telNumber)) {
                    $tel = preg_replace('/([0-9]{2})([0-9]{4})([0-9]{4})/', '$1-$2-$3', $telNumber);
                } else {
                    $tel = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '$1-$2-$3', $telNumber);
                }
                break;
            case 9:
                $tel = preg_replace('/([0-9]{5})([0-9]{4})/', '$1-$2', $telNumber);
                break;
            case 8:
                if (preg_match('/^(00[0-9])/', $telNumber)) {
                    $tel = preg_replace('/([0-9]{3})([0-9]{1})([0-9]{4})/', '$1-$2-$3', $telNumber);
                } else {
                    $tel = preg_replace('/([0-9]{4})([0-9]{4})/', '$1-$2', $telNumber);
                }
                break;
        }
        return $tel;
    }

    /**
     * @brief 유저 순번 변경
     * @param Request $request : 유저 순번, 유저 고유 번호
     * @return ridiact /users
     * @throws \Exception
    */
    public function userOrderChange(Request $request)
    {
        try {

            $msg = '순번 변경이 실패했습니다.';
            $updateCheck = false;
            $userModel = new Noticeboard();
            $userOrder = $request->input('userOrder', NuLL);
            $userOrderSort = Arr::sortRecursive($userOrder);
            $userIndex = $request->input('userIndex', Null);
            
            if (isset($userOrderSort) || isset($userIndex)) {
                foreach ($userOrderSort as $index => $Order) {
                    $updateUserIndex =  $userIndex[$index];
                    $result = $userModel->userOrderChange($updateUserIndex,$Order);
                    if ($result == 1) {
                        $msg = '순번이 변경됐습니다';
                        $updateCheck = true;
                    }
                }
            }
            
            return response()->json(array('msg' => $msg, 'updateCheck' => $updateCheck));
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            //$msg = '순번 변경이 실패했습니다.';
            $updateCheck = false;
            return response()->json(array('msg' => $msg, 'updateCheck' => $updateCheck));
        }
    }
}