<?php

get_header();

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
					$has_voted 		= get_user_meta( get_current_user_ID(), '_idea'.$id.'_has_voted', true);
					$total_votes 	= idea_factory_get_votes( $id );
					$status      	= idea_factory_get_status( $id );

					$status_class   = $status ? sprintf('idea-factory--entry__%s', $status ) : false;

					?>
					<section class="idea-factory--entry-wrap <?php echo sanitize_html_class( $status_class );?> <?php echo $has_voted ? 'idea-factory--hasvoted' : false;?>">

						<?php do_action('idea_factory_entry_wrap_top', $id ); ?>

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