<?php
require '../../src/vendor/autoload.php';
require	'lib/pdo_sqllight.php';

class Greeting {

    public function __construct() {
        $this->name = "John Doe";
        $this->db =  Flight::db();
    }

    public function hello() {
        echo "Hello {$this->name}!";
        echo $this->db->query('select count(name) from public.okgrpoups')->fetch(\PDO::FETCH_ASSOC)["count"];
    }
}

function api_get_groups_from_db ($limit=400) {
    Flight::json(get_groups_from_db($limit));
}

function api_get_one_group() {
    Flight::json(get_one_group());
}

function api_get_checked_groups_count() {
    Flight::json(get_checked_groups_count());
}

function api_get_usable_groups_count(){
    Flight::json(get_usable_groups_count());
}

function api_export_usable_groups() {
    Flight::json(export_usable_groups());
}
function api_get_unic_posters_groups_count() {
    Flight::json(get_unic_posters_groups_count());
}

function api_export_unic_posters_groups() {
    Flight::json(export_unic_posters_groups());
}

function api_export_checked_groups() {
    Flight::json(export_checked_groups());
}

function api_export_unchecked_groups () {
    Flight::json(export_unchecked_groups());
}

function api_get_total_groups_count() {
    Flight::json(get_total_groups_count());
}

// Register class with constructor parameters
Flight::register('db', 'PDO', array('pgsql:host=localhost;dbname=ok_web_2;','root','weichuoraecah7eZiib7Ie'));

$db = Flight::db();

$greeting = new Greeting();

Flight::route('/', [$greeting,'hello']);

Flight::route('/get_groups_from_db/@limit', 'api_get_groups_from_db');
Flight::route('/get_one_group', 'api_get_one_group');
Flight::route('/get_checked_groups_count', 'api_get_checked_groups_count');
Flight::route('/get_usable_groups_count', 'api_get_usable_groups_count');
Flight::route('/export_usable_groups', 'api_export_usable_groups');
Flight::route('/get_unic_posters_groups_count', 'api_get_unic_posters_groups_count');
Flight::route('/export_unic_posters_groups', 'api_export_unic_posters_groups');
Flight::route('/export_checked_groups', 'api_export_checked_groups');
Flight::route('/export_unchecked_groups', 'api_export_unchecked_groups');
Flight::route('/get_total_groups_count','api_get_total_groups_count');

Flight::route('/add_groups_to_db', function () {
        $arr = json_decode(file_get_contents("php://input"), true);
        Flight::json(add_groups_to_db($arr));
});

Flight::route('/set_group_checked', function () {
        $data = json_decode(file_get_contents("php://input"), true);
        Flight::json(set_group_checked($data[0]));
});

Flight::route('/set_un_Usable_as_un_Cheked', function () {
        $data = json_decode(file_get_contents("php://input"), true);
        Flight::json(set_unUsable_as_unCheked($data[0]));
});


Flight::route('/set_group_Usable', function () {
        $data = json_decode(file_get_contents("php://input"), true);
        Flight::json(set_group_Usable($data[0]));
});

Flight::route('/set_group_un_Usable', function () {
        $data = json_decode(file_get_contents("php://input"), true);
        Flight::json(set_group_unUsable($data[0]));
});

Flight::route('/set_group_Cheked', function () {
        $data = json_decode(file_get_contents("php://input"), true);
        Flight::json(set_group_Cheked($data[0]));
});

Flight::route('/set_group_un_Cheked', function () {
        $data = json_decode(file_get_contents("php://input"), true);
        Flight::json(set_group_unCheked($data[0]));
});

Flight::route('/update_video_stats', function () {
        $data = json_decode(file_get_contents("php://input"), true);
        Flight::json(update_video_stats($data["url"], $data["sa"]));
});

Flight::route('/update_board_stats', function () {
        $data = json_decode(file_get_contents("php://input"), true);
        Flight::json(update_board_stats($data["url"], $data["sa"]));
});


Flight::start();


?>
