<?php

get_header();

	$intro_message = idea_factory_get_option('if_welcome','if_settings_main',apply_filters('idea_factory_default_message', __('Submit and vote for new features!','idea-factory')));

	?>
	<div class="idea-factory--wrap">

		<?php if ( is_user_logged_in() ): ?>
		<aside class="idea-factory--layout-submit">
			<div class="idea-factory--submit-left">
				<?php echo idea_factory_media_filter( $intro_message );?>
			</div>
			<div class="idea-factory--submit-right">
				<a href="#" data-toggle="modal" data-target=".idea-factory-modal" class="idea-factory--button idea-factory-trigger">Submit Idea</a>
			</div>
		</aside>
		<?php endif; ?>

		<section class="idea-factory--layout-main">
			<?php

			$args = array(
				'post_type' 		=> 'ideas',
	            'meta_key' 			=> '_idea_votes',
            	'orderby'   		=> 'meta_value_num',
            	'order'     		=> 'DESC'

			);

			$q = new WP_Query( apply_filters('idea_factory_ideas_query', $args ) );

			if ( $q->have_posts() ):

				while( $q->have_posts() ) : $q->the_post();

					// setup some vars
					$has_voted = get_user_meta( get_current_user_ID(), '_idea'.get_the_ID().'_has_voted', true);
					$total_votes = get_post_meta( get_the_ID(), '_idea_votes', true);
					?>
					<section class="idea-factory--entry-wrap <?php echo $has_voted ? 'idea-factory--hasvoted' : false;?>">

						<div class="idea-factory--controls">
							<?php if ( !$has_voted && is_user_logged_in() ){ ?>
								<a class="idea-factory vote-up" data-user-id="<?php echo get_current_user_ID();?>" data-post-id="<?php echo (int) get_the_ID();?>" href="#"></a>
								<a class="idea-factory vote-down" data-user-id="<?php echo get_current_user_ID();?>" data-post-id="<?php echo (int) get_the_ID();?>" href="#"></a>
							<?php } ?>
							<div class="idea-factory--totals">
								<?php

									if ( 1 == $total_votes ) {

										echo '1 vote';

									} elseif( !empty( $total_votes ) ) {

										echo (int) $total_votes.' votes';

									}

								?>
							</div>
						</div>

						<div class="idea-factory--entry">
							<?php the_title('<h2>','</h2>');?>
							<?php the_content();?>
						</div>

					</section>

					<?php

				endwhile;

				wp_reset_query();

			else:

				apply_filters('idea_factory_form_title', _e('No ideas found. Why not submit one?','idea-factory'));

			endif;
			?>
		</section>

	</div>

	<?php if ( is_user_logged_in() ): ?>
	<div class="modal fade idea-factory-modal" tabindex="-1">
		<div class="modal-dialog ">
		    <div class="modal-content">
		    	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</button>

		    	<div class="modal-header">
		    		<h3 class="modal-title"><?php apply_filters('idea_factory_submit_idea_label', _e('Submit idea','idea-factory'));?></h3>
		    	</div>
		    	<div class="modal-body">

					<div id="ah-entry--form-results"></div>
					<form id="ah-entry--form" method="post" enctype="multipart/form-data">

						<?php do_action('idea_factory_inside_form_top');?>

						<label for="idea-title"><?php apply_filters('idea_factory_form_title', _e('Title','idea-factory'));?></label>
						<input type="text" name="idea-title" value="" placeholder="My Awesome Submission">

						<label for="idea-description"><?php apply_filters('idea_factory_form_title', _e('Description','idea-factory'));?></label>
						<textarea form="ah-entry--form" name="idea-description" value="" placeholder="Make the description meaningful!"></textarea>

						<?php do_action('idea_factory_inside_form_bottom');?>

						<input type="hidden" name="action" value="process_entry">
						<input type="hidden" name="user_id" value="<?php echo get_current_user_ID(); ?>">
						<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('if-entry-nonce'); ?>"/>

						<div class="modal-footer">
							<input class="idea-factory--button" type="submit" value="<?php apply_filters('idea_factory_submit_label', _e('Submit','idea-factory'));?>">
						</div>
					</form>

				</div>
			</div>
		</div>
	</div>
	<?php endif;

	get_footer();