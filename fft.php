<?php
error_reporting(0);
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

function chance($cookie, $ua, $target, $jumlah){
    $random="Fllbck dong kak | Udh aku fllw ya,follbcknya | jgn lupa difollbck | Followback ya | followbck ya | fllwback ya | fllwbck ya | follback ya | fllback ya | follbck ya | fllbck ya | ditunggu followback nya | ditunggu followbck nya | ditunggu fllwbck nya | ditunggu fllwback nya | ditunggu follback nya | ditunggu follbck nya | ditunggu fllback nya | ditunggu fllbck nya | hai followback yay | hai followbck yay | hai fllwbck yay | hai fllwback yay | followbalik yay | follow balik ya";
    $random = explode("|", $random);
    $terpilih = $random[mt_rand(0, count($random)-1)];
    $cek = request(1, $ua, 'users/'.$target.'/usernameinfo/',$cookie);
    $cek = json_decode($cek[1]);
    $id = $cek->user->pk;
    $jumlah = ($jumlah=='sampehabis') ? $getinfo->user->following_count-1 : $jumlah-1;
    $c = 0;
    $listids = array();
		do{
			$parameters = ($c>0) ? '?max_id='.$c : '';
			$req = request(1, $ua, 'friendships/'.$id.'/followers/'.$parameters, $cookie);
			$req = json_decode($req[1]);
			$iki = $req->users;
			for($i=0;$i<count($iki);$i++):
				if(count($listids)<=$jumlah)
					$listids[count($listids)] = $req->users[$i]->pk;
					$namanya[count($namanya)] = $req->users[$i]->username;
					$private[count($private)] = $req->users[$i]->is_private;

			endfor;
			$c = (isset($req->next_max_id)) ? $req->next_max_id : 0;
		}while(count($listids)<=$jumlah);
		for($i=0;$i<count($listids);$i++):
			if($private[$i] == "1"){
			    $iki = "@".$namanya[$i]." [<font color=green>></font>] Akunnya di Private <font color=blue>BGSD</font> [<font color=yellow>SKIP</font>] <br>";
			}else{
			    $show = request(1, $ua, 'friendships/show/'.$listids[$i].'/', $cookies);
                    $getshow = json_decode($show[1]);
                    if($getshow->following == false){
			        $getmedia = request(1, $ua, 'feed/user/'.$listids[$i].'/', $cookies);
                    $getmedia = json_decode($getmedia[1]);
                    if($getmedia->num_results > 0){
                    $media_id = $getmedia->items[0]->id;
					$cross = request(1, $ua, 'friendships/create/'.$listids[$i].'/', $cookie, hook('{"user_id":"'.$listids[$i].'"}'));
                    $cross = json_decode($cross[1]);
                    if($cross->status == "ok"){
                    $follow = "<font color=green>Follow</font>";} else {$follow = "<font color=red>Follow</font>";}
                    $komen = request(1, $ua, 'media/'.$media_id.'/comment/', $cookies, hook('{"comment_text":"'.$terpilih.'"}'));
                    $komen = json_decode($komen[1]);
                    if($komen->status == "ok"){
                    $hasilkomen = "<font color=green>Comment</font>";} else {$hasilkomen = "<font color=red>Comment</font>";}
                    $like = request(1, $ua, 'media/'.$media_id.'/like/', $cookies, hook('{"media_id":"'.$media_id.'"}'));
                    $like = json_decode($like[1]);
                    if($like->status == "ok"){
                    $hasillike = "<font color=green>Like</font>";} else {$hasillike = "<font color=red>Like</font>";}
                    $iki = "@".$namanya[$i]." [<font color=green>></font>] ".$follow.",".$hasilkomen.",".$hasillike." [".$terpilih."] <br>";
						sleep(10);
                    }else{
			        $iki = "@".$namanya[$i]." [<font color=green>></font>] Timeline Kosong <font color=blue>BGSD</font> [<font color=yellow>SKIP</font>] <br>";
                    }
                    }else{
                    $iki = "@".$namanya[$i]." [<font color=green>></font>] Sudah Di Follow <br>";
                    }
                    }
					return $iki;
		endfor;
}
echo "[>] Username: ";
$username = read();
echo "[>] Password: ";
$password = read();
echo "[>] Target: ";
$target = read();
echo "[>] Jumlah: ";
$jumlah = read();
echo "[>] Sleep: ";
$sleep = read();
    $aib = add($username, $password);
    $go = json_decode($aib);
    if($go->result<>true){
	echo $go->msg;
	exit();
	}else
for ($x = 0; $x <= $jumlah; $x++){
	$ib = chance($go->cookie, $go->ua, $target, 3);
    echo ' '.date("H:i:s").""  .$ib. "\n";
	sleep($sleep);
}
