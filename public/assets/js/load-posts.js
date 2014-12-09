jQuery(document).ready(function($) {

	var pageNum 	= parseInt(idea_factory.startPage) + 1,
		max 		= parseInt(idea_factory.maxPages),
		nextLink 	= idea_factory.nextLink,
		label    	= idea_factory.label,
		label_loading = idea_factory.label_loading;

	if(pageNum <= max) {
		$('.idea-factory--wrap')
			.append('<div class="idea-factory--layout-main clearfix idea-factory--layout-main-'+ pageNum +'"></div>')
			.append('<p class="idea-factory--loadmore fix"><a class="idea-factory--button" href="#">'+label+'</a></p>');

	}

	$('.idea-factory--loadmore a').click(function() {

		// Are there more posts to load?
		if(pageNum <= max) {

			// Show that we're working.
			$(this).text(label_loading);

			$('.idea-factory--layout-main-'+ pageNum).load(nextLink + ' .idea-factory--entry-wrap',
				function() {
					// Update page number and nextLink.
					pageNum++;
					nextLink = nextLink.replace(/\/page\/[0-9]?/, '/page/'+ pageNum);

					// Add a new placeholder, for when user clicks again.
					$('.idea-factory--loadmore').before('<div class="idea-factory--layout-main clearfix idea-factory--layout-main-'+ pageNum +'"></div>')

					// Update the button message.
					if(pageNum <= max) {
						$('.idea-factory--loadmore a').text(label);
					} else {
						$('.idea-factory--loadmore a').fadeOut();
					}

				}
			);
		}

		return false;
	});

});