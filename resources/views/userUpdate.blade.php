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
            <form id="userUpdate" name="userUpdate" enctype="multipart/form-data"  method="post" action="/userUpdate">
                <table class = "table table-bordered">
                    @csrf
                    <input type="hidden" name="_method" value="put" />
                    <input type="hidden" id="userIndex" name="userIndex" value="{{$userData['userIndex']}}" />
                    <tbody>
                        <tr>
                            <td>비밀번호</td>
                            <td>
                                <input type="password" id ="userPw" name="userPw" maxlength="20"/>
                            </td>
                        </tr>
                        <tr>
                            <td>비밀번호 확인</td>
                            <td>
                                <input type="password" id="userPwCheck" name="userPwCheck" />
                            </td>
                        </tr>
                        <tr>
                            <td>전화번호</td>
                            <td>
                                <input type="text" id="tel" name="tel" class="numberOnly" value="{{$userData['tel']}}"  maxlength="11" />
                            </td>
                        </tr>
                        <tr>
                            <td>이메일</td>
                            <td>
                                <input type="text" id="email" name="email" value="{{$userData['email']}}"  maxlength="50" /> @
                                <select id="emailDomain" name="emailDomain">
                                    <option value="naver.com" @if ($userData['emailDomain'] == 'naver.com') selected @endif >naver.com</option>
                                    <option value="gmail.com" @if ($userData['emailDomain'] == 'gmail.com') selected @endif >gmail.com</option>
                                    <option value="daum.com" @if ($userData['emailDomain'] == 'daum.com') selected @endif >daum.com</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>적립금</td>
                            <td>
                                <input type="text" id="accumulated" name="accumulated" class="numberOnly text-right" value="{{$userData['accumulated']}}" maxlength="10" placeholder="0"/>
                            </td>
                        </tr>
                        <tr>
                            <td>우편번호</td>
                            <td>
                                <input type="text" id="addressNum" name="addressNum" class="text-right" value="{{$userData['addressNum']}}"  maxlength="5" readonly/>
                                <button type="button" class="btn" onclick="addressModal()">우편번호 찾기</button>
                            </td>
                        </tr>
                        <tr>
                        <td>기본주소</td>
                            <td>
                                <input type="text" id="addressRoad" name="addressRoad" style="width:100%"  value="{{$userData['addressRoad']}}" readonly/>
                            </td>
                        </tr>
                        <tr>
                            <td>상세주소</td>
                            <td>
                                <input type="text" id="addressDetail"name="addressDetail" style="width:100%" value="{{$userData['addressDetail']}}" />
                            </td>
                        </tr>
                        <tr>
                        <td>파일 업로드</td>
                            <td>
                                <input type="file" id="file" name="file" value="" Onchange="filePreView(this);" /><img id="preImg" src="{{$userData['imgUrl']}}"  width="200" height="200"/>
                            </td>
                        </tr>
                        <tr>
                            <td>비고</td>
                            <td>
                                <textarea rows="2" id="etc" name="etc" style="width:100%" value="{{$userData['etc']}}" maxlength="300">{{$userData['etc']}}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary" onclick="update(this);">저장하기</button>
            </form>
        </div>

        <script>
        
            //@brief    파일 이미지 보기
            //@param    input : 선택한 파일의 경로
            function filePreView(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.readAsDataURL(input.files[0]);

                    reader.onload = function (e) {
                        $('#preImg').attr('src', e.target.result);
                    }
                }
            }

            //@brief    다음 우편번호 api
            function addressModal() {
                new daum.Postcode({
                oncomplete: function(data) {
                    // 우편번호와 주소 정보
                    document.getElementById('addressNum').value = data.zonecode;
                    document.getElementById('addressRoad').value = data.roadAddress;
                    // 커서를 상세주소 필드로 이동한다.
                    document.getElementById('addressDetail').focus();
                }
                }).open({
                    //중복 켜짐 제거
                    popupName : 'postCodePopup'
                });
            }

            //@brief    데이터 유효성 검사 및 전송
            function update(){

                //특수문자, 문자, 숫자 정규식 지정
                var specialCharacter = /[~!@#$%^&*()<>?]/g;
                var character = /[a-z]/ig;
                var number = /[0-9]/g;

                //비밀번호, 비밀번호 확인 숫자, 문자, 특수문자 확인
                var pw = $('#userPw').val();
                var pwCheck = $('#userPwCheck').val();
                var pwNumberCheck = pw.search(number);
                var pwCharacterCheck = pw.search(character);
                var pwSpecialCharacterCheck = pw.search(specialCharacter);

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
                
                if (pw != '' || pwCheck != '') {
                    //비밀번호 자릿수 및 공백, 영어 숫자 특문 혼용 확인, 일치 확인
                    if (pw !== pwCheck && pw && pwCheck) {
                        alert('비밀번호가 일치하지 않습니다.');
                        return false;
                    } else if (pw.length < 8 || pw.length > 20) {
                        alert('비밀번호는 8자리 ~ 20자리 이내로 입력해주세요.');
                        return false;
                    } else if (pw.search(/\s/) != -1) {
                        alert('비밀번호는 공백 없이 입력해주세요.');
                        return false;
                    } else if (pwNumberCheck < 0 || pwCharacterCheck < 0 || pwSpecialCharacterCheck < 0 ) {
                        alert('영문,숫자, 특수문자를 혼합하여 입력해주세요.');
                        return false;
                    } 
                }

                //전화번호 빈값 및 자릿수 확인
                if (Number($('#tel').val().length) <= 0) {
                    alert('전화번호를 입력해주세요');
                    return false;
                } else if (Number($('#tel').val().length) < 8 || Number($('#tel').val().length) > 11) {
                    alert('전화번호를 재대로 입력해주세요');
                    return false;
                } 

                //이메일 빈값 확인
                if(!email) {
                    alert('이메일을 입력해주세요');
                    return false;
                } 

                //이메일 도메인 확인
                if ($('#emailDomain').val() == '선택') {
                    alert('이메일 도메인을 선택해주세요.');
                    return false;
                }

                //이메일 특문 및 공백 확인
                if (emailSpecialCharacterCheck > -1 || email.search(/\s/) != -1) {
                    alert('정상적인 email을 입력해주세요');
                    return false; 
                } 

                //적립금 빈값 확인
                if (!$('#accumulated').val()) {
                    alert('적립금 액수를 입력해주세요.');
                    return false;
                } 

                //주소 빈값 확인
                if(!addressNum) {
                    alert('우편번호를 입력해주세요');
                    return false;
                } else if(!addressRoad) {
                    alert('도로명주소를 입력해주세요');
                    return false;
                }

                //주소 특문 확인
                if (addressNumCharacterCheck > -1 || addressNumSpecialCharacterCheck > -1) {
                    alert('정상적인 우편번호를 입력해주세요');
                    return false;
                } else  if (addressRoadSpecialCharacterCheck > -1) {
                    alert('정상적인 도로명주소를 입력해주세요');
                    return false;
                }

                //파일 유무 확인
                if($('#file').val() != '') {
                    var ext = $('#file').val().split('.').pop().toLowerCase();
                    //확장자명이 jpg나 png일떄만 실행
                    if($.inArray(ext,['jpg','png']) == -1){
                        alert('jpg, png 파일만 업로드 가능합니다.');
                        return false;
                    }
                } 

                var userIndex = $('#userIndex').val();
                var formData = new FormData($('#userUpdate')[0]);

                //유저 업데이트
                $.ajax({
                    url:'/userUpdate/' + userIndex,
                    type:'post',
                    data:formData,
                    datatype:'json',
                    processData: false, 
                    contentType: false,   
                    success:function(result){
                        alert(result.msg);
                        $(location).attr('href', '/users');
                    },
                    error:function(request){
                        var errors = request.responseJSON.errors;
                        var error = '';
                        $.each(errors, function(index, value) {
                            //유효성 검사가 복수로 걸릴경우 반복문돌면서 메시지 통합
                            error += value + '\n'; 
                        });
                        alert(error);
                    }
                });
            }

            $(document).ready(function(){
                //@brief    숫자필드에 문자입력시 공백
                $('.numberOnly').keyup(function() {
                    $(this).val($(this).val().replace(/[^0-9]/g,''));
                });
            });
        </script>
    </body>
</html>