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
        <?php get_sidebar('left')?>
    </div>
    <div class="news">
        <?php if (have_posts()) {
            while(have_posts()){
                the_post();
                $category = get_the_category();
                $cat_link = get_category_link($category);?>
                
                <p><?php echo $category[0]->cat_name;?></p> <!--Имя рубрики-->
                <a href="<?php echo $cat_link;?>">ссылка на станицу</a> <!--ссылка на станицу-->
                <div>
                    <a href="<?php the_permalink();?>"> <!--Выводит URL поста-->
                        <?php the_title();?><!--заголовок записи-->
                    </a>
                </div>
                <?php the_content();?><!--текст записи-->
                <time> <?php the_date('j M Y');?></time><!-- вывод числа создания записи-->
                <?php the_excerpt()?>                   <!-- вывод отрвка в теге Р-->
                    <p><?php echo get_the_excerpt()?></p>   <!-- вывод отрвка без тега Р-->
                <br><br>
            <?php}?>
        <?php}?>
    </div>
    <div class= "right-sidebar">
        <?php get_sidebar('right')?>
    </div>
</div>

<?php get_footer(); ?>