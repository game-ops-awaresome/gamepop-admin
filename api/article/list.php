<?php
define('OPTIONS', 'article');
include_once '../../inc/session.php';
?>
<?php
/**
 * Created by PhpStorm.
 * User: meathill
 * Date: 14-3-17
 * Time: 上午10:11
 */
include_once "../../inc/Article.class.php";
$article = new Article();

$args = $_REQUEST;
$request = file_get_contents('php://input');
if ($request) {
  $args = array_merge($_POST, json_decode($request, true));
}
header("Content-Type:application/json;charset=utf-8");
switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    fetch($article, $args);
    break;

  case 'PATCH':
    update($article, $args);
    break;

  default:
    header("HTTP/1.1 406 Not Acceptable");
    break;
}

function fetch($article, $args) {
  $path = $args['id'];
  $params = explode('/', $path);
  $id = $params[0];

  $pagesize = isset($args['pagesize']) ? (int)$args['pagesize'] : 20;
  $page = isset($args['page']) ? (int)$args['page'] : 0;
  $keyword = empty($args['keyword']) ? '' : trim(addslashes(strip_tags($args['keyword'])));

  $total = $article->get_article_number_by_id($id, $keyword);
  $total = (int)$total[$id];
  if ($id) {
    $articles = $article->get_articles_by_game($id, $pagesize, $page, $keyword);
  } else {
    $articles = $article->get_articles($pagesize, $page, $keyword);
  }

  echo json_encode(array(
    'total' => $total,
    'list' => $articles,
  ));
}

function update($article, $args) {
  $url = $_SERVER['PATH_INFO'];
  $id = substr($url, 1);

  if ($article->update($id, $args)) {
    echo json_encode(array(
      'code' => 0,
      'msg' => '更新成功',
    ));
  } else {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(array(
      'code' => 1,
      'msg' => '更新失败',
    ));
  }
}