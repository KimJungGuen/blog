<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
    </head>

    <body>
        <div id="create" class="contains">
            <form id="userCreate" class="form" name="userCreate" enctype="multipart/form-data"  method="post" action="/users">
                <input type="hidden" id="multipleCount" name="multipleCount" value="{{ old('multipleCount', 1)}}">
                @csrf
                @for($index = 0; $index < old('multipleCount', 1); $index++)
                <table id="createTable" class="createTable table table-bordered">
                    <tbody>
                        <tr>
                            <td>이름</td>
                            <td>
                                <input type="text" id="name" class="name" name="name[]" value="{{ old('name.' . $index) }}" maxlength="5" />
                            </td>
                        </tr>
                        <tr>
                            <td>아이디</td>
                            <td>
                                <input type="text" id="userId" class="userId" name="userId[]" value="{{ old('userId.' . $index) }}" maxlength="20" onkeydown="idCheckClear(this);"/>
                                <input type="hidden" id="idStatus" class="idStatus" value="false" />
                                <button type="button" id="idCheck" class="btn btn-success" onclick="userIdCheck(this);">아이디중복확인</button>
                            </td>
                        </tr>
                        <tr>
                            <td>비밀번호</td>
                            <td>
                                <input type="password" id="userPw" class="userPw" name="userPw[]" value="" maxlength="20"/>
                            </td>
                        </tr>
                        <tr>
                            <td>비밀번호 확인</td>
                            <td>
                                <input type="password" id="userPwCheck" class="userPwCheck" name="userPwCheck[]" value="" maxlength="20"/>
                            </td>
                        </tr>
                        <tr>
                            <td>성별</td>
                            <td>
                                <select id="gender" class="gender" name="gender[]">
                                    <option @if (old('gender.' . $index) == '선택') selected @endif>선택</option>
                                    <option @if (old('gender.' . $index) == 'M') selected @endif value="M">남</option>
                                    <option @if (old('gender.' . $index) == 'F') selected @endif value="F">여</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>나이</td>
                            <td>
                                <input type="text" id="age" class="age numberOnly" name="age[]" value="{{ old('age.' . $index) }}" maxlength="2" />
                            </td>
                        </tr>
                        <tr>
                            <td>전화번호</td>
                            <td>
                                <input type="text" id="tel" class="tel numberOnly" name="tel[]" value="{{ old('tel.' . $index) }}" maxlength="11" />
                            </td>
                        </tr>
                        <tr>
                            <td>이메일</td>
                            <td>
                                <input type="text" id="email" class="email" name="email[]" value="{{ old('email.' . $index) }}" maxlength="30" /> @
                                <select id="emailDomain" class="emailDomain" name="emailDomain[]">
                                    <option @if (old('emailDomain.' . $index) == '선택') selected @endif>선택</option>
                                    <option value="naver.com" @if (old('emailDomain.' . $index) == 'naver.com') selected @endif>naver.com</option>
                                    <option value="gmail.com" @if (old('emailDomain.' . $index) == 'gmail.com') selected @endif >gmail.com</option>
                                    <option value="daum.com" @if (old('emailDomain.' . $index) == 'daum.com') selected @endif >daum.com</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>적립금</td>
                            <td>
                                <input type="text" id="accumulated" class="accumulated numberOnly text-right" name="accumulated[]" value="{{ old('accumulated.' . $index) }}" maxlength="10" placeholder="0"/>
                            </td>
                        </tr>
                        <tr>
                            <td>결혼 여부</td>
                            <td>
                                <input type="radio" class="findMarry" name="marry_{{ $index }}" value="S" @if (old('marry.' . $index) == 'S') checked @endif/>미혼
                                <input type="radio" class="findMarry" name="marry_{{ $index }}" value="M" @if (old('marry.' . $index) == 'M') checked @endif/>기혼
                            </td>
                        </tr>
                        <tr>
                            <td>우편번호</td>
                            <td>
                                <input type="text" id="addressNum" class="addressNum numberOnly" name="addressNum[]" value="{{ old('addressNum.' . $index) }}" readonly maxlength="200"/>
                                <button type="button" id="{{ $index }}" class="addressBtn btn" onclick="addressModal(this)">우편번호 찾기</button>
                            </td>
                        </tr>
                        <tr>
                            <td>기본주소</td>
                            <td><input type="text" id="addressRoad" class="addressRoad" name="addressRoad[]" value="{{ old('addressRoad.' . $index) }}" style="width:100%" readonly maxlength="200"/></td>
                        </tr>
                        <tr>
                            <td>상세주소</td>
                            <td><input type="text" id="addressDetail" class="addressDetail" name="addressDetail[]" value="{{ old('addressDetail.' . $index) }}" style="width:100%" maxlength="50" /></td>
                        </tr>
                        <tr>
                            <td>파일 업로드</td>
                            <td>
                                <input type="file" id="file" class="file" name="file_0" value="" Onchange="filePreView(this);" />     
                                <img id="preImg" class="preImg" name="preImg" src="#" width="200" height="200"/>
                            </td>
                        </tr>
                        <tr>
                            <td>비고</td>
                            <td><textarea rows="2" id="etc" class="etc" name="etc[]" style="width:100%" maxlength="300">{{ old('etc.' . $index) }}</textarea></td>
                        </tr>
                    </tbody>
                </table>
                @endfor
            </form>
            <span><input type="checkbox" id="agree" value="1" />개인정보수집동의</span>
        </div>
        <button type="button" id="submitBtn" class="btn btn-primary" onclick="validate();">저장하기</button>
        <button type="button" id="duplicateBtn" class="btn btn-primary" onclick="duplicate();">다중 등록</button>
        <button type="button" id="deleteBtn" class="btn btn-danger" onclick="duplicateDelete();">다중 등록 취소</button>

        @if ($errors->any())
            <input type="hidden" id="validationErrors" value="{{ $errors->first() }}" />
        @endif
        @if (\Session::has('msg'))
            <input type="hidden" id="userRegisterMsg" value="{{ \Session::get('msg') }}" />
        @endif
        <script>
            //@brief 테이블 복제
            function duplicate()
            {
                var index = $('table').length
                if (index < 3) {
                    $afterMarry = $('input[name=marray]:checked');
                    $table = $('#createTable').clone(true);
                    $($table).find('input.findMarry').attr('name', 'marry_' + index);
                    $($table).find('input.findMarry').prop('checked', false);
                    
                    $($table).appendTo($('#userCreate'));
                    $('#multipleCount').attr('value', Number(index) + 1);
                    $('.addressBtn').eq(index).attr('id', index);
                    $('.idStatus').eq(index).val(false);
                    $('.file').eq(index).attr('name', 'file_' + index);
                    Initialization(index);
                }
            }
            //@brief 복제된 테이블 삭제
            function duplicateDelete()
            {
                var index = $('table').length;
                if (index > 1) {
                    $('.createTable').eq(index-1).remove();
                    $('#multipleCount').val(Number(index) - 1);
                }
            }
            //@brief 복제 테이블 초기화
            function Initialization(index)
            {
                $('.name').eq(index).val('');
                $('.userId').eq(index).val('');
                $('.userPw').eq(index).val('');
                $('.userPwCheck').eq(index).val('');
                $('.gender').eq(index).val('선택');
                $('.age').eq(index).val('');
                $('.tel').eq(index).val('');
                $('.email').eq(index).val('');
                $('.emailDomain').eq(index).val('선택');
                $('.accumulated').eq(index).val('');
                $('.addressNum').eq(index).val('');
                $('.addressRoad').eq(index).val('');
                $('.addressDetail').eq(index).val('');
                $('.file').eq(index).val('');
                $('.preImg').eq(index).attr('src', '');
                $('.etc').eq(index).val('');
            }
            //에러 및 성공, 실패 메시지 출력
            window.onload = function () 
            {
                var errors = $('#validationErrors').val();
               
                if (errors != undefined) {
                    alert(errors);
                    return false;
                }
                var userRegisterMsg = $('#userRegisterMsg').val();
                if (userRegisterMsg != undefined) {
                    alert(userRegisterMsg); 
                    $(location).attr('href', '/users');
                    return false;
                }
            }
            /**
             * @brief   필드 빈 값 확인
             * @param   mixed value
             * @return  boolean
             */
            function valueCheck(value) 
            {
                return (
                    (value == '') 
                    || (value == 0) 
                    || (value == undefined) 
                    || (value == '0') 
                    || (value == '선택') 
                    || (value == 'false')
                );
            }
            
            //@brief    아이디 중복확인 초기화
            //@param    input : $('#idCheck')
            function idCheckClear(input) 
            {
                $(input).closest('td').find('input#idStatus').val(false);
            }
            //@brief    유저 파일 업로드시 미리보기
            //@param    input : $('.file').ea()
            function filePreView(input) 
            {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.readAsDataURL(input.files[0]);
                    reader.onload = function (e) {
                        $(input).next().attr('src', e.target.result);
                    }
                }
            }
            //@brief    다음 주소 찾기 api
            //@param    input : $('.address').eq()
            function addressModal(input) 
            {
                var index = $(input).attr('id');
                new daum.Postcode({
                oncomplete: function(data) {
                    // 우편번호와 주소 정보를 해당 필드에 넣는다.
                    document.getElementsByClassName('addressNum')[index].value = data.zonecode;
                    document.getElementsByClassName('addressRoad')[index].value = data.roadAddress;
                    // 커서를 상세주소 필드로 이동한다.
                    document.getElementsByClassName('addressDetail')[index].focus();
                }
                }).open({
                //중복으로 안켜지게 한다.
                    popupName : 'postCodePopup'
                });
            }
            
            //@brief    유저 아이디 중복 확인
            //@param    $idCheck : $('.idCheck').eq()
            function userIdCheck($idCheck)
            {
                var userId = $($idCheck).closest('td').find('input#userId').val();
                var $userIdCheck = $($idCheck).closest('td').find('input#idStatus');
                var userIdArray = [];
                var limit = $('table').length;
                var duplicateCount = 0;
                for (var index = 0; index < limit; index++) {
                    userIdArray[index] = $('.userId').eq(index).val();
                }
                if(userId == '') {
                    return alert('Id를 입력해주세요.');
                }
                for (var index = 0; index < limit; index++) {
                    if (userIdArray[index] == userId) {
                        duplicateCount++;
                    }
                }
                if (duplicateCount > 1) {
                    return alert('다른 다중등록 ID와 중복됐습니다.');
                }
                  
                var userIdSpecialCharacter = userId.search(/[~!@#$%^&*()<>?]/ig);

                //아이디 특수문자 확인 및 아이디 값 전송 
                if(userIdSpecialCharacter < 0) {
                    $.ajax({
                        url:'/userIdCheck',
                        type:'post',
                        data:{
                            'userId':userId,
                            '_token':$('input[name=_token]').val()
                        },
                        datatype:'json',
                        success:function(result){
                            alert(result.msg);
                            $userIdCheck.val(result.check);
                            return false;
                        },
                        error:function(request){
                            var error = request.responseJSON.errors;
                            return alert(error['userId']);
                        }
                    });
                } else {
                    return alert('ID에 특문을 제외하고 입력해주세요.');
                }
            }
            
            //@brief    데이터 유효성 판단 및 전송
            function validate()
            {
                //특수문자, 문자, 숫자 정규식 지정
                var specialCharacter = /[`~!@#$%^&\*\(\)_\+=\{\}\[\]/;:'"<>,\|\.\?\s\\\-]/;
                var character = /[a-z]/ig;
                var number = /[0-9]/g;
                var Hangul = /[ㄱ-ㅎㅏ-ㅣ]/;
                var marrySingleNumber = 0;
                var marryNumber = 1;
                for(var index = 0 ; index < $('table').length ; index++) {
                    
                    //search() 검사하는 값이 없을경우 -1을 반환
                    //비밀번호, 비밀번호 확인 숫자, 문자, 특수문자 확인    
                    var $pw = $('.userPw').eq(index);
                    var $pwCheck = $('.userPwCheck').eq(index);
                    var pwNumberCheck = $pw.val().search(number);
                    var pwCharacterCheck = $pw.val().search(character);
                    var pwSpecialCharacterCheck = $pw.val().search(specialCharacter);

                    //이름 숫자, 특수문자 확인
                    var name = $('.name').eq(index).val();
                    var nameNumberCheck = name.search(number);
                    var nameSpecialCharacterCheck = name.search(specialCharacter);
                    var nameHangulCharacterCheck = name.search(Hangul);
                    
                    //유저 아이디 특수문자, 한글 초성 확인
                    var userId = $('.userId').eq(index).val();
                    var userIdHangulCharacterCheck = userId.search(Hangul);
                    var userIdSpecialCharacterCheck = userId.search(specialCharacter);
                    var userIdCharacterCheck = userId.search(character);

                    //이메일 특수문자 확인
                    var email = $('.email').eq(index).val();
                    var emailCharacterCheck = email.search(character);
                    var emailSpecialCharacterCheck = email.search(specialCharacter);
                    var emailHangulCharacterCheck = email.search(Hangul);

                    //나이
                    var age = $('.age').eq(index).val();
                    var ageCharacterCheck = age.search(character);
                    var ageSpecialCharacterCheck = age.search(specialCharacter);
                    var ageHangulCharacterCheck = age.search(Hangul);
                    
                    //전화번호
                    var tel = $('.tel').eq(index).val();
                    var telCharacterCheck = tel.search(character);
                    var telSpecialCharacterCheck = tel.search(specialCharacter);
                    var telHangulCharacterCheck = tel.search(Hangul);

                    //적립금
                    var accumulated = $('.accumulated').eq(index).val();
                    var accumulatedCharacterCheck = accumulated.search(character);
                    var accumulatedSpecialCharacterCheck = accumulated.search(specialCharacter);
                    var accumulatedHangulCharacterCheck = accumulated.search(Hangul);

                    //우편번호 문자, 특수문자 확인
                    var addressNum = $('.addressNum').eq(index).val();
                    var addressNumCharacterCheck = addressNum.search(character);
                    var addressNumSpecialCharacterCheck = addressNum.search(specialCharacter);
                    var addressNumHangulCharacterCheck = addressNum.search(Hangul);

                    //도로명 주소 특수문자 확인
                    var addressRoad = $('.addressRoad').eq(index).val();
                    var addressRoadSpecialCharacterCheck = addressRoad.search(/[`~!@#$%^&\*\+=;:'"\{\}\?\\\|]/g);
                    var addressRoadHangulCharacterCheck = addressRoad.search(Hangul);

                    //상세주소 특수문자 확인
                    var addressDetail = $('.addressDetail').eq(index).val();
                    var addressDetailSpecialCharacterCheck = addressDetail.search(/[`~!@#$%^&\*\+=\{\};:'<>"\/\?\\\|]/g);
                    var addressDetailHangulCharacterCheck = addressDetail.search(Hangul);
                    var idStatus = $('.idStatus').eq(index).val();
                    var gender = $('.gender').eq(index).val();
                    var emailDomain = $('.emailDomain').eq(index).val();
                    var file = $('.file').eq(index).val();
                    var marrySingleCheck = false;
                    var marryCheck = false;
                    
                    if (index == 0) {
                        marrySingleCheck = $('.findMarry').eq(Number(index) + marrySingleNumber).prop('checked');
                        marryCheck = $('.findMarry').eq(Number(index) + marryNumber).prop('checked');
                    } else if (index > 0) {    
                        marrySingleCheck = $('.findMarry').eq(Number(index) + marrySingleNumber).prop('checked');
                        marryCheck = $('.findMarry').eq(Number(index) + marryNumber).prop('checked');
                    }

                    marrySingleNumber++;
                    marryNumber++;

                    //이름 빈값 확인
                    if (name.length < 2) 
                    {
                        alert('이름을 2자 이상 입력해주세요');
                        return false;
                    } else if (nameNumberCheck > -1 || nameSpecialCharacterCheck > -1 || nameHangulCharacterCheck > -1) {
                        alert('정상적인 이름을 입력해주세요');
                        return false;
                    } 

                    //아이디 빈 값 확인
                    if (valueCheck(userId)) {
                        alert('아이디를 입력해주세요');
                        return false;
                    } else if (userId.length <= 5 || userId.length >= 20) {
                        alert('아이디는 5자 이상 20자 이하로 입력해주세요.');
                        return false;
                    } else if (userIdHangulCharacterCheck > -1 || userIdSpecialCharacterCheck > -1) {
                        alert('아이디는 영문이거나 영문, 숫자 혼용 만 가능합니다.');
                        return false;
                    } else if (userIdCharacterCheck == -1) {
                        alert('아이디는 영문이거나 영문, 숫자 혼용으로 입력해주세요');
                        return false;
                    }

                    //아이디 중복 확인
                    if (valueCheck(idStatus)) {
                        alert('아이디 중복확인을 해주세요');
                        return false;
                    } 

                    //비밀번호 빈 값 확인
                    if (valueCheck($pw.val())) {
                        alert('비밀번호를 입력해주세요');
                        return false;
                    } else if (valueCheck($pwCheck.val())) {
                        alert('비밀번호 확인을 입력해주세요');
                        return false;
                    } 

                    //비밀번호 자릿수 및 공백, 영어 숫자 특문 혼용 확인, 일치 확인
                    if ($pw.val() !== $pwCheck.val()) {
                        alert('비밀번호가 일치하지 않습니다.');
                        $pw.val('');
                        $pwCheck.val('');
                        return false;
                    } else if($pw.val().search(/\s/) != -1) {
                        alert('비밀번호는 공백없이 입력해주세요.');
                        $pw.val('');
                        $pwCheck.val('');
                        return false;
                    } else if ($pw.val().length < 8 || $pw.val().length > 20) {
                        alert('비밀번호는 8자리 ~ 20자리 이내로 입력해주세요.');
                        $pw.val('');
                        $pwCheck.val('');
                        return false;
                    } else if (pwNumberCheck < 0 || pwCharacterCheck < 0 || pwSpecialCharacterCheck < 0 ) {
                        alert('영문,숫자, 특수문자를 혼합하여 입력해주세요.');
                        $pw.val('');
                        $pwCheck.val('');
                        return false;
                    } 

                    //성별 확인
                    if(valueCheck(gender)) {
                        alert('성별을 선택해주세요');
                        return false;
                    } 

                    //나이 빈 값 확인 및 범위 확인
                    if(valueCheck(age)) {
                        alert('나이를 입력해주세요');
                        return false;
                    } else if (age <= 0 && age >= 100) {
                        alert('나이를 재대로 입력해주세요');
                        return false;
                    } else if (ageCharacterCheck > -1 || ageSpecialCharacterCheck > -1 || ageHangulCharacterCheck > -1) {
                        alert('나이는 숫자만 입력해주세요.')
                        return false;
                    }
    
                    //전화번호 빈값 확인 및 전화번소 자릿수 확인
                    if (tel.length <= 0) {
                        alert('전화번호를 입력해주세요');
                        return false;
                    } else if (tel.length < 8 || tel.length > 11) {
                        alert('전화번호를 재대로 입력해주세요');
                        return false;
                    } else if (telCharacterCheck > -1 || telSpecialCharacterCheck > -1 || telHangulCharacterCheck > -1) {
                        alert('전화번호는 숫자만 입력해주세요.')
                        return false;
                    }

                    //이메일 빈 값 확인
                    if(valueCheck(email)) {
                        alert('이메일을 입력해주세요');
                        return false;
                    } 

                    //이메일 도메인 확인
                    if (valueCheck(emailDomain)) {
                        alert('이메일 도메인을 선택해주세요.');
                        return false;
                    }

                    //이메일 특수문자 및 공백 확인
                    if (emailSpecialCharacterCheck > -1 || emailHangulCharacterCheck > -1 || emailCharacterCheck == -1) {
                        alert('정상적인 email을 입력해주세요');
                        return false; 
                    } 

                    //적립금 빈 값 확인
                    if (valueCheck(accumulated) || accumulated <= 0) {
                        alert('적립금은 0원이상 입력해주세요.');
                        return false;
                    } else if (accumulatedCharacterCheck > -1 || accumulatedSpecialCharacterCheck > -1 ||  accumulatedHangulCharacterCheck > -1) {
                        alert('적립금은 숫자만 입력해주세요.');
                        return false;
                    } else if (accumulated > 2100000000) {
                        alert('21억 이하로 입력해주세요.');
                        return false;
                    } else if (accumulated.search(/^0/) > -1) {
                        alert('맨 앞자리 0은 제외해 주세요');
                        return false;
                    }

                    //결혼상태 확인
                    if (marrySingleCheck == false && marryCheck == false) {
                        alert('결혼 상태를 체크해주세요')
                        return false;
                    }

                    //주소 빈 값 확인
                    if (valueCheck(addressNum)) {
                        alert('우편번호를 입력해주세요');
                        return false;
                    } else if (valueCheck(addressRoad)) {
                        alert('도로명주소를 입력해주세요');
                        return false;
                    } else if (valueCheck(addressDetail)) {
                        alert('상세주소를 입력해주세요');
                        return false;
                    }

                    //주소 특수문자 확인
                    if (addressNumCharacterCheck > -1 || addressNumSpecialCharacterCheck > -1 || addressNumHangulCharacterCheck > -1) {
                        alert('정상적인 우편번호를 입력해주세요');
                        return false;
                    } else if (addressRoadSpecialCharacterCheck > -1 || addressRoadHangulCharacterCheck > -1) {
                        alert('정상적인 도로명주소를 입력해주세요');
                        return false;
                    } else if (addressDetailSpecialCharacterCheck > -1 || addressDetailHangulCharacterCheck > -1) {
                        alert('정상적인 상세주소를 입력해주세요');
                        return false;
                    }

                    //파일 유무 확인
                    if(file != '') {
                        //파일의 이름중에서 확장자만을 추출한다.
                        var ext = file.split('.').pop().toLowerCase();
                        //확장자명이 jpg나 png일떄만 실행
                        if($.inArray(ext,['jpg','png']) == -1){
                            alert('jpg, png 파일만 업로드 가능합니다.');
                            return false;
                        }
                    } 
                }
                
                var agree = $('#agree:checked').val();

                //개인정보수집동의 확인
                if (valueCheck(agree)) {
                    alert('개인정보수집동의 박스를 체크해주세요.');
                    return false;
                }

                $('.form').submit();
            }
            
            $(document).ready(function()
            {
                //@brief    숫자필드에 문자입력시 공백
                $('.numberOnly').keyup(function() 
                {
                    $(this).val($(this).val().replace(/[^0-9]/g,''));
                });
            });
        </script>
    </body>
</html>