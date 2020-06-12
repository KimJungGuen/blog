<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NoticeBoard extends Model
{
  use SoftDeletes;
  protected $table = 'user';
  public $timestamps = false;

  //모든 유저 조회
  public function getUserAll() 
  {
    $users = $this->withTrashed()
                  ->get();

    return $users;
  }

  //해당 인덱스 유저 조회 PK기준
  public function getUserPw(int $userIndex, String $userPw)
  {
    $user = $this->withTrashed()
                 ->Where('index', $userIndex)
                 ->Where('user_pw', $userPw)
                 ->count();

    return $user;
  }

  //해당 유저id로 조회
  public function getUserId(String $userId)
  {
    $user = $this->withTrashed()
                 ->where('user_id', $userId)
                 ->value('user_id');

    return $user;
  }

  //하나의 유저 조회
  public function getUser(int $userIndex)
  {
    $user = $this->withTrashed()
                 ->firstWhere('index', $userIndex);

    return $user;
  }

  //유저 등록
  //성공시 true
  public function userInsert(array $user) 
  {
    $result = $this->insert ([
      'user_id' => $user['userId'],
      'user_pw' => $user['userPw'],
      'name' => $user['name'],
      'gender' => $user['gender'],
      'age' => $user['age'],
      'accumulated' => $user['accumulated'],
      'email' => $user['email'],
      'address_num' => $user['addressNum'],
      'address_road' => $user['addressRoad'],
      'address_detail' => $user['addressDetail'],
      'etc' => $user['etc'],
      'join_date' => $user['join_date'],
      'marry' => $user['marry'],
      'tel' => $user['tel'],
      'file' => $user['file']
    ]);
 
    return $result;
  }

  //유저 업데이트
  //성공시 1
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

  //첫번째 검색필드와 검색 키워드 where 로직
  public function scopeSearchFilterFirst($query, array $search)
  {
    //설정된 필터와 키워드가 있을경우
    if (!empty($search['filterFirst']) && !empty($search['searchFirstWord'])) {
      //email 필터를 골랐을 경우 도메인을 검색에서 제외
      if($search['filterFirst'] == 'email') {
        return $query->where($search['filterFirst'], 'like', '%'. $search['searchFirstWord'] .'%@%'); 
      }
      //설정된 필터와 검색어로 검색
      return $query->where($search['filterFirst'], 'like', '%'. $search['searchFirstWord'] .'%'); 
    } else {
      return $query; //없을경우는 설정 제외
    }
  }

  //두번째 검색필드와 검색 키워드 where 로직
  public function scopeSearchfilterSecond($query, array $search)
  {
    //설정된 필터와 키워드가 있을경우
    if (!empty($search['filterSecond']) && !empty($search['searchSecondWord'])) {
      //email 필터를 골랐을 경우 도메인을 검색에서 제외
      if($search['filterSecond'] == 'email') {
        return $query->where($search['filterSecond'], 'like', '%'. $search['searchSecondWord'] .'%@%'); 
      }
      //설정된 필터와 검색어로 검색
      return $query->where($search['filterSecond'], 'like', '%'. $search['searchSecondWord'] .'%'); 
    } else {
      return $query; //없을 경우는 설정 제외
    }
  }

  //유저 상태에 따른 검색 
  public function scopeSearchStatus($query, array $search) 
  {
    switch ($search['userStatus']) {
      case 'all':
        return $query->withTrashed(); //소프트딜리트 된 데이터까지 포함
        break;
      case 'active':
        return $query; // 활성화된 데이터만 검색하면되기때문에 따로 지정될게 없음
        break;
      case 'sleep':
        return $query->onlyTrashed(); //소프트 딜리트 된 데이터 만 검색
        break;
      default:
        return $query;
        break;
    }
  }

  //정확한 날짜 비교를 위해 날짜 포맷 변경
  public function scopeSearchDateFormat($query, array $search)
  {
    $search['searchDateFirst'] = Carbon::createFromFormat('Y-m-d', $search['searchDateFirst'])->format("Y-m-d 00:00:00");
    $search['searchDateSecond'] = Carbon::createFromFormat('Y-m-d', $search['searchDateSecond'])->format("Y-m-d 23:59:59");

    return $query->whereBetween('join_date', [$search['searchDateFirst'], $search['searchDateSecond']]); //두 날짜 사이에 가입 날짜 조회
  }

  //모든 유저 검색
  public function getUserList(array $search) 
  {
    $users = $this->searchStatus($search) //유저 상태에 따른 검색
                  ->searchFilterFirst($search)  //검색필터와 검색어 where절 첫번쨰 필드
                  ->searchfilterSecond($search)  //검색필터와 검색어 where절 두번쨰 필드
                  ->when($search['gender'] == 'M' , function($query, $search) { //유저 성별에 따른 조건 검색
                    return $query->where('gender', 'M'); // 1 = 남
                  })
                  ->when($search['gender'] == 'F', function($query, $search) {
                    return $query->where('gender', 'F'); //  2 = 여
                  })
                  ->searchDateFormat($search)
                  ->orderBy($search['sort'], $search['orderBy'])
                  ->paginate($search['searchPageLimit']);

    return $users;
  }

  //Index 해당 유저 삭제
  //성공시 삭제된 row의 수
  public function deleteUser(array $userIndex)
  {
    $result = $this->whereIn('index', $userIndex)->Delete();

    return $result;
  }

  //이미 삭제된 유저인지 확인
  public function deleteCheck(array $userIndex)
  {
    $result = $this->withTrashed()->whereIn('index', $userIndex)->whereNotNull('deleted_at')->count();

    return $result;
  }


  //유저 통계 구하기
  public function averageData() {
    
    $sqlResult = DB::select(
      "select 
        (select count('index') from user) AS total,
        (select count('index') from user where deleted_at IS NOT NULL) AS sleep,
        (select count('index') from user where deleted_at IS NULL) AS active,
        (select count('index') from user where gender = 1) AS male,
        (select count('index') from user where gender = 2) AS female,
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
