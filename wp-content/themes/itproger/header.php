<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php wp_head(); ?>
    <?php if (is_singular('post')) {
        echo '<link rel="stylesheet" href="' . get_template_directory_uri() . '/assets/css/post.css">';
    }?>
</head>
<body>
    <header>
        <div class="logotype">
            <?php 
                $image_array = get_field('logo');
                if ( $image_array ) { ?>
                    <img class="logo"
                        src="<?php echo esc_url($image_array['url']); ?>"
                        alt="<?php echo esc_attr($image_array['alt']); ?>"
                    />
            <?php 
            } ?>
        </div>
        <div class="search">
            <input class="searchField" type="text" placeholder="Поиск">
        </div>
        <div class="panel-menu">
            <?php wp_nav_menu( [ 'menu' => 'Главное меню' ] ); ?>
        </div>
    </header>   