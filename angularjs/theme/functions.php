<?
add_theme_support( 'post-thumbnails' ); 
function new_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'new_excerpt_length');
function div_wrapper($content) {
    $pattern = '~<iframe.*</iframe>|<embed.*</embed>~';
    preg_match_all($pattern, $content, $matches);
    foreach ($matches[0] as $match) {
        $wrappedframe = '<div fit-vids>' . $match . '</div>';
        $content = str_replace($match, $wrappedframe, $content);
    }
    return $content;    
}
add_filter('the_content', 'div_wrapper');
function add_taxonomies_to_pages() {
	register_taxonomy_for_object_type( 'category', 'page' );
}
add_action( 'init', 'add_taxonomies_to_pages' );
@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M' );
@ini_set( 'max_execution_time', '300' );
?>