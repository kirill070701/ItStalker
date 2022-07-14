<?php

function blog_assets(){

    wp_enqueue_style('style', get_template_directory_uri() . '/style.css');

    wp_enqueue_style('fonts', get_template_directory_uri() . '/assets/css/fonts.css');

    wp_enqueue_style('autorization', get_template_directory_uri() . '/assets/css/autorization.css');

    wp_enqueue_style('category', get_template_directory_uri() . '/assets/css/category.css');

    wp_enqueue_style('adaptation', get_template_directory_uri() . '/assets/css/adaptation.css');


    if (get_page_uri() == "contacts" or get_page_uri() == "about-us" or get_page_uri() == "privacy-policy") {
        wp_enqueue_style('page-contacts', get_template_directory_uri() . '/assets/css/page-contacts.css');
    }
    
    wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', 'https://code.jquery.com/jquery-3.6.0.min.js');
	wp_enqueue_script( 'jquery' );

    wp_enqueue_script('script', get_template_directory_uri() . '/assets/js/script.js', array('jquery'));
    wp_enqueue_script('registration', get_template_directory_uri() . '/assets/js/registration.js', array('jquery'));
    wp_enqueue_script('domNavigation', get_template_directory_uri() . '/assets/js/DomNavigation.js', array('jquery'));
    
}

show_admin_bar(false);

add_theme_support('post-thumbnails');

add_action('wp_enqueue_scripts', 'blog_assets');

if ( function_exists('register_sidebar') )  
    register_sidebar();

function register_left_sidebars(){
    register_sidebar( array(
    'name'          => "left-panel-saite",
    'id'            => 'left-sidebar',
    'description'   => 'Эти виджеты будут показаны в левой колонке сайта',
    'before_widget' => '<div  class="left-section-list ">', 
    'after_widget'  => '</div>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>'
    ) );
}

function register_header_sidebars(){
    register_sidebar( array(
    'name'          => "header-panel-saite",
    'id'            => 'logotype-sidebar',
    'description'   => 'Логотип сайта 1',
    'before_widget' => '<div  class="logo">', 
    'after_widget'  => '</div>',
    'before_title'  => '<h2 class="widget-logotype">',
    'after_title'   => '</h2>'
    ) );
}

function register_logotype_sidebars(){
    register_sidebar( array(
    'name'          => "logotype-saite",
    'id'            => 'logotype',
    'description'   => 'Логотип сайта 2',
    'before_widget' => '<div  class="logo">', 
    'after_widget'  => '</div>',
    'before_title'  => '<h2 class="widget-logotype">',
    'after_title'   => '</h2>'
    ) );
}

add_action( 'widgets_init', 'register_left_sidebars' );
add_action( 'widgets_init', 'register_logotype_sidebars' );
add_action( 'widgets_init', 'register_header_sidebars' );

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

add_action( 'wp_login_failed', 'my_front_end_login_fail' );


function wpdocs_custom_login() {
    if( isset( $_POST['logout']))
        wp_logout();     
}

add_action( 'after_setup_theme', 'wpdocs_custom_login');

function registrition_user(){
    if( isset( $_POST['registration'])){
        wp_insert_user(
            array(
                'user_login'    => $_POST['email'],
                'user_email'    => $_POST['email'],
                'user_pass'     => $_POST['pass'],
                'nickname'      => $_POST['nickname'],
                'display_name'  => $_POST['nickname'],
            )
        );
    }
}
add_action( 'registration_new_user', 'registrition_user');
do_action('registration_new_user');


?>
