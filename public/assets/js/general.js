jQuery(document).ready(function($){

	//vars
	var ajaxurl			= idea_factory.ajaxurl,
		results         = $('#idea-factory--entry--form-results');


	// entry handler
  	$('#idea-factory--entry--form').submit(function(e) {

  		var $this = $(this);

  		e.preventDefault();

	   	if ( $.trim( $('#idea-factory--entryform_title').val() ) === '' || $.trim( $('#idea-factory--entryform_description').val() ) === '' ) {
	        $(results).html('Title and description are required.');
	        $this.find('input').css('border-color','#d9534f');
	        $this.find('textarea').css('border-color','#d9534f');
	        return false;
	    }

		$this.find(':submit').attr( 'disabled','disabled' );

  		var data = $this.serialize();

	  	$.post(ajaxurl, data, function(response) {
	  		$(results).html(response);
	  		location.reload();
	    });

    });

	$( '.idea-factory' ).live('click', function(e) {
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
