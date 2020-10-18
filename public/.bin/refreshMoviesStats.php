#!/usr/bin/php
<?php
/**
 * Video stats Counter
 *
 * PHP Version 7
 *
 * @category None
 * @package  None
 * @author   Anatoliy Poloz <anatoliy.poloz@gmail.com>
 * @license  https://www.freebsd.org/copyright/freebsd-license.html BSD
 * @link     http://localhost
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../lib/video-updates.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();
$dsn = "mysql:host=" . $_ENV['MYSQL_HOST']
    .";dbname=" . $_ENV['MYSQL_DATABASE']
    .";charset=UTF8";

$pdo = new PDO($dsn, $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);
$fluent = new \Envms\FluentPDO\Query($pdo);

updateDeletedVideosStats($pdo);

$vids = getRecentlyUnupdatedClips($pdo);
$results = array_map(
    function ($elem) use ($fluent) {
        $movie_id = $elem['video_id'];
        $url = "https://ok.ru/web-api/videoyandexfeed/" . $movie_id;
        $movieFullSiteUrl = "https://ok.ru/video/" . $movie_id;
        $xml = file_get_contents($url);
        $domEl = new SimpleXMLElement($xml);

        $ovs   = json_decode(json_encode($domEl->children('ovs', true)));
        $stats = json_decode(
            json_encode(@$domEl->xpath("//*[local-name()='stats']")[0]),
            true
        );
        $fullSiteStats = getStatsFromFullSiteVersion(
            $_ENV['COOKIES'],
            $movieFullSiteUrl
        );
        if ($fullSiteStats['authorName'] != false) {
            $ovs->stats = array_merge($stats, $fullSiteStats);
        } else {
            $ovs->stats = $stats;
        }
        $json_stats = json_encode($ovs);
        updateVideoStats($json_stats, $fluent);
        $result = getLastRegisteredVideoStat($json_stats, $fluent);
        echo "$movie_id done\n";
        return $result;
    },
    $vids
);
var_dump($results);
