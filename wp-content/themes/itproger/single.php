<?php  
    get_header(); 
    $category = get_the_category();
    $cat_link = get_category_link($category);
?>

<div class="blog">
    <div class="left-sidebar">
        <?php get_sidebar('left');?>
    </div>
    <div class="news">
        <?php
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                ?>
                <div class="article">
                    <div class="description-post ">
                        <div class="header-post">
                            <div class="autor-post">
                                <div class="avatar-autor">
                                    <?php echo get_avatar( get_the_author_meta('user_email'), 32 ); ?>
                                </div>
                                <p class="name-autor"><?php the_author();?></p>
                            </div>
                            <div class="datе-post">
                                <p><?php echo $category[0]->cat_name;?></p>
                                <time> <?php the_date('j M Y');?></time>
                            </div>
                        </div>
                        <div <?php post_class();?> id="post-<?php the_ID();?>">
                            <h1 class="title-post"><?php the_title();?></h1>
                            <?php the_content();?>
                        </div>
                    </div>
                </div>
            <?php
            }
        }
        ?>
        <div class="sample-posts">  
            <?php
            $categories = get_the_category($post->ID);
            if ($categories) {
                $category_ids = array();
                foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;
                $args=array(
                    'category__in' => $category_ids, 
                    'post__not_in' => array($post->ID), //Не выводить текущую запись
                    'showposts'=>5, // Указываем сколько похожих записей выводить
                    'caller_get_posts'=>1
                );
                $my_query = new wp_query($args);
                if( $my_query->have_posts() ) {
                    ?>
                    <ul>
                        <?php
                            while ($my_query->have_posts()) {
                                $my_query->the_post();
                                ?>
                                <div class="additional-article">
                                    <div class="article-additional-main">
                                        <div class="description-post">
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
                        ?>
                    </ul>
                    <?php
                }
                wp_reset_query();
            }
            ?>
        </div>
    </div>
    <div class= "right-sidebar">
        <?php get_sidebar('right')?>
    </div>
</div>



<?php get_footer(); ?>