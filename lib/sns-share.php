<?php //SNS関係の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

if ( !function_exists( 'is_sns_share_buttons_count_visible' ) ):
function is_sns_share_buttons_count_visible(){
  return is_sns_top_share_buttons_count_visible() || is_sns_bottom_share_buttons_count_visible();
}
endif;
//_vis_numeric(0));

//ツイート数取得
if ( !function_exists( 'fetch_twitter_count_raw' ) ):
function fetch_twitter_count_raw($url){
  $url = rawurlencode( $url );
  $args = array( 'sslverify' => true );
  $subscribers = wp_remote_get( "https://jsoon.digitiminimi.com/twitter/count.json?url=$url", $args );
  $res = '0';
  if (!is_wp_error( $subscribers ) && $subscribers["response"]["code"] === 200) {
       $body = $subscribers['body'];
    $json = json_decode( $body );
    $res = ($json->{"count"} ? $json->{"count"} : '0');
  }
  return intval($res);
}
endif;

//count.jsoonからTwitterのツイート数を取得
if ( !function_exists( 'fetch_twitter_count' ) ):
function fetch_twitter_count($url = null) {

  //_v('$res');
  global $post;
  $transient_id = TRANSIENT_SHARE_PREFIX.'twitter_'.$post->ID;
  //DBキャッシュからカウントの取得
  if (is_sns_share_count_cache_enable()) {
    $count = get_transient( $transient_id );
    if ( is_numeric($count) ) {
      return $count;
    }
  }

  if (!$url) {
    $url = get_the_permalink();
  }
  $res = fetch_twitter_count_raw($url);
  //別スキームカウントの取得
  if (is_sns_share_count_cache_enable() && is_another_scheme_sns_share_count()) {
    $res = $res + fetch_twitter_count_raw(get_another_scheme_url($url));
  }

  //DBキャッシュへ保存
  if (is_sns_share_count_cache_enable()) {
    set_transient( $transient_id, $res, HOUR_IN_SECONDS * get_sns_share_count_cache_interval() );
  }
  return $res;
}
endif;

//Twitterカウントの取得
if ( !function_exists( 'get_twitter_count' ) ):
function get_twitter_count($url = null) {
  if (!is_sns_share_buttons_count_visible())
    return null;

  if (is_scc_twitter_exists()) {
    return scc_get_share_twitter();
  } else {
    return null;
  }
}
endif;

//Facebookシェア数の取得
if ( !function_exists( 'fetch_facebook_count_raw' ) ):
function fetch_facebook_count_raw($url){
  //URLをURLエンコード
  $encoded_url = rawurlencode( $url );
  //オプションの設定
  $args = array( 'sslverify' => true );
  //Facebookにリクエストを送る
  $response = wp_remote_get( 'https://graph.facebook.com/?id='.$encoded_url, $args );
  $res = 0;

  //取得に成功した場合
  if (!is_wp_error( $response ) && $response["response"]["code"] === 200) {
    $body = $response['body'];
    $json = json_decode( $body ); //ジェイソンオブジェクトに変換する
    $res = ($json->{'share'}->{'share_count'} ? $json->{'share'}->{'share_count'} : 0);
  }
  return intval($res);
}
endif;

//Facebookシェア数を取得する
if ( !function_exists( 'fetch_facebook_count' ) ):
function fetch_facebook_count($url = null) {
  global $post;
  $transient_id = TRANSIENT_SHARE_PREFIX.'facebook_'.$post->ID;
  //DBキャッシュからカウントの取得
  if (is_sns_share_count_cache_enable()) {
    $count = get_transient( $transient_id );
    if ( is_numeric($count) ) {
      return $count;
    }
  }


  if (!$url) {
    $url = get_the_permalink();
  }
  $res = fetch_facebook_count_raw($url);
  //別スキームカウントの取得
  if (is_sns_share_count_cache_enable() && is_another_scheme_sns_share_count()) {
    $res = $res + fetch_facebook_count_raw(get_another_scheme_url($url));
  }
  //_v($res);

  //DBキャッシュへ保存
  if (is_sns_share_count_cache_enable()) {
    set_transient( $transient_id, $res, HOUR_IN_SECONDS * get_sns_share_count_cache_interval() );
  }
  return $res;
}
endif;

//Facebookカウントの取得
if ( !function_exists( 'get_facebook_count' ) ):
function get_facebook_count($url = null) {
  if (!is_sns_share_buttons_count_visible())
    return null;

  if (is_scc_facebook_exists()) {
    return scc_get_share_facebook();
  } else {
    return fetch_facebook_count($url);
  }
}
endif;

