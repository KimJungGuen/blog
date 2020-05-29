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
       <option class='basicFilter' value="{{$searchData['filterFir']}}">{{$filters['filterFir']}}</option>
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
       <option class='basicFilter' value="{{$searchData['filterSec']}}">{{$filters['filterSec']}}</option>
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
       @switch($searchData['userStatus'])
         @case(3)
           <input id='searchUserAll' name='searchUserAll' value=3 type='checkbox' checked> 모든계정
           <input id='searchUserActive' name='searchUserActive' value=1 type='checkbox'> 사용계정
           <input id='searchUserSleep' name='searchUserSleep' value=2 type='checkbox'> 휴먼계정
           @break
         @case(2)
           <input id='searchUserAll' name='searchUserAll' value=3 type='checkbox'> 모든계정
           <input id='searchUserActive' name='searchUserActive' value=1 type='checkbox'> 사용계정
           <input id='searchUserSleep' name='searchUserSleep' value=2  checked type='checkbox'> 휴먼계정
           @break
         @case(1)
           <input id='searchUserAll' name='searchUserAll' value=3 type='checkbox'> 모든계정
           <input id='searchUserActive' name='searchUserActive' value=1 checked type='checkbox'> 사용계정
           <input id='searchUserSleep' name='searchUserSleep' value=2 type='checkbox'> 휴먼계정
           @break
       @endswitch
    </td>
   </tr>
   <tr>
    <td>성별</td>
    <td>
       @switch($filters['gender'])
         @case(3)
           <input type='radio' id='gender' name='gender' value=3 checked/>전체
           <input type='radio'  name='gender' value=1 />남
           <input type='radio'  name='gender' value=2 />여
           @break
         @case(1)
           <input type='radio' id='gender' name='gender' value=3 />전체
           <input type='radio'  name='gender' value=1  checked />남
           <input type='radio'  name='gender' value=2 />여
           @break
         @case(2)
           <input type='radio' id='gender' name='gender' value=3 />전체
           <input type='radio'  name='gender' value=1 />남
           <input type='radio'  name='gender' value=2 checked />여
           @break
       @endswitch
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
         <option value="{{$filters['sortValue']}}">{{$filters['sort']}}</option>
         <option value='index'>번호</option>
         <option value='accumulated'>적립금</option>
         <option value='age'>나이</option>
       </select>
       <select id='orderBy' name='orderBy'>
         <option value="{{$filters['orderByValue']}}">{{$filters['orderBy']}}</option>
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
