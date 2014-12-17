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
			'hide_submit'	=> 'off',
			'hide_voting'	=> 'off',
			'hide_votes'	=> 'off'
		);
		$atts = shortcode_atts( $defaults, $atts );

		$postid = get_the_ID();

		$show_submit  = 'on' !== $atts['hide_submit'];
		$show_voting  = 'on' !== $atts['hide_voting'];
		$show_votes   = 'on' !== $atts['hide_votes'];

		ob_start();

		do_action('idea_factory_sc_layout_before', $postid);

		?><div class="idea-factory--wrap"><?php

			do_action('idea_factory_sc_layout_before_entries', $postid);

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

				$max = $q->max_num_pages;

				wp_localize_script('idea-factory-script', 'idea_factory',  idea_factory_localized_args( $max , $paged ) );

				if ( $q->have_posts() ):

					while( $q->have_posts() ) : $q->the_post();

						// setup some vars
						$id             = get_the_ID();

						if ( is_user_logged_in() ) {

							$has_voted 		= get_user_meta( get_current_user_ID(), '_idea'.$id.'_has_voted', true);

						} elseif( $public_can_vote ) {

							$has_voted 		= idea_factory_has_public_voted( $id );

						}

						$total_votes 	= idea_factory_get_votes( $id );
						$status      	= idea_factory_get_status( $id );

						$status_class   = $status ? sprintf('idea-factory--entry__%s', $status ) : false;

						?>
						<section class="idea-factory--entry-wrap <?php echo sanitize_html_class( $status_class );?> <?php echo $has_voted ? 'idea-factory--hasvoted' : false;?>">

							<?php do_action('idea_factory_sc_entry_wrap_top', $postid ); ?>

							<div class="idea-factory--controls">

								<?php if ( idea_factory_is_voting_active( $id ) && $show_voting ){

									echo idea_factory_vote_controls( $id );

								}

								if ( $total_votes && $show_votes ) { ?>
									<div class="idea-factory--totals">
										<?php

										if ( 1 == $total_votes ) {
											printf(
												'<span class="idea-factory--totals_label">' . apply_filters( 'idea_factory_vote', __( '%s vote','idea-factory' ) ) . '</span>',
												'<span class="idea-factory--totals_num">1</span>'
											);

										} elseif( !empty( $total_votes ) ) {

											printf(
												'<span class="idea-factory--totals_label">' . apply_filters( 'idea_factory_votes', __( '%s votes','idea-factory' ) ) . '</span>',
												'<span class="idea-factory--totals_num">'.(int) $total_votes.'</span>'
											);

										}

										?>
									</div>
								<?php }

								echo idea_factory_vote_status( $id );

								?>

							</div>

							<div class="idea-factory--entry">

								<?php the_title('<h2>','</h2>');

								the_content(); ?>

							</div>

							<?php do_action('idea_factory_sc_entry_wrap_bottom', $postid ); ?>

						</section>

						<?php


					endwhile;

				else:

					apply_filters('idea_factory_no_ideas', _e('No ideas found. Why not submit one?','idea-factory'));

				endif;
				wp_reset_query();
				?>
			</section>

			<?php do_action('idea_factory_sc_layout_after_entries', $postid); ?>

		</div>

		<?php if ( $show_submit ) { echo idea_factory_submit_modal(); }

		do_action('idea_factory_sc_layout_after', $postid);

		return ob_get_clean();

	}
}
new ideaFactoryShortcodes;