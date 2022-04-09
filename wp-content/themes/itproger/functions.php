<?php

function blog_assets(){

    wp_enqueue_style('style', get_template_directory_uri() . '/style.css');

    wp_enqueue_style('style', get_template_directory_uri() . '/assets/css/fonts.css');

    wp_enqueue_script('script', get_template_directory_uri() . '/assets/js/script.js', array(), '20151215', true);
}

show_admin_bar(false);

add_theme_support('post-thumbnails');

add_action('wp_enqueue_scripts', 'blog_assets');

if ( function_exists('register_sidebar') )  
    register_sidebar();

function register_left_sidebars(){
    register_sidebar( array(
    'name' => "left-panel-saite",
    'id' => 'left-sidebar',
    'description' => 'Эти виджеты будут показаны в левой колонке сайта',
    'before_widget' => '<div  class="left-section-list ">', 
    'after_widget' => '</div>',
    'before_title' => '<h2 class="widget-title">',
    'after_title' => '</h2>'
    ) );
}

add_action( 'widgets_init', 'register_left_sidebars' );

add_theme_support('menus');

function pagination(){
    $args = array(
        'show_all'     => false, // показаны все страницы участвующие в пагинации
        'end_size'     => 2,     // количество страниц на концах
        'mid_size'     => 2,     // количество страниц вокруг текущей
        'prev_next'    => true,  // выводить ли боковые ссылки "предыдущая/следующая страница".
        'prev_text'    => __('«'),
        'next_text'    => __('»'),
        'add_args'     => false, // Массив аргументов (переменных запроса), которые нужно добавить к ссылкам.
        'add_fragment' => '',     // Текст который добавиться ко всем ссылкам.
        'screen_reader_text' => __( 'Posts navigation' ),
    );
    the_posts_pagination($args);
}
/*
function gt_get_post_view() {
    $count = get_post_meta( get_the_ID(), 'post_views_count', true );
    return $count ;
}
function gt_set_post_view() {
    $key = 'post_views_count';
    $post_id = get_the_ID();
    $count = (int) get_post_meta( $post_id, $key, true );
    $count++;
    update_post_meta( $post_id, $key, $count );
}
function gt_posts_column_views( $columns ) {
    $columns['post_views'] = 'Views';
    return $columns;
}
function gt_posts_custom_column_views( $column ) {
    if ( $column === 'post_views') {
        echo gt_get_post_view();
    }
}
add_filter( 'manage_posts_columns', 'gt_posts_column_views' );
add_action( 'manage_posts_custom_column', 'gt_posts_custom_column_views' );*/
?>
