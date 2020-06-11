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
      <form id='searchForm' name='search' class='form' method='get' action='/userSearch'>
        <input type='hidden' id='searchPageLimit' name='searchPageLimit' value='' />
        <table class='table table-bordered'>
          <thead class='text-center table-primary'>
            <tr><th colspan='2'>검색</th></tr>
          </thead>
          <tbody >
            <tr>
              <td class='table-primary' rowspan='2'>검색어</td>
              <td>
                <select id='filterFir' name='filterFir'>
                  <option value=''>선택</option>
                  <option value='user_id' @if ($searchData['filterFir'] == 'user_id') selected @endif>ID</option>
                  <option value='name' @if ($searchData['filterFir'] == 'name') selected @endif>이름</option>
                  <option value='email' @if ($searchData['filterFir'] == 'email') selected @endif>email</option>
                </select>
                <input type='text' id='searchFirWord' name='searchFirWord' value="{{$searchData['searchTextFir']}}" />
              </td>
            </tr>
            <tr>
              <td>
                <select id='filterSec' name='filterSec'>
                  <option value=''>선택</option>
                  <option value='user_id' @if ($searchData['filterSec'] == 'user_id') selected @endif>ID</option>
                  <option value='name' @if ($searchData['filterSec'] == 'name') selected @endif>이름</option>
                  <option value='email' @if ($searchData['filterSec'] == 'email') selected @endif>email</option>
                </select>
                <input type='text' id='searchSecWord' name='searchSecWord' value="{{$searchData['searchTextSec']}}" />
              </td>
            </tr>
            <tr>
              <td class='table-primary'>상태</td>
              <td>
                <input id='searchUserAll' name='searchUserAll' value='all' onclick='statusCheckBox();'  type='checkbox' @if ($searchData['userStatus'] == 'all') checked @endif> 모든계정
                <input id='searchUserActive' name='searchUserActive' value='active' onclick='statusCheckBox();' type='checkbox' @if ($searchData['userStatus'] == 'active') checked @endif> 사용계정
                <input id='searchUserSleep' name='searchUserSleep' value='sleep' onclick='statusCheckBox();' type='checkbox' @if ($searchData['userStatus'] == 'sleep') checked @endif> 휴먼계정
              </td>
            </tr>
            <tr>
              <td class='table-primary'>성별</td>
              <td>
                <input type='radio' id='gender' name='gender' value='all' @if ($searchData['gender'] == 'all') checked @endif/>전체
                <input type='radio'  name='gender' value='M' @if ($searchData['gender'] == 'M') checked @endif />남
                <input type='radio'  name='gender' value='F' @if ($searchData['gender'] == 'F') checked @endif />여
              </td>
            </tr>
            <tr>
              <td class='table-primary'>가입일</td>
              <td>
                <input type='date' id='searchDateFir' name='searchDateFir' value="{{$searchData['searchDateFir']}}"/>
                <input type='date' id='searchDateSec' name='searchDateSec' value="{{$searchData['searchDateSec']}}"/>
              </td>
            </tr>
            <tr>
              <td class='table-primary'>정렬</td>
              <td>
                <select id='sort' name='sort' onchange='changePage();'>
                  <option value='index' @if ($searchData['sort'] == 'index') selected @endif>번호</option>
                  <option value='accumulated' @if ($searchData['sort'] == 'accumulated') selected @endif>적립금</option>
                  <option value='age' @if ($searchData['sort'] == 'age') selected @endif>나이</option>
                </select>
                  <select id='orderBy' name='orderBy' onchange='changePage();'>
                  <option value='asc' @if ($searchData['orderBy'] == 'asc') selected @endif>오름차순</option>
                  <option value='desc' @if ($searchData['orderBy'] == 'desc') selected @endif>내림차순</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>
                <button type='button' class='btn' onclick='searchUsers();'>검색</button>
                <button type='button' class='btn' onclick='searchDefault();'>초기화</button>
              <td>
            </tr>
          </tbody>
        </table>
      </form>
    </div>

    <div>
      <p style='position:absolute; height:3.3%; top:40%; left:20%;' > 조회된 건 수 : {{$pageView['total']}} </p>
      <input type='button' class='btn btn-primary'
      style='position:absolute; top:40%; left:87%; height:3.5%;'
      value='유저등록'
      onclick="location.href='/user'"/>

      <input type='button' class='btn btn-danger'
      style='position:absolute; top:40%; left: 92%; height: 3.5%;'
      value='유저탈퇴'
      onclick='userDelete()'/>
      <form id='ordinaryForm' name='ordinary' class='form' method='get' action='/users'>
        <input type='hidden' id='listSort' name='listSort' value="{{$searchData['sort']}}" />
        <input type='hidden' id='listOrderBy' name='listOrderBy' value="{{$searchData['orderBy']}}" />
        <select style='position:absolute; height:3.3%; top:40%; left:98%;' id='pageLimit' name='pageLimit' onchange='changePage();'>
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
          <tbody>
          @if(isset($users) && count($users) > 0)
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
          @else
            <tr><td class='table-dark' colspan='14'>동록된 데이터가 없습니다.</td></tr>
          @endif
          </tbody>
          
        </table>
          <span style='position:relative; top:5%; left:43%;'>{{$users->appends(request()->query())->links()}}</span>
      </form>
    </div>


    <div>
      <table class='table text-center table-bordered'>
        <thead>
          <tr>
            <td class='table-primary' colspan='5'>통계자료</td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class='table-primary' rowspan='2'>계정별</td>
            <td>총 계정</td>
            <td>사용계정</td>
            <td>휴면 계정</td>
            <td>탈퇴계정</td>
          </tr>
          <tr>
            <td>{{$avgData['total']}}명</td>
            <td>{{$avgData['active']}}명</td>
            <td>{{$avgData['sleep']}}명</td>
            <td>0명</td>
          </tr>
          <tr>
            <td class='table-primary' rowspan='2'>성별</td>
            <td>남자</td>
            <td>여자</td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td>{{$avgData['male']}}명</td>
            <td>{{$avgData['female']}}명</td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td class='table-primary' rowspan='2'>연령별</td>
            <td>19세 이하</td>
            <td>20~30 대</td>
            <td>40~50 대</td>
            <td>60세 이상</td>
          </tr>
          <tr>
            <td>{{$avgData['children']}}명</td>
            <td>{{$avgData['young']}}명</td>
            <td>{{$avgData['adult']}}명</td>
            <td>{{$avgData['old']}}명</td>
          </tr>
          <tr>
            <td class='table-primary' rowspan='2'>적립금별</td>
            <td>1,000원 미만</td>
            <td>1,000 ~ 9,999원</td>
            <td>10,000원 이상</td>
            <td>적립금 총액</td>
          </tr>
          <tr>
            <td>{{$avgData['minAccumulated']}}명</td>
            <td>{{$avgData['middleAccumulated']}}명</td>
            <td>{{$avgData['hightAccumulated']}}명</td>
            <td>{{$avgData['totalAccumulated']}}원</td>
          </tr>
        </tbody>
      </table>
    </div>
    @if($errors->any())
      <input type='hidden' id='error' value="{{$errors->first('searchDateFir')}}" />
    @endif

    @csrf
    <script>

      //검색 시작일이 종료일보다 빠를경우
      (function errorMessage() {
        var error = $('#error').val();
        if(error != null) {
          alert(error);
        }
      })()

      //검색 폼의 상태 체크박스 상태 컨트롤
      function statusCheckBox() {
        var $userAll = $('#searchUserAll');
        var $userActive = $('#searchUserActive');
        var $userSleep = $('#searchUserSleep');
        //사용 혹은 휴면 버튼 클릭시 모든계정 버튼 비활성화           
        if (event.target.id != 'searchUserAll') {
          $userAll.prop('checked', false);
        }

        //사용 또는 휴면 버튼과 모든계정 버튼을 누를경우 사용 또는 휴면 버튼 비활성화 
        if ($userActive.prop('checked') && $userAll.prop('checked')) {
          $userActive.prop('checked', false);
        } 
        if ($userSleep.prop('checked') &&  $userAll.prop('checked')) {
          $userSleep.prop('checked', false);
        }

        //사용 버튼과 휴면 버튼을 둘다 누를 경우 둘다 비활성화 모든 계정 버튼 활성화 
        if ($userActive.prop('checked') && $userSleep.prop('checked')) {
          $userAll.prop('checked', true);
          $userActive.prop('checked', true);
          $userSleep.prop('checked', true);
        }

        //모든 버튼 클릭시 전부 살아있을경우 비활성화
        if (event.target.id == 'searchUserAll' && $userActive.prop('checked') && $userSleep.prop('checked') && $userAll.prop('checked')) {
            $userAll.prop('checked', false);
            $userActive.prop('checked', false);
            $userSleep.prop('checked', false);
        } else if (event.target.id == 'searchUserAll' && !$userActive.prop('checked') && !$userSleep.prop('checked') && $userAll.prop('checked')) {
            $userActive.prop('checked', true);
            $userSleep.prop('checked', true);
        }
      }


      //유저 비밀번호 체크 ajax
      function userPwCheck(index) {
        var userPw = prompt('비밀번호를 입력해주세요');

        if (userPw) {
          var userIndex = index;
          $.ajax({
            url:'/userPwCheck/'+index,
            type:'post',
            data:{'userIndex' : userIndex,
                  'userPw' : userPw,
                  '_token' : $('input[name=_token]').val()},
            datatype:'json',
            success:function(result){
              alert('비밀번호가 일치합니다.')
              if (result.pwCheck) {
                $(location).attr('href', '/userUpdate/' + userIndex);
              }
            },
            error:function(request,sts,error){
              var error = request.responseJSON.errors;
              alert(error['userPw']);
            }
          });
        } else if (userPw == '') {
          alert('비밀번호를 입력해주세요.');
        }
      }

      //유저 조회
      function searchUsers() {
        //첫번쨰 검색어 필드 특문체크
        var searchFirWord = $('#searchFirWord').val();
        var textCheckFir = searchFirWord.search(/[~!@#$%^&*()<>?]/g);

        //두번쨰 검색어 필트 특문체크
        var searchSecWord = $('#searchSecWord').val();
        var textCheckSec = searchSecWord.search(/[~!@#$%^&*()<>?]/g);

        //검색날짜 시작일 종료일
        var searchDateAfter = $('#searchDateFir').val();
        var searchDateBefore = $('#searchDateSec').val();

        var userAll = $('#searchUserAll').prop('checked');
        var userActive = $('#searchUserActive').prop('checked');
        var userSleep = $('#searchUserSleep').prop('checked');

        //체크박스를 모두 해제하고 보낼경우
        if (!userAll && !userActive && !userSleep) {
          alert('검색할 계정 상태를 최소 하나는 골라주세요.');
          return false;
        }
        
        //시작일이나 종료일이 없을경우
        if (!searchDateAfter || !searchDateBefore) {
          alert('시작일이나 종료일이 안정했졌습니다.');
          return false;
        }

        //날짜연산을 위해서 문자 -제거
        var dateAfter = searchDateAfter.split('-');
        var dateBefore = searchDateBefore.split('-');
        var dateValue = 0;

        for (var index=0; index<3; index++) {
          dateValue += (dateBefore[index] - dateAfter[index]);
        }

        //검색 시작일이 종료일보다 늦을경우
        if (dateValue < 0) {
          alert('시작일을 종료일보다 앞 선 날짜로 설정해주세요.');
          return false;
        }
        
        //기존 pageLimit은 form이달라 값에 포함이 안되어 따로 searchForm에 할당
        $('#searchPageLimit').attr('value',$('#pageLimit option:selected').val());

        if (textCheckFir == -1 && textCheckSec == -1) {
            $('#searchForm').submit();
        } else {
          alert('검색어를 재대로 입력해주세요.');
          return false;
        }
      }

      //유저조회 조건 초기화
      function searchDefault() {
        var today = new Date();

        //년도 xxxx형식
        var year = today.getFullYear();
        
        //월 0~11
        //첫번째 날짜 비교 필터의 월 
        var monthAfter = (today.getMonth()+1 < 10) ? '0' + (today.getMonth()) : (today.getMonth()+1);

        //1월일 경우 작년으로 바꾸고 12월로 해준다.
        if (today.getMonth() == 0) {
          year = today.getFullYear()-1;
          monthAfter = today.getMonth()+12;
        }
        
        //두번째 날짜 비교 필터의 월
        var monthBefore = (today.getMonth()+1 < 10) ? '0' + (today.getMonth()+1) : (today.getMonth()+1);

        //일
        var day = (today.getDate() < 10) ? '0' + today.getDate() : today.getDate();

        //각자 구한 년 월 일 을 합침
        var dateDefaultAfter = year + '-' + monthAfter + '-' + day;
        var dateDefaultBefore = year + '-' + monthBefore + '-' + day;

        //radio
        $('#gender').prop('checked', true);
        //text
        $('#searchFirWord').val('');
        $('#searchSecWord').val('');
        //date
        $('#searchDateFir').val(dateDefaultAfter);
        $('#searchDateSec').val(dateDefaultBefore);
        //check box
        $('#searchUserAll').prop('checked', true);
        $('#searchUserActive').prop('checked', false);;
        $('#searchUserSleep').prop('checked', false);;
        //serlect box
        $('.basicFilter').attr('value', '');
        $('.basicFilter').text('선택');
        $('#filterFir').val('');
        $('#filterSec').val('');
        $('#sort').val('index');
        $('#orderBy').val('asc');
      }

      //users페이지와 userSearch페이지 orderBy 및 pageLimit 재정렬
      function changePage() {
        //url기준으로 메인 users페이지 조절인지 search페이지 조절인지 구분
        var actionUrl = $(location).attr('href').split('/').pop().split('?');

        if ( 'users' == actionUrl[0]) {
          //검색 전 재정렬시 검색어 필드는 초기화
          //filter
          $('#filterFir').val('');
          $('#filterSec').val('');
          //text
          $('#searchFirWord').val('');
          $('#searchSecWord').val('');
        }
        
        $('#listOrderBy').attr('value',$('#orderBy option:selected').val());
        $('#listSort').attr('value',$('#sort option:selected').val());
        //기존 pageLimit은 form이달라 값에 포함이 안되어 따로 searchForm에 할당
        $('#searchPageLimit').attr('value',$('#pageLimit option:selected').val());

        ('users' == actionUrl[0]) ? $('#ordinaryForm').submit() : $('#searchForm').submit();
      }

      //유저 삭제
      function userDelete() {
        var deleteCheck = confirm('선택된 유저를 삭제하시겠습니까?');
        var userIndex = {};
        var indexValueCheck = false;

        //삭제 확인창에서 확인을 누른경우
        if (deleteCheck) { 
          //체크된 딜리트 박스들의 값들을 each반복문을 돌면서 배열에 삽입 ex)foreach
          $('.deleteBox:checked').each(function(index){
            userIndex[index] = $(this).val();
            //체크된 박스가 있을경우에 반복문이 돌아서 true가 들어감
            indexValueCheck = true;
          });
          //선택된 유저가 있을경우 해당유저 index번호를 보내서 soft delete처리
          if (indexValueCheck) {
            $.ajax({
              url:'/userDelete',
              type:'delete',
              data:{
                'userIndex' : userIndex,
                '_token' : $('input[name=_token]').val()
                },
              datatype:'json',
              success:function(result){
                if (result.deleteRow) {
                  alert(result.msg);
                  $(location).attr('href', '/users');
                } else {
                  alert('이미 휴면이거나 없는 유저입니다.');
                }
              },
              error:function(request,sts,error){
                alert(request.responseJSON.errors['userIndex']);
              }
            });
          } else { //선택된 유저가 없을 경우
            alert('유저를 선택해주세요.');
          }
        }
      }
    </script>
  </body>
</html>
