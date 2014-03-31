<?php
define('OPTIONS', 'article');
include_once '../../inc/session.php';
?>
<?php
/**
 * Created by PhpStorm.
 * User: meathill
 * Date: 14-3-17
 * Time: 上午11:38
 */
include_once "../../inc/Game.class.php";
$game = new Game();

$url = $_SERVER['PATH_INFO'];
$id = substr($url, 1);
$conditions = array(
  'guide_name' => $id,
);

$result = $game->select(Game::$INFO)
  ->where($conditions)
  ->fetch(PDO::FETCH_ASSOC);

if (DEBUG) {
  if (substr($result['icon_path'], 0, 7) === 'upload/') {
    $$result['icon_path'] = 'http://admin.yxpopo.com/' . $result['icon_path'];
  }
}
echo json_encode($result);