<?php
/**
 * Author: Todd Motto | @toddmotto
 * URL: html5blank.com | @html5blank
 * Custom functions, support, custom post types and more.
 */


require_once "_/modules/is-debug.php";
if (HTML5_DEBUG) {
  require_once "_/modules/debug.php";
}
require_once "_/modules/sync.php";

/*------------------------------------*\
    External Modules/Files
\*------------------------------------*/

// Load any external files you have here

/*------------------------------------*\
    Theme Support
\*------------------------------------*/

if (!isset($content_width))
{
    $content_width = 900;
}

if (function_exists('add_theme_support'))
{

    // Add Thumbnail Theme Support
    add_theme_support('post-thumbnails');


   //  add_image_size('custom-size', 700, 200, true); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');

    // Add Support for Custom Backgrounds - Uncomment below if you're going to use
    /*add_theme_support('custom-background', array(
    'default-color' => 'FFF',
    'default-image' => get_template_directory_uri() . '/_/img/bg.jpg'
    ));*/
}

/*------------------------------------*\
    Functions
\*------------------------------------*/

// HTML5 Blank navigation
function html5blank_nav()
{
    wp_nav_menu(
    array(
        'theme_location'  => 'main-menu',
        'menu'            => '',
        'container'       => 'div',
        'container_class' => 'menu-{menu slug}-container',
        'container_id'    => '',
        'menu_class'      => 'menu',
        'menu_id'         => '',
        'echo'            => true,
        'fallback_cb'     => 'wp_page_menu',
        'before'          => '',
        'after'           => '',
        'link_before'     => '',
        'link_after'      => '',
        'items_wrap'      => '<ul>%3$s</ul>',
        'depth'           => 0,
        'walker'          => ''
        )
    );
}

// Load HTML5 Blank scripts (header.php)
function html5blank_header_scripts()
{
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {
      // Scripts minify
      wp_register_script('html5blankscripts-vendor', get_template_directory_uri() . '/_/scripts/dist/vendor.js', array(), '1.0.0');
      wp_enqueue_script('html5blankscripts-vendor');
      // Scripts minify
      wp_register_script('html5blankscripts-min', get_template_directory_uri() . '/_/scripts/dist/scripts.min.js', array('html5blankscripts-vendor'), '1.0.0');
      wp_enqueue_script('html5blankscripts-min');
    }
}

// Load HTML5 Blank conditional scripts
function html5blank_conditional_scripts()
{
    if (is_page('pagenamehere')) {
        // Conditional script(s)
        wp_register_script('scriptname', get_template_directory_uri() . '/_/scripts/scriptname.js', array('jquery'), '1.0.0');
        wp_enqueue_script('scriptname');
    }
}

// Load HTML5 Blank styles
function html5blank_styles()
{
   // Vendor CSS
   wp_register_style('html5blankvendorcss', get_template_directory_uri() . '/_/styles/dist/vendor.css', array(), '1.0');
   wp_enqueue_style('html5blankvendorcss');
   // Custom CSS
   wp_register_style('html5blankcssmin', get_template_directory_uri() . '/_/styles/dist/style.css', array('html5blankvendorcss'), '1.0');
   wp_enqueue_style('html5blankcssmin');
}

// Register HTML5 Blank Navigation
function register_html5_menu()
{
    register_nav_menus(array( // Using array to specify more menus if needed
        'main-menu' => __('Main Menu', 'html5blank') // Main Navigation
    ));
}

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}

// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function my_css_attributes_filter($var)
{
    return is_array($var) ? array() : '';
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list($thelist)
{
    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class($classes)
{
    global $post;
    if (is_home()) {
        $key = array_search('blog', $classes);
        if ($key > -1) {
            unset($classes[$key]);
        }
    } elseif (is_page()) {
        $classes[] = sanitize_html_class($post->post_name);
    } elseif (is_singular()) {
        $classes[] = sanitize_html_class($post->post_name);
    }

    return $classes;
}

// Remove the width and height attributes from inserted images
function remove_width_attribute( $html ) {
   $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
   return $html;
}


// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function html5wp_pagination()
{
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}

// Custom Excerpts
function html5wp_index($length) // Create 20 Word Callback for Index page Excerpts, call using html5wp_excerpt('html5wp_index');
{
    return 20;
}

// Create 40 Word Callback for Custom Post Excerpts, call using html5wp_excerpt('html5wp_custom_post');
function html5wp_custom_post($length)
{
    return 40;
}

// Create the Custom Excerpts callback
function html5wp_excerpt($length_callback = '', $more_callback = '')
{
    global $post;
    if (function_exists($length_callback)) {
        add_filter('excerpt_length', $length_callback);
    }
    if (function_exists($more_callback)) {
        add_filter('excerpt_more', $more_callback);
    }
    $output = get_the_excerpt();
    $output = apply_filters('wptexturize', $output);
    $output = apply_filters('convert_chars', $output);
    $output = '<p>' . $output . '</p>';
    echo $output;
}

// Custom View Article link to Post
function html5_blank_view_article($more)
{
    global $post;
    return '... <a class="view-article" href="' . get_permalink($post->ID) . '">' . __('View Article', 'html5blank') . '</a>';
}

// Remove Admin bar
function remove_admin_bar()
{
    return false;
}

// Remove 'text/css' from our enqueued stylesheet
function html5_style_remove($tag)
{
    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}

// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function remove_thumbnail_dimensions( $html )
{
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}

// Custom Gravatar in Settings > Discussion
function html5blankgravatar ($avatar_defaults)
{
    $myavatar = get_template_directory_uri() . '/_/img/gravatar.jpg';
    $avatar_defaults[$myavatar] = "Custom Gravatar";
    return $avatar_defaults;
}



/*------------------------------------*\
    Actions + Filters + ShortCodes
\*------------------------------------*/

// Add Actions
add_action('init', 'html5blank_header_scripts'); // Add Custom Scripts to wp_head
// add_action('wp_print_scripts', 'html5blank_conditional_scripts'); // Add Conditional Page Scripts
add_action('wp_enqueue_scripts', 'html5blank_styles'); // Add Theme Stylesheet
add_action('init', 'register_html5_menu'); // Add HTML5 Blank Menu
add_action('init', 'html5wp_pagination'); // Add our HTML5 Pagination

// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

// Add Filters
add_filter('avatar_defaults', 'html5blankgravatar'); // Custom Gravatar in Settings > Discussion
add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
// add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected classes (Commented out by default)
// add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected ID (Commented out by default)
// add_filter('page_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> Page ID's (Commented out by default)
add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
add_filter('the_excerpt', 'shortcode_unautop'); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
add_filter('the_excerpt', 'do_shortcode'); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
add_filter('excerpt_more', 'html5_blank_view_article'); // Add 'View Article' button instead of [...] for Excerpts
add_filter('show_admin_bar', 'remove_admin_bar'); // Remove Admin bar
add_filter('style_loader_tag', 'html5_style_remove'); // Remove 'text/css' from enqueued stylesheet
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to thumbnails
add_filter('post_thumbnail_html', 'remove_width_attribute', 10 ); // Remove width and height dynamic attributes to post images
add_filter('image_send_to_editor', 'remove_width_attribute', 10 ); // Remove width and height dynamic attributes to post images

// Remove Filters
remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether

// Shortcodes
// add_shortcode('html5_shortcode_demo', 'html5_shortcode_demo'); // You can place [html5_shortcode_demo] in Pages, Posts now.
// add_shortcode('html5_shortcode_demo_2', 'html5_shortcode_demo_2'); // Place [html5_shortcode_demo_2] in Pages, Posts now.

// Shortcodes above would be nested like this -
// [html5_shortcode_demo] [html5_shortcode_demo_2] Here's the page title! [/html5_shortcode_demo_2] [/html5_shortcode_demo]


/*------------------------------------*\
    ShortCode Functions
\*------------------------------------*/

// Shortcode Demo with Nested Capability
function html5_shortcode_demo($atts, $content = null)
{
    return '<div class="shortcode-demo">' . do_shortcode($content) . '</div>'; // do_shortcode allows for nested Shortcodes
}

// Shortcode Demo with simple <h2> tag
function html5_shortcode_demo_2($atts, $content = null) // Demo Heading H2 shortcode, allows for nesting within above element. Fully expandable.
{
    return '<h2>' . $content . '</h2>';
}


/*------------------------------------*\
    Custom functions
\*------------------------------------*/

// alter loop for custom front page
add_action("pre_get_posts", "prcs_custom_front_page");
function prcs_custom_front_page($wp_query) {
	if (is_admin()) return;
   if ( $wp_query->is_front_page() && $wp_query->is_main_query() ) {
      $wp_query->set('post_type', array('works'));
      // $wp_query->set('page_id', ''); // empty page id
      // fix conditional fucntions like is_front_page or is_single ect
      $wp_query->is_front_page = 1;
      $wp_query->is_home = 1;
      // $wp_query->is_archive = 1;
      // $wp_query->is_post_type_archive = 1;
      // $wp_query->is_page = 0;
      // $wp_query->is_singular = 0;
    }
}


// add_action("pre_get_posts", "prcs_hidden_posts");
// function prcs_hidden_posts($wp_query) {
// 	if (is_admin()) return;
//    if ( $wp_query->is_main_query() && !$wp_query->is_single) {
//       $wp_query->set( 'post__not_in', array(280, 310) );
//     }
// }


// hide posts with 'hidden' tag (except in single view)
add_action("pre_get_posts", "prcs_hidden_tag");
function prcs_hidden_tag($wp_query) {
	if (is_admin()) return;
  if(is_tag()) return;
   if ( $wp_query->is_main_query() && !$wp_query->is_single) {
      $wp_query->set( 'tag__not_in', array(3) ); // 'hidden' tag
    }
}


function prcs_thumbnail_data($size = 'medium') {
   $id = get_post_thumbnail_id();
   $img = wp_get_attachment_image_src( $id, $size );
   $mime = get_post_mime_type($id);
   $scale = intval( types_render_field('thumbnail-scale' , array()) ) / 100;

   $html = "";
   if ($mime == "image/gif") {
      $gif = wp_get_attachment_image_src( $id, 'full' );
      $img[0] = $gif[0];
      $html = wp_get_attachment_image($id, 'full');
   } else {
      $html = wp_get_attachment_image($id, $size);
   }

   if ($scale == 0) $scale = 1;
   return array(
      "url" => $img[0],
      "width" => $img[1] *= $scale,
      "height" => $img[2] *= $scale,
      "mime" => $mime,
      "html" => $html
   );
}

// srand(0);
function prcs_rnd_margins($min, $max, $steps) {
   if ($steps < 1) $steps = 1;
   $step_size = ($max - $min) / $steps;
   $top  = $min + rand(0, $steps) * $step_size;
   $left = $min + rand(0, $steps) * $step_size;
   return 'margin-top:' . $top . '%; margin-top:' . $top . 'vw; margin-left:' . $left . '%; ';
}

function prcs_time_ago($ptime) {
    $etime = time() - $ptime;
    if ($etime < 1){
        return '0 seconds';
    }
    $a = array( 365 * 24 * 60 * 60  =>  'year',
                 30 * 24 * 60 * 60  =>  'month',
                      24 * 60 * 60  =>  'day',
                           60 * 60  =>  'hour',
                                60  =>  'minute',
                                 1  =>  'second'
                );
    $a_plural = array( 'year'   => 'years',
                       'month'  => 'months',
                       'day'    => 'days',
                       'hour'   => 'hours',
                       'minute' => 'minutes',
                       'second' => 'seconds'
                );
    foreach ($a as $secs => $str){
        $d = $etime / $secs;
        if ($d >= 1){
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
        }
    }
}


/*------------------------------------*\
   Social Posts
\*------------------------------------*/

function prcs_should_insert_social($idx, $num_insert, $num_base, $offset=0.5) {
   $num_total = $num_insert + $num_base;
   $step = $num_total / $num_insert;
   if ($idx > $num_total-1) return false; // it's an error case
   // check if idx is in the insert list
   $insert_idx = 0;
   $n = 0;
   do {
      $insert_idx = floor($n * $step + $offset);
      if ($insert_idx == $idx) return true;
      $n++;
   } while ($insert_idx < $idx);
   return false;
}

// compare timestamps of two social post objects. for sorting
function prcs_compare_timestamps($obj1, $obj2) {
   $t1 = $obj1->timestamp;
   $t2 = $obj2->timestamp;
   if ($t1 == $t2) return 0;
   return ($t1 > $t2) ? -1 : 1; // sort DESC
}

// get a number of social posts from the database.
function prcs_get_social_posts($count) {
   $posts = array_merge(
      PrcsSync::get_instagram_posts($count),
      PrcsSync::get_twitter_posts($count)
   );
   usort($posts, 'prcs_compare_timestamps');
   return array_slice($posts, 0, $count);
}

function debug($thing) {
  echo '<pre>';
  print_r($thing);
  echo '</pre>';
}

/*------------------------------------*\
   Video Embedding
\*------------------------------------*/
// [bartag foo="foo-value"]
function prcs_vimeo_shortcode( $atts ) {
   // print_r($atts);
   $a = shortcode_atts( array(
     'width' => '640',
     'height' => '360',
     'autoplay' => '0',
     'loop' => '0'
   ), $atts );
   $id = $atts[0];
   if ( empty($id) ) return '';
   $autoplay = $a['autoplay'] == '0' || strtolower( $a['autoplay'] ) == 'false' ? 0 : 1;
   $loop = $a['loop'] == '0' || strtolower( $a['loop'] ) == 'false' ? 0 : 1;
   $video_url = sprintf(
      '//player.vimeo.com/video/%s?autoplay=%s&badge=0&byline=0&color=cccccc&loop=%s&portrait=0&title=1',
      $id,
      $autoplay,
      $loop
   );
   $width = intval($a['width']);
   $height = intval($a['height']);
   $aspect = $height / $width * 100;

   $out = '';
   $out .= sprintf('<div class="vimeo-outer" style="max-width:%spx;">', $width);
   $out .= sprintf('<div class="vimeo-inner" style="position:relative; width:100%%; padding-top:%s%%">', $aspect);
   $out .= sprintf(
   '<iframe class="vimeo" src="%s" width="100%%" height="100%%" style="position:absolute; top:0; left:0;" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>' . PHP_EOL,
      $video_url
   );
   $out .= '</div>';
   $out .= '</div>';

   return $out;
}
add_shortcode( 'vimeo', 'prcs_vimeo_shortcode' );


/*------------------------------------*\
   Metadata
\*------------------------------------*/
function prcs_schemaorg_publisher_extra_tags( $metatags ) {
    // Organization Postal Address
    $metatags[] = '<!-- Scope BEGIN: Organization Postal Address -->';
    $metatags[] = '<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
    $metatags[] = '<meta itemprop="streetAddress" content="Florianigasse 50/1/5" />';
    $metatags[] = '<meta itemprop="postalCode" content="1080" />';
    $metatags[] = '<meta itemprop="addressLocality" content="Vienna, Austria" />';
    $metatags[] = '</span> <!-- Scope END: Organization Postal Address -->';

    return $metatags;
}
add_filter( 'amt_schemaorg_publisher_extra', 'prcs_schemaorg_publisher_extra_tags' );

function prc_metatags_filter( $metatags ) {
    //var_dump($metatags);
    $exclude = array('article:author', 'author');
    // New array to hold the modified meta tags
    $metatags_new = array();
    foreach ( $metatags as $metatag ) {
      $skip = false;
      foreach ( $exclude as $ex ) {
         if ( strpos($metatag, $ex) !== false ) {
            $skip = true;
            break;
         }
      }
      if (!$skip) {
         $metatags_new[] = $metatag;
      }
    }
    return $metatags_new;
}
add_filter( 'amt_metadata_head', 'prc_metatags_filter' );

function prc_metatags_filter2( $metatags ) {
   if (!is_front_page) return $metatags;

   foreach ( $metatags as $metatag ) {
      if ( strpos($metatag, 'og:title') !== false || strpos($metatag, 'twitter:title') !== false) {
         $metatags_new[] = str_replace('Studio ', '', $metatag);
      } else {
         $metatags_new[] = $metatag;
      }
    }
    return $metatags_new;
}
add_filter( 'amt_metadata_head', 'prc_metatags_filter2' );


/*------------------------------------*\
   remove emojicons
\*------------------------------------*/
function disable_wp_emojicons() {

  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

  // filter to remove TinyMCE emojis
  add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}
add_action( 'init', 'disable_wp_emojicons' );
