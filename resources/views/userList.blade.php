<!doctype html>
<html>
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

    <div>
     <form name='search' method='get' action='/userSearch'>
      <input type='hidden' name='searchPageLimit' value="{{$pageView['pageLimit']}}" />
      <table class='table'>
        <thead class='text-center'>
          <tr><th colspan='2'>검색</th></tr>
        </thead>
        <tbody >
          <tr>
            <td rowspan='2'>검색어</td>
            <td>
              <select id='filterFir' name='filterFir'>
                <option value=''>선택</option>
                <option value='user_id'>ID</option>
                <option value='name'>이름</option>
                <option value='email'>email</option>
              </select>
              <input type='text' id='searchFirWord' name='searchFirWord' />
            </td>
          </tr>
          <tr>
            <td>
              <select  id='filterSec' name='filterSec'>
                <option value=''>선택</option>
                <option value='user_id'>ID</option>
                <option value='name'>이름</option>
                <option value='email'>email</option>
              </select>
              <input type='text' id='searchSecWord' name='searchSecWord' />
            </td>
          </tr>
          <tr>
            <td>상태</td>
              <td>
                <input id='searchUserAll' name='searchUserAll' value='%' type='checkbox' checked> 모든계정
                <input id='searchUserActive' name='searchUserActive' value='가입' type='checkbox'> 사용계정
                <input id='searchUserSleep' name='searchUserSleep' value='휴면' type='checkbox'> 휴먼계정
              </td>
          </tr>
          <tr>
            <td>성별</td>
            <td>
              <input type='radio' id='gender' name='gender' value='%' checked/>전체
              <input type='radio'  name='gender' value='1'/>남
              <input type='radio'  name='gender' value='2'/>여
            </td>
          </tr>
          <tr>
            <td>가입일</td>
            <td>
              <input type='date' id='searchDateFir' name='searchDateFir' />
              <input type='date' id='searchDateSec' name='searchDateSec' />
            </td>
          </tr>
          <tr>
            <td>정렬</td>
            <td>
              <select id='sort' name='sort'>
                <option value='index'>번호</option>
                <option value='accumulated'>적립금</option>
                <option value='age'>나이</option>
              </select>
              <select id='orderBy' name='orderBy'>
                <option value='asc'>오름차순</option>
                <option value='desc'>내림차순</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <button type='button' class='btn' onclick='this.form.submit();'>검색</button>
              <button type='button' class='btn' onclick='defalut();'>초기화</button>
            <td>
          </tr>
        </tbody>
      </form>
    </div>

    <div>
      조회된 건 수 : {{$pageView['total']}}

      <input type='button' class='btn btn-primary'
      style='position: absolute; left: 90%; height: 2.5%'/
      value='유저등록'
      onclick="location.href='/user'"/>

      <input type='button' class='btn btn-primary'
      style='position: absolute; left: 95%; height: 2.5%'/
      value='유저삭제'
      onclick="location.href='/user'"/>
      @csrf
      <form name='pageing' method='get' action='/users'>
        <select style='position : absolute; left:98%; top:0px;' name='pageLimit' OnChange='this.form.submit();'>
          @for($i=1;$i<=10;$i++)
            @if($i == $pageView['pageLimit'])
              <option value={{$i}} selected>{{$i}}</option>
            @else
              <option value={{$i}}>{{$i}}</option>
            @endif
          @endfor
        </select>

        <table class='table table-bordered text-center'>
          <thead>
            <tr>
              <th><input type='checkbox'></th>
              <th>순번</th>
              <th>번호</th>
              <th>상태</th>
              <th>이름</th>
              <th>아이디</th>
              <th>성별</th>
              <th>나이</th>
              <th>전화번호</th>
              <th>email</th>
              <th>적립금</th>
              <th>가입일</th>
              <th>이동</th>
              <th>순서변경</th>
            </tr>
          </thead>
          @if($pageView['perPage'] < 1)
          <tbody class='text-center'>
            <tr><td class='table-dark' colspan='14'>동록된 데이터가 없습니다.</td></tr>
          </tbody>
          @elseif($pageView['perPage'] >= 1
          && $pageView['pageLimit'] > 0)
          <tbody>
            @foreach($users as $user)
            <tr name='user' onclick="userPwCheck('{{$user->index}}');">
              <td><input type='checkbox'></td>
              <td>{{$loop->iteration}}</td>
              <td >{{$user->index}}</td>
              <td >{{$user->user_status}}</td>
              <td >{{$user->name}}</td>
              <td >{{$user->user_id}}</td>
              <td >{{$user->gender}}</td>
              <td >{{$user->age}}</td>
              <td >{{$user->tel}}</td>
              <td >{{$user->email}}</td>
              <td >{{$user->accumulated}}</td>
              <td >{{$user->join_date}}</td>
              <td>이동</td>
              <td>순서변경</td>
            </tr>
              @endforeach
          </tbody>
          @endif
        </table>
          {{$users->appends(request()->input())->links()}}
      </form>
    </div>

    <div>
    </div>

    <script>
      //유저 비밀번호 체크 ajax
      function userPwCheck(index){
        var userPw = prompt('비밀번호를 입력해주세요');
        var userId = index;
        $.ajax({
          url:'/userPwCheck',
          type:'post',
          data:{'userIndex' : userId,
                'userPw' : userPw,
                '_token' : $('input[name=_token]').val()},
          datatype:'json',
          success:function(result){
            alert(result.msg);
            if (result.pwCheck) {
              var updateForm = $('<form></form>');
              var url = 'userUpdate/' + index;
              updateForm.attr('action', url);
              updateForm.attr('method', 'post');
              updateForm.append('@csrf');
              updateForm.appendTo('body');
              updateForm.submit();
            }
            //$(location).attr('href', '/userUpdate/' + userId);
          },
          error:function(request,sts,error){
            alert('통신에러');
          }
        });
      }
      //유저조회 조건 초기화
      function defalut(){
        //radio
        $('#gender').prop('checked', true);
        //text
        $('#searchFirWord').val('');
        $('#searchSecWord').val('');
        //date
        $('#searchDateFir').val('2000-01-01');
        $('#searchDateSec').val('2050-01-01');
        //check box
        $('#searchUserAll').prop('checked', true);
        $('#searchUserActive').prop('checked', false);;
        $('#searchUserSleep').prop('checked', false);;
        //serlect box
        $('#filterFir').val('');
        $('#filterSec').val('');
        $('#sort').val('index');
        $('#orderBy').val('asc');
      }

      //select box 전달
      function selectRequest(){

      }
    </script>
  </body>
</html>
