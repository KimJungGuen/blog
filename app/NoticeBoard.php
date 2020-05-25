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

    public function test(){
      //$user = $this->where('user_status','like','가%')->get();
      $user = $this->where('gender', 'like', '%')->get();

      return $user;
    }
    //유저 등록
    public function userInsert($request, int $no, String $address, String $email,String $tel, String $userStatus, String $userFile) {
      $this->insert ([
          'no' => $no,
          'user_id' => $request->input('userId'),
          'user_pw' => $request->input('userPw'),
          'name' => $request->input('name'),
          'gender' => $request->input('gender'),
          'age' => $request->input('age'),
          'accumulated' => $request->input('accumulated'),
          'email' => $email,
          'address' => $address,
          'etc' => $request->input('etc'),
          'join_date' => now(),
          'marry' => $request->input('marry'),
          'tel' => $tel,
          'user_status' => $userStatus,
          'file' => $userFile
        ]);
    }
    //유저 업데이트
    public function userUpdate(int $userIndex, String $address, String $email, String $phone, String $path, $request){
      $d = $this->where('index', $userIndex)
           ->update(['address' => $address,
                     'email' => $email,
                     'tel' => $phone,
                     'file' => $path,
                     'etc' => $request->input('etc'),
                     'accumulated' => $request->input('accumulated'),
                     'user_pw' => $request->input('userPw')]);

                     dd($d);

    }
    // 검색어 하나
    // 검색어 둘
    // 검색어 없을떄
    // 오른차순
    // 내림차운

    //검색필터 두개가 전부 있을때 사용함
    public function serchFullFilter($serch, $order, $pageLimit){
      $users = $this->where($serch['filterFir'], 'like', '%'.$serch['serchTextFir'].'%') // 첫번쨰 필드 필터와 필드 내용
                    ->where($serch['filterSec'], 'like', '%'.$serch['serchTextSec'].'%') // 두번째 필드 필터와 필드 내용
                    ->where('user_status', 'like', $serch['serchUserStatus']) //모든, 사용, 휴면 계정
                    ->where('gender', 'like', $serch['gender'])  //전체 [1-2] 남 1 여 2 성별
                    ->whereBetween('join_date', [$serch['serchDateFir'], $serch['serchDateSec']]) //두 날짜 사이에 가입 날짜 조회
                    ->orderBy($order['sort'], $order['orderBy'])
                    ->paginate($pageLimit);

      return $users;
    }

}
