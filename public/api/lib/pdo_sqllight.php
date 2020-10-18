<?php

$conf_min_members=10000;
$conf_max_members=100000000;
/* */
function do_log($logtext) {
	global $user_name,$bot_index;
	$home_dir = "./log";
	$real_log_dir = $home_dir ;
	
	if (!is_dir($real_log_dir)) {
		mkdir($real_log_dir, 0777);
	} else {
		chmod($real_log_dir, 0777);
	}
	

	$real_log_file = $real_log_dir . "/".$bot_index."_".date('Y-m-d') . '.log';
	$str_to_log	   = date('Y-m-d H:i:s ') . '[' . $user_name . '] ' . mb_convert_encoding(trim($logtext),"CP866","UTF-8") .PHP_EOL;
	echo $str_to_log;
	$h = fopen($real_log_file , 'ab');
	fwrite($h, $str_to_log);
	fclose($h);
}


function init_sql_db() {

  global $DBH;

   try {  
      # SQLite  
      //$DBH = new PDO("sqlite:db/database.db");  
      //Flight::register('db', 'PDO', array('pgsql:host=localhost;dbname=ok_web_2;','root','weichuoraecah7eZiib7Ie'));

      # pgsql
      $DBH =  Flight::db();
   } catch(PDOException $e) {  
       echo $e->getMessage();  
   }

}


function add_groups_to_db ($ar) {
  $DBH =  Flight::db();
  global $conf_min_members,$conf_max_members;

  //var_dump($ar,count($ar));
  /*
  $table_create=" CREATE TABLE OKGRPOUPS(
     NAME           TEXT    NOT NULL,
     CNT            INT     NOT NULL,
     URL        	  VARCHAR(255) UNIQUE,
     CHECKED        BOOLEAN,
     USABLE	  BOOLEAN)";

  $STH = $DBH->prepare($table_create);  
  $STH->execute();   


  exit;
  */
  //var_dump(count($ar));

   $grp_res_added_cnt=0;
   foreach ($ar as $a) {
   
      $data[0]=$a["name"];
      $data[1]=$a["cnt"];
      $data[2]=$a["href"];
   
      if ($a["cnt"] < $conf_min_members && $a["cnt"] > $conf_max_members) {
		  
         $data[3]=1; // it is not usable by count less then 10k, and then it marked as cheked;
		 
      } else {
		  
         $data[3]=0; // count greater 10k, then group need to chek on can posting, and popularyty;
		 
      }
	  
      $data[4]=0; // by default group is not usable;

      //var_dump($data);
	  
      $STH = $DBH->prepare("INSERT INTO public.OKGRPOUPS (NAME, CNT, URL, CHECKED, USABLE) values (?, ?, ?, ?, ?)");  
      $res=$STH->execute($data);
	  
	  if ($res==true) {
		  $grp_res_added_cnt++;
	  }
   }
   
   //do_log(" Groups count added to DB ".$grp_res_added_cnt);
   return $grp_res_added_cnt;

}



function get_groups_from_db($limit=400) {
$DBH =  Flight::db();
$r=false;

$STH = $DBH->query("SELECT URL, CHECKED, CNT from OKGRPOUPS where CHECKED=0 limit ".$limit);

//var_dump($STH,$DBH);

$STH->setFetchMode(PDO::FETCH_ASSOC);  

      while($row = $STH->fetch()) {  
            $r[]=$row;
      }
	  //var_dump($r);
	  //shuffle($r);
return $r;
}

function get_one_group() {
	$ar=get_groups_from_db();
	//$key=array_rand($ar);
	$r=$ar[mt_rand(0, count($ar) - 1)];
	var_dump($r);
	return $r;
}

function get_checked_groups_count(){
	$DBH =  Flight::db();
	$r=false;

	$STH = $DBH->query("SELECT count(*) as n from public.OKGRPOUPS where CHECKED=1");

$STH->setFetchMode(PDO::FETCH_ASSOC);  

$row = $STH->fetch();
$r   = $row["n"];
return $r;
}

function get_usable_groups_count(){
	$DBH =  Flight::db();
	$r=false;

	$STH = $DBH->query("SELECT count(*) as n from public.OKGRPOUPS where USABLE=1");

$STH->setFetchMode(PDO::FETCH_ASSOC);  

$row = $STH->fetch();
$r   = $row["n"];

return $r;
}

function export_usable_groups() {
	$DBH =  Flight::db();
	$r=false;

	$STH = $DBH->query("SELECT URL,NAME from public.OKGRPOUPS where USABLE=1");

    while($row = $STH->fetch()) {  
            $r.="\"".$row["url"]."\",\"".$row["name"]."\" ".PHP_EOL;
      }

return $r;
}

function get_unic_posters_groups_count(){
	$DBH =  Flight::db();
	$r=false;

	$STH = $DBH->query("SELECT count(*) as n from public.OKGRPOUPS where USABLE=0 and uniq_postrers >5");
	//var_dump($STH, $DBH);
	$STH->setFetchMode(PDO::FETCH_ASSOC);  

	$row = $STH->fetch();
	$r   = $row["n"];

return $r;
}

function export_unic_posters_groups() {
	$DBH =  Flight::db();
	$r=false;

	$STH = $DBH->query("SELECT URL,NAME from public.OKGRPOUPS where USABLE=0 and uniq_postrers >5");

    while($row = $STH->fetch()) {  
            $r.="\"".$row["url"]."\",\"".$row["name"]."\" ".PHP_EOL;
      }

return $r;
}

function export_checked_groups() {
	$DBH =  Flight::db();
	$r=false;

	$STH = $DBH->query("SELECT URL,NAME from public.OKGRPOUPS where CHECKED=1");

    while($row = $STH->fetch()) {  
            $r.="\"".$row["url"]."\",\"".$row["name"]."\" ".PHP_EOL;
      }

return $r;
}

function export_unchecked_groups() {
	$DBH =  Flight::db();
	$r=false;

	$STH = $DBH->query("SELECT URL,NAME,CNT from public.OKGRPOUPS where CHECKED=0");

    while($row = $STH->fetch()) {  
            $r.="\"".$row["url"]."\",\"".$row["name"]."\",\"".$row["cnt"]."\" ".PHP_EOL;
      }

return $r;
}

function get_total_groups_count(){
	$DBH =  Flight::db();
	$r=false;

	$STH = $DBH->query("SELECT count(*) as n from public.OKGRPOUPS");

$STH->setFetchMode(PDO::FETCH_ASSOC);  

$row = $STH->fetch();
$r   = $row["n"];
return $r;
}
//get_checked_groups_count get_usable_groups_count get_total_groups_count
function set_group_checked ($url) {
      $DBH =  Flight::db();

      $data[0]=$url;
      $STH = $DBH->prepare("UPDATE public.OKGRPOUPS SET CHECKED=1 where URL=?");  
      $STH->execute($data);
      return true;

}

function reSet_All_groups(){
    $DBH =  Flight::db();


    $STH = $DBH->prepare("UPDATE public.OKGRPOUPS SET CHECKED=0, USABLE=0");  
    $STH->execute();

}

function delete_All_groups(){
	$DBH =  Flight::db();


    $STH = $DBH->prepare("delete from public.OKGRPOUPS");  
    $STH->execute();

}

function set_unUsable_as_unCheked () {
$DBH =  Flight::db();

      //$data[0]=$url;
      $STH = $DBH->prepare("UPDATE public.OKGRPOUPS SET CHECKED=0 where USABLE=0");  
      $STH->execute();

}
	


function set_group_Usable ($url) {
$DBH =  Flight::db();

      $data[0]=$url;
      $STH = $DBH->prepare("UPDATE public.OKGRPOUPS SET USABLE=1 where URL=?");  
      $STH->execute($data);

}


function set_group_unUsable ($url) {
$DBH =  Flight::db();

      $data[0]=$url;
      $STH = $DBH->prepare("UPDATE public.OKGRPOUPS SET USABLE=0 where URL=?");  
      $STH->execute($data);

}

function set_group_Cheked ($url) {
$DBH =  Flight::db();

      $data[0]=$url;
      $STH = $DBH->prepare("UPDATE public.OKGRPOUPS SET CHECKED=1 where URL=?");  
      $STH->execute($data);

}


function set_group_unCheked ($url) {
$DBH =  Flight::db();

      $data[0]=$url;
      $STH = $DBH->prepare("UPDATE public.OKGRPOUPS SET CHECKED=0 where URL=?");  
      $STH->execute($data);

}
function update_video_stats($url,$sa) {
      $DBH =  Flight::db();

      $data[0]=$sa["sum"];
      $data[1]=$sa["ave"];
      $data[2]=$sa["max"];
      $data[3]=$sa["vtotal"];
      $data[4]=$url;
      
      $STH = $DBH->prepare("UPDATE public.OKGRPOUPS SET vws_sum=?, vws_ave=?, vws_max=?, videos_count=? where URL=?");
      $STH->execute($data);

}

/*
update_video_stats($ga["URL"],
                  array("sum"=>$vws_sum,
                  "ave"=>$vws_ave,
                  "max"=>$vws_max,
                  "vtotal"=>$videos_count));
*/

function update_board_stats($url,$sa) {

      $DBH =  Flight::db();

      $data[0]=$sa["posters_count"];
      $data[1]=$sa["uniq_postrers"];
      $data[2]=$sa["likes_ave"];
      $data[3]=$sa["likes_max"];
      $data[4]=$sa["repost_ave"];
      $data[5]=$sa["repost_max"];
      $data[6]=$sa["comments_ave"];
      $data[7]=$sa["comments_max"];
      $data[8]=$url;

      $STH = $DBH->prepare("UPDATE public.OKGRPOUPS SET stats_n_posters_count=?, uniq_postrers=?, 
                                                 stats_n_likes_ave=?, 	  stats_n_likes_max=?,
                                                 stats_n_repost_ave=?,    stats_n_repost_max=?,
                                                 stats_n_comments_ave=?,  stats_n_comments_max=?  where URL=?");
      $STH->execute($data);

}

/* 
set_group_checked set_group_Usable set_group_unUsable
init_sql_db();
add_groups_to_db ($ar);      
var_dump(get_groups_from_db());

//update_video_stats($ga["URL"],$vws_sum,$vws_ave,$vws_max,$videos_count)
//$stats_n_posters_count,$uniq_postrers,
//$stats_n_likes_ave,$stats_n_likes_max,
//$stats_n_repost_ave,$stats_n_repost_max,
//$stats_n_comments_ave,$stats_n_comments_max
*/

?>
