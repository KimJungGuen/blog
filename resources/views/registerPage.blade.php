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
        <div class="contains">
            <form id="userCreate" name="userCreate" enctype="multipart/form-data"  method="post" action="/users">
                @csrf
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>이름</td>
                            <td>
                                <input type="text" id="name" name="name" maxlength="5" />
                            </td>
                        </tr>
                        <tr>
                            <td>아이디</td>
                            <td>
                                <input type="text" id="userId" name="userId" maxlength="20" onkeydown="idCheckClear();"/>
                                <input type="hidden" id="idStatus" value="false" />
                                <button type="button" id="idCheck" class="btn btn-success" onclick="userIdCheck();">아이디중복확인</button>
                            </td>
                        </tr>
                        <tr>
                            <td>비밀번호</td>
                            <td>
                                <input type="password" id="userPw" name="userPw" maxlength="20"/>
                            </td>
                        </tr>
                        <tr>
                            <td>비밀번호 확인</td>
                            <td>
                                <input type="password" id="userPwCheck" name="userPwCheck" maxlength="20"/>
                            </td>
                        </tr>
                        <tr>
                            <td>성별</td>
                            <td>
                                <select id="gender" name="gender">
                                    <option>선택</option>
                                    <option value="M">남</option>
                                    <option value="F">여</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>나이</td>
                            <td>
                                <input type="text" id="age" name="age" class="numberOnly" maxlength="2" />
                            </td>
                        </tr>
                        <tr>
                            <td>전화번호</td>
                            <td>
                                <input type="text" id="tel" name="tel" class="numberOnly" maxlength="11" />
                            </td>
                        </tr>
                        <tr>
                            <td>이메일</td>
                            <td>
                                <input type="text" id="email" name="email" maxlength="30" /> @
                                <select id="emailDomain" name="emailDomain">
                                    <option>선택</option>
                                    <option value="naver.com">naver.com</option>
                                    <option value="gmail.com">gmail.com</option>
                                    <option value="daum.com">daum.com</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>적립금</td>
                            <td>
                                <input type="text" id="accumulated" name="accumulated" class="numberOnly text-right" maxlength="10" placeholder="0"/>
                            </td>
                        </tr>
                        <tr>
                            <td>결혼 여부</td>
                            <td>
                                <input type="radio"  name="marry" value="S" />미혼
                                <input type="radio"  name="marry" value="M" />기혼
                            </td>
                        </tr>
                        <tr>
                            <td>우편번호</td>
                            <td>
                                <input type="text" id="addressNum" name="addressNum" readonly/>
                                <button type="button" class="btn" onclick="addressModal()">우편번호 찾기</button>
                            </td>
                        </tr>
                        <tr>
                            <td>기본주소</td>
                            <td><input type="text" id="addressRoad" name="addressRoad" style="width:100%" readonly/></td>
                        </tr>
                        <tr>
                            <td>상세주소</td>
                            <td><input type="text" id="addressDetail"name="addressDetail" style="width:100%" maxlength="50" /></td>
                        </tr>
                        <tr>
                            <td>파일 업로드</td>
                            <td><input type="file" id="file" name="file" value="" Onchange="filePreView(this);" /><img id="preImg" src="#"  width="200" height="200"/></td>
                        </tr>
                        <tr>
                            <td>비고</td>
                            <td><textarea rows="2" id="etc" name="etc" style="width:100%" maxlength="300"></textarea></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="checkbox" id="agree" name="agree" value="1" />개인정보수집동의
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" id="submitBtn" class="btn btn-primary" onclick="validate();">저장하기</button>
                <button type="button" id="Btn" class="btn btn-primary" onclick="">다중등록</button>
            </form>
        </div>

        <script>
            /**
             * @brief   필드 빈 값 확인
             * @param   mixed value
             * @return  boolean
             */
            function valueCheck(value) {
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
            function idCheckClear() {
                  $('#idStatus').val(false);
            }

            //@brief    유저 파일 업로드시 미리보기
            function filePreView(input) 
            {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.readAsDataURL(input.files[0]);

                    reader.onload = function (e) {
                        $('#preImg').attr('src', e.target.result);
                    }
                }
            }

            //@brief    다음 주소 찾기 api
            function addressModal() 
            {
                new daum.Postcode({
                oncomplete: function(data) {
                    // 우편번호와 주소 정보를 해당 필드에 넣는다.
                    document.getElementById('addressNum').value = data.zonecode;
                    document.getElementById('addressRoad').value = data.roadAddress;
                    // 커서를 상세주소 필드로 이동한다.
                    document.getElementById('addressDetail').focus();
                }
                }).open({
                //중복으로 안켜지게 한다.
                    popupName : 'postCodePopup'
                });
            }

            //@brief    유저 아이디 중복 확인
            function userIdCheck()
            {
                var userId = $('#userId').val();
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
                            $('#idStatus').val(result.check);
                        },
                        error:function(request){
                            var error = request.responseJSON.errors;
                            alert(error['userId']);
                        }
                    });
                } else {
                    alert('ID에 특문을 제외하고 입력해주세요.');
                }
            }

            //@brief    데이터 유효성 판단 및 전송
            function validate()
            {
                //특수문자, 문자, 숫자 정규식 지정
                var specialCharacter = /[~!@#$%^&*()<>?]/g;
                var character = /[a-z]/ig;
                var number = /[0-9]/g;

                //search() 검사하는 값이 없을경우 -1을 반환
                //비밀번호, 비밀번호 확인 숫자, 문자, 특수문자 확인
                var pw = $('#userPw').val();
                var pwCheck = $('#userPwCheck').val();
                var pwNumberCheck = pw.search(number);
                var pwCharacterCheck = pw.search(character);
                var pwSpecialCharacterCheck = pw.search(specialCharacter);

                //이름 숫자, 특수문자 확인
                var name = $('#name').val();
                var nameNumberCheck = name.search(number);
                var nameSpecialCharacterCheck = name.search(specialCharacter);
                
                var userId = $('#userId').val();
                var userIdHangulCharacterCheck = userId.search(/[ㄱ-ㅎ]/g);

                //이메일 특수문자 확인
                var email = $('#email').val();
                var emailSpecialCharacterCheck = email.search(specialCharacter);

                //우편번호 문자, 특수문자 확인
                var addressNum = $('#addressNum').val();
                var addressNumCharacterCheck = addressNum.search(character);
                var addressNumSpecialCharacterCheck = addressNum.search(specialCharacter);

                //도로명 주소 특수문자 확인
                var addressRoad = $('#addressRoad').val();
                var addressRoadSpecialCharacterCheck = addressRoad.search(/[@$%^&*?]/g);

                //상세주소 특수문자 확인
                var addressDetail = $('#addressDetail').val();
                var addressDetailSpecialCharacterCheck = addressDetail.search(/[@$%^&*?]/g);

                var age = $('#age').val();
                var tel = $('#tel').val();

                //이름 빈값 확인
                if (name.length < 2) 
                {
                    alert('이름을 2자 이상 입력해주세요');
                    return false;
                } 

                //이름 특수문자 확인
                if (nameNumberCheck > -1 || nameSpecialCharacterCheck > -1) {
                    alert('정상적인 이름을 입력해주세요');
                    return false;
                } 

                //아이디 빈 값 확인
                if (valueCheck(userId)) {
                    alert('아이디를 입력해주세요');
                    return false;
                } 

                if (userIdHangulCharacterCheck > -1) {
                    alert('유저아이디는 숫자와 영어만 가능합니다.');
                    return false;
                }

                //아이디 중복 확인
                if (valueCheck($('#idStatus').val())) {
                    alert('아이디 중복확인을 해주세요');
                    return false;
                } 

                //비밀번호 빈 값 확인
                if (valueCheck(pw)) {
                    alert('비밀번호를 입력해주세요');
                    return false;
                } 

                //비밀번호 확인 빈 값 확인
                if (valueCheck(pwCheck)) {
                    alert('비밀번호 확인을 입력해주세요');
                    return false;
                } 

                //비밀번호 자릿수 및 공백, 영어 숫자 특문 혼용 확인, 일치 확인
                if (pw !== pwCheck) {
                    alert('비밀번호가 일치하지 않습니다.');
                    return false;
                } else if(pw.search(/\s/) != -1) {
                    alert('비밀번호는 공백없이 입력해주세요.');
                } else if (pw.length < 8 || pw.length > 20) {
                    alert('비밀번호는 8자리 ~ 20자리 이내로 입력해주세요.');
                    return false;
                } else if (pwNumberCheck < 0 || pwCharacterCheck < 0 || pwSpecialCharacterCheck < 0 ) {
                    alert('영문,숫자, 특수문자를 혼합하여 입력해주세요.');
                    return false;
                } 

                //성별 확인
                if(valueCheck($('#gender').val())) {
                    alert('성별을 선택해주세요');
                    return false;
                } 

                //나이 빈 값 확인 및 범위 확인
                if(valueCheck(age)) {
                    alert('나이를 입력해주세요');
                    return false;
                } else if (Number(age) <= 0 && Number(age) >= 100) {
                    alert('나이를 재대로 입력해주세요');
                    return false;
                } 
   
                //전화번호 빈값 확인 및 전화번소 자릿수 확인
                if (Number(tel.length) <= 0) {
                    alert('전화번호를 입력해주세요');
                    return false;
                } else if (Number(tel.length) < 8 || Number(tel.length) > 11) {
                    alert('전화번호를 재대로 입력해주세요');
                    return false;
                } 

                //이메일 빈 값 확인
                if(valueCheck(email)) {
                    alert('이메일을 입력해주세요');
                    return false;
                } 

                //이메일 도메인 확인
                if (valueCheck($('#emailDomain').val())) {
                    alert('이메일 도메인을 선택해주세요.');
                    return false;
                }

                //이메일 특수문자 및 공백 확인
                if (emailSpecialCharacterCheck > -1) {
                    alert('정상적인 email을 입력해주세요');
                    return false; 
                } 

                //적립금 빈 값 확인
                if (valueCheck($('#accumulated').val()) || $('#accumulated').val() <= 0) {
                    alert('적립금 액수를 입력해주세요.');
                    return false;
                } 
                
                // 결혼 상태 확인
                if (valueCheck($('input[name=marry]:checked').val())) {
                      alert('결혼 상태를 체크해주세요')
                      return false;
                }

                //주소 빈 값 확인
                if(valueCheck(addressNum)) {
                    alert('우편번호를 입력해주세요');
                    return false;
                } else if(valueCheck(addressRoad)) {
                    alert('도로명주소를 입력해주세요');
                    return false;
                }

                //주소 특수문자 확인
                if (addressNumCharacterCheck > -1 || addressNumSpecialCharacterCheck > -1) {
                    alert('정상적인 우편번호를 입력해주세요');
                    return false;
                } else  if (addressRoadSpecialCharacterCheck > -1) {
                    alert('정상적인 도로명주소를 입력해주세요');
                    return false;
                }

                //파일 유무 확인
                if($('#file').val() != '') {
                    //파일의 이름중에서 확장자만을 추출한다.
                    var ext = $('#file').val().split('.').pop().toLowerCase();
                    //확장자명이 jpg나 png일떄만 실행
                    if($.inArray(ext,['jpg','png']) == -1){
                        alert('jpg, png 파일만 업로드 가능합니다.');
                        return false;
                    }
                } 

                //개인정보수집동의 확인
                if (valueCheck($('#agree:checked').val())) {
                    alert('개인정보수집동의 박스를 체크해주세요.');
                    return false;
                }

                var formData =new FormData($('#userCreate')[0]);
                $.ajax({
                    url:'/users',
                    type:'post',
                    data:formData,
                    processData:false,  
                    contentType:false, 
                    datatype:'json',
                    success:function(result)
                    {
                        alert(result.msg);
                        $(location).attr('href', '/users');
                    }, 
                    error:function(request)
                    {
                        var errors = request.responseJSON.errors;
                        var error = '';
                        $.each(errors, function(index, value) {
                            error += value + '\n';
                        });
                        alert(error);
                    }
                });   
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