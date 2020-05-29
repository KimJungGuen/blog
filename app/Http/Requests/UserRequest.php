<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        /*
        name notNull, 문자만, 2자에서 5자 사이
        userId notNull, 5자에서 20자 사이, user db조회 id중복 안될때만
        userPw notNull,
        userPwCheck notNull, 숫자만
        gender notNull, 숫자만
        age notNull, 숫자만
        tel notNull, 숫자만, 최소 11자이상
        email notNull,
        emailDomain notNull,
        accumulated notNull, 숫자만
        marry notNull, 숫자만
        accumulated notNull, 숫자만
        addressNum notNull, ,숫자만
        addressRoad notNull,
        */
        return [
          'name' => 'required|alpha|between:2,5',
          'userId' => 'required|between:5,20|unique:user,user_id',
          'age' => 'required|integer',
          'userPw' => 'required|same:userPwCheck',
          'tel' => 'required',
          'gender' => 'required|numeric',
          'accumulated' => 'required|integer',
          'addressNum' => 'required|max:5',
          'addressRoad' => 'required',
          'marry' => 'required',
          'email' => 'required'
        ];
    }
}
