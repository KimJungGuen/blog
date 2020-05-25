
public function pageng(Request $request,int $dbRow)
{
  $pageLimit = $request->input('pageLimit');
  $page = $request->input('page', '0');
  $row = $dbRow;

  if ($pageLimit > 0 && $row % $pageLimit == 0) {
    $pageSet = $row / $pageLimit;
  } elseif ($pageLimit == 0) {
    $pageSet = 0;
  } else {
    $pageSet = $row/$pageLimit+1;
  }

  return array(
    'row'=>$row,
    'pageLimit'=>$pageLimit,
    'pageSet'=>$pageSet,
    'page'=>$page
  );
}


//

$row = $model->rowCount();
$pagenation = $this->pageng($request, $row);
$page = $pagenation['page'];
$skipRow = 0;
$pageLimit = $request->input('pageLimit');

$model->page();


if ($page > 1) {
    for ($i=0;$i<$page-1;$i++) {
      $skipRow += $pageLimit;
    }
}
//$users = $user->getList($pageLimit,$page,$row,$skipRow);

if ($pageLimit != 0 && ($row-$skipRow) >= $pageLimit) {

  $users = $model->getUserpage($pageLimit, $skipRow);

} elseif($pageLimit != 0 && ($row-$skipRow) < $pageLimit) {

  $users = $model->getUserpageMin($skipRow, $row);

  for($i=0;$i<$row - $skipRow;$i++) {
    $users[$i]->join_date = str::substr($users[$i]->join_date, 0, 10);
  }

} elseif($pageLimit == 0) {

  $users = $model->getUserAll();
}

if (($row-$skipRow) >= $pageLimit) {
  for($i=0;$i<$pageLimit;$i++) {
    $users[$i]->join_date = str::substr($users[$i]->join_date, 0, 10);
  }














  $model = new NoticeBoard();
  $users = $model->getList(10);


  $pageLimit = $request->input('pageLimit');

  if($pageLimit != null){
  $page = $model->pageDivision($pageLimit);
  $page->perPage();
  $page->
  $users = $model->getList();
  }




  '_token' : $('input[name=_token]').val(),
        'userId' : $('#userID').val()





        $model = new NoticeBoard();
        $url = Str::of(url()->current());

        // basename /를 기준으로 가장 마지막으로 /를 만난 문자열 반환 /를 포함하지않을경우 전체반환
        //매개변수는 제거할 문자열
        $userIndex = $url->basename();
        dd((int)$userIndex->__toString());
        $user = $model->getUser($userIndex);




  public function getList(int $limit, int $row) {
    $users = $this->offset($limit)->limit($row)->get();
    return $users;
  }

  public function test($request){
    $this->insert([
      'name' => $request->input('name')
    ]);
  }
  public function pageDivision(int $pageLimit) {
    $page = $this->paginate($pageLimit);
    //dd($page ->nextPageUrl());

    return $page;
  }
