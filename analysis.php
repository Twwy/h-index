<?php
//http://weibo.com/1667401345/fans?page=60


require('database.php');
$db = new database ;

if(empty($_POST)){
	if(empty($_GET)) exit('no');
	hindex($_GET['id']);
	exit();
}

//hindex(1);

function add($name, $uid, $follow, $fans, $weibo){
	global $db;
	$insertArray = array(
		'uid' => $uid,
		'name' => $name,
		'weibo' => $weibo,
		'follow' => $follow,
		'fans' => $fans
	);
	$db->insert('user', $insertArray);
	return $db->insertId();
}


function fans($user_id, $fans_id){
	global $db;
	$sql = "SELECT fans_id FROM fans WHERE user_id = {$user_id} AND fan_user_id = {$fans_id}";
	$result = $db->query($sql, 'row');
	if(!empty($result)) return;
	$insertArray = array(
		'user_id' => $user_id,
		'fan_user_id' => $fans_id
	);
	$db->insert('fans', $insertArray);
}

function id($uid){
	global $db;
	$sql = "SELECT user_id FROM user WHERE uid = {$uid}";
	return $db->query($sql, 'row');	
}

function hindex($user_id){
	global $db;
	$index = 0;
	$fans = 1;

	$sql = "SELECT count(fans_id) FROM fans WHERE user_id = {$user_id}";
	$result = $db->query($sql, 'row');
	$total = $result['count(fans_id)'];

	while ($index != $fans && $fans >= $index){
		$index++;
		$sql = "SELECT count(user.user_id) FROM user,fans WHERE fans.user_id = {$user_id} AND user.user_id = fans.fan_user_id AND user.fans > {$index}";
		$result = $db->query($sql, 'row');
		$fans = $result['count(user.user_id)'];	
		echo 'fans:'.$fans.'--index:'.$index.'<br />';
	}

	$db->update('user', 
		array('h_index' => $index, 
			'has_index' => $total
		), "user_id = {$user_id}");

	echo 'H-index:'.$index;
}


$uself = json_decode($_POST['uself'], true);

$users = json_decode($_POST['users'], true);

$result = id($uself['id']);

if(empty($result)){
	$id = add($uself['name'], $uself['id'], $uself['follow'], $uself['fans'], $uself['weibo']);
}else{
	$id = $result['user_id'];
}

foreach ($users as $key => $value) {
	$result = id($value['id']);
	if(empty($result)){
		$fans_id = add($value['name'], $value['id'], $value['follow'], $value['fans'], $value['weibo']);
	}else{
		$fans_id = $result['user_id'];
	}
	fans($id, $fans_id);
}


/*hindex($id);*/






/*print_r($uself);
print_r($users);
*/

?>