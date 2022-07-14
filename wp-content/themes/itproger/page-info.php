<?php /*Template Name: Contacts-Politics-About_us*/?>
<?php  get_header();?>
<main>
    <div class="blog">
        <div class="left-sidebar">
            <?php dynamic_sidebar('left-sidebar');?>
        </div>
        <div class="news">
            <div class="article">
                <div class="block-image-logotype">
                    <a href="/" ><?php dynamic_sidebar( 'logotype');?></a>
                </div>
                <div class="block-name">
                    <p>ITSTALKER</p>
                    <p>Последние статьи и новости в IT сфере, и все что с ней связано.</p>
                </div>
            </div>
            <div class="article">
                <?php
                    if( have_posts() ){
                        while( have_posts() ){
                            the_post();
                            ?>
                            <div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
                                <h1><?php the_title(); ?></h1>
                                <?php the_content(); ?>
                            </div>
                            <?php
                        }
                    }?>
            </div>
        </div>
        <div class="right-sidebar">
            <div class="article panel-socials-networks">
                <div class="link">
                    <a href="/">
                        <div class="social-network-contact">
                            <img class="icon-social-network" src="<?php echo esc_url( get_template_directory_uri() . "/assets/img/inet.png");?>" alt="inet">
                            <div class="name-social-network">
                                <h2>Сайт</h2>
                                <p>/itstalker.ru</p>
                            </div>
                            
                        </div>
                    </a>
                </div>
                <div class="link">
                    <a href="/">
                        <div class="social-network-contact">
                            <img class="icon-social-network" src="<?php echo esc_url( get_template_directory_uri() . "/assets/img/tel.png");?>" alt="Telegram">
                            <div class="name-social-network">
                                <h2>Telegram</h2>
                                <p>/Telegram</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="link">
                    <a href="/">
                        <div class="social-network-contact">
                            <img class="icon-social-network" src="<?php echo esc_url( get_template_directory_uri() . "/assets/img/email.png");?>" alt="Email">
                            <div class="name-social-network">
                                <h2>Почта</h2>
                                <p>/Email</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="link">
                    <a href="/">
                        <div class="social-network-contact">
                            <img class="icon-social-network" src="<?php echo esc_url( get_template_directory_uri() . "/assets/img/vk.png");?>" alt="VK">
                            <div class="name-social-network">
                                <h2>ВКонтакте</h2>
                                <p>/Vk</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>



<?php get_footer(); ?>