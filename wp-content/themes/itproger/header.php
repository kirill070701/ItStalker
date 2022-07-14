<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title("")?></title>
    <?php wp_head(); ?>
    <?php if (is_singular('post')) {
        echo '<link rel="stylesheet" href="' . get_template_directory_uri() . '/assets/css/post.css">';
    }?>
</head>
<body>
    <?php ?>
    <header>
        <div class="menu-mobil">
            <div class="one-radius">
                <div class="two-radius">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                
            </div>
        </div>
        <div class="logotype">
            <a href="/"><?php  dynamic_sidebar( 'logotype-sidebar' ); ?></a>
        </div>
        <div class="search">
            <form action="<?php echo home_url( '/' ) ?>" class="form-search">
                <input class="searchField" type="search" name="s" autocomplete="off" placeholder="Поиск">
            </form>
        </div>
        <div class="panel-menu">
            <?php 
            if (is_user_logged_in()) {
                echo get_avatar(get_current_user_id(), 58, '', 'avatar', array('class' => "user-img"));
            }else{?>
                <img class="user-img avatar"
                    src="<?php echo esc_url( get_template_directory_uri() . "/assets/img/user.png"); ?>"
                    alt="<?php echo esc_attr("user"); ?>"
                />        
            <?php }?>
        </div>
    </header>
       
    <div class="autorization">
        <div class="field-autorization" id="field-autorization">
            <?php if (is_user_logged_in()) {?>
                <div class="info-user">
                    <div class="panel-info-user">
                        <div class="info-avatar-user">
                            <div class="avatar-user">
                                <?php global $wpdb;
                                    $avatar = $wpdb->get_var("SELECT `avatar` FROM `avatars_users` WHERE `id_users` = 2")
                                ?>
                                <?php echo get_avatar(get_current_user_id(), 100, '', 'avatar-user', array('class' => "user-img"));?>
                                <!-- <div class="new-foto">
                                    <input class="button-new-foto" type="submit" value="Изменить">
                                </div> -->
                            </div>
                        </div>
                        <div class="info-name-user">
                            <?php 
                                global $current_user;
                                get_currentuserinfo();
                            ?>
                            <p class="name-user"> <?php echo $current_user->display_name; ?></p>
                        </div>
                    </div>
                    <form action="" method="post" class="button-exit">
                        <input type="hidden" name="logout">
                        <input type="submit" class="button-enter" value="Выход">
                    </form>
                </div>
            <?php } else{ ?>
                <div class="panel">
                    <div class="panel-autorization-user">
                        <p class='text-autorization'>Авторизация</p>
                        <form name="loginform" class="loginform" action="<?php bloginfo('url') ?>/wp-login.php" method="post"> 
                            <input type="text" name="log" id="user_login" class="input-data-user" placeholder="Email"/>
                            <canvas class='line'></canvas>
                            <input type="password" name="pwd" id="user_pass" class="input-data-user" placeholder="Пароль"/>
                            <canvas class='line'></canvas>
                            <input type="submit" name="wp-submit" id="wp-submit" class="button-enter" value="Войти" /> 
                            <input type="hidden" name="redirect_to" value="<?php bloginfo('url') ?>/" /> 
                            <input type="hidden" name="testcookie" value="1" />
                        </form>
                        <div class="back-right">
                            <button class="button-back" id="button-back-right">
                                <img class="button-back"
                                    src="<?php echo esc_url( get_template_directory_uri() . "/assets/img/1.png"); ?>"
                                    alt="<?php echo esc_attr("назад"); ?>" >
                            </button>
                        </div>
                    </div>
                    <div class="panel-user">
                        <a href="/"><?php  dynamic_sidebar( 'logotype' ); ?></a>
                        <p class="welcome">Добро пожаловать!</p>
                        <input type="button" id="button-registration" value="Регистрация">
                        <input type="button" id="button-autorization" value="Войти">
                    </div>
                    <div class="panel-registration-user">
                    <p class='text-registrtion'>Регистрация</p>
                        <form action="/" class="loginform" method="post">
                            <input type="hidden" name="registration">
                            <input type="email" name="email" class="input-data-user" id="email" placeholder="email" required autocomplete="off">
                            <canvas class='line'></canvas>
                            <input type="text" name="nickname" class="input-data-user" id="nickname" placeholder="Имя" required autocomplete="off">
                            <canvas class='line'></canvas>
                            <input type="password" name="pass" class="input-data-user" id="password" placeholder="Пароль" required autocomplete="off">
                            <canvas class='line'></canvas>
                            <input type="password" id="cor-password" class="input-data-user" placeholder="Повторите пароль" autocomplete="off">
                            <canvas class='line'></canvas>
                            <input type="submit" class="button-enter" id="button-enter" value="Зарегистрироваться">
                        </form>
                        <div class="back-left">
                            <button class="button-back" id="button-back-left">
                                <img class="button-back"
                                    src="<?php echo esc_url( get_template_directory_uri() . "/assets/img/1.png"); ?>"
                                    alt="<?php echo esc_attr("назад"); ?>" >
                            </button>
                        </div>
                    </div>
                </div> 
            <?php } ?>
        </div>
    </div>



