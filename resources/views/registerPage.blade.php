<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
          crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
          integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
          crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
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
            <td><input type="text" id="name" name="name" maxlength="5" /></td>
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
            <td><input type="password" id="userPw" name="userPw" maxlength="20"/></td>
          </tr>
          <tr>
            <td>비밀번호 확인</td>
            <td><input type="password" id="userPwCheck" name="userPwCheck" maxlength="20"/></td>
          </tr>
          <tr>
            <td>성별</td>
            <td>
              <select id="gender" name="gender">
                <option selected>선택</option>
                <option value="1">남</option>
                <option value="2">여</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>나이</td>
            <td><input type="text" id="age" name="age" class="numberOnly" maxlength="2" /></td>
          </tr>
          <tr>
            <td>전화번호</td>
            <td><input type="text" id="tel" name="tel" class="numberOnly" maxlength="11" /></td>
          </tr>
          <tr>
            <td>이메일</td>
            <td>
              <input type="text" id="email" name="email" maxlength="30" /> @
              <select id="emailDomain" name="emailDomain">
                <option selected>선택</option>
                <option value="naver.com">naver.com</option>
                <option value="gmail.com">gmail.com</option>
                <option value="daum.com">daum.com</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>적립금</td>
            <td><input type="text" id="accumulated" name="accumulated" value=0 class="numberOnly text-right" maxlength="50" onclick="accumlatedClear();"/></td>
          </tr>
          <tr>
            <td>결혼 여부</td>
            <td>
              <input type="radio"  name="marry" value="0" />미혼
              <input type="radio"  name="marry" value="1" />기혼
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
            <td><input type="file" id="file" name="file" value="" Onchange="fileImg(this);" /><img id="preImg" src="#"  width="200" height="200"/></td>
          </tr>
          <tr>
            <td>비고</td>
            <td><textarea rows="2" id="etc" name="etc" style="width:100%"></textarea></td>
          </tr>
          <tr>
            <td colspan="2">
              <input type="checkbox" id="agree" name="agree" value="1" />
              개인정보수집동의
            </td>
          </tr>
        </tbody>
      </table>

      <button type="button" id="submitBtn" class="btn btn-primary" onclick="validate();">저장하기</button>
      </form>
    </div>
    <script>

    //id 필드 다시 수정시 id체크 초기화
    function idCheckClear() {
      $("#idStatus").val(0);
      return false;
    }

    //클릭시 적립금 텍스트 초기화
    function accumlatedClear()
    {
      $("#accumulated").val("");
      return false;
    }

    //유저 파일 업로드시 미리보기
    function fileImg(input) 
    {
      if (input.files && input.files[0]) {
      //파일을 읽기위해 fileEader API를 사용
      var reader = new FileReader();

      reader.readAsDataURL(input.files[0]);
        //read가 끝나면 onload 트리거 발생
        reader.onload = function (e) {
          //result값은 base64로 인코딩된 데이터
          $("#preImg").attr("src", e.target.result);
        }
      }
    }

    //다음 주소 api
    function addressModal() 
    {
        new daum.Postcode({
            oncomplete: function(data) {
                // 우편번호와 주소 정보를 해당 필드에 넣는다. 이떄 주소는 도로명만 넣어진다.
                document.getElementById("addressNum").value = data.zonecode;
                document.getElementById("addressRoad").value = data.roadAddress;
                // 커서를 상세주소 필드로 이동한다.
                document.getElementById("addressDetail").focus();
            }
        }).open({
          //팝업창 옵션으로 이름을 주어 중복으로 안켜지게 한다.
          popupName : "postCodePopup"
        });
    }

    //유저 id중복체크
    function userIdCheck()
    {
      var userId = $("#userId").val();
      var userIdSpe = userId.search(/[~!@#$%^&*()<>?]/ig);

      if(userIdSpe < 0) {
        $.ajax({
          url:"/userIdCheck",
          type:"post",
          data:{
            "userId" : userId,
            "_token" : $("input[name=_token]").val()
          },
          datatype:"json",
          success:function(result){
            alert(result.msg);
            $("#idStatus").val(result.check);
            return false;
          },
            error:function(request){
            var error = request.responseJSON.errors;
            alert(error["userId"]);
            return false;
          }
        });
      } else {
        alert("ID에 특문을 제외하고 입력해주세요.");
        return false;
      }
    }
    //데이터 유효성 판단
    function validate()
    {
      var pw = $("#userPw").val();
      var pwCheck = $("#userPwCheck").val();

      //search() 검사하는 값이 없을경우 -1을 반환
      var pwNum = pw.search(/[0-9]/);
      var pwEng = pw.search(/[a-z]/i);
      var pwSpe = pw.search(/[~!@#$%^&*()<>?]/g);

      //유저 이름 특문 체크
      var name = $("#name").val();
      var nameSpe = name.search(/[~!@#$%^&*()<>?]/g);
      var nameNum = name.search(/[0-9]/g);

      //email 특문체크
      var email = $("#email").val();
      var emailCheck = email.search(/[~!@#$%^&*()<>?]/g);

      //addressDetail 특문체크
      var addressNum = $("#addressNum").val();
      var addressNumCheck = addressNum.search(/[~!@#$%^&*()<>?]/g);
      var addressEngCheck = addressNum.search(/[a-z]/ig);

      //addressDetail 특문체크
      var addressRoad = $("#addressRoad").val();
      var addressRoadCheck = addressRoad.search(/[~!@#$%^&*()<>?]/g);

      //addressDetail 특문체크
      var addressDetail = $("#addressDetail").val();
      var addressDetailCheck = addressDetail.search(/[~!@#$%^&*()<>?]/g);

      var age = $("#age").val();
      var ageEng = age.search(/[a-z]/ig);
      var ageSpe = age.search(/[~!@#$%^&*()<>?]/g);

      //이름 빈값 체크
      if (name.length < 2) 
      {
        alert("이름을 2자 이상 입력해주세요");
        return false;
      } 

      //이름 특문 체크
      if (nameNum > -1 || nameSpe > -1 || name.search(/\s/) != -1) {
        alert("정상적인 이름을 입력해주세요");
        return false;
      } 
      
      //아이디 빈값체크 체크
      if (!$("#userId").val()) {
        alert("아이디를 입력해주세요");
        return false;
      } 
  
      //아이디 중복확인
      if ($("#idStatus").val() == 0) {
        alert("아이디 중복확인을 해주세요");
        return false;
      } 
      
      //비밀번호 빈값 체크
      if (!pw) {
        alert("비밀번호를 입력해주세요");
        return false;
      } 
      
      //비밀번호 확인 빈값 체크
      if (!pwCheck) {
        alert("비밀번호 확인을 입력해주세요");
        return false;
      } 
      

      //비밀번호 자릿수 및 공백, 영어 숫자 특문 혼용 확인, 일치 확인
      if (pw !== pwCheck && pw && pwCheck) {
        alert("비밀번호가 일치하지 않습니다.");
        return false;
      } else if (pw.length < 8 || pw.length > 20) {
       alert("비밀번호는 8자리 ~ 20자리 이내로 입력해주세요.");
       return false;
      } else if (pw.search(/\s/) != -1) {
       alert("비밀번호는 공백 없이 입력해주세요.");
       return false;
      } else if (pwNum < 0 || pwEng < 0 || pwSpe < 0 ) {
       alert("영문,숫자, 특수문자를 혼합하여 입력해주세요.");
       return false;
      } 
      
      //성별체크 확인
      if($("#gender").val() === "선택") {
        alert("성별을 선택해주세요");
        return false;
      } 
      
      //나이 빈값확인 및 정상값 체크
      if(!age) {
        alert("나이를 입력해주세요");
        return false;
      } else if (Number(age) <= 0 && Number(age) >= 100) {
        alert("나이를 재대로 입력해주세요");
        return false;
      } 

      //나이 특문 및 문자 검사
      if (ageEng > -1 || ageSpe > -1) {
        alert("나이는 숫자만 입력해주세요.");
        return false;
      }
      
      //전화번호 빈값 체크 및 전화번소 자릿수 체크
      if (Number($("#tel").val().length) <= 0) {
        alert("전화번호를 입력해주세요");
        return false;
      } else if (Number($("#tel").val().length) != 11) {
        alert("전화번호를 재대로 입력해주세요");
        return false;
      } 
      
      //이메일 빈값 체크
      if(!email == "") {
        alert("이메일을 입력해주세요");
        return false;
      } 
      
      //이메일 도메인 체크
      if ($("#emailDomain").val() == "선택") {
        alert("이메일 도메인을 선택해주세요.");
        return false;
      }
      
      //이메일 특문 및 공백 체크
      if (emailCheck > -1 || email.search(/\s/) != -1) {
        alert("정상적인 email을 입력해주세요");
        return false; 
      } 
      
      //적립금 빈값 체크
      if (!$("#accumulated").val() || $("#accumulated").val() <= 0) {
        alert("적립금 액수를 입력해주세요.");
        return false;
      } 

      //결혼 상태 체크
      if (!$("input[name=marry]").prop("checked")) {
        alert("결혼 상태를 체크해주세요")
        return false;
      }

      //주소 빈값 체크
      if(!addressNum) {
        alert("우편번호를 입력해주세요");
        return false;
      } else if(!addressRoad) {
        alert("도로명주소를 입력해주세요");
        return false;
      } else if (!addressDetail){
        alert("상세주소를 입력해주세요");
        return false;
      }
      
      //주소 특문 체크
      if (addressNumCheck > -1 || addressEngCheck > -1) {
        alert("정상적인 우편번호를 입력해주세요");
        return false;
      } else  if (addressRoadCheck > -1) {
        alert("정상적인 도로명주소를 입력해주세요");
        return false;
      } else if (addressDetailCheck > -1) {
        alert("정상적인 상세주소를 입력해주세요");
        return false;
      } 
      
      //파일 유무 체크
      if($("#file").val() != "") {
        //파일의 이름중에서 확장자만을 추출한다.
        var ext = $("#file").val().split(".").pop().toLowerCase();
        //확장자명이 jpg나 png일떄만 실행
        if($.inArray(ext,["jpg","png"]) == -1){
          alert("jpg, png 파일만 업로드 가능합니다.");
          return false;
        }
      } 
      
      if (!$("#agree:checked").val()) {
        alert("개인정보수집동의 박스를 체크해주세요.");
        return false;
      }

      //ajax file전송을 위해서 FormData를 활용 *업데이트 부분 주석과 동일
      var formData =new FormData($("#userCreate")[0]);
      $.ajax({
        url: "/users",
        type: "post",
        data: formData,
        processData: false,  
        contentType: false, 
        datatype:"json",
        success:function(result)
        {
          alert(result.msg);
          $(location).attr("href", "/users");
        }, 
        error:function(request)
        {
          var errors = request.responseJSON.errors;
          var error = "";
          $.each(errors, function(index, value) {
            error += value + "\n";
          });
          alert(error);
        }
      });   
    }

    $(document).ready(function()
    {
      //숫자입력 필드에 문자열 입력시 공백으로 바꿈
      $(".numberOnly").keyup(function() 
      {
        $(this).val($(this).val().replace(/[^0-9]/g,""));
      });
    });
</script>
  </body>
</html>
