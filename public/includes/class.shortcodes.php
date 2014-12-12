<?php

/*
*
*	Class responsible for building teh various shortcodes used
*
*/
class ideaFactoryShortcodes {

	function __construct() {

		add_shortcode('idea_factory', array($this,'idea_factory_sc'));

	}

	/**
	*	Show teh votes and vote form within a shortcode
	* 	@since version 1.1
	*/
	function idea_factory_sc($atts, $content = null) {

		$defaults = array(
			'hide_submit'	=> 'off'
		);
		$atts = shortcode_atts( $defaults, $atts );

		$show_submit = 'on' !== $atts['hide_submit'];

		ob_start();

		?><div class="idea-factory--wrap"><?php

			if ( $show_submit ) { echo idea_factory_submit_header(); } ?>

			<section class="idea-factory--layout-main">
				<?php

				$paged = get_query_var('paged') ? get_query_var('paged') : 1;

				$args = array(
					'post_type'			=> 'ideas',
					'meta_key'			=> '_idea_votes',
					'orderby'			=> 'meta_value_num',
					'paged'				=> $paged
				);
				$q = new WP_Query( apply_filters('idea_factory_query_args', $args ) );

				if ( $q->have_posts() ):

					while( $q->have_posts() ) : $q->the_post();

						// setup some vars
						$id             = get_the_ID();
						$has_voted 		= get_user_meta( get_current_user_ID(), '_idea'.$id.'_has_voted', true);
						$total_votes 	= idea_factory_get_votes( $id );
						$status      	= idea_factory_get_status( $id );

						$status_class   = $status ? sprintf('idea-factory--entry__%s', $status ) : false;

						?>
						<section class="idea-factory--entry-wrap <?php echo sanitize_html_class( $status_class );?> <?php echo $has_voted ? 'idea-factory--hasvoted' : false;?>">

							<div class="idea-factory--controls">

								<?php if ( !$has_voted && is_user_logged_in() && 'approved' !== $status ){ ?>
									<a class="idea-factory vote-up" data-user-id="<?php echo get_current_user_ID();?>" data-post-id="<?php echo (int) $id;?>" href="#"></a>
									<a class="idea-factory vote-down" data-user-id="<?php echo get_current_user_ID();?>" data-post-id="<?php echo (int) $id;?>" href="#"></a>
								<?php }

								if ( $total_votes ) { ?>
									<div class="idea-factory--totals">
										<?php

											if ( 1 == $total_votes ) {

												echo '<span class="idea-factory--totals_num">1</span> <span class="idea-factory--totals_label">vote</span>';

											} elseif( !empty( $total_votes ) ) {

												echo '<span class="idea-factory--totals_num">'.(int) $total_votes.'</span><span class="idea-factory--totals_label"> votes</span>';

											}

										?>
									</div>
								<?php }

								if ( 'open' !== $status && false !== $status ) { ?>
									<div class="idea-factory--status">
										<?php echo '<span class="idea-factory--status_'.sanitize_html_class( $status ).'">'.esc_attr( $status ).'</span>';?>
									</div>
								<?php } ?>

							</div>

							<div class="idea-factory--entry">

								<?php the_title('<h2>','</h2>');

								the_content(); ?>

							</div>

						</section>

						<?php


					endwhile;

					previous_posts_link( 'Newer &raquo;', $q->max_num_pages );
        			next_posts_link('Older &raquo;', $q->max_num_pages);

				else:

					apply_filters('idea_factory_no_ideas', _e('No ideas found. Why not submit one?','idea-factory'));

				endif;
				wp_reset_query();
				?>
			</section>


		</div>

		<?php if ( $show_submit ) { echo idea_factory_submit_modal(); }

		return ob_get_clean();

	}
}
new ideaFactoryShortcodes;