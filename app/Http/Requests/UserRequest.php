<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
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
    public function rules(Request $request)
    {   
        //dump($request->all());
        switch (Route::currentRouteName())
        {

            //한글 유니코드 Hangul Compatibility Jamo 참조 https://codepoints.net/hangul_compatibility_jamo
            //laravel 한글 정규 표현식 문제 찾아보기
            //유저 등록
            case 'register':
                return [
                    'name' => 'required|alpha|between:2,5|not_regex:/[\\x{3131}-\\x{318e}]/u',
                    'userId' => 'required|alpha_num|between:5,20|unique:user,user_id|not_regex:/[\\x{3131}-\\x{318e}]/u',
                    'age' => 'required|integer|max:99',
                    'userPw' => 'required|same:userPwCheck|between:5,20',
                    'userPwCheck' => 'required|between:5,20',
                    'tel' => 'required|digits_between:8,11',
                    'gender' => ['required', 'alpha', 'max:1', Rule::in(['M', 'F'])],
                    'accumulated' => 'required|integer|between:1,2100000000',
                    'addressNum' => 'required|max:5|regex:/[0-9]/',
                    'addressRoad' => 'required|addressCharacterCheck',
                    'addressDetail' => 'required|addressCharacterCheck',
                    'marry' => ['required', 'alpha', 'max:1', Rule::in(['S', 'M'])],
                    'email' => 'required|alpha_num|max:20|not_regex:/[\\x{3131}-\\x{318e}]/u',
                    'emailDomain' => [Rule::in(['naver.com', 'daum.com', 'gmail.com'])],
                    'agree' => 'required'
                ];

            //유저 업데이트
            case 'update':
                return [
                    'userPw' => 'same:userPwCheck',
                    'tel' => 'required|digits_between:8,11',
                    'accumulated' => 'required|integer|between:1,21000000000',
                    'addressNum' => 'required|max:5|regex:/[0-9]/',
                    'addressRoad' => 'required|addressCharacterCheck',
                    'addressDetail' => 'required|addressCharacterCheck',
                    'email' => 'required|alpha_num|max:20|not_regex:/[\\x{3131}-\\x{318e}]/u',
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
                    'name.not_regex' => '이름을 제대로 입력해주세요',
                    'userId.required' => 'Id를 입력해주세요.',
                    'userId.unique' => 'Id가 중복되었습니다.',
                    'userId.between' => 'Id를 5자에서 20자 사이로 입력해주세요.',
                    'userId.not_regex' => 'Id를 제대로 입력해주세요',
                    'userId.alpha_num' => 'Id에 특수문자는 입력불가능입니다',
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
                    'gender.in' => '성별을 선택해주세요',
                    'accumulated.required' => '적립금을 입력해주세요.',
                    'accumulated.integer' => '적립금은 숫자만 입력해주세요',
                    'accumulated.between' => '적립금은 1원이상 21억 원 사이로 입력해주세요',
                    'addressNum.required' => '우편번호를 입력해주세요.',
                    'addressNum.max' => '정상적인 우편번호를 입력해주세요.',
                    'addressRoad.required' => '도로명을 입력해주세요',
                    'addressDetail.required' => '상제주소를 입력해주세요',
                    'marry.required' => '결혼상태를 선택해주세요.',
                    'marry.alpha' => '결혼상태의 값이 비정상적입니다.',
                    'marry.max' => '결혼상태의 값이 비정상적입니다.',
                    'marry.in' => '결혼상태 값이 비정상입니다.',
                    'email.required' => 'email을 입력해주세요.',
                    'email.alpha_num' => '정상적인 email을 입력해주세요.',
                    'email.not_regex' => '정상적인 email을 입력해주세요',
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
                    'accumulated.between' => '적립금은 1원이상 21억 원 사이로 입력해주세요',
                    'addressNum.required' => '우편번호를 입력해주세요.',
                    'addressNum.max' => '정상적인 우편번호를 입력해주세요.',
                    'addressRoad.required' => '도로명을 입력해주세요',
                    'addressDetail.required' => '상제주소를 입력해주세요',
                    'email.required' => 'email을 입력해주세요.',
                    'email.alpha_num' => '정상적인 email을 입력해주세요.',
                    'email.not_regex' => '정상적인 email을 입력해주세요',
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
