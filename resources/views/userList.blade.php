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
        <div>
            <form id="searchForm" name="search" class="form" method="get" action="/users">
                <input id="searchPageLimit" name="searchPageLimit" value="5" type="hidden" /> 
                <table class="table table-bordered">
                    <thead class="text-center table-primary">
                        <tr><th colspan="2">검색</th></tr>
                    </thead>
                    <tbody >
                        <tr>
                            <td class="table-primary" rowspan="2">검색어</td>
                            <td>
                                <select id="filterFirst" name="filterFirst">
                                    <option value="">선택</option>
                                    <option value="user_id" @if ($searchData['filterFirst'] == 'user_id') selected @endif>ID</option>
                                    <option value="name" @if ($searchData['filterFirst'] == 'name') selected @endif>이름</option>
                                    <option value="email" @if ($searchData['filterFirst'] == 'email') selected @endif>email</option>
                                </select>
                                <input type="text" id="searchFirstWord" name="searchFirstWord" value="{{$searchData['searchFirstWord']}}" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select id="filterSecond" name="filterSecond">
                                    <option value="">선택</option>
                                    <option value="user_id" @if ($searchData['filterSecond'] == 'user_id') selected @endif>ID</option>
                                    <option value="name" @if ($searchData['filterSecond'] == 'name') selected @endif>이름</option>
                                    <option value="email" @if ($searchData['filterSecond'] == 'email') selected @endif>email</option>
                                </select>
                                <input type="text" id="searchSecondWord" name="searchSecondWord" value="{{$searchData['searchSecondWord']}}" />
                            </td>
                        </tr>
                        <tr>
                            <td class="table-primary">상태</td>
                            <td>
                                <input id="searchUserAll" name="searchUserAll" value="all" onclick="statusCheckBox();" type="checkbox" @if ($searchData['userStatus'] == 'all') checked @endif> 모든계정
                                <input id="searchUserActive" name="searchUserActive" value="active" onclick="statusCheckBox();" type="checkbox" @if ($searchData['userStatus'] == 'active') checked @elseif ($searchData['userStatus'] == 'all') checked @endif> 사용계정
                                <input id="searchUserSleep" name="searchUserSleep" value="sleep" onclick="statusCheckBox();" type="checkbox" @if ($searchData['userStatus'] == 'sleep') checked @elseif ($searchData['userStatus'] == 'all') checked @endif> 휴먼계정
                            </td>
                        </tr>
                        <tr>
                            <td class="table-primary">성별</td>
                            <td>
                                <input type="radio" id="gender" name="gender" value="all" @if ($searchData['gender'] == 'all') checked @endif/>전체
                                <input type="radio"  name="gender" value="M"  @if ($searchData['gender'] == 'M') checked @endif/>남
                                <input type="radio"  name="gender" value="F"  @if ($searchData['gender'] == 'F') checked @endif/>여
                            </td>
                        </tr>
                        <tr>
                            <td class="table-primary">가입일</td>
                            <td>
                                <input type="date" id="searchDateFirst" name="searchDateFirst" value="{{$searchData['searchDateFirst']}}"/>
                                <input type="date" id="searchDateSecond" name="searchDateSecond" value="{{$searchData['searchDateSecond']}}"/>
                            </td>
                        </tr>
                        <tr>
                        <td class="table-primary">정렬</td>
                        <td>
                            <select id="sortIndex" name="sortIndex" onchange="changePage();">
                                <option value="index" @if ($searchData['sortIndex'] == 'index') selected @endif>번호</option>
                                <option value="accumulated" @if ($searchData['sortIndex'] == 'accumulated') selected @endif>적립금</option>
                                <option value="age" @if ($searchData['sortIndex'] == 'age') selected @endif> 나이</option>
                            </select>
                                <select id="orderBy" name="orderBy" onchange="changePage();">
                                <option value="asc" @if ($searchData['orderBy'] == 'asc') selected @endif>오름차순</option>
                                <option value="desc" @if ($searchData['orderBy'] == 'desc') selected @endif>내림차순</option>
                            </select>
                        </td>
                        </tr>
                        <tr>
                            <td>
                                <button type="button" class="btn" onclick="searchUsers();">검색</button>
                                <button type="button" class="btn" onclick="searchDefault();">초기화</button>
                            <td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>

        <div>
            <p style="position:absolute; height:3.3%; top:40%; left:20%;" > 조회된 건 수 : {{$users->total()}}  </p>
            <input type="button" class="btn btn-primary" style="position:absolute; top:40%; left:87%; height:3.5%;" value="유저등록" onclick="location.href='/user'"/>
            <input type="button" class="btn btn-danger" style="position:absolute; top:40%; left: 92%; height: 3.5%;" value="유저탈퇴" onclick="userDelete()"/>

            <select style="position:absolute; height:3.3%; top:40%; left:98%;" id="pageLimit" name="pageLimit" onchange="changePage();">
                @for($i = 1 ; $i <= 10 ; $i++)
                    @if($i == $searchData['searchPageLimit'])
                        <option value={{$i}} selected>{{ $i }}</option>
                    @else
                        <option value={{$i}}>{{ $i }}</option>
                    @endif
                @endfor
            </select>

            <form id="ordinaryForm" name="ordinary" class="form" method="get" action="/users">
                <table class="table table-bordered text-center">
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
                            @foreach($users as $index => $user)
                                <tr name="user" onclick="userPwCheck('{{$user->index}}');" >
                                    <td onclick="event.cancelBubble=true"><input class="deleteBox" type="checkbox" value="{{$user->index}}"></td>
                                    <td>{{ $loop->iteration + $users->perPage() * ($users->currentPage() - 1) }}</td>
                                    <td>{{ $user->index }}</td>
                                    <td>{{ $userStatus[$index] }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->user_id }}</td>
                                    <td>{{ $user->gender }}</td>
                                    <td>{{ $user->age }}</td>
                                    <td>{{ $user->tel }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->accumulated }}</td>
                                    <td>{{ $user->join_date }}</td>
                                    <td>이동</td>
                                    <td>순서변경</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="table-dark" colspan="14">동록된 데이터가 없습니다.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <span style="position:relative; top:5%; left:43%;">{{$users->appends(request()->query())->links()}}</span>
            </form>
        </div>


        <div>
            <table class="table text-center table-bordered">
                <thead>
                    <tr>
                        <td class="table-primary" colspan="5">통계자료</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="table-primary" rowspan="2">계정별</td>
                        <td>총 계정</td>
                        <td>사용계정</td>
                        <td>휴면 계정</td>
                        <td>탈퇴계정</td>
                    </tr>
                    <tr>
                        <td>{{ $averageData['total'] }}명</td>
                        <td>{{ $averageData['active'] }}명</td>
                        <td>{{ $averageData['sleep'] }}명</td>
                        <td>0명</td>
                    </tr>
                    <tr>
                        <td class="table-primary" rowspan="2">성별</td>
                        <td>남자</td>
                        <td>여자</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>{{ $averageData['male'] }}명</td>
                        <td>{{ $averageData['female'] }}명</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="table-primary" rowspan="2">연령별</td> 
                        <td>19세 이하</td>
                        <td>20~30 대</td>
                        <td>40~50 대</td>
                        <td>60세 이상</td>
                    </tr>
                    <tr>
                        <td>{{ $averageData['children'] }}명</td>
                        <td>{{ $averageData['young'] }}명</td>
                        <td>{{ $averageData['adult'] }}명</td>
                        <td>{{ $averageData['old'] }}명</td>
                    </tr>
                    <tr>
                        <td class="table-primary" rowspan="2">적립금별</td>
                        <td>1,000원 미만</td>
                        <td>1,000 ~ 9,999원</td>
                        <td>10,000원 이상</td>
                        <td>적립금 총액</td>
                    </tr>
                    <tr>
                        <td>{{ $averageData['minAccumulated'] }}명</td>
                        <td>{{ $averageData['middleAccumulated'] }}명</td>
                        <td>{{ $averageData['hightAccumulated'] }}명</td>
                        <td>{{ $averageData['totalAccumulated'] }}원</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @csrf
        <script>

            //@brief    유저 상태 체크박스 조작
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

            //@brief    유저 비밀번호 확인
            //@param    int index : 클릭한 유저의 고유번호
            function userPwCheck(index) {
                var userPw = prompt('비밀번호를 입력해주세요');
                //유저 비밀번호 입력했을 때 전송
                if (userPw) {
                    var userIndex = index;
                    $.ajax({
                        url:'/userPwCheck/' + index,
                        type:'post',
                        data:{
                            'userIndex':userIndex,
                            'userPw':userPw,
                            '_token':$('input[name=_token]').val()
                        },
                        datatype:'json',
                        success:function(result){
                            if (result.pwCheck) {
                                alert('비밀번호가 일치합니다.');
                                $(location).attr('href', '/userUpdate/' + userIndex);
                                ret
                            } else {
                                alert('비밀번호가 틀렸습니다.');

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

            //@brief    유저 검색
            function searchUsers() {
            var specialCharacter = /[~!@#$%^&*()_+-=\[\]\\{}\|;:'"<>/?]/g;
                
                //첫 번쨰 검색어 특수문자 확인
                var searchFirstWord = $('#searchFirstWord').val();
                var textCheckFirst = searchFirstWord.search(specialCharacter);

                //두 번째 검색어 특수문자 확인 
                var searchSecondWord = $('#searchSecondWord').val();
                var textCheckSecond = searchSecondWord.search(specialCharacter);

                //첫 번째, 두 번째 필터
                var filterFirst = $('#filterFirst option:selected').val();
                var filterSecond = $('#filterSecond option:selected').val();

                //검색날짜 시작일 종료일
                var searchDateAfter = $('#searchDateFirst').val();
                var searchDateBefore = $('#searchDateSecond').val();

                //유저 상태 체크박스
                var userAll = $('#searchUserAll').prop('checked');
                var userActive = $('#searchUserActive').prop('checked');
                var userSleep = $('#searchUserSleep').prop('checked');
                
                //필터와 검색어 확인
                if (filterFirst == false && searchFirstWord) {
                    alert('첫번째 검색 필터를 선택해주세요');
                    return false;
                } else if (filterFirst && searchFirstWord == '') {
                    alert('첫번째 검색어를 입력해주세요.');
                    return false;
                }

                if (filterSecond == false && searchSecondWord) {
                    alert('두번쨰 검색 필터를 선택해주세요');
                    return false;
                } else if (filterSecond && searchSecondWord == '') {
                    alert('두번쨰 검색어를 입력해주세요.');
                    return false;
                }

                //유저 상태 체크 박스 확인 
                if (userAll == false && userActive == false && userSleep == false) {
                    alert('검색할 계정 상태를 최소 하나는 골라주세요.');
                    return false;
                }

                //시작일이나 종료일 확인
                if (searchDateAfter == '' || searchDateBefore == '') {
                    alert('시작일이나 종료일이 안정했졌습니다.');
                    return false;
                }

                //시작일 종료일 연산
                var dateAfter = searchDateAfter.split('-');
                var dateBefore = searchDateBefore.split('-');
                var afterYear = dateAfter[0];
                var beforeYear = dateBefore[0];
                var afterMonth = dateAfter[1];
                var beforeMonth = dateBefore[1];
                var afterDay = dateAfter[2];
                var beforeDay = dateBefore[2];
                var dateAlert = function() {alert('시작일을 종료일보다 앞 선 날짜로 설정해주세요.')};
                var yearCheck = (beforeYear - afterYear < 0) ? false : true;
                var monthCheck = (beforeMonth - afterMonth < 0) ? false : true;
                var dayCheck = (beforeDay - afterDay < 0) ? false : true;
                if (yearCheck) {
                    if (monthCheck) {
                        if (dayCheck == false && beforeMonth - afterMonth == 0 && beforeYear - afterYear == 0) {
                            dateAlert();
                            return false;
                        }
                    } else if (beforeYear - afterYear == 0) {
                        dateAlert()
                        return false;
                    }
                } else { 
                    dateAlert()
                    return false;
                }

                //pageLimit 값 전송
                $('#searchPageLimit').attr('value',$('#pageLimit option:selected').val());
                if (textCheckFirst == -1 && textCheckSecond == -1) {
                   $('#searchForm').submit();
                } else {
                    alert('검색어를 재대로 입력해주세요.');
                    return false;
                }
            }

            //@brief    유저 조회 조건 초기화
            function searchDefault() {
                var today = new Date();
                
                var year = today.getFullYear();

                //시작일 월 설정
                var monthAfter = (today.getMonth()+1 < 10) ? '0' + (today.getMonth()) : (today.getMonth()+1);

                //1월일 경우 작년으로 바꾸고 12월로 해준다.
                if (today.getMonth() == 0) {
                    year = today.getFullYear()-1;
                    monthAfter = today.getMonth()+12;
                }

                //종료일 월 설정
                var monthBefore = (today.getMonth()+1 < 10) ? '0' + (today.getMonth()+1) : (today.getMonth()+1);
                var day = (today.getDate() < 10) ? '0' + today.getDate() : today.getDate();

                //시작일 종료일
                var dateDefaultAfter = year + '-' + monthAfter + '-' + day;
                var dateDefaultBefore = year + '-' + monthBefore + '-' + day;
                
                //radio 성별
                $('#gender').prop('checked', true);

                //text 검색어
                $('#searchFirstWord').val('');
                $('#searchSecondWord').val('');

                //date 시작일, 종료일
                $('#searchDateFirst').val(dateDefaultAfter);
                $('#searchDateSecond').val(dateDefaultBefore);

                //check box 유저 상태
                $('#searchUserAll').prop('checked', false);
                $('#searchUserActive').prop('checked', true);;
                $('#searchUserSleep').prop('checked', false);;

                //serlect box 검색 필터, 정렬 필터
                $('.basicFilter').attr('value', '');
                $('.basicFilter').text('선택');
                $('#filterFirst').val('');
                $('#filterSecond').val('');
                $('#sortIndex').val('index');
                $('#orderBy').val('asc');
            }

            //@brief    페이지 수정
            function changePage() {
                var urlQueryString = $(location).attr('href').split('?').pop();
                var getValue = urlQueryString.split('&');
                var searchQueryString = '';

                for (var index = 1; index <= getValue.length-3; index++) {
                    console.log(getValue[index]);
                    searchQueryString += '&' + getValue[index];
                }

                var sortIndex = $('#sortIndex option:selected').val();
                var orderBy = $('#orderBy option:selected').val();
                $('#searchPageLimit').attr('value',$('#pageLimit option:selected').val());
                var queryString = 'searchPageLimit=' + $('#searchPageLimit').val() + '&sortIndex=' + sortIndex + '&orderBy=' + orderBy;
                searchQueryString = 'searchPageLimit=' + $('#searchPageLimit').val() + searchQueryString + '&sortIndex=' + sortIndex + '&orderBy=' + orderBy;

                //현재 queryString에 따라 전송 데이터 구분 
                if (getValue.length > 9) {
                    $(location).attr('href', '/users?' + searchQueryString );
                } else {
                    $(location).attr('href', '/users?' + queryString);
                }
            }

            //@brief    유저 삭제
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
                                'userIndex':userIndex,
                                '_token':$('input[name=_token]').val()
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