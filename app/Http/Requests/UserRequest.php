<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
                    'age' => 'required|integer|max:99',
                    'userPw' => 'required|same:userPwCheck|between:5,20',
                    'userPwCheck' => 'required|between:5,20',
                    'tel' => 'required|digits_between:8,11',
                    'gender' => 'required|alpha|max:1',
                    'accumulated' => 'required|integer|min:0',
                    'addressNum' => 'required|max:5',
                    'addressRoad' => 'required',
                    'marry' => 'required|alpha|max:1',
                    'email' => 'required|alpha_num',
                    'emailDomain' => [Rule::in(['naver.com', 'daum.com', 'gmail.com'])],
                    'agree' => 'required'
                ];

            //유저 업데이트
            case 'update':
                return [
                    'userPw' => 'same:userPwCheck',
                    'tel' => 'required|digits_between:8,11',
                    'accumulated' => 'required|integer|min:0',
                    'addressNum' => 'required|max:5',
                    'addressRoad' => 'required',
                    'email' => 'required|alpha_num',
                    'emailDomain' => [Rule::in(['naver.com', 'daum.com', 'gmail.com'])]
                ];

            //유저 검색
            case 'search':
                return [
                    'searchDateFir' => 'before_or_equal:searchDateSec'
                ];

            //유저 idCheck
            case 'userIdCheck':
                return [
                    'userId' => 'required|between:5,20|unique:user,user_id|not_regex:/[ㄱ-ㅎ]/'
                ];
            
            //유저 pwCheck
            case 'userPwCheck':
                return [
                    'userPw' => 'required',
                ];
        }
    }

    public function messages()
    {
        switch (Route::currentRouteName())
        {
            //유저 등록
            case 'register':
                return [
                    'name.required' => '이름을 입력해주세요.',
                    'name.alpha' => '이름엔 문자만 입력해주세요.',
                    'name.between' => '2자에서 5자 사이로 입력해주세요.',
                    'userId.required' => 'id를 입력해주세요.',
                    'userId.unique' => 'id가 중복되었습니다.',
                    'userId.between' => 'Id를 5자에서 20자 사이로 입력해주세요.',
                    'age.required' => '나이를 입력해주세요.',
                    'age.integer' => '나이는 숫자만 입력해주세요.',
                    'age.max' => '정상적인 나이를 입력해주세요',
                    'userPw.required' => '비밀번호를 입력해주세요.',
                    'userPw.same' => '비밀번호와 비밀번호 확인이 일치하지 않습니다.',
                    'userPw.between' => '비밀번호는 8자에서 20자 사이로 입력해주세요.',
                    'userPwCheck.required' => '비밀번호 확인을 입력해주세요.',
                    'userPwCheck.between' => '비밀번호는 8자에서 20자 사이로 입력해주세요.',
                    'tel.required' => '전화번호를 입력해주세요.',
                    'tel.digits_between' => '전화번호는 11자리와 숫자만 입력해주세요.',
                    'gender.required' => '성별을 선택해주세요.',
                    'gender.alpha' => '비정상적인 성별 값입니다.',
                    'gender.max' => '비정상적인 성별 값입니다.',
                    'accumulated.required' => '적립금을 입력해주세요.',
                    'accumulated.integer' => '적립금은 숫자만 입력해주세요',
                    'accumulated.min' => '적립금은 0원 이하로 할 수 없습니다.',
                    'addressNum.required' => '우편번호를 입력해주세요.',
                    'addressNum.max' => '정상적인 우편번호를 입력해주세요.',
                    'addressRoad.required' => '도로명을 입력해주세요',
                    'marry.required' => '결혼상태를 선택해주세요.',
                    'marry.alpha' => '결혼상태의 값이 비정상적입니다.',
                    'marry.max' => '결혼상태의 값이 비정상적입니다.',
                    'email.required' => 'email을 입력해주세요.',
                    'email.alpha_num' => '정상적인 email을 입력해주세요.',
                    'emailDomain.in' => 'email 도메인을 선택해주세요.',
                    'agree.required' => '개인정보 이용 동의를 해주세요.'
                ];
        
            //유저 업데이트
            case 'update':
                return [
                    'userPw.same' => '비밀번호와 비밀번호 확인이 일치하지 않습니다.',
                    'tel.required' => '전화번호를 입력해주세요.',
                    'tel.digits_between' => '전화번호는 11자리와 숫자만 입력해주세요.',
                    'accumulated.required' => '적립금을 입력해주세요.',
                    'accumulated.integer' => '적립금은 숫자만 입력해주세요',
                    'accumulated.min' => '적립금은 0원 이하로 할 수 없습니다.',
                    'addressNum.required' => '우편번호를 입력해주세요.',
                    'addressNum.integer' => '정상적인 우편번호를 입력해주세요.',
                    'addressNum.max' => '정상적인 우편번호를 입력해주세요.',
                    'addressRoad.required' => '도로명을 입력해주세요',
                    'email.required' => 'email을 입력해주세요.',
                    'email.alpha_num' => '정상적인 email을 입력해주세요.',
                    'emailDomain.in' => 'email 도메인을 선택해주세요.'
                ];
            
            //유저 검색
            case 'search':
                return [
                    'searchDateFir.before_or_equal' => '시작일을 종료일보다 앞 선 날짜로 정해주세요.'
                ];

            //유저 idCheck
            case 'userIdCheck':
                return [
                    'userId.required' => 'id를 입력해주세요.',
                    'userId.unique' => 'id가 중복되었습니다.',
                    'userId.between' => 'Id를 5자에서 20자 사이로 입력해주세요.',
                ];

            //유저 pwCheck
            case 'userPwCheck':
                return [
                    'userPw.required' => '비밀번호를 입력해주세요.'
                ];
        }
    }
}
