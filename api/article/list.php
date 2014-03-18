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
$DB = include_once "../../inc/pdo.php";
include_once "../../inc/Article.class.php";
$article = new Article($DB);

$methods = array(
  'GET' => 'fetch',
  'PATCH' => 'update',
);
$args = $_REQUEST;
$request = file_get_contents('php://input');
if ($request) {
  $args = array_merge($_POST, json_decode($request, true));
}
$method = $methods[$_SERVER['REQUEST_METHOD']];
header("Content-Type:application/json;charset=utf-8");
if ($method) {
  $method($article, $args);
}

function fetch($article, $args) {
  $path = $args['path'];
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