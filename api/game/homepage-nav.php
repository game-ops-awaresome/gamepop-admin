<?php
/**
 * Created by PhpStorm.
 * User: meathill
 * Date: 14-3-31
 * Time: 下午1:44
 */
include_once "../../inc/Spokesman.class.php";
include_once "../../inc/Game.class.php";
require_once "../../inc/Article.class.php";
include_once "../../inc/API.class.php";

$api = new API('game|article_wb', array(
  'fetch' => fetch,
  'update' => update,
  'delete' => delete,
  'create' => create,
));

function fetch($args) {
  $game = new Game();
  $article = new Article();
  $conditions = Spokesman::extract(true);

  $nav = $game->select(Game::$HOMEPAGE_NAV)
    ->where($conditions)
    ->where(array('status' => 0))
    ->order('seq', 'ASC')
    ->fetchAll(PDO::FETCH_ASSOC);

  $categories = array();
  foreach ($nav as $nav_item) {
    $categories[] = $nav_item['category'];
  }

  $categories = $article->select(Article::$ALL_CATEGORY, $article->count())
    ->where($conditions)
    ->where(array('category' => $categories), '', \gamepop\Base::R_IN)
    ->where(array('status' => Article::NORMAL), Article::TABLE)
    ->group('id', Article::CATEGORY)
    ->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

  foreach ($nav as $key => $value) {
    $value['guide_name'] = $conditions['guide_name'];
    $nav[$key] = array_merge($value, (array)$categories[$value['category']]);
  }

  $result = array(
    'total' => count($nav),
    'list' => $nav,
  );

  Spokesman::say($result);
}

function create($args, $attr) {
  $game = new Game();
  unset($attr['NUM']);
  unset($attr['cate']);
  unset($attr['label']);
  $attr = array_merge($attr, Spokesman::extract(true));
  if (isset($attr['image'])) {
    $attr['image'] = str_replace('http://r.yxpopo.com/', '', $attr['image']);
  }
  $result = $game->insert($attr, Game::HOMEPAGE_NAV)
    ->execute()
    ->lastInsertId();
  $attr = array_merge(array('id' => $result), $attr);
  Spokesman::judge($result, '创建成功', '创建失败', $attr);
}

function delete($args) {
  $attr = array(
    'status' => 1,
  );
  update($args, $attr);
}

function update($args, $attr, $success = '更新成功', $error = '更新失败') {
  $game = new Game();
  $conditions = Spokesman::extract(true);
  if (isset($args['image'])) {
    $args['image'] = str_replace('http://r.yxpopo.com/', '', $args['image']);
  }
  $result = $game->update($attr, Game::HOMEPAGE_NAV)
    ->where($conditions)
    ->execute();
  Spokesman::judge($result, $success, $error, $args);
}