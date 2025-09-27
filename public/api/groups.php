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

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();
$dsn = "mysql:host=" . $_ENV['MYSQL_HOST']
    .";dbname=" . $_ENV['MYSQL_DATABASE']
    .";charset=UTF8";

$pdo = new PDO($dsn, $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);
$fluent = new \Envms\FluentPDO\Query($pdo);
$added = 0;
$recivedGroup = file_get_contents('php://input');

if (isset($_GET['add'])) {

  $values = ['url' => $recivedGroup, 'is_checked' => 1, 'is_usable' => 1];
  $set = ['is_checked' => 1, 'is_usable' => 1];

  $row = $fluent->from('groups')->select('groups.url')->where('url', $recivedGroup)->limit(1)->fetch();

  if(isset($row) && $row['url'] == $recivedGroup)  {
    $query = $fluent->update('groups')->set($set)->where('url', $recivedGroup)->execute();
  } else {
    $query = $fluent->insertInto('groups', $values)->execute();
  }

  echo "group $recivedGroup is marked as usable $query";

} elseif (isset($_GET['mark_as_closed'])) {

  $values = ['url' => $recivedGroup, 'is_checked' => 1, 'is_usable' => 0];
  $set = ['is_checked' => 1, 'is_usable' => 0];

  $row = $fluent->from('groups')->select('groups.url')->where('url', $recivedGroup)->limit(1)->fetch();

  if(isset($row) && $row['url'] == $recivedGroup)  {
    $query = $fluent->update('groups')->set($set)->where('url', $recivedGroup)->execute();
  } else {
    $query = $fluent->insertInto('groups', $values)->execute();
  }

  echo "group $recivedGroup is marked as Closed $query";

} elseif (isset($_GET['get_next'])) {
  /* $row = $fluent->from('groups')->select('groups.url')->where('is_checked', 0)->limit(1)->fetch(); */
  $res = [];
  $rows = $fluent->from('groups')->select('groups.url')->where('is_checked', 0)->limit(30);
  foreach ($rows as $row) {
    array_push($res, $row);
  }
  shuffle($res);
  echo $res[0]['url'];
} else {

  try {
    $recivedGroups = json_decode(file_get_contents('php://input'), true);
    foreach($recivedGroups as $group) {
      $row = $fluent->from('groups')->select('groups.url')->where('url', $group)->limit(1)->fetch();
      if(is_null($row['url'])) {
        $values = ['url' => $group, 'is_checked' => 0, 'is_usable' => 0];
        $query = $fluent->insertInto('groups', $values)->execute();
        $added += 1;
      }
    }
    echo "group added count $added pushed ". count($recivedGroups) . "\n";

  } catch(Exception $e) {
    var_dump($e->getMessage());
  }
}

$fluent->close();

