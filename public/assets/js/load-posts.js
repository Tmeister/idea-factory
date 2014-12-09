jQuery(document).ready(function($) {

	// The number of the next page to load (/page/x/).
	var pageNum = parseInt(idea_factory.startPage) + 1;

	// The maximum number of pages the current query can return.
	var max = parseInt(idea_factory.maxPages);

	// The link of the next page of posts.
	var nextLink = idea_factory.nextLink;

	if(pageNum <= max) {
		$('.idea-factory--wrap')
			.append('<div class="idea-factory--layout-main clearfix idea-factory--layout-main-'+ pageNum +'"></div>')
			.append('<p class="idea-factory--loadmore fix"><a href="#">Load More Stories</a></p>');

	}

	$('.idea-factory--loadmore a').click(function() {


		// Are there more posts to load?
		if(pageNum <= max) {

			// Show that we're working.
			$(this).text('Loading stories...');

			$('.idea-factory--layout-main-'+ pageNum).load(nextLink + ' .idea-factory--entry-wrap',
				function() {
					// Update page number and nextLink.
					pageNum++;
					nextLink = nextLink.replace(/\/page\/[0-9]?/, '/page/'+ pageNum);

					// Add a new placeholder, for when user clicks again.
					$('.idea-factory--loadmore')
						.before('<div class="idea-factory--layout-main clearfix idea-factory--layout-main-'+ pageNum +'"></div>')

					// Update the button message.
					if(pageNum <= max) {
						$('.idea-factory--loadmore a').text('Load More Ideas');
					} else {
						$('.idea-factory--loadmore a').text('No more ideas found');
					}

				}
			);
		} else {
			$('.idea-factory--loadmore a').append('.');
		}

		return false;
	});

});