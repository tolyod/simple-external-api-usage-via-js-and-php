<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/lib/video-updates.php';

$movieXmlUrl = "./t.xml";
$xml = file_get_contents($movieXmlUrl);
$domEl = new SimpleXMLElement($xml);

$ovs = json_decode(
    json_encode($domEl->children('ovs', true))
);

$stats = json_decode(
    json_encode(@$domEl->xpath("//*[local-name()='stats']")[0]),
    true
);

var_dump($ovs, $stats);


