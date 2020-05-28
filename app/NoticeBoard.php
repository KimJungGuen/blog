<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticeBoard extends Model
{
    use SoftDeletes;
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
    //해당 유저id로 조회
    public function getUserId(String $userId){
      $user = $this->where('user_id', $userId)->get();

      return $user;
    }

    public function test(){
      //$user = $this->where('user_status','like','가%')->get();
      $user = $this->where('index', '39')->Delete();

      return $user;
    }

    //유저 등록
    public function userInsert($user) {
      $result = $this->insert (['user_id' => $user['userId'],
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
                                'file' => $user['file']
                              ]);
      return $result;
    }
    //유저 업데이트
    public function userUpdate($userData) {
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

    //모든 유저 검색
    public function searchFullUser($search, $order, $pageLimit) {
      $users = $this->withTrashed() //모든 유저
                    ->where($search['filterFir'], 'like', '%'.$search['searchTextFir'].'%') // 첫번쨰 필드 필터와 필드 내용
                    ->where($search['filterSec'], 'like', '%'.$search['searchTextSec'].'%') // 두번째 필드 필터와 필드 내용
                    ->where('gender', 'like', $search['gender'])  //전체 [1-2] 남 1 여 2 성별
                    ->whereBetween('join_date', [$search['searchDateFir'], $search['searchDateSec']]) //두 날짜 사이에 가입 날짜 조회
                    ->orderBy($order['sort'], $order['orderBy'])
                    ->paginate($pageLimit);
      return $users;
    }
    //사용 유저 검색
    public function searchActiveUser($search, $order, $pageLimit) {
      $users = $this->where($search['filterFir'], 'like', '%'.$search['searchTextFir'].'%') // 첫번쨰 필드 필터와 필드 내용
                    ->where($search['filterSec'], 'like', '%'.$search['searchTextSec'].'%') // 두번째 필드 필터와 필드 내용
                    ->where('gender', 'like', $search['gender'])  //전체 [1-2] 남 1 여 2 성별
                    ->whereBetween('join_date', [$search['searchDateFir'], $search['searchDateSec']]) //두 날짜 사이에 가입 날짜 조회
                    ->orderBy($order['sort'], $order['orderBy'])
                    ->paginate($pageLimit);
      return $users;
    }
    //휴면 유저 검색
    public function searchSleepUser($search, $order, $pageLimit) {
      $users = $this->onlyTrashed() //휴면계정만
                    ->where($search['filterFir'], 'like', '%'.$search['searchTextFir'].'%') // 첫번쨰 필드 필터와 필드 내용
                    ->where($search['filterSec'], 'like', '%'.$search['searchTextSec'].'%') // 두번째 필드 필터와 필드 내용
                    ->where('gender', 'like', $search['gender'])  //전체 [1-2] 남 1 여 2 성별
                    ->whereBetween('join_date', [$search['searchDateFir'], $search['searchDateSec']]) //두 날짜 사이에 가입 날짜 조회
                    ->orderBy($order['sort'], $order['orderBy'])
                    ->paginate($pageLimit);
      return $users;
    }
    //Index 해당 유저 삭제
    public function deleteUser($index) {
      $result = $this->where('index', $index)->Delete();

      return $result;
    }

}