//はてブ数の取得
if ( !function_exists( 'fetch_hatebu_count_raw' ) ):
function fetch_hatebu_count_raw($url){
  //取得するURL(ついでにURLエンコード)
  $encoded_url = rawurlencode($url);
  //オプションの設定
  $args = array( 'sslverify' => true );
  //Facebookにリクエストを送る
  $response = wp_remote_get( 'http://api.b.st-hatena.com/entry.count?url='.$encoded_url, $args );
  $res = 0;

  //取得に成功した場合
  if (!is_wp_error( $response ) && $response["response"]["code"] === 200) {
    $body = $response['body'];
    $res = !empty($body) ? $body : 0;
  }
  return intval($res);
}
endif;

if ( !function_exists( 'fetch_hatebu_count' ) ):
function fetch_hatebu_count($url = null) {

  global $post;
  $transient_id = TRANSIENT_SHARE_PREFIX.'hatebu_'.$post->ID;
  //DBキャッシュからカウントの取得
  if (is_sns_share_count_cache_enable()) {
    $count = get_transient( $transient_id );
    if ( is_numeric($count) ) {
      // _edump(
      //   array('value' => $transient_id.'-'.$count, 'file' => __FILE__, 'line' => __LINE__),
      //   'label', 'tag', 'ade5ac'
      // );
      return $count;
    }
  }


  if (!$url) {
    $url = get_the_permalink();
  }
  $res = fetch_hatebu_count_raw($url);
  //別スキームカウントの取得
  if (is_sns_share_count_cache_enable() && is_another_scheme_sns_share_count()) {
    $res = $res + fetch_hatebu_count_raw(get_another_scheme_url($url));
  }

  //DBキャッシュへ保存
  if (is_sns_share_count_cache_enable()) {
    set_transient( $transient_id, $res, HOUR_IN_SECONDS * get_sns_share_count_cache_interval() );
  }

  return $res;
}
endif;

//はてブカウントの取得
if ( !function_exists( 'get_hatebu_count' ) ):
function get_hatebu_count($url = null) {
  if (!is_sns_share_buttons_count_visible())
    return null;

  if (is_scc_hatebu_exists()) {
    return scc_get_share_hatebu();
  } else {
    return fetch_hatebu_count($url);
  }
}
endif;

//Google+シェア数の取得
if ( !function_exists( 'fetch_google_plus_count_raw' ) ):
function fetch_google_plus_count_raw($url){
  $query = 'https://apis.google.com/_/+1/fastbutton?url=' . urlencode( $url );
  //URL（クエリ）先の情報を取得
  $args = array( 'sslverify' => true );
  $result = wp_remote_get($query, $args);
  $res = 0;
  if (!is_wp_error($result)) {
    // 正規表現でカウント数のところだけを抽出
    preg_match( '/\[2,([0-9.]+),\[/', $result["body"], $count );
    $res = isset($count[1]) ? intval($count[1]) : 0;
  }
  return intval($res);
}
endif;

//Google＋カウントの取得
if ( !function_exists( 'fetch_google_plus_count' ) ):
function fetch_google_plus_count($url = null) {

  global $post;
  $transient_id = TRANSIENT_SHARE_PREFIX.'google_plus_'.$post->ID;
  //DBキャッシュからカウントの取得
  if (is_sns_share_count_cache_enable()) {
    $count = get_transient( $transient_id );
    if ( is_numeric($count) ) {
      return $count;
    }
  }

  if (!$url) {
    $url = get_the_permalink();
  }
  $res = fetch_google_plus_count_raw($url);
  //別スキームカウントの取得
  if (is_sns_share_count_cache_enable() && is_another_scheme_sns_share_count()) {
    $res = $res + fetch_google_plus_count_raw(get_another_scheme_url($url));
  }

  //DBキャッシュへ保存
  if (is_sns_share_count_cache_enable()) {
    set_transient( $transient_id, $res, HOUR_IN_SECONDS * get_sns_share_count_cache_interval() );
  }

  // 共有数を表示
  return $res;
}
endif;

//Google＋カウントの取得
if ( !function_exists( 'get_google_plus_count' ) ):
function get_google_plus_count($url = null) {
  if (!is_sns_share_buttons_count_visible())
    return null;

  if (is_scc_gplus_exists()) {
    return scc_get_share_gplus();
  } else {
    return null;
  }
}
endif;

//Pocketストック数の取得
if ( !function_exists( 'fetch_pocket_count_raw' ) ):
function fetch_pocket_count_raw($url){
  $res = 0;
  $url = urlencode($url);
  $query = 'https://widgets.getpocket.com/v1/button?label=pocket&count=horizontal&v=1&url='.$url.'&src=' . $url;
  //URL（クエリ）先の情報を取得
  $args = array( 'sslverify' => true );
  $result = wp_remote_get($query, $args);
  //var_dump($result["body"]);
  //_v($result);
  if (!is_wp_error($result)) {
    // 正規表現でカウント数のところだけを抽出
    $body = isset($result["body"]) ? $result["body"] : null;
    if ($body) {
      preg_match( '/<em id="cnt">([0-9.]+)<\/em>/i', $result["body"], $count );
      $res = isset($count[1]) ? intval($count[1]) : 0;
    }
  }

  return intval($res);
}
endif;

