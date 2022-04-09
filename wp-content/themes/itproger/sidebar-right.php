<div class="sidebar-right">
    <div class="top-news">
        <h2 class="popular-news">Популярные новости</h2>
        

        <ul>
 
            <?php
            $args = array( 'posts_per_page' => 5 );
            $myposts = pvc_get_most_viewed_posts( $args );
            foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
            
            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
            
            <?php
            endforeach;
            wp_reset_postdata(); ?>
        
        </ul>
    </div>
</div>