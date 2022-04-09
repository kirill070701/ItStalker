<?php  get_header(); ?>
<?php
    $category = get_the_category();
    $cat_link = get_category_link($category);
?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
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
                        <div class="article-footer">
                            
                        </div>
                    <?php
                }
            }
        ?>
    </div>
    <div class= "right-sidebar">
        <?php get_sidebar('right')?>
    </div>
</div>



<?php get_footer(); ?>