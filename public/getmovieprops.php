<?php
/**
 * Video stats API
 *
 * PHP Version 7
 *
 * @category None
 * @package  None
 * @author   Anatoliy Poloz <anatoliy.poloz@gmail.com>
 * @license  https://www.freebsd.org/copyright/freebsd-license.html BSD
 * @link     http://localhost
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/lib/video-updates.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$dsn = "mysql:host=" . $_ENV['MYSQL_HOST']
    .";dbname=" . $_ENV['MYSQL_DATABASE']
    .";charset=UTF8";

$pdo = new PDO($dsn, $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);
$fluent = new \Envms\FluentPDO\Query($pdo);
$fullSiteStats = [];

$movie_id = intval($_GET["mvid"]);
$movieXmlUrl = "https://ok.ru/web-api/videoyandexfeed/" . $movie_id;
$movieFullSiteUrl = "https://ok.ru/video/" . $movie_id;
$xml = file_get_contents($movieXmlUrl);
$domEl = new SimpleXMLElement($xml);

$ovs = json_decode(
    json_encode($domEl->children('ovs', true))
);
$stats = json_decode(
    json_encode(@$domEl->xpath("//*[local-name()='stats']")[0]),
    true
);

$fullSiteStats['authorName'] = false;

if (in_array($ovs->status, ['published', 'blocked'])) {
    $fullSiteStats = getStatsFromFullSiteVersion(
        $_ENV['COOKIES'],
        $movieFullSiteUrl
    );
}

if ($fullSiteStats['authorName'] != false) {
    $ovs->stats = array_merge($stats, $fullSiteStats);
} else {
    $ovs->stats = $stats;
}
/* $ovs->fullSiteStats = $fullSiteStats; */
$json_stats = json_encode($ovs);
updateVideoStats($json_stats, $fluent);
$result = getLastRegisteredVideoStat($json_stats, $fluent);
header('Content-Type: application/json');
echo $result;