//Pocketカウントの取得
if ( !function_exists( 'fetch_pocket_count' ) ):
function fetch_pocket_count($url = null) {

  global $post;
  $transient_id = TRANSIENT_SHARE_PREFIX.'pocket_'.$post->ID;
  //DBキャッシュからカウントの取得
  if (is_sns_share_count_cache_enable()) {
    $count = get_transient( $transient_id );
    if ( is_numeric($count) ) {
      return $count;
    }
  }
  $res = 0;

  if (!$url) {
    $url = get_the_permalink();
  }
  $res = fetch_pocket_count_raw($url);
  //別スキームカウントの取得
  if (is_sns_share_count_cache_enable() && is_another_scheme_sns_share_count()) {
    $res = $res + fetch_pocket_count_raw(get_another_scheme_url($url));
  }

  //DBキャッシュへ保存
  if (is_sns_share_count_cache_enable()) {
    set_transient( $transient_id, $res, HOUR_IN_SECONDS * get_sns_share_count_cache_interval() );
  }

  // 共有数を表示
  return $res;
}
endif;

//Pocketカウントの取得
if ( !function_exists( 'get_pocket_count' ) ):
function get_pocket_count($url = null) {
  if (!is_sns_share_buttons_count_visible())
    return null;

  if (is_scc_pocket_exists()) {
    return scc_get_share_pocket();
  } else {
    return fetch_pocket_count($url);
  }
}
endif;

//SNS Count Cacheプラグインはインストールされているか
function is_scc_exists(){
  return function_exists('scc_get_share_twitter');
}

//ツイート数取得関数が存在しているか
function is_scc_twitter_exists(){
  return function_exists('scc_get_share_twitter');
}

//Facebookシェア数取得関数が存在しているか
function is_scc_facebook_exists(){
  return function_exists('scc_get_share_facebook');
}

//Google＋シェア数取得関数が存在しているか
function is_scc_gplus_exists(){
  return function_exists('scc_get_share_gplus');
}

//はてブ数取得関数が存在しているか
function is_scc_hatebu_exists(){
  return function_exists('scc_get_share_hatebu');
}

//Pocketストック数取得関数が存在しているか
function is_scc_pocket_exists(){
  return function_exists('scc_get_share_pocket');
}

//トータルシェア数取得関数が存在しているか
function is_scc_total_exists(){
  return function_exists('scc_get_share_total');
}

//feedly購読者数取得関数が存在しているか
function is_scc_feedly_exists(){
  return function_exists('scc_get_follow_feedly');
}

//Push7購読者数取得関数が存在しているか
function is_scc_push7_exists(){
  return function_exists('scc_get_follow_push7');
}


//シェア対象ページのURLを取得する
if ( !function_exists( 'get_share_page_url' ) ):
function get_share_page_url(){
  // if ( is_singular() ) {
  //   $url = get_the_permalink();
  // } else {
  //   $url = home_url();
  // }
  $url = get_requested_url();
  return $url;
}
endif;

//シェア対象ページのタイトルを取得する
if ( !function_exists( 'get_share_page_title' ) ):
function get_share_page_title(){
  if ( is_singular() ) {
    $title = get_the_title();
  } else {
    $title = wp_get_document_title();
  }
  return $title;
}
endif;

//Twitter IDを含めるURLパラメータを取得
function get_twitter_via_param(){
  if ( get_the_author_twitter_id() && is_twitter_id_include() ) {
    return '&amp;via='.get_the_author_twitter_id();
  }
}

//ツイート後にフォローを促すパラメータを取得
function get_twitter_related_param(){
  if ( get_the_author_twitter_id() && is_twitter_related_follow_enable() ) {
    return '&amp;related='.get_the_author_twitter_id();//.':フォロー用の説明文';
  }
}

//TwitterのシェアURLを取得
if ( !function_exists( 'get_twitter_share_url' ) ):
function get_twitter_share_url(){
  $hash_tag = null;
  if (get_twitter_hash_tag()) {
    $hash_tag = '+'.urlencode( get_twitter_hash_tag() );
  }
  return 'https://twitter.com/intent/tweet?text='.urlencode( get_share_page_title() ).$hash_tag.'&amp;url='.
  urlencode( get_share_page_url() ).
  get_twitter_via_param(). //ツイートにメンションを含める
  get_twitter_related_param();//ツイート後にフォローを促す
}
endif;

