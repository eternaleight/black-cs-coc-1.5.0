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
	<a href="/"><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 64 64" stroke-width="1.2" stroke="#aAaBb2" fill="none" class="duration-300 transform transition-all" style="width: 48px; height: 48px;"><ellipse cx="23.07" cy="14.99" rx="15.22" ry="5.24" stroke-linecap="round"></ellipse><path d="M38.3 21.8c0 2.89-6.82 5.24-15.23 5.24S7.85 24.69 7.85 21.8M27.38 33.43h-4.31c-8.41 0-15.22-2.35-15.22-5.24M24.91 40h-1.84c-8.41 0-15.22-2.34-15.22-5.24M25.7 46.53a22.48 22.48 0 01-2.63.08c-8.41 0-15.22-2.35-15.22-5.24M7.85 41.42V15.01M38.3 30.01V14.99" stroke-linecap="round"></path><ellipse cx="40.93" cy="35.82" rx="15.22" ry="5.24" stroke-linecap="round"></ellipse><path d="M56.15 42.63c0 2.9-6.81 5.24-15.22 5.24S25.7 45.53 25.7 42.63M56.15 49c0 2.9-6.81 5.25-15.22 5.25S25.7 51.91 25.7 49M25.7 48.92V35.07M56.15 49.21V35.64" stroke-linecap="round"></path></svg></a>
</div>
