<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class NoticeBoard extends Model
{
    use SoftDeletes;
    protected $table = 'user';
    public $timestamps = false;
    //test용 삭제 예정
    public function test ()
    {
      $this->insert(['age' => 'dsfsdf']);
    }

    //paginate를 이용한 페이지 정보 및 표시할 유저정보 조회
    public function getList(int $pageLimit, array $searchData)
    {
      $users = $this->orderBy($searchData['listSort'], $searchData['listOrderBy'])
                    ->paginate($pageLimit);

      return $users;
    }

    //모든 유저 조회
    public function getUserAll() 
    {
      $users = $this->withTrashed()
                    ->get();
      return $users;
    }

    //해당 인덱스 유저 조회 PK기준
    public function getUserPw(int $userIndex)
    {
      $user = $this->withTrashed()
                   ->where('index', $userIndex)
                   ->value('user_pw');
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
    public function userInsert(array $user) 
    {
      $result = $this->insert (['user_id' => $user['userId'],
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
    public function userUpdate(array $userData) 
    {
      $result = $this->withTrashed()
                     ->where('index', $userData['userIndex'])
                     ->update(['user_pw' => $userData['userPw'],
                               'email' => $userData['email'],
                               'accumulated' => $userData['accumulated'],
                               'address_num' => $userData['addressNum'],
                               'address_road' => $userData['addressRoad'],
                               'address_detail' => $userData['addressDetail'],
                               'tel' => $userData['tel'],
                               'file' => $userData['file'],
                               'etc' => $userData['etc']]);
                    

      return $result;
    }

    //첫번째 검색필드와 검색 키워드 where 로직
    public function scopeSearchFilterFir($query,Collection $search)
    {
      //설정된 필터와 키워드가 있을경우
      if (!empty($search['filterFir']) && !empty($search['searchTextFir'])
        ) {
        return $query->where($search['filterFir'], 'like', '%'.$search['searchTextFir'].'%'); //설정된 필터와 검색어로 검색
      } else {
        return $query; //없을경우는 설정 제외
      }
    }
    //두번째 검색필드와 검색 키워드 where 로직
    public function scopeSearchFilterSec($query, Collection $search)
    {
      //설정된 필터와 키워드가 있을경우
      if (!empty($search['filterSec']) && !empty($search['searchTextSec'])) {
        return $query->where($search['filterSec'], 'like', '%'.$search['searchTextSec'].'%'); //설정된 필터와 검색어로 검색
      } else {
        return $query; //없을 경우는 설정 제외
      }
    }

    //유저 상태에 따른 검색 
    public function scopeSearchStatus($query, Collection $search) 
    {
      switch ($search['userStatus']) {
        case 'all':
          return $query->withTrashed(); //소프트딜리트 된 데이터까지 포함
        case 'active':
          return $query; // 활성화된 데이터만 검색하면되기때문에 따로 지정될게 없음
        case 'sleep':
          return $query->onlyTrashed(); //소프트 딜리트 된 데이터 만 검색
      }
    }

    //모든 유저 검색
    public function searchUsers(Collection $search, int $pageLimit) 
    {
      //dd($search['filterFir']);
      $users = $this->searchStatus($search) //유저 상태에 따른 검색
                    ->searchFilterFir($search)  //검색필터와 검색어 where절 첫번쨰 필드
                    ->searchFilterSec($search)  //검색필터와 검색어 where절 두번쨰 필드
                    ->when($search['gender'] == 'M' , function($query, $search) { //유저 성별에 따른 조건 검색
                      return $query->where('gender', 1); // 1 = 남
                    })
                    ->when($search['gender'] == 'F', function($query, $search) {
                      return $query->where('gender', 2); //  2 = 여
                    })
                    //->where('gender', 'like', $search['gender'])  //전체 [1-2] 남 1 여 2 성별
                    ->whereBetween('join_date', [$search['searchDateFir'], $search['searchDateSec']]) //두 날짜 사이에 가입 날짜 조회
                    ->orderBy($search['sort'], $search['orderBy'])
                    ->paginate($pageLimit);
      return $users;
    }


    //Index 해당 유저 삭제
    public function deleteUser(array $userIndex)
    {
      $result = $this->whereIn('index', $userIndex)->Delete();
      return $result;
    }


    public function average()
    { 
      //계정
      //총 계정
      $total = $this->withTrashed()->count();
      //사용 계정
      $active = $this->count();
      //휴면 계정
      $softDelete = $this->onlyTrashed()->count();

      //남자 여자 male female 
      $male = $this->where('gender', 1)->count();
      $female = $this->where('gender', 2)->count();

      //나이
      //19세 이하
      $children = $this->where('age', '<', 20)->count();
      //20~30대
      $young = $this->wherebetween('age', [20, 39])->count();
      //40~50대
      $adult = $this->wherebetween('age', [40, 59])->count();
      //60대 이상
      $old = $this->where('age', '>=', 60)->count();

      //적립금
      //1,000원 이하
      $minAccumulated = $this->where('accumulated', '<', 1000)->count();
      //1,000~9,999원
      $middleAccumulated = $this->wherebetween('accumulated', [1000, 9999])->count();
      //1,0000원 이상
      $hightAccumulated = $this->where('accumulated', '>', 10000)->count();
      //총액
      $totalAccumulated = $this->sum('accumulated');


      return $total;

    }

}
