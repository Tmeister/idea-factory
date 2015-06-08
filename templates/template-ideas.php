<?php

get_header();

	$public_can_vote = idea_factory_get_option('if_public_voting','if_settings_main');

	do_action('idea_factory_layout_before'); ?>

	<div class="idea-factory--wrap">

		<?php echo idea_factory_submit_header();

		do_action('idea_factory_before_entries'); ?>

		<section class="idea-factory--layout-main">
			<?php

			if ( have_posts() ):

				while( have_posts() ) : the_post();

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

						<?php do_action('idea_factory_entry_wrap_top', $id ); ?>

						<div class="idea-factory--controls">

							<?php if ( idea_factory_is_voting_active( $id ) ){
								echo idea_factory_vote_controls( $id );
							}

							if ( $total_votes ) { ?>
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

							echo idea_factory_vote_status( $id );?>

						</div>

						<div class="idea-factory--entry">

							<?php the_title('<h2>','</h2>');

							the_content();

							do_action('idea_factory_entry_bottom', $id ); ?>

						</div>

						<?php do_action('idea_factory_entry_wrap_bottom', $id ); ?>

					</section>

					<?php

				endwhile;

				wp_reset_query();

			else:

				apply_filters('idea_factory_no_ideas', _e('No ideas found. Why not submit one?','idea-factory'));

			endif;
			?>
		</section>

		<?php do_action('idea_factory_after_entries'); ?>

	</div>

	<?php do_action('idea_factory_layout_after');

	echo idea_factory_submit_modal();

	get_footer();