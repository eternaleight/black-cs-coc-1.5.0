<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//ファイルのディレクトリパスを取得する（最後の/付き）
if ( !function_exists( 'abspath' ) ):
function abspath($file){return dirname($file).'/';}
endif;

require_once abspath(__FILE__).'lib/_defins.php'; //定数を定義

//アップデートチェックの初期化
require abspath(__FILE__).'lib/theme-update-checker.php'; //ライブラリのパス
$example_update_checker = new ThemeUpdateChecker(
  strtolower(THEME_PARENT_DIR), //テーマフォルダ名
  'https://raw.githubusercontent.com/yhira/cocoon/master/update-info.json' //JSONファイルのURL
);

//本文部分の冒頭を綺麗に抜粋する
if ( !function_exists( 'get_content_excerpt' ) ):
function get_content_excerpt($content, $length = 70){
  $content = apply_filters( 'content_excerpt_before', $content);
  //_v($content);
  $content = cancel_blog_card_deactivation($content, false);
  $content = preg_replace('/<!--more-->.+/is', '', $content); //moreタグ以降削除
  $content = strip_tags($content);//タグの除去
  $content = strip_shortcodes($content);//ショートコード削除
  $content = str_replace('&nbsp;', '', $content);//特殊文字の削除（今回はスペースのみ）
  $content = preg_replace('/\[.+?\]/i', '', $content); //ショートコードを取り除く
  $content = preg_replace(URL_REG, '', $content); //URLを取り除く
  // $content = preg_replace('/\s/iu',"",$content); //余分な空白を削除
  //$lengthが整数じゃなかった場合の処理
  if (is_int(intval($length))) {
    $length = intval($length);
  } else {
    $length = 70;
  }
  $over    = intval(mb_strlen($content)) > $length;
  $content = mb_substr($content, 0, $length);//文字列を指定した長さで切り取る
  if ( $over && $more = get_entry_card_excerpt_more() ) {
    $content = $content.$more;
  }
  $content = esc_html($content);

  $content = apply_filters( 'content_excerpt_after', $content);

  return $content;
}
endif;

//WP_Queryの引数を取得
if ( !function_exists( 'get_related_wp_query_args' ) ):
function get_related_wp_query_args(){
  global $post;
  if (!$post) {
    $post = get_random_posts(1);
  }
  //var_dump($post);
  //if ( 1 ) {
  if ( is_related_association_type_category() ) {
    //カテゴリ情報から関連記事をランダムに呼び出す
    $categories = get_the_category($post->ID);
    $category_IDs = array();
    foreach($categories as $category):
      array_push( $category_IDs, $category->cat_ID);
    endforeach ;
    if ( empty($category_IDs) ) return;
    $args = array(
      'post__not_in' => array($post->ID),
      'posts_per_page'=> intval(get_related_entry_count()),
      'category__in' => $category_IDs,
      'orderby' => 'rand',
      'no_found_rows' => true,
    );
  } else {
    //タグ情報から関連記事をランダムに呼び出す
    $tags = wp_get_post_tags($post->ID);
    $tag_IDs = array();
    foreach($tags as $tag):
      array_push( $tag_IDs, $tag->term_id);
    endforeach ;
    if ( empty($tag_IDs) ) return;
    $args = array(
      'post__not_in' => array($post -> ID),
      'posts_per_page'=> intval(get_related_entry_count()),
      'tag__in' => $tag_IDs,
      'orderby' => 'rand',
      'no_found_rows' => true,
    );
  }
  return apply_filters('get_related_wp_query_args', $args);
}
endif;

//images/no-image.pngを使用するimgタグに出力するサイズ関係の属性
if ( !function_exists( 'get_noimage_sizes_attr' ) ):
function get_noimage_sizes_attr($image = null){
  if (!$image) {
    $image = get_no_image_160x90_url();
  }
  $w = THUMB160WIDTH;
  $h = THUMB160HEIGHT;
  $sizes = ' srcset="'.$image.' '.$w.'w" width="'.$w.'" height="'.$h.'" sizes="(max-width: '.$w.'px) '.$w.'vw, '.$h.'px"';
  return $sizes;
}
endif;

//投稿ナビのサムネイルタグを取得する
if ( !function_exists( 'get_post_navi_thumbnail_tag' ) ):
function get_post_navi_thumbnail_tag($id, $width = THUMB120WIDTH, $height = THUMB120HEIGHT){
  $thumb = get_the_post_thumbnail( $id, 'thumb'.strval($width), array('alt' => '') );
  if ( !$thumb ) {
    $image = get_template_directory_uri().'/images/no-image-%s.png';

    //表示タイプ＝デフォルト
    if ($width == THUMB120WIDTH) {
      $w = THUMB120WIDTH;
      $h = THUMB120HEIGHT;
      $image = get_no_image_160x90_url();
      $wh_attr = ' srcset="'.$image.' '.$w.'w" width="'.$w.'" height="'.$h.'" sizes="(max-width: '.$w.'px) '.$w.'vw, '.$h.'px"';
    } else {//表示タイプ＝スクエア
      $image = get_no_image_150x150_url();
      $wh_attr = ' srcset="'.$image.' '.W120.'w" width="'.W120.'" height="'.W120.'" sizes="(max-width: '.W120.'px) '.W120.'vw, '.W120.'px"';
    }
    $thumb = '<img src="'.$image.'" alt="" class="no-image post-navi-no-image"'.$wh_attr.' />';
  }
  return $thumb;
}
endif;

///////////////////////////////////////
// グローバルナビに説明文を加えるウォーカークラス
///////////////////////////////////////
class menu_description_walker extends Walker_Nav_Menu {
  function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    global $wp_query;
    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

    $class_names = $value = '';

    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
    //$classes[] = 'fa';
    if ($item->description) {
      $classes[] = 'menu-item-has-description';
    }

    $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
    $class_names = ' class="'. esc_attr( $class_names ) . '"';
    $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

    $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
    $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

    $prepend = '<div class="item-label">';
    $append = '</div>';
    $description  = ! empty( $item->description ) ? '<div class="item-description sub-caption">'.esc_attr( $item->description ).'</div>' : '';

    $item_output = $args->before;
    $item_output .= '<a'. $attributes .'>';
    $item_output .= '<div class="caption-wrap">';
    $item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
    $item_output .= $description.$args->link_after;
    $item_output .= '</div>';
    $item_output .= '</a>';
    $item_output .= $args->after;

    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }
}

//アーカイブタイトルの取得
if ( !function_exists( 'get_archive_chapter_title' ) ):
function get_archive_chapter_title(){
  $chapter_title = null;
  if( is_category() ) {//カテゴリページの場合
    $cat_id = get_query_var('cat');
    $icon_font = '<span class="fa fa-folder-open"></span>';
    if ($cat_id && get_category_title($cat_id)) {
      $chapter_title .= $icon_font.get_category_title($cat_id);
    } else {
      $chapter_title .= single_cat_title( $icon_font, false );
    }
  } elseif( is_tag() ) {//タグページの場合
    $chapter_title .= single_tag_title( '<span class="fa fa-tags"></span>', false );
  } elseif( is_tax() ) {//タクソノミページの場合
    $chapter_title .= single_term_title( '', false );
  } elseif( is_search() ) {//検索結果
    $search_query = trim(strip_tags(get_search_query()));
    if (empty($search_query)) {
      $search_query = __( 'キーワード指定なし', THEME_NAME );
    }
    $chapter_title .= '<span class="fa fa-search"></span>"'.$search_query.'"';
  } elseif (is_day()) {
    //年月日のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar"></span>'.get_the_time('Y-m-d');
  } elseif (is_month()) {
    //年と月のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar"></span>'.get_the_time('Y-m');
  } elseif (is_year()) {
    //年のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar"></span>'.get_the_time('Y');
  } elseif (is_author()) {//著書ページの場合
    $chapter_title .= '<span class="fa fa-user"></span>'.esc_html(get_queried_object()->display_name);
  } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
    $chapter_title .= 'Archives';
  } else {
    $chapter_title .= 'Archives';
  }
  return $chapter_title;
}
endif;

//アーカイブ見出しの取得
if ( !function_exists( 'get_archive_chapter_text' ) ):
function get_archive_chapter_text(){
  $chapter_text = null;

  //アーカイブタイトルの取得
  $chapter_text .= get_archive_chapter_title();

  //返り値として返す
  return $chapter_text;
}
endif;

//'wp-color-picker'の呼び出し順操作（最初の方に読み込む）
add_action('admin_enqueue_scripts', 'admin_enqueue_scripts_custom');
if ( !function_exists( 'admin_enqueue_scripts_custom' ) ):
function admin_enqueue_scripts_custom($hook) {
  wp_enqueue_script('colorpicker-script', get_template_directory_uri() . '/js/color-picker.js', array( 'wp-color-picker' ), false, true);
}
endif;

//投稿管理画面のカテゴリリストの階層を保つ
add_filter('wp_terms_checklist_args', 'solecolor_wp_terms_checklist_args', 10, 2);
if ( !function_exists( 'solecolor_wp_terms_checklist_args' ) ):
function solecolor_wp_terms_checklist_args( $args, $post_id ){
 if ( isset($args['checked_ontop']) && ($args['checked_ontop'] !== false )){
    $args['checked_ontop'] = false;
 }
 return $args;
}
endif;

//リダイレクト
add_action( 'wp','wp_singular_page_redirect', 0 );
if ( !function_exists( 'wp_singular_page_redirect' ) ):
function wp_singular_page_redirect() {
  //リダイレクト
  if (is_singular() && $redirect_url = get_singular_redirect_url()) {
    //URL形式にマッチする場合
    if (preg_match(URL_REG, $redirect_url)) {
      redirect_to_url($redirect_url);
    }
  }
}
endif;

//マルチページページャーの現在のページにcurrentクラスを追加
add_filter('wp_link_pages_link', 'wp_link_pages_link_custom');
if ( !function_exists( 'wp_link_pages_link_custom' ) ):
function wp_link_pages_link_custom($link){
  //リンク内にAタグが含まれていない場合は現在のページ
  if (!includes_string($link, '</a>')) {
    $link = str_replace('class="page-numbers"', 'class="page-numbers current"', $link);
  }
  return $link;
}
endif;

//メインクエリの出力順変更
add_action( 'pre_get_posts', 'change_main_loop_sort_order' );
if ( !function_exists( 'change_main_loop_sort_order' ) ):
function change_main_loop_sort_order( $query ) {
  if (is_get_index_sort_orderby_modified()) {
    if ($query->is_main_query()) {
      $query->set( 'orderby', 'modified' );
    }
  }
}
endif;


// add_action( 'widgets_init', function()
// {
//     _v($GLOBALS['wp_widget_factory']);
//     if ( empty ( $GLOBALS['wp_widget_factory'] ) )
//         return;

//     $GLOBALS['wp_widget_factory']->widgets = array();
// }, 20);

// global $wp_registered_sidebars;
// _v($wp_registered_sidebars);



// /**************************
//  * レーダーチャート
//  ***************************/
// function radar_chart($atts)
// {
//     if (is_null($atts)) {
//         return '';
//     }

//     extract(shortcode_atts([
//         'title1' => '項目1',
//         'score1' => 1,
//         'title2' => '項目2',
//         'score2' => 1,
//         'title3' => '項目3',
//         'score3' => 1,
//         'title4' => '項目4',
//         'score4' => 1,
//         'title5' => '項目5',
//         'score5' => 1,
//         'aria_label' => 'レーダーチャート'
//     ],$atts));

//     $item1_array=[" L 160 128"," L 160 106"," L 160 84"," L 160 62"," L 160 40"];
//     $item2_array=[" L 140 143"," L 120 136"," L 100 129"," L 80 122"," L 60 115"];
//     $item3_array=[" L 147.5 167"," L 135 184"," L 122.5 201"," L 110 218"," L 97.5 235"];
//     $item4_array=[" L 172.5 167"," L 185 184"," L 197.5 201"," L 210 218"," L 222.5 235"];
//     $item5_array=[" L 180 143"," L 200 136"," L 220 129"," L 240 122"," L 260 115"];

//     for($i = 1; $i <= 5; $i++){
//         $index = ${"score{$i}"}-1;
//         ${"item{$i}"} = ${"item{$i}_array"}[$index];
//     }
//     $item1_replaceM=str_replace(" L", "M", $item1);

//     $point_regex_patern = "/L (\d+.*\d*) (\d+.*\d*)/";
//     for($i = 1; $i <= 5; $i++){
//         preg_match($point_regex_patern,${"item{$i}"},${"item{$i}_point"});
//         ${"item{$i}_pointX"} = ${"item{$i}_point"}[1];
//         ${"item{$i}_pointY"} = ${"item{$i}_point"}[2];
//     }
//     $line_positions = $item1_replaceM.$item5.$item4.$item3.$item2.$item1;

//     $point_color = '#ff9630'; //座標の色
//     $aria_color = '#f8c678';  //塗り潰しの色


//     $output = <<<EOF
//     <div style="text-align:center;">
//       <div class="radar-chart">
//         <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="320" height="291"  viewbox="0 0 320 291" role="img" aria-label="{$aria_label}">
//             <g class="radar-chart-grid">
//                 <path fill="none" d="M 160 150 L 160 40" stroke="#dce5eb" stroke-width="1" zIndex="1" opacity="1"></path>
//                 <path fill="none" d="M 160 150 L 260 115" stroke="#dce5eb" stroke-width="1" zIndex="1" opacity="1"></path>
//                 <path fill="none" d="M 160 150 L 222.5 235" stroke="#dce5eb" stroke-width="1" zIndex="1" opacity="1"></path>
//                 <path fill="none" d="M 160 150 L 97.5 235" stroke="#dce5eb" stroke-width="1" zIndex="1" opacity="1"></path>
//                 <path fill="none" d="M 160 150 L 60 115" stroke="#dce5eb" stroke-width="1" zIndex="1" opacity="1"></path>
//             </g>
//             <g class="radar-chart-grid">
//                 <path fill="none" d="M 160 150 L 160 150 L 160 150 L 160 150 L 160 150 L 160 150 L 160 150 L 160 150 L 160 150 L 160 150"
//                       stroke="#dce5eb" stroke-width="1" zIndex="1" opacity="1"></path>
//                 <path fill="none" d="M 160 128 L 140 143 L 147.5 167  L 172.5 167 L 180 143 L 160 128" stroke="#dce5eb" stroke-width="1"
//                       zIndex="1" opacity="1"></path>
//                 <path fill="none" d="M 160 106 L 120 136 L 135 184  L 185 184 L 200 136  L 160 106" stroke="#dce5eb" stroke-width="1" zIndex="1"
//                       opacity="1"></path>
//                 <path fill="none" d="M 160 84  L 100 129 L 122.5 201  L 197.5 201 L 220 129  L 160 84" stroke="#dce5eb" stroke-width="1"
//                       zIndex="1" opacity="1"></path>
//                 <path fill="none" d="M 160 62  L 80 122 L 110 218 L 210 218 L 240 122  L 160 62" stroke="#dce5eb" stroke-width="1" zIndex="1"
//                       opacity="1"></path>
//                 <path fill="none" d="M 160 40  L 60 115 L 97.5 235  L 222.5 235 L 260 115 L 160 40" stroke="#dce5eb" stroke-width="1" zIndex="1"
//                       opacity="1"></path>
//             </g>
//             <g class="radar-chart-aria">
//                 <g transform="translate(0,0) scale(1 1)">
//                     <path fill="{$aria_color}" d="{$line_positions}" fill-opacity="0.3"></path>
//                     <path fill="none" d="{$line_positions}" stroke="{$point_color}" stroke-width="1" zIndex="1" stroke-linejoin="round" stroke-linecap="round"></path>
//                 </g>
//                 <g class="radar-chart-point">
//                     <circle cx="{$item1_pointX}" cy="{$item1_pointY}" r="3" fill="{$point_color}" />
//                     <circle cx="{$item2_pointX}" cy="{$item2_pointY}" r="3" fill="{$point_color}" />
//                     <circle cx="{$item3_pointX}" cy="{$item3_pointY}" r="3" fill="{$point_color}" />
//                     <circle cx="{$item4_pointX}" cy="{$item4_pointY}" r="3" fill="{$point_color}" />
//                     <circle cx="{$item5_pointX}" cy="{$item5_pointY}" r="3" fill="{$point_color}" />
//                 </g>
//             </g>
//         </svg>
//         <ul class="radar-chart-dls">
//             <li class="radar-chart-dl1">
//                 <dl>
//                     <dt>{$title1}</dt>
//                     <dd>{$score1}</dd>
//                 </dl>
//             </li>
//             <li class="radar-chart-dl2">
//                 <dl>
//                     <dt>{$title2}</dt>
//                     <dd>{$score2}</dd>
//                 </dl>
//             </li>
//             <li class="radar-chart-dl3">
//                 <dl>
//                     <dt>{$title3}</dt>
//                     <dd>{$score3}</dd>
//                 </dl>
//             </li>
//             <li class="radar-chart-dl4">
//                 <dl>
//                     <dt>{$title4}</dt>
//                     <dd>{$score4}</dd>
//                 </dl>
//             </li>
//             <li class="radar-chart-dl5">
//                 <dl>
//                     <dt>{$title5}</dt>
//                     <dd>{$score5}</dd>
//                 </dl>
//             </li>
//         </ul>
//       </div>
//     </div>
// EOF;

//     return $output;
// }
// add_shortcode('radar_chart', 'radar_chart');
