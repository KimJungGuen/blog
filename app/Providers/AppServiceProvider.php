<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('addressCharacterCheck', function ( $address, $value, $parameters, $validator) {
            
            $specialCharacterCheck = preg_match('/[`~!@#$%^&\*_=\+\{\}\\\|;:\'"\/\?]/', $value);
            $hangulCharacterCheck = preg_match('/[\\x{3131}-\\x{318e}]/u', $value);
            return ($specialCharacterCheck || $hangulCharacterCheck) ? false : true;
        });

        Validator::replacer('addressCharacterCheck', function ($address, $value, $parameters, $validator) {
            return '주소를 제대로 입력해주세요.';
        });

        Validator::extend('mix_alpha_num', function($filed, $value, $parameters, $validator) {
            
            $characterCheck = preg_match('/[a-z]/i', $value);
            $numCheck = preg_match('/[0-9]/', $value);
            $mixCharacterCheck = ($characterCheck && $numCheck) ? true : false;

            return ($characterCheck || $mixCharacterCheck) ? true : false;
        });

        Validator::replacer('mix_alpha_num', function ($filed, $value, $parameters, $validator) {
            return '영문 또는 영문,숫자 혼합으로 입력해주세요.';
        });


    }
}
