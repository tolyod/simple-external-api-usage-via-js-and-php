<?php

$movie_id=intval($_GET["mvid"]);
$url="https://ok.ru/web-api/videoyandexfeed/".$movie_id;
$xml=file_get_contents($url);
$domEl = new SimpleXMLElement($xml);

$ovs   = json_decode(json_encode($domEl->children('ovs', true)));
$stats = json_decode(json_encode($domEl->xpath("//*[local-name()='stats']")[0]));
$ovs->stats=$stats;

header('Content-Type: application/json');
echo json_encode($ovs);

?>