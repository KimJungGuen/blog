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
                                <input type="text" id="accumulated" name="accumulated" class="numberOnly text-right" value="{{$userData['accumulated']}}" maxlength="15"  onclick="accumlatedClear();"/>
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
                                <input type="file" id="file" name="file" value="" Onchange="fileImg(this);" /><img id="preImg" src="{{$userData['imgUrl']}}"  width="200" height="200"/>
                            </td>
                        </tr>
                        <tr>
                            <td>비고</td>
                            <td>
                                <textarea rows="2" id="etc" name="etc" style="width:100%" value="{{$userData['etc']}}">{{$userData['etc']}}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary" onclick="update(this);">저장하기</button>
            </form>
        </div>

        <script>
        
            //유저 파일 업로드시 미리보기
            function fileImg(input) {
                if (input.files && input.files[0]) {
                    //파일을 읽기위해 fileEader API를 사용
                    var reader = new FileReader();

                    reader.readAsDataURL(input.files[0]);
                    //read가 끝나면 onload 트리거 발생
                    reader.onload = function (e) {
                    //result값은 base64로 인코딩된 데이터
                        $('#preImg').attr('src', e.target.result);
                    }
                }
            }

            //다음 우편번호 api
            function addressModal() {
                new daum.Postcode({
                oncomplete: function(data) {
                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                    document.getElementById('addressNum').value = data.zonecode;
                    document.getElementById('addressRoad').value = data.roadAddress;
                    // 커서를 상세주소 필드로 이동한다.
                    document.getElementById('addressDetail').focus();
                }
                }).open({
                //팝업창 옵션으로 이름을 주어 중복으로 안켜지게 한다.
                    popupName : 'postCodePopup'
                });
            }

            //데이터 유효성 검사 및 전송
            function update(){
                var pw = $('#userPw').val();
                var pwCheck = $('#userPwCheck').val();
                var num = pw.search(/[0-9]/);
                var eng = pw.search(/[a-z]/i);
                var spe = pw.search(/[`~!@#$%^&*()<>?]/);

                //email 특문체크
                var email = $('#email').val();
                var emailCheck = email.search(/[`~!@#$%^&*()<>?]/g);

                //도로명 주소 특문체크
                var addressDetail = $('#addressDetail').val();
                var addressDetailCheck = addressDetail.search(/[`~!@#$%^&*()<>?]/g);

                var addressRoad = $('#addressRoad').val();
                var addressRoadCheck = addressRoad.search(/[`~!@#$%^&*()<>?]/g);

                var addressNum = $('#addressNum').val();
                var addressNumCheck = addressNum.search(/[`~!@#$%^&*()<>?]/g);
                var addressEngCheck = addressNum.search(/[a-z]/ig); 
    
                
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
                    } else if (num < 0 || eng < 0 || spe < 0 ) {
                        alert('영문,숫자, 특수문자를 혼합하여 입력해주세요.');
                        return false;
                    } 
                }

                //전화번호 빈값 체크 및 전화번소 자릿수 체크
                if (Number($('#tel').val().length) <= 0) {
                    alert('전화번호를 입력해주세요');
                    return false;
                } else if (Number($('#tel').val().length) != 11) {
                    alert('전화번호를 재대로 입력해주세요');
                    return false;
                } 

                //이메일 빈값 체크
                if(!email) {
                    alert('이메일을 입력해주세요');
                    return false;
                } 

                //이메일 도메인 체크
                if ($('#emailDomain').val() == '선택') {
                    alert('이메일 도메인을 선택해주세요.');
                    return false;
                }

                //이메일 특문 및 공백 체크
                if (emailCheck > -1 || email.search(/\s/) != -1) {
                    alert('정상적인 email을 입력해주세요');
                    return false; 
                } 

                //적립금 빈값 체크
                if (!$('#accumulated').val()) {
                    alert('적립금 액수를 입력해주세요.');
                    return false;
                } 

                //주소 빈값 체크
                if(!addressNum) {
                    alert('우편번호를 입력해주세요');
                    return false;
                } else if(!addressRoad) {
                    alert('도로명주소를 입력해주세요');
                    return false;
                } else if (!addressDetail){
                    alert('상세주소를 입력해주세요');
                    return false;
                }

                //주소 특문 체크
                if (addressNumCheck > -1 || addressEngCheck > -1) {
                    alert('정상적인 우편번호를 입력해주세요');
                    return false;
                } else  if (addressRoadCheck > -1) {
                    alert('정상적인 도로명주소를 입력해주세요');
                    return false;
                } else if (addressDetailCheck > -1) {
                    alert('정상적인 상세주소를 입력해주세요');
                    return false;
                } 

                //파일 유무 체크
                if($('#file').val() != '') {
                    //파일의 이름중에서 확장자만을 추출한다.
                    var ext = $('#file').val().split('.').pop().toLowerCase();
                    //확장자명이 jpg나 png일떄만 실행
                    if($.inArray(ext,['jpg','png']) == -1){
                        alert('jpg, png 파일만 업로드 가능합니다.');
                        return false;
                    }
                } 

                var formData =new FormData($('#userUpdate')[0]);
                //유저 업데이트
                $.ajax({
                    url:'/userUpdate',
                    type:'post',
                    data:formData,
                    datatype:'json',
                    processData: false,  //지정 안되어있을경우 항상 true 값은 queryString을보냄 file이 포함될경우 false로 해야함
                    contentType: false,   
                    success:function(result){
                        alert(result.msg);
                        //$(location).attr('href', '/users');
                    },
                    error:function(request){
                        var errors = request.responseJSON.errors; //json형태로 변수에 errors값 삽입
                        var error = '';
                        $.each(errors, function(index, value) {
                        error += value + '\n'; //유효성 검사가 복수로 걸릴경우 반복문돌면서 메시지 통합
                        });
                        alert(error);
                    }
                });
            }
            $(document).ready(function(){
                //숫자입력 필드에 키업 이벤트 발생시 숫자필터 외의 문자열을 공백으로 수정
                $('.numberOnly').keyup(function() {
                    $(this).val($(this).val().replace(/[^0-9]/g,''));
                });
            });
        </script>
    </body>
</html>
