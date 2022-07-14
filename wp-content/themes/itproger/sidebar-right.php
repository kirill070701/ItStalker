<div class="sidebar-right">
    <div class="top-news">
        <h2 class="popular-news">Популярные записи</h2>
        <ul>
            <?php
            $args = array( 'posts_per_page' => 7 );
            $myposts = pvc_get_most_viewed_posts( $args );
            foreach ( $myposts as $post ) : 
                setup_postdata( $post ); ?>
                <li>
                    <h3 class="title-top-post"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a><br></h3>
                    <p class="except-top-post"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php echo get_the_excerpt()?></a></p>
                    <div class="article-footer">
                        <div class="post-views post-53 entry-meta">
                            <span class="post-views-icon dashicons dashicons-visibility"></span>
                            <span class="post-views-label"> </span>
                            <span class="post-top-views-count"><strong><?php echo pvc_get_post_views($post->ID) ?></strong></span>
                        </div>
                    </div>
                </li>
                
                <?php
            endforeach;
            wp_reset_postdata(); ?>

        </ul>
    </div>
</div>