//FacebookのシェアURLを取得
if ( !function_exists( 'get_facebook_share_url' ) ):
function get_facebook_share_url(){
  return '//www.facebook.com/sharer/sharer.php?u='.urlencode( get_share_page_url() ).'&amp;t='. urlencode( get_share_page_title() );//ツイート後にフォローを促す
}
endif;

//はてブのシェアURLを取得
if ( !function_exists( 'get_hatebu_share_url' ) ):
function get_hatebu_share_url(){
  $url = get_share_page_url();
  if (strpos($url, 'https://') === 0) {
    $u = preg_replace('/https:\/\//', 's/', $url);
  } else {
    $u = preg_replace('/http:\/\//', '', $url);
  }
  return '//b.hatena.ne.jp/entry/'.$u;
}
endif;

//Google+のシェアURLを取得
if ( !function_exists( 'get_google_plus_share_url' ) ):
function get_google_plus_share_url(){
  return '//plus.google.com/share?url='.rawurlencode( get_share_page_url() );
}
endif;

//PocketのシェアURLを取得
if ( !function_exists( 'get_pocket_share_url' ) ):
function get_pocket_share_url(){
  return '//getpocket.com/edit?url='.get_share_page_url();
}
endif;

//LINEのシェアURLを取得
if ( !function_exists( 'get_line_share_url' ) ):
function get_line_share_url(){
  return '//timeline.line.me/social-plugin/share?url='.urlencode(get_share_page_url());
}
endif;

//PinterestのシェアURLを取得
if ( !function_exists( 'get_pinterest_share_url' ) ):
function get_pinterest_share_url(){
  return '//www.pinterest.com/pin/create/button/?url='.urlencode(get_share_page_url());
}
endif;

//PinterestのシェアURLを取得
if ( !function_exists( 'get_copy_share_url' ) ):
function get_copy_share_url(){
  if (is_amp()) {
    return get_template_directory_uri().'/lib/common/copy.php?title='.urlencode( get_share_page_title() ).'&amp;url='.urlencode(get_share_page_url());
  } else {
    return 'javascript:void(0)';
  }
}
endif;

//シェアボタンを表示するか
if ( !function_exists( 'is_sns_share_buttons_visible' ) ):
function is_sns_share_buttons_visible($option){
  return (is_sns_bottom_share_buttons_visible() && $option == SS_BOTTOM) ||
         (is_sns_top_share_buttons_visible() && $option == SS_TOP);
}
endif;

//Twitterシェアボタンを表示するか
if ( !function_exists( 'is_twitter_share_button_visible' ) ):
function is_twitter_share_button_visible($option){
  return (is_bottom_twitter_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_twitter_share_button_visible() && $option == SS_TOP);
}
endif;

//Facebookシェアボタンを表示するか
if ( !function_exists( 'is_facebook_share_button_visible' ) ):
function is_facebook_share_button_visible($option){
  return (is_bottom_facebook_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_facebook_share_button_visible() && $option == SS_TOP);
}
endif;

//はてブシェアボタンを表示するか
if ( !function_exists( 'is_hatebu_share_button_visible' ) ):
function is_hatebu_share_button_visible($option){
  return (is_bottom_hatebu_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_hatebu_share_button_visible() && $option == SS_TOP);
}
endif;

//Google+シェアボタンを表示するか
if ( !function_exists( 'is_google_plus_share_button_visible' ) ):
function is_google_plus_share_button_visible($option){
  return (is_bottom_google_plus_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_google_plus_share_button_visible() && $option == SS_TOP);
}
endif;

//Pocketシェアボタンを表示するか
if ( !function_exists( 'is_pocket_share_button_visible' ) ):
function is_pocket_share_button_visible($option){
  return (is_bottom_pocket_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_pocket_share_button_visible() && $option == SS_TOP);
}
endif;

//LINE@シェアボタンを表示するか
if ( !function_exists( 'is_line_at_share_button_visible' ) ):
function is_line_at_share_button_visible($option){
  return (is_bottom_line_at_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_line_at_share_button_visible() && $option == SS_TOP);
}
endif;

//Pinterestシェアボタンを表示するか
if ( !function_exists( 'is_pinterest_share_button_visible' ) ):
function is_pinterest_share_button_visible($option){
  return (is_bottom_pinterest_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_pinterest_share_button_visible() && $option == SS_TOP);
}
endif;

//コピーシェアボタンを表示するか
if ( !function_exists( 'is_copy_share_button_visible' ) ):
function is_copy_share_button_visible($option){
  return (is_bottom_copy_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_copy_share_button_visible() && $option == SS_TOP);
}
endif;

