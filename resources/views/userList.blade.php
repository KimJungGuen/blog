<!doctype html>
<html>
  <head>
    <!-- section 1 !-->
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
    <!-- section 1 !-->
  </head>
  <body>

    <div>
     @if (!$searchData) <!--초기 메인페이지 에서 검색 데이터가 없을경우 !-->
     <form id='searchForm' name='search' method='get' action='/userSearch'>
      <input type='hidden' id='searchPageLimit' name='searchPageLimit' value='' />
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
                <input id='searchUserAll' name='searchUserAll' value=3  type='checkbox' checked> 모든계정
                <input id='searchUserActive' name='searchUserActive' value=1 type='checkbox'> 사용계정
                <input id='searchUserSleep' name='searchUserSleep' value=2 type='checkbox'> 휴먼계정
              </td>
          </tr>
          <tr>
            <td>성별</td>
            <td>
              <input type='radio' id='gender' name='gender' value=3 checked/>전체
              <input type='radio'  name='gender' value=1 />남
              <input type='radio'  name='gender' value=2 />여
            </td>
          </tr>
          <tr>
            <td>가입일</td>
            <td>
              <input type='date' id='searchDateFir' name='searchDateFir' value="{{now()->format('Y-m-d')}}"/>
              <input type='date' id='searchDateSec' name='searchDateSec' value="{{now()->format('Y-m-d')}}"/>
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
              <button type='button' class='btn' onclick='searchUsers();'>검색</button>
              <button type='button' class='btn' onclick='defalut();'>초기화</button>
            <td>
          </tr>
        </tbody>
      </form>

      @elseif ($searchData) <!--검색 후에 데이터가 있을경우 !-->
      <form id='searchForm' name='search' method='get' action='/userSearch'>
       <input type='hidden' id='searchPageLimit' name='searchPageLimit' value='' />
       <table class='table'>
         <thead class='text-center'>
           <tr><th colspan='2'>검색</th></tr>
         </thead>
         <tbody >
           <tr>
             <td rowspan='2'>검색어</td>
             <td>
               <select id='filterFir' name='filterFir'>
                 <option value="{{$searchData['filterFir']}}">선택</option>
                 <option value='user_id'>ID</option>
                 <option value='name'>이름</option>
                 <option value='email'>email</option>
               </select>
               <input type='text' id='searchFirWord' name='searchFirWord' value="{{$searchData['searchTextFir']}}" />
             </td>
           </tr>
           <tr>
             <td>
               <select  id='filterSec' name='filterSec'>
                 <option value="{{$searchData['filterSec']}}">선택</option>
                 <option value='user_id'>ID</option>
                 <option value='name'>이름</option>
                 <option value='email'>email</option>
               </select>
               <input type='text' id='searchSecWord' name='searchSecWord' value="{{$searchData['searchTextSec']}}"/>
             </td>
           </tr>
           <tr>
             <td>상태</td>
               <td>
                 <input id='searchUserAll' name='searchUserAll' value=3 type='checkbox' checked> 모든계정
                 <input id='searchUserActive' name='searchUserActive' value=1 type='checkbox'> 사용계정
                 <input id='searchUserSleep' name='searchUserSleep' value=2 type='checkbox'> 휴먼계정
               </td>
           </tr>
           <tr>
             <td>성별</td>
             <td>
               <input type='radio' id='gender' name='gender' value=3 checked/>전체
               <input type='radio'  name='gender' value=1 />남
               <input type='radio'  name='gender' value=2 />여
             </td>
           </tr>
           <tr>
             <td>가입일</td>
             <td>
               <input type='date' id='searchDateFir' name='searchDateFir' value="{{$searchData['searchDateFir']}}" />
               <input type='date' id='searchDateSec' name='searchDateSec' value="{{$searchData['searchDateSec']}}" />
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
               <button type='button' class='btn' onclick='searchUsers();'>검색</button>
               <button type='button' class='btn' onclick='defalut();'>초기화</button>
             <td>
           </tr>
         </tbody>
       </form>
      @endif
    </div>



    <div>
      <p style='position:absolute; height:3.3%; top:40%; left:8%;' > 조회된 건 수 : {{$pageView['total']}} </p>

      <input type='button' class='btn btn-primary'
      style='position:absolute; top:40%; left:85%; height:3.5%;'/
      value='유저등록'
      onclick="location.href='/user'"/>

      <input type='button' class='btn btn-primary'
      style='position:absolute; top:40%; left: 90%; height: 3.5%;'/
      value='유저삭제'
      onclick='userDelete()'/>
      <form id='ordinaryForm' name='ordinary' method='get' action='/users'>
        <select style='position:absolute; height:3.3%; top:40%; left:98%;' id='pageLimit' name='pageLimit' OnChange='ChangePageLimit();'>
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
              <th>삭제</th>
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
            <tr name='user' onclick="userPwCheck('{{$user->index}}');" >
              <td onclick='event.cancelBubble=true'><input class='deleteBox' type='checkbox' value='{{$user->index}}'></td>
              <td>{{$loop->iteration}}</td>
              <td >{{$user->index}}</td>
              <td >{{$userStatus[$loop->index]}}</td>
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
          {{$users->appends(request()->query())->links()}}
      </form>
    </div>


    <div>
    </div>
    @csrf
    <script>
      //유저 비밀번호 체크 ajax
      function userPwCheck(index) {
        var userPw = prompt('비밀번호를 입력해주세요');
        var userIndex = index;
        $.ajax({
          url:'/userPwCheck',
          type:'post',
          data:{'userIndex' : userIndex,
                'userPw' : userPw,
                '_token' : $('input[name=_token]').val()},
          datatype:'json',
          success:function(result){
            alert(result.msg);
            if (result.pwCheck) {
              $(location).attr('href', '/userUpdate/' + userIndex);
            }
          },
          error:function(request,sts,error){
            alert('통신에러');
          }
        });
      }
      //유저 조회
      function searchUsers() {
        //첫번쨰 검색어 필드 특문체크
        var name = $('#searchFirWord').val();
        var nameCheck = name.search(/[~!@#$%^&*()<>?]/g);

        //두번쨰 검색어 필트 특문체크
        var email = $('#searchSecWord').val();
        var emailCheck = name.search(/[~!#$%^&*()<>?]/g);

        //기존 pageLimit은 form이달라 값에 포함이 안되어 따로 searchForm에 할당
        $('#searchPageLimit').attr('value',$('#pageLimit option:selected').val());

        if (!$('#filterFir').val() || !$('#filterSec').val()) { // 검색 필터가 안 정해져있을떄
          alert('검색필터를 골라주세요.');
          return;
        } else if ($('#filterFir').val() && $('#filterSec').val()) {
            if (!$('#searchFirWord').val() || !$('#searchSecWord').val()) { //검색어 필드가 빈 값일때
              alert('검색어를 입력해주세요.');
              return;
            } else if ($('#searchFirWord').val() && $('#searchSecWord').val()) {
              $('#searchForm').submit();
            }
        }
      }
      //users페이지와 userSearch페이지 구분후 pageLimit설정
      function ChangePageLimit() {
        //url기준으로 메인 users페이지 조절인지 search페이지 조절인지 구분
        var actionUrl = $(location).attr('href').split('/').pop().split('?');
        //get 데이터를 다시 나눔
        var query = $(location).attr('href').split('?').pop().split('&');
        //기존 pageLimit은 form이달라 값에 포함이 안되어 따로 searchForm에 할당
        $('#searchPageLimit').attr('value',$('#pageLimit option:selected').val());
        //param의 첫번쨰 데이터는 페이지 표시수가 바뀌면 수정돼야 한다
        var param = 'searchPageLimit=' + $('#searchPageLimit').val();;
        //나누어진 query를 다시 합침
        $.each(query, function(index, value) {
          if (index > 0 && index < 11) {
            param += ('&' + value);
          }
        });
        //actionUrl 변수에 따라 데이터 전송 할곳을 선택
        (('users' == actionUrl[0])
        ? $('#ordinaryForm').submit()
        : $(location).attr('href', '/userSearch?' + param));
      }
      //유저조회 조건 초기화
      function defalut() {
        //radio
        $('#gender').prop('checked', true);
        //text
        $('#searchFirWord').val('');
        $('#searchSecWord').val('');
        //date
        $('#searchDateFir').val('');
        $('#searchDateSec').val('');
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
      //유저 삭제
      function userDelete() {
        var deleteCheck = confirm('선택된 유저를 삭제하시겠습니까?');
        var userIndex = {};

        if (deleteCheck) {
          $('.deleteBox:checked').each(function(index){
            userIndex[index] = $(this).val();
          });

          $.ajax({
            url:'/userDelete',
            type:'delete',
            data:{'userIndex' : userIndex,
                  '_token' : $('input[name=_token]').val()},
            datatype:'json',
            success:function(result){
              if (result.deleteRow) {
                alert(result.msg);
              } else {
                alert('회원 탈퇴가 실패했습니다.')
              }
            },
            error:function(request,sts,error){
              alert('통신에러');
            }
          });
        }
      }
    </script>
  </body>
</html>
