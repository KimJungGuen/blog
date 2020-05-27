<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NoticeBoard extends Model
{
    protected $table = 'user';
    public $timestamps = false;
    //paginate를 이용한 페이지 정보 및 표시할 유저정보 조회
    public function getList(int $pageLimit){
      $users = $this->paginate($pageLimit);

      return $users;
    }
    //모든 유저 조회
    public function getUserAll() {
      $users = $this->get();
      return $users;
    }
    //해당 인덱스 유저 조회 PK기준
    public function getUser(int $userIndex){
      $user = $this->where('index', $userIndex)->get();

      return $user;
    }
    public function getUserId(String $userId){
      $user = $this->where('user_id', $userId)->get();

      return $user;
    }

    public function test(){
      //$user = $this->where('user_status','like','가%')->get();
      $user = $this->where('gender', '1')
                   ->where('name', '김정근')->get();

      return $user;
    }
    //유저 등록
    public function userInsert($user) {
      $result = $this->insert ([
          'user_id' => $user['userId'],
          'user_pw' => $user['userPw'],
          'name' => $user['name'],
          'gender' => $user['gender'],
          'age' => $user['age'],
          'accumulated' => $user['accumulated'],
          'email' => $user['email'],
          'address' => $user['address'],
          'etc' => $user['etc'],
          'join_date' => $user['join_date'],
          'marry' => $user['marry'],
          'tel' => $user['tel'],
          'user_status' => $user['userStatus'],
          'file' => $user['file']
        ]);
      return $result;
    }
    //유저 업데이트
    public function userUpdate($userData){
      $result = $this->where('index', $userData['userIndex'])
           ->update(['user_pw' => $userData['userPw'],
                     'email' => $userData['email'],
                     'accumulated' => $userData['accumulated'],
                     'address' => $userData['address'],
                     'tel' => $userData['tel'],
                     'file' => $userData['file'],
                     'etc' => $userData['etc']]);

      return $result;
    }
    // 검색어 하나
    // 검색어 둘
    // 검색어 없을떄
    // 오른차순
    // 내림차운

    //검색필터 두개가 전부 있을때 사용함
    public function searchFullFilter($search, $order, $pageLimit){
      $users = $this->where($search['filterFir'], 'like', '%'.$search['searchTextFir'].'%') // 첫번쨰 필드 필터와 필드 내용
                    ->where($search['filterSec'], 'like', '%'.$search['searchTextSec'].'%') // 두번째 필드 필터와 필드 내용
                    ->where('user_status', 'like', $search['searchUserStatus']) //모든, 사용, 휴면 계정
                    ->where('gender', 'like', $search['gender'])  //전체 [1-2] 남 1 여 2 성별
                    ->whereBetween('join_date', [$search['searchDateFir'], $search['searchDateSec']]) //두 날짜 사이에 가입 날짜 조회
                    ->orderBy($order['sort'], $order['orderBy'])
                    ->paginate($pageLimit);

      return $users;
    }

}
