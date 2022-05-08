<?php
/**
 * The template for displaying image attachments.
 *
 * @package Palm Healing Lite
 */
get_header(); ?>
<div class="container">
	<div id="content_navigator">
     <div class="page_content">
        <section class="site-main">
			<?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                        <div class="entry-meta">
                            <?php
                                $palm_healing_lite_metadata = wp_get_attachment_metadata();
								/* translators: %s: for time and date*/
                                printf( wp_kses( 'Published <span class="entry-date"><time class="entry-date" datetime="%1$s">%2$s</time></span> at <a href="%3$s">%4$s &times; %5$s</a> in <a href="%6$s" rel="gallery">%7$s</a>', 'palm-healing-lite' ),
                                    esc_attr( get_the_date( 'c' ) ),
                                    esc_html( get_the_date() ),
                                    esc_url( wp_get_attachment_url() ),
                                    esc_attr($palm_healing_lite_metadata['width']),
                                    esc_attr($palm_healing_lite_metadata['height']),
                                    esc_url( get_permalink( $post->post_parent ) ),
                                    esc_html(get_the_title( $post->post_parent ))
                                );
                                edit_post_link( esc_html__( 'Edit', 'palm-healing-lite' ), '<span class="edit-link">', '</span>' );
                            ?>
                        </div><!-- .entry-meta -->
                        <nav role="navigation" id="image-navigation" class="image-navigation">
                            <div class="nav-previous"><?php previous_image_link( false, __( '<span class="meta-nav">&larr;</span> Previous', 'palm-healing-lite' ) ); ?></div>
                            <div class="nav-next"><?php next_image_link( false, __( 'Next <span class="meta-nav">&rarr;</span>', 'palm-healing-lite' ) ); ?></div>
                        </nav><!-- #image-navigation -->
                    </header><!-- .entry-header -->
                    <div class="entry-content">
                        <div class="entry-attachment">
                            <div class="attachment">
                                <?php palm_healing_lite_the_attached_image(); ?>
                            </div><!-- .attachment -->
                            <?php if ( has_excerpt() ) : ?>
                            <div class="entry-caption">
                                <?php the_excerpt(); ?>
                            </div><!-- .entry-caption -->
                            <?php endif; ?>
                        </div><!-- .entry-attachment -->
                        <?php
                            the_content();
                            wp_link_pages( array(
                                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'palm-healing-lite' ),
                                'after'  => '</div>',
                            ) );
                        ?>
                    </div><!-- .entry-content -->
                    <?php edit_post_link( esc_html__( 'Edit', 'palm-healing-lite' ), '<footer class="entry-meta"><span class="edit-link">', '</span></footer>' ); ?>
                </article><!-- #post-## -->
                <?php
                    // If comments are open or we have at least one comment, load up the comment template
                    if ( comments_open() || '0' != get_comments_number() )
                        comments_template();
                ?>
            <?php endwhile; // end of the loop. ?>
        </section>
        <?php get_sidebar();?>
        <div class="clear"></div>
    </div>
    </div>
</div>
<?php get_footer(); ?>