<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;


/**
 * @file    NoticeBoard.php
 * @brief   유저 관리 모델 클래스
*/
class NoticeBoard extends Model
{
    use SoftDeletes;
    protected $table = 'user';
    public $timestamps = false;

    /**
     * @brief   전체 유저 조회
     * @param   void
     * @return  Collection $users
    */
    public function getUserAll() 
    {
        $users = $this->withTrashed()
                      ->get();

        return $users;
    }

    /**
     * @brief   유저 비밀번호 확인
     * @param   int $userIndex : 유저 고유번호, String $userPw : 입력된 비밀번호
     * @return  int $checkValue
    */
    public function userPwCheck(int $userIndex, String $userPw)
    {
        $checkValue = $this->withTrashed()
                           ->Where('index', $userIndex)
                           ->Where('user_pw', $userPw)
                           ->count();
        
        return $checkValue;
    }

    /**
     * @brief   유저 아이디 조회
     * @param   String $inputUserId : 입력된 아이디
     * @return  String $userId
    */
    public function getUserId(String $inputUserId)
    {
        $userId = $this->withTrashed()
                       ->where('user_id', $inputUserId)
                       ->value('user_id');

        return $userId;
    }

    /**
     * @brief   유저 조회
     * @param   void
     * @return  Collection $result
    */
    public function getUserInfo(int $userIndex)
    {
        $user = $this->withTrashed()
                     ->firstWhere('index', $userIndex);

        return $user;
    }

    /**
     * @brief   유저 마지막 순번 조회
     * @param   void
     * @return  int $userN 
    */
    public function getUserLastOrder()
    {
        $userNo = $this->max('no');

        return $userNo;
    }

    /**
     * @brief   유저 순번 변경
     * @param   int $userIndex, int $userNo : 유저 고유 번호, 유저 순번
     * @return  int $result
    */
    public function userOrderChange(int $userIndex, int $userOrder)
    {
        $result = $this->withTrashed()
                       ->where('index', $userIndex)
                       ->update(['no' => $userOrder]);
                       

        return $result;
    }

    /**
     * @brief   유저 등록
     * @param   array $user : 입력된 데이터
     * @return  boolean $result
    */
    public function userInsert(array $user) 
    {   
        for($index = 0; $index < $user['multipleCount'] ; $index++){
            $users[$index] = [
                'user_id' => $user['userId'][$index],
                'user_pw' => $user['userPw'][$index],
                'name' => $user['name'][$index],
                'gender' => $user['gender'][$index],
                'age' => $user['age'][$index],
                'accumulated' => $user['accumulated'][$index],
                'email' => $user['email'][$index],
                'address_num' => $user['addressNum'][$index],
                'address_road' => $user['addressRoad'][$index],
                'address_detail' => $user['addressDetail'][$index],
                'etc' => $user['etc'][$index],
                'join_date' => $user['join_date'],
                'marry' => $user['marry'][$index],
                'tel' => $user['tel'][$index],
                'no' => $user['userOrder'][$index],
                'file' => (isset($user['file'][$index])) ? $user['file'][$index] : NULL
            ];
            
        }
        $result = $this->insert($users);
        return $result;
    }

    /**
     * @brief   유저 수정
     * @param   array $userData : 입력된 데이터
     * @return  int $result
    */
    public function userUpdate(array $userData) 
    {
        $result = $this->withTrashed()
                      ->where('index', $userData['userIndex'])
                      ->update([
                          'user_pw' => $userData['userPw'],
                          'email' => $userData['email'],
                          'accumulated' => $userData['accumulated'],
                          'address_num' => $userData['addressNum'],
                          'address_road' => $userData['addressRoad'],
                          'address_detail' => $userData['addressDetail'],
                          'tel' => $userData['tel'],
                          'file' => $userData['file'],
                          'etc' => $userData['etc']
                        ]);

        return $result;
    }

    /**
     * @brief   첫 번째 유저 검색 조건 필터
     * @param   array $search : 검색 조건(filterFirst + filterTextFirst)
     * @return  Builder $query
    */
    public function scopeSearchFilterFirst($query, array $search)
    {
        //필터와 검색어 확인
        if (isset($search['filterFirst']) && isset($search['searchFirstWord'])) {
            //email 필터일 경우 다른 검색 조건 적용
            if($search['filterFirst'] == 'email') {
                return $query->where($search['filterFirst'], 'like', '%'. $search['searchFirstWord'] .'%@%'); 
            }
            //설정된 필터와 검색어로 검색
            return $query->where($search['filterFirst'], 'like', '%'. $search['searchFirstWord'] .'%'); 
        } else {
            return $query;
        }
    }

    /**
     * @brief   두 번째 유저 검색 조건 필터
     * @param   array $search : 검색 조건(filterSecond + filterTextSecond)
     * @return  Builder $query
    */
    public function scopeSearchfilterSecond($query, array $search)
    {
        //필터와 검색어 확인
        if (isset($search['filterSecond']) && isset($search['searchSecondWord'])) {
            //email 필터일 경우 다른 검색 조건 적용
            if($search['filterSecond'] == 'email') {
                return $query->where($search['filterSecond'], 'like', '%'. $search['searchSecondWord'] .'%@%'); 
            }
                //설정된 필터와 검색어로 검색
                return $query->where($search['filterSecond'], 'like', '%'. $search['searchSecondWord'] .'%'); 
        } else {
            return $query;
        }
    }

    /**
     * @brief   유저 검색 조건 상태
     * @param   array $search : 검색 조건(유저 상태)
     * @return  Builder $query
    */
    public function scopeSearchStatus($query, array $search) 
    {
        switch ($search['userStatus']) {
            case 'all':
            return $query->withTrashed(); //모든 유저 만 검색
            break;
        case 'active':
            return $query;                //사용 유저 만 검색
            break;
        case 'sleep':
            return $query->onlyTrashed(); //휴면 유저 만 검색
            break;
        default:
            return $query;
            break;
        }
    }

    /**
     * @brief   유저 검색 조건 날짜
     * @param   array $search : 검색 조건(날짜)
     * @return  Builder $query
    */
    public function scopeSearchDateFormat($query, array $search)
    {
        //정확한 비교를 위해 날짜 포맷 변경
        $search['searchDateFirst'] = Carbon::createFromFormat('Y-m-d', $search['searchDateFirst'])->format("Y-m-d 00:00:00");
        $search['searchDateSecond'] = Carbon::createFromFormat('Y-m-d', $search['searchDateSecond'])->format("Y-m-d 23:59:59");

        return $query->whereBetween('join_date', [$search['searchDateFirst'], $search['searchDateSecond']]);
    }

    /**
     * @brief   유저 검색
     * @param   array $search : 검색 조건
     * @return 
    */
    public function getUserList(array $search) 
    {
        $users = $this->searchStatus($search) 
                      ->searchFilterFirst($search)  
                      ->searchfilterSecond($search) 
                      ->when($search['gender'] == 'M' , function($query, $search) { //유저 성별에 따른 조건 검색
                         return $query->where('gender', 'M'); // 1 = 남
                      })
                      ->when($search['gender'] == 'F', function($query, $search) {
                          return $query->where('gender', 'F'); //  2 = 여
                      })
                      ->searchDateFormat($search)
                      ->orderBy($search['sortIndex'], $search['orderBy']) //검색 결과 정렬
                      ->orderBy('index', 'asc')
                      ->paginate($search['searchPageLimit']);   

        return $users;
    }

    /**
     * @brief   유저 삭제
     * @param   array $userIndex : 유저 고유 번호 []
     * @return  int $result
    */
    public function sleepUser(array $userIndex)
    {
      $result = $this->whereIn('index', $userIndex)->Delete();

      return $result;
    }

    /**
     * @brief   유저 삭제 확인 
     * @param   array $userIndex : 유저 고유 번호 []
     * @return  int $result
    */
    public function sleepUserCheck(array $userIndex)
    {
        $result = $this->withTrashed()->whereIn('index', $userIndex)->whereNotNull('deleted_at')->count();

        return $result;
    }

    /**
     * @brief   휴면 유저 순번 재할당
     * @param   int $userIndex : 유저 고유 번호
     * @return  int $result
    */
    public function sleepUserOrderChange($userIndex)
    {
        $result = $this->withTrashed()
                       ->where('index', $userIndex)
                       ->update(['no' => 99999999]);

        return $result;
    }



    /**
     * @brief   유저 리스트 > 유저 통계
     * @param   void
     * @return  Collection $result
    */
    public function averageData() 
    {
        $sqlResult = DB::select(
            "select 
            (select count('index') from user) AS total,
            (select count('index') from user where deleted_at IS NOT NULL) AS sleep,
            (select count('index') from user where deleted_at IS NULL) AS active,
            (select count('index') from user where gender = 'M') AS male,
            (select count('index') from user where gender = 'F') AS female,
            (select count('index') from user where age < 20) AS children,
            (select count('index') from user where age between 20 and 39) AS young,
            (select count('index') from user where age between 40 and 59) AS adult,
            (select count('index') from user where age >= 60) AS old,
            (select count('index') from user where accumulated < 1000) AS minAccumulated,
            (select count('index') from user where accumulated between 1000 and 9999) AS middleAccumulated,
            (select count('index') from user where accumulated >= 10000) AS hightAccumulated,
            (select sum(accumulated) from user) AS totalAccumulated"
        );

        $result = collect($sqlResult[0]);
        return $result;
    }
}