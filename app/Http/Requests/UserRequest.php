<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Route;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch (Route::currentRouteName())
        {
            //유저 등록
            case 'register':
                return [
                'name' => 'required|alpha|between:2,5',
                'userId' => 'required|between:5,20|unique:user,user_id',
                'age' => 'required|integer',
                'userPw' => 'required|same:userPwCheck',
                'tel' => 'required|between:10,12',
                'gender' => 'required|numeric',
                'accumulated' => 'required|integer',
                'addressNum' => 'required|max:5',
                'addressRoad' => 'required',
                'marry' => 'required|numeric',
                'email' => 'required'
                ];
            break;

            //유저 업데이트
            case 'update':
                return[
                'userPw' => 'required|same:userPwCheck',
                'tel' => 'required|between:10,12',
                'accumulated' => 'required|integer',
                'addressNum' => 'required|max:5',
                'addressRoad' => 'required',
                'email' => 'required'
                ];
            break;
        }
    }

    public function messages()
    {
        switch (Route::currentRouteName())
        {
            //유저 등록 유효성 검사 메시지
            case 'register':
                return [
                    'name.required' => '이름을 입력해주세요.',
                    'name.alpha' => '문자만 입력해주세요.',
                    'name.between' => '2자에서 5자 사이로 입력해주세요.',
                    'userId.required' => 'id를 입력해주세요.',
                    'userId.unique' => 'id가 중복되었습니다.',
                    'age.required' => '나이를 입력해주세요.',
                    'age.integer' => '나이는 숫자만 입력해주세요.',
                    'age.max' => '정상적인 나이를 입력해주세요',
                    'userPw.required' => '비밀번호를 입력해주세요.',
                    'userPw.same' => '비밀번호와 비밀번호 확인이 일치하지 않습니다.',
                    'tel.required' => '전화번호를 입력해주세요.',
                    'tel.between' => '전화번호는 11자리만 입력해주세요.',
                    'gender.required' => '성별을 선택해주세요.',
                    'gender.numeric' => '성별을 선택해주세요.',
                    'accumulated.required' => '적립금을 입력해주세요.',
                    'accumulated.integer' => '적립금은 숫자만 입력해주세요',
                    'addressNum.required' => '우편번호를 입력해주세요.',
                    'addressNum.max' => '정상적인 우편번호를 입력해주세요.',
                    'addressRoad.required' => '도로명을 입력해주세요',
                    'marry.required' => '결혼상태를 선택해주세요.',
                    'marry.numeric' => '비정상적인 접근입니다.',
                    'email.required' => 'email을 입력해주세요.'
                ];
            break;

            //유저 업데이트 유효성 검사 메시지
            case 'update':
                return[
                    'userPw.required' => '비밀번호를 입력해주세요.',
                    'userPw.same' => '비밀번호와 비밀번호 확인이 일치하지 않습니다.',
                    'tel.required' => '전화번호를 입력해주세요.',
                    'tel.between' => '전화번호는 11자리만 입력해주세요.',
                    'accumulated.required' => '적립금을 입력해주세요.',
                    'accumulated.integer' => '적립금은 숫자만 입력해주세요',
                    'addressNum.required' => '우편번호를 입력해주세요.',
                    'addressNum.max' => '정상적인 우편번호를 입력해주세요.',
                    'addressRoad.required' => '도로명을 입력해주세요',
                    'email.required' => 'email을 입력해주세요.'
                ];
            break;
        }
    }
}
