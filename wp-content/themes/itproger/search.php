<?php  get_header();?>
<main>
    <img class="background_img"
        src="<?php echo esc_url( get_template_directory_uri() . "/assets/img/фон.jpg"); ?>"
        alt="<?php echo esc_attr("фон"); ?>"
    />


    <div class="blog">
        <div class="left-sidebar">
            <?php get_sidebar('left')?>
        </div>
        <div class="news">
        <h2 class="popular-news"><?php single_cat_title(); ?></h2>
            <?php
                if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                        ?>
                            <div class="article">
                                <div class="article-main">
                                    <div class="description-post">
                                        <div class="autor-post">
                                            <div class="avatar-autor">
                                                <?php echo get_avatar( get_the_author_meta('user_email'), 32 ); ?>
                                            </div>
                                            <p class="name-autor"><?php the_author();?></p>
                                        </div>
                                        <div <?php post_class();?> id="post-<?php the_ID();?>">
                                            <h2 class="title-post"><a href="<?php the_permalink();?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title();?></a></h2>
                                            <p class="except-post"><a href="<?php the_permalink();?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php echo get_the_excerpt();?></a></p>
                                        </div>
                                    </div>
                                    <div class="icon-post">
                                        <a href="<?php the_permalink();?>"><?php echo get_the_post_thumbnail( $post->ID , 'thumbnail' );?></a>
                                    </div>
                                </div>
                                <div class="article-footer">
                                    <div class="post-views post-53 entry-meta">
                                        <span class="post-views-icon dashicons dashicons-visibility"></span>
                                        <span class="post-views-label"> </span>
                                        <span class="post-views-count"><strong><?php echo pvc_get_post_views($post->ID) ?></strong></span>
                                    </div>
                                </div>
                            </div>
                        <?php
                    }
                }else
            ?>
        </div>
        <div class= "right-sidebar">
            <?php get_sidebar('right')?>
        </div>
    </div>


    <?php pagination() ?>
</main>


<?php get_footer(); ?>