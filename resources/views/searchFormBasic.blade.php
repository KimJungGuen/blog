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
