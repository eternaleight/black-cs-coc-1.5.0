<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */ ?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
<?php //ヘッドタグ内挿入用のアクセス解析用テンプレート
get_template_part('tmp/head-analytics'); ?>
<meta charset="utf-8">
<?php //AMPの案内タグを出力
if ( has_amp_page() ): ?>
<link rel="amphtml" href="<?php echo get_amp_permalink(); ?>">
<?php endif ?>
<?php //Google Search Consoleのサイト認証IDの表示
if ( get_google_search_console_id() ): ?>
<!-- Google Search Console -->
<meta name="google-site-verification" content="<?php echo get_google_search_console_id() ?>" />
<!-- /Google Search Console -->
<?php endif;//Google Search Console終了 ?>
<?php //Google Tag Manager
if (is_analytics() && $tracking_id = get_google_tag_manager_tracking_id()): ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo $tracking_id; ?>');</script>
<!-- End Google Tag Manager -->
<?php endif //Google Tag Manager終了 ?>

<?php // force Internet Explorer to use the latest rendering engine available ?>
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<?php // mobile meta (hooray!) ?>
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="viewport" content="width=device-width, initial-scale=1"/>

<?php //自動アドセンス
get_template_part('tmp/ad-auto-adsense'); ?>


<?php wp_head(); ?>

<?php //カスタムフィールドの挿入（カスタムフィールド名：head_custom
get_template_part('tmp/head-custom-field'); ?>

<?php //headで読み込む必要があるJavaScript
get_template_part('tmp/head-javascript'); ?>

<?php //ヘッドタグ内挿入用のユーザー用テンプレート
get_template_part('tmp-user/head-insert'); ?>
	
	<!-- ダークモード -->
<link type="text/css" rel="stylesheet" href id="theme-mode">
<script src="/wp-content/themes/cocoon-1.5.0/js/darkmode.js" defer></script>
</head>

<body <?php body_class(); ?> itemscope itemtype="https://schema.org/WebPage">
<?php //body最初に挿入するアクセス解析ヘッダータグの取得
get_template_part('tmp/body-top-analytics'); ?>

<?php //ユーザーカスタマイズ用
get_template_part('tmp-user/body-top-insert'); ?>

<?php //サイトヘッダーからコンテンツまでbodyタグ最初のHTML
get_template_part('tmp/body-top'); ?>
<div class="icon" style="width: 40px;outline: 0px solid white;position: absolute;top:-170px;left:20px;fill: #EFF6FC;">
	<a href="/"><svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 64 64" stroke-width="1.2" stroke="#CACBD2" fill="none" class="duration-300 transform transition-all" style="width: 48px; height: 48px;"><path d="M34.82 52.73H14.69V22.18a1 1 0 01.52-.87l18.13-9.91a1 1 0 011.48.88zM48.87 52.73H34.92V21.59L48.4 29.3a1 1 0 01.47.85zM28.1 24.86h-7.04M43.66 32.41h-3.52M43.66 36.9h-3.52M43.66 41.71h-3.52M43.66 46.19h-3.52M28.1 30.44h-7.04M28.1 35.94h-7.04M28.1 41.44h-7.04M28.1 46.94h-7.04M9.46 52.73h45.08" stroke-linecap="round"></path></svg></a>
</div>
