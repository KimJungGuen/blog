<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css'
          integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm'
          crossorigin='anonymous'>
    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js'
          integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q'
          crossorigin='anonymous'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js'
            integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl'
            crossorigin='anonymous'></script>
    <script src='https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js'></script>

  </head>
  <body>

    <div class='contains'>
      <form name='userUpdate' enctype='multipart/form-data'  method='post' action='/userUpdate'>
      <table class = 'table table-bordered'>
        @csrf
        <input type='hidden' name='_method' value='put' />
        <input type='hidden' name='userIndex' value="{{$userData['userIndex']}}" />
        <tbody>
          <tr>
            <td>비밀번호</td>
            <td><input type='password' id ='userPw' name='userPw' value="{{$userData['userPw']}}"  maxlength='20'/></td>
          </tr>
          <tr>
            <td>비밀번호 확인</td>
            <td><input type='password' id='userPwCheck' name='userPwCheck' value="{{$userData['userPw']}}" /></td>
          </tr>
          <tr>
            <td>전화번호</td>
            <td><input type='text' id='tel' name='tel' class='numberOnly' value="{{$userData['tel']}}"  maxlength='11' /></td>
          </tr>
          <tr>
            <td>이메일</td>
            <td>
              <input type='text' id='email' name='email' value="{{$userData['email']}}"  maxlength='50' /> @
              <select name='emailDomain'>
                <option value='{{$userData['emailDomain']}}'>{{$userData['emailDomain']}}</option>
                <option value='naver.com'>naver.com</option>
                <option value='gmail.com'>gmail.com</option>
                <option value='daum.com'>daum.com</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>적립금</td>
            <td><input type='text' id='accumulated' name='accumulated' class='numberOnly text-right' value="{{$userData['accumulated']}}" maxlength='15' /></td>
          </tr>
          <tr>
            <td>우편번호</td>
            <td>
              <input type='text' id='addressNum' name='addressNum' class='text-right' value="{{$userData['addressNum']}}"  maxlength='5' readonly/>
              <button type='button' class='btn' onclick='addressModal()'>우편번호 찾기</button>
            </td>
          </tr>
          <tr>
            <td>기본주소</td>
            <td><input type='text' id='addressRoad' name='addressRoad' style='width:100%'  value="{{$userData['addressRoad']}}" readonly/></td>
          </tr>
          <tr>
            <td>상세주소</td>
            <td><input type='text' id='addressDetail'name='addressDetail' style='width:100%' value="{{$userData['addressDetail']}}" /></td>
          </tr>
          <tr>
            <td>파일 업로드</td>
            <td><input type='file' id='file' name='file' value='' /></td>
          </tr>
          <tr>
            <td>비고</td>
            <td><textarea rows='2' name='etc' style='width:100%' value="{{$userData['etc']}}">{{$userData['etc']}}</textarea></td>
          </tr>
        </tbody>
      </table>
      <button type='button' class='btn btn-primary' onclick='update();'>저장하기</button>
      </form>
    </div>

  <script>
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
  		 var num = pw.search(/[0-9]/);
  		 var eng = pw.search(/[a-z]/i);
  		 var spe = pw.search(/[`~!@#$%^&*()<>?]/);

       //email 특문체크
       var email = $('#email').val();
       var emailCheck = name.search(/[`~!@#$%^&*()<>?]/g);

       //addressDetail 특문체크
       var address = $('#addressDetail').val();
       var addressCheck = name.search(/[`~!@#$%^&*()<>?]/g);

       if(!$('#userPw').val()) {
  			 alert('비밀번호를 입력해주세요');
  			 return;
  		 } if(!$('#userPwCheck').val()) {
  			 alert('비밀번호 확인을 입력해주세요');
  			 return;
  		 } if($('#userPw').val() !== $('#userPwCheck').val()
  			 && $('#userPw').val()
  			 && $('#userPwCheck').val()) {
  			 alert('비밀번호가 일치하지 않습니다.');
  			 return;
  		 } else if(pw.length < 8 || pw.length > 20) {
  			alert('8자리 ~ 20자리 이내로 입력해주세요.');
  			return;
  		 } else if(pw.search(/\s/) != -1){
  			alert('비밀번호는 공백 없이 입력해주세요.');
  			return;
  		 } else if(num < 0 || eng < 0 || spe < 0 ) {
  			alert('영문,숫자, 특수문자를 혼합하여 입력해주세요.');
  			return;
  		 } if(Number($('#tel').val().length) <= 0) {
  			 alert('전화번호를 입력해주세요');
  			 return;
  		 } else if(Number($('#tel').val().length) >= 12) {
  			 alert('전화번호를 재대로 입력해주세요');
  			 return;
  		 } if(!$('#email').val()) {
  			 alert('이메일을 입력해주세요');
  			 return;
  		 } if (emailCheck > -1 || email.search(/\s/) != -1) {
         alert('정상적인 email을 입력해주세요');
         return;
       } if(!$('#addressNum').val()) {
  			 alert('주소를 입력해주세요');
  			 return;
  		 } if (addressCheck > -1) {
         alert('정상적인 주소를 입력해주세요');
         return;
       } if($('#file').val() != '') {
  			 var ext = $('#file').val().split('.').pop().toLowerCase();
  			 if($.inArray(ext,['jpg','png']) == -1){
  				 alert('jpg, png 파일만 업로드 가능합니다.');
  				 return;
  			 }
  		 }
        $('form').submit();
    }
    $(document).ready(function(){
      //var aaa = prompt('비밀번호를 입력해주세요');
      //숫자입력 필드에 키업 이벤트 발생시 숫자필터 외의 문자열을 공백으로 수정
  		$('.numberOnly').keyup(function() {
  			$(this).val($(this).val().replace(/[^0-9]/g,''));
  		});
    });
  </script>
  </body>
</html>
