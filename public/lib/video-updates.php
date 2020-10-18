<?php
/**
 * Video stats update helper functions
 *
 * PHP Version 7
 *
 * @category Library
 * @package  OkVideoStats
 * @author   Anatoliy Poloz <anatoliy.poloz@gmail.com>
 * @license  https://www.freebsd.org/copyright/freebsd-license.html BSD
 * @link     http://localhost
 */

/**
 * Insert last state to videos_readings table
 *
 * @param string $data   is a json string from outer fetch function
 * @param object $fluent fluent state object
 *
 * @return array [$data, $videoInsertStatus, $insStatus] returnin same input
 * string if no exceptions video insertion status and record insertion status
 */
function updateVideoStats($data, $fluent)
{
    $obj = json_decode($data, true);
    $obj['login'] = @mb_strlen($obj['login']) ? $obj['login'] : '';
    $id = $obj['content_id'];
    $isIdExists = $fluent->from('videos')->where('id', $id)->limit(1)->fetch();
    if (!$isIdExists) {
        $video_title = mb_substr($obj['title'], 0, 255, 'utf-8') ?? '';
        $videos_values = [
            'id' => $id,
            'video_title' => $video_title,
            'description' => ''
        ];
        $videoInsert = $fluent->insertInto('videos')->values($videos_values);
        $videoInsertStatus = $videoInsert->execute();
    }
    $videos_readings_values = [
        'video_id' => $id,
        'reading_value' => json_encode($obj)
    ];
    $ins = $fluent->insertInto('videos_readings')->values($videos_readings_values);
    $insStatus = $ins->execute();
    return [$data, ($videoInsertStatus ?? 'ok'), $insStatus];
}

/**
 * Gets last state to videos_readings table
 *
 * @param string $data   is a json string from outer fetch function
 * @param object $fluent fluent state object
 *
 * @return string $data last data in table
 */
function getLastRegisteredVideoStat($data, $fluent)
{
    $obj = json_decode($data, true);
    $id = $obj['content_id'];
    $query = $fluent->from('videos_readings')
        ->select(null)
        ->select(['id','reading_value'])
        ->where('video_id', $id)
        ->where('not status_virtual', 'deleted')
        ->orderBy('date_created desc')
        ->limit(1);
    $queryResult = $query->fetch('reading_value');
    $json = (!$queryResult) ? $data : $queryResult;
    $resultObj = json_decode($json, true);

    if ($obj['status'] === 'deleted') {
        $resultObj['status'] = 'deleted';
    }

    return json_encode($resultObj);
}

/**
 * Update deleted videos stats from data table
 *
 * @param object $pdo pdo initiated object
 *
 * @return any
 */
function updateDeletedVideosStats($pdo)
{
    $sql = 'update videos
        set status="deleted"
        where id in (
            select distinct video_id
            from videos_readings
            where status_virtual="deleted"
        )';
    $pdo->query($sql);
}

/**
 * Get list of 1 hour ago or greater updated movies
 *
 * @param object $pdo configured pdo object
 *
 * @return array
 * */
function getRecentlyUnupdatedClips($pdo)
{
    $sql = 'select max(vr.id) as max_id,
          max(vr.date_created) as last_date,
          vr.video_id from videos_readings vr
          join videos vs
          where vs.id = vr.video_id and vs.status != \'deleted\'
          group by video_id
          having last_date <= NOW() - INTERVAL 1 HOUR';
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get video stats from site full version
 *
 * @param string $cookies  string of cookies including auth
 * @param string $videoUrl url of full site video version
 *
 * @return array
 * */
function getStatsFromFullSiteVersion($cookies, $videoUrl)
{
    $opts = array(
        'http' => array(
            'method' => "GET",
            'header' => "Accept-language: en\r\n" .
            $cookies . "\r\n"
        )
    );

    $context = stream_context_create($opts);
    $html = file_get_contents($videoUrl, false, $context);

    $dom = new DOMDocument();
    @$dom->loadHtml($html);
    $xpathQueryViews = '//span[contains(@class, "vp-layer-info_views")]';
    $xpathQueryComments = '//a[@data-module="CommentWidgets"]/span[@class="widget_count js-count"]';
    $xpathQueryShares = '//button[@data-type="RESHARE"]/span[@class="widget_count js-count"]';
    $xpathQueryLikes = '//span[contains(@class,"js-klass")]/span[contains(@class, "js-count")]';
    $xpathQueryOkVideoModule = '//div[@data-module="OKVideo"]/@data-options';
    $xpath = new DOMXpath($dom);
    $nodesViews = count($xpath->query($xpathQueryViews))
        ? preg_replace('/[\D]/u', '', $xpath->query($xpathQueryViews)[0]->textContent)
        : false;
    $nodesComments = count($xpath->query($xpathQueryComments)) ?
        $xpath->query($xpathQueryComments)[0]->textContent
        : false;
    $nodesShares = count($xpath->query($xpathQueryShares)) ?
        $xpath->query($xpathQueryShares)[0]->textContent
        : false;
    $nodesLikes = count($xpath->query($xpathQueryLikes)) ?
        $xpath->query($xpathQueryLikes)[0]->textContent
        : false;
    $okVideoOpts = count($xpath->query($xpathQueryOkVideoModule)) ?
        json_decode($xpath->query($xpathQueryOkVideoModule)[0]->value, true)
        : false;
    $okVideoMetadata = $okVideoOpts ?
        json_decode($okVideoOpts['flashvars']['metadata'], true)
        : false;

    $result = [
        'views' => $nodesViews,
        'comments' => $nodesComments,
        'shares' => $nodesShares,
        'likes' => $nodesLikes,
        'groupUrl' => (
            $okVideoMetadata
            ? "https://ok.ru/group/" . $okVideoMetadata['movie']['groupId'] . "/"
            : false ),
        'authorProfile' => (
            $okVideoMetadata
            ? "https://ok.ru" . $okVideoMetadata['author']['profile']
            : false) ,
        'authorName' => (
            $okVideoMetadata
            ? $okVideoMetadata['author']['name']
            : false)
    ];
    return $result;
}
