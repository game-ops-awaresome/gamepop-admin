<?php
define('OPTIONS', 'article');
include_once '../../inc/session.php';
?>
<?php
/**
 * Created by PhpStorm.
 * User: meathill
 * Date: 14-3-17
 * Time: 下午5:13
 */

include_once "../../inc/Article.class.php";
$article = new Article();

$methods = array(
  'GET' => 'fetch',
  'PATCH' => 'update',
);

$args = $_REQUEST;
$request = file_get_contents('php://input');
if ($request) {
  $args = array_merge($_POST, json_decode($request, true));
}
$url = $_SERVER['PATH_INFO'];
$id = (int)substr($url, 1);

header("Content-Type:application/json;charset=utf-8");
if (!isset($_SERVER['REQUEST_METHOD'], $methods)) {
  header("HTTP/1.1 406 Not Acceptable");
  exit(json_encode(array(
    'code' => 1,
    'msg' => '指令错误',
  )));
}
$method = $methods[$_SERVER['REQUEST_METHOD']];
if ($method) {
  $method($article, $id, $args);
}

function fetch($article, $id) {
  $result = $article->get_article_by_id($id);

  echo json_encode($result);
}

function update($article, $id, $args) {
  $result = $article->update($id, $args);
  if ($result) {
    $result = array('code' => 0, 'msg' => '修改成功');
  } else {
    header('HTTP/1.1 400 Bad Request');
    $result = array('code' => 1, 'msg' => '修改失败');
  }
  echo json_encode($result);
}