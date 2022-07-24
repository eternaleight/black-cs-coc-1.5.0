<?php //ボディータグ上部
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */ ?>
<div id="container" class="container<?php echo get_additional_container_classes(); ?> cf">
  <?php //サイトヘッダー
  get_template_part('tmp/header-container'); ?>

  <?php //通知エリア
  get_template_part('tmp/notice'); ?>

  <?php //アピールエリア
  get_template_part('tmp/appeal'); ?>

  <?php //カルーセル
  get_template_part('tmp/carousel'); ?>

  <?php //投稿パンくずリストがメイン手前の場合
  if (is_single() && is_single_breadcrumbs_position_main_before()){
    get_template_part('tmp/breadcrumbs');
  } ?>

  <?php //固定ページパンくずリストがメイン手前の場合
  if (is_page() && is_page_breadcrumbs_position_main_before()){
    get_template_part('tmp/breadcrumbs-page');
  } ?>

  <?php //メインカラム手前に挿入するユーザー用テンプレート
  get_template_part('tmp-user/main-before'); ?>

  <div id="content" class="content cf">

    <div id="content-in" class="content-in wrap cf">

        <main id="main" class="main<?php echo get_additional_main_classes(); ?>" itemscope itemtype="https://schema.org/Blog">

<div class="icon" style="width: 40px;outline: 0px solid white;position: absolute;top:-130px;left:20px;fill: #EFF6FC;">
	<a href="/"><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 64 64" stroke-width="1.2" stroke="#CACBD2" fill="none" class="duration-300 transform transition-all size" style="width: 48px; height: 48px;"><path d="M34.82 52.73H14.69V22.18a1 1 0 01.52-.87l18.13-9.91a1 1 0 011.48.88zM48.87 52.73H34.92V21.59L48.4 29.3a1 1 0 01.47.85zM28.1 24.86h-7.04M43.66 32.41h-3.52M43.66 36.9h-3.52M43.66 41.71h-3.52M43.66 46.19h-3.52M28.1 30.44h-7.04M28.1 35.94h-7.04M28.1 41.44h-7.04M28.1 46.94h-7.04M9.46 52.73h45.08" stroke-linecap="round"></path></svg></a>
</div>
