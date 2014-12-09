jQuery(document).ready(function($){

	//vars
	var ajaxurl			= idea_factory.ajaxurl,
		results         = $('#ah-form--results');


	// entry handler
  	$('#ah-entry--form').submit(function(e) {

  		e.preventDefault();

  		var data = $(this).serialize();

	  	$.post(ajaxurl, data, function(response) {
	  		$('#ah-entry--form-results').html(response);
	  		location.reload();
	    });

    });

	$( '.idea-factory' ).on('click', function(e) {
		e.preventDefault();

		var $this = $(this);

		var data      = {
			action:    $this.hasClass('vote-up') ? 'process_vote_up' : 'process_vote_down',
			user_id:   $this.data('user-id'),
			post_id:   $this.data('post-id'),
			nonce:     idea_factory.nonce
		};

		$.post( ajaxurl, data, function(response) {
			if( response == 'success' ) {
				$this.parent().addClass('voted');
				$this.parent().html('Thanks for voting!');
			} else {
				alert( 'Aww snap, something went wrong.' );
				location.reload();
			}
		} );
	});
});
