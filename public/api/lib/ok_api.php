<?php

function get_auth_keys() {
    return ["app_key"=>"CBADLJKMEBABABABA",
        "app_sec_key"=>"34F936A01CB6505CB290DFB8",
        "uid"=>"577641271999"];

}

function get_group_info($id) {
    $keys = get_auth_keys();
    $req="application_key=".$keys["app_key"]."fields=*format=jsonmethod=group.getInfouids=".$id.$keys["app_sec_key"];
    //echo $req."\n";
    $req_md5=md5($req);
    $url_req="https://api.ok.ru/fb.do?application_key=".$keys["app_key"]."&fields=*&format=json&method=group.getInfo&uids=".$id."&sig=".$req_md5;
    
    $result = json_decode($json_resp=file_get_contents($url_req, false),true);
    //var_dump($result);
    file_put_contents("dump/grp_info_".$id.".json",$json_resp);

    return $result;
}

function get_group_videos_count($id) {
    $keys = get_auth_keys();
    $req="application_key=".$keys["app_key"]."counterTypes=videosformat=jsongroup_id=".$id."method=group.getCountersuid=".$keys["uid"].$keys["app_sec_key"];
    
    $req_md5=md5($req);
    $url_req="https://api.ok.ru/fb.do?application_key=".$keys["app_key"]."&counterTypes=videos&format=json&group_id=".$id."&method=group.getCounters&uid=".$keys["uid"]."&sig=".$req_md5;
    $result = json_decode($json_resp=file_get_contents($url_req, false),true);
    //var_dump($result);
    file_put_contents("dump/grp_video_cnt_".$id.".json",$json_resp);

    return $result;
}

      //var_dump(get_group_info("54014104633362"));
      //$DBH = new PDO("sqlite:db/database.db");  
      //Flight::register('db', 'PDO', array('pgsql:host=localhost;dbname=ok_web_2;','root','weichuoraecah7eZiib7Ie'));

      $DBH = new PDO("pgsql:host=localhost;dbname=ok_web_2;user=root;password=weichuoraecah7eZiib7Ie");
      $STH = $DBH->query("SELECT URL from OKGRPOUPS where CHECKED=0");

      $STH->setFetchMode(PDO::FETCH_ASSOC);  

      while($row = $STH->fetch()) {  
            $r[]=$row["url"];
      }
      
      
      $ids=array_map(function ($e) {
        if(preg_match("/.*st.groupId=([0-9]{1,})/xi",$e,$m)) {
          return $m[1];
        }        
      }, $r);
      
      $infs=array_map(function ($id) use ($DBH) {
      
        $gr_info=get_group_info($id)[0];
        $gr_video_count=get_group_videos_count($id);
        
        if($gr_info["video_tab_hidden"] == true || intval($gr_video_count["counters"]["videos"]) <10) {
        
          $data[0]="%dk?st.cmd=altGroupMain&st.groupId=".$gr_info["uid"];
          $STH = $DBH->prepare("update public.OKGRPOUPS set checked=1 where url like ?");
          $STH->execute($data);

        }

      }, $ids);
      
?>