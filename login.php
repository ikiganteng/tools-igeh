<?php
require 'mainconfig.php';
date_default_timezone_set("Asia/Jakarta");
function read ($length='255') 
{ 
   if (!isset ($GLOBALS['StdinPointer'])) 
   { 
      $GLOBALS['StdinPointer'] = fopen ("php://stdin","r"); 
   } 
   $line = fgets ($GLOBALS['StdinPointer'],$length); 
   return trim ($line); 
} 

function add($username, $password){
   $postq = json_encode([
		'phone_id' => generateUUID(true),
		'_csrftoken' => get_csrftoken(),
		'username' => $username,
		'guid' => generateUUID(true),
		'device_id' => generateUUID(true),
		'password' => $password,
		'login_attempt_count' => 0
	]);
	$a = request(1, generate_useragent(), 'accounts/login/', 0, hook($postq));
	$header = $a[0];
	$a = json_decode($a[1]);
	if($a->status<>'ok'){
    	$msg = $a->message;
		$array = json_encode(['result' => false, 'msg' => $msg]);
		}else{
		preg_match_all('%Set-Cookie: (.*?);%',$header,$d);$cookies = '';
		for($o=0;$o<count($d[0]);$o++)$cookies.=$d[1][$o].";";
	    $ua = generate_useragent();
		$array = json_encode(['result' => true, 'cookie' => $cookies, 'ua' => generate_useragent()]);
    }
		return $array;
}
echo "[>] Username: ";
$username = read();
echo "[>] Password: ";
$password = read();
    $aib = add($username, $password);
    $go = json_decode($aib);
    if($go->result<>true){
	echo $go->msg;
	exit();
	}else
echo "ua : ".$go->ua;
echo "cookie : ".$go->cookie;
