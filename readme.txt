=== Idea Factory ===
Contributors: nphaskins
Author URI:  http://nickhaskins.com
Plugin URI: http://nickhaskins.com/idea-factory
Donate link: http://nickhaskins.com/idea-factory
Tags: vote, voting, idea, feedback, user submission, front end submission, front end voting
Requires at least: 3.8
Tested up to: 4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Front end submission and voting system.

== Description ==

Idea Factory was born out of necessity, and the frustration of the lack of plugins that did exactly what I wanted it to do. This plugin allows users to submit new ideas from the front-end, and vote on them. Currently it allows a user to vote once per idea, then locks them out. It's also currently limited to logged in users, for now.

* AJAX powered front-end submission and voting
* Limited to logged in users for now
* 1 vote allowed per user per idea
* Emails the admin of a new submission
* Extensible with hooks and actions on events
* More ideas loaded with AJAX on front-end
* Mobile friendly

== Installation ==

= Uploading in WordPress Dashboard =

1. Navigate to 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `idea-factory.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `idea-factory.zip`
2. Extract the `idea-factory` directory to your computer
3. Upload the `idea-factory` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Frequently Asked Questions ==

= Can I override the layout? =
Yep. Copy the file from idea-factory/templates/template-ideas into your theme and it will use your file instead. Note, you'll need to keep track of updates to the source file and update accordingly. Check out using hooks, actions, or filters for adding, or changing things.

== Screenshots ==

1. What you see when you visit yoursite.com/ideas
2. User submit new ideas within a popup modal
3. New ideas are logged as a custom post type
4. You can change a few settings here
5. And more settings here

== Upgrade Notice ==

= 1.0 =
* Initial Release

== Changelog ==

= 1.1 =
* NEW - Added a "threshold" option where, when set with a numerical value such as 10, each idea will then be automatically approved or declined based on reaching 10 or more total votes
* NEW - Added an option to manually reset all votes on all ideas back to zero
* NEW - Added a status column within the edit posts screen to show the status of each idea
* NEW - Added a shortcode [idea_factory] to show the ideas and voting form with options to hide the form, votes, or voting
* NEW - Added an option to disable the automatic archive
* TWEAK - Instead of posts being put into draft when the option is selected, they are put into "pending" allowing proper review

= 1.0 =
* Initial Release
