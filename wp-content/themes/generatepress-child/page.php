<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package GeneratePress
 */

get_header(); ?>
	<?php
        $attr = array ( 
            'width' => '32', //input only number, in pixel
            'height' => '32', //input only number, in pixel
            'margin' => '4', //input only number, in pixel
            'display' => 'vertical', //horizontal | vertical
            'alignment' => 'right', //center | left | right
           	'selected_icons' => array ( '1', '2') 
            //you can get the icon ID form Manage Icons page
        );
        if ( function_exists('cn_social_icon') ) echo cn_social_icon( $attr ); 
        ?>
        
	<div id="primary" <?php generate_content_class();?>>
		<main id="main" <?php generate_main_class(); ?>>
			<?php do_action('generate_before_main_content'); ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

				<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() ) : ?>
					<div class="comments-area">
						<?php comments_template(); ?>
					</div>
				<?php endif; ?>

			<?php endwhile; // end of the loop. ?>
			<?php do_action('generate_after_main_content'); ?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php 
do_action('generate_sidebars');
get_footer();