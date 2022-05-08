=== Grow Social ===
Contributors: Mediavine, iova.mihai
Tags: social share, social sharing, social sharing buttons, social share buttons, social, social media, social share icon, social share counts, social sharing icon
Requires at least: 4.7
Tested up to: 5.9.2
Requires PHP: 7.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The best social sharing plugin for your WordPress website and the only social sharing plugin you will ever need.

== Description ==
**The Best Looking Social Sharing Buttons**

Grow Social by Mediavine is one of the easiest to use social sharing plugins available. It lets you add highly customizable social share buttons that will integrate beautifully with your website's design, leading to increased interactions and social media shares.

The free version of the plugin comes with five of the biggest social media platforms, mainly Facebook, Twitter, Pinterest and LinkedIn.

For each social media platform you can customize the social share button to your liking. You can choose to have a simple social media icon share button, a share button with a bold label to catch the eye and even social sharing buttons with social share counts, so that you visitors have social media proof.

You can place the social sharing buttons before and after the post content or have them follow your readers as they scroll up and down the page, in the form of a social media floating sidebar.

**Social Share Count**: Display the posts social share count to provide social media proof and increase your website's credibility.

**Social Share Buttons Locations**: You can place the social sharing buttons in 4 different locations of your website. You can opt in to display them before and/or after the content of your posts on whichever post type you want. Also you can choose to display the social share buttons in a floating sidebar that follows the user as he/she scrolls the page. The floating sidebar can be placed on the left or on the right side of the screen.

= Main Features =
* **Before and After Content Social Share Buttons** - Place the social share buttons right before your content, after your content or both
* **Floating Sidebar Social Share Buttons** - Make the social share buttons follow the user as he/she scrolls up and down your webpage by adding floating buttons on the left or right side of the screen
* **Editable Button Labels** - Edit the labels that appear in the share buttons, to maximize your engagement
* **Retina Ready Sharp Share Icons** - Grow Social by Mediavine uses an icon font to display the best looking social media icons on any screen

= Premium Features =
* **10+ Social Media Networks more** - Reach more people by adding any of the following social share networks Reddit, VK, Yummly, WhatsApp, Buffer, Telegram, Flipboard, Pocket, Tumblr and Email
* **Mobile Sticky Footer Social Share Buttons** - Your website needs to be mobile ready and Grow Social by Mediavine is here to help
* **Pop-Up Social Share Buttons** - Trigger a pop-up with the social sharing buttons when a user does an action 
* **Custom Button Colors and Hover Colors** - The social networks colors are beautiful, but you don't have to limit yourself. Add any color to your social share buttons to match your website's design.
* **Shortcode for Social Share Buttons** - Place the buttons anywhere in your template files or the body of your content with the [mv_grow_share] shortcode
* **Link Shortening through Bitly** - Hide long URL's behind their shorter version with the Bitly integration
* **Google Analytics UTM tracking** - Track the source of your incoming traffic with the help of the Google Analytics UTM parameters
* **Social Media Follow Buttons Widget** - Place social media follow buttons for the following social media networks: Facebook, Twitter, Pinterest, LinkedIn, Reddit, Instagram, YouTube, Vimeo, SoundCloud, Twitch, Yummly and Behance. Use the [mv_grow_follow] shortcode to place the follow buttons anywere in your template files
* **Sharable Quotes ( Click to Tweet ) Feature** - Let your readers easily share a custom tweet with just one click
* **Import / Export Settings** - Move all the settings from one website to another with just a few clicks

= Powerful Social Sharing Features =

**Social Proof**
Display social sharing counts for each social media network, for positive social media proof.

**Click to Tweet**
Let your users share your posts on Twitter with just one click.

**Social Media Custom Content**
Customize the social media images and descriptions that your users share on their social media profiles.

= Help us Translate =
We want to reach as many people as possible. Help us translate the plugin in your language and get a 20% discount code for your premium purchases. Contact us here: grow@mediavine.com

= Documentation =

https://product-help.mediavine.com/

= Website =

https://marketplace.mediavine.com/grow-social-pro/

== Installation ==

1. Upload the social-pug folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Activate each location where you wish the buttons to appear


== Frequently Asked Questions ==

= What social sharing buttons are supported? =

We currently support Facebook, Twitter, Pinterest and LinkedIn as social sharing buttons in the free version. The Pro version adds many more social media networks for both social sharing and social follow.

= Can I customize the social information that is being shared on social media? =

By default Grow Social will use the post's title and featured image to populate what is being shared on the social platforms. If you wish to have full control on what gets shared when a visitor uses the social sharing buttons, please visit our website and check out Grow Social Pro.

= Will your social sharing plugin slow down my website? =

No. Grow Social is a lightweight plugin that was built with efficiency in mind. Unlike other social sharing plugins, Grow Social will not slow down your website.

= Can I place the social share buttons only on posts and pages? =

You can place both the inline social share buttons and the floating sidebar social sharing buttons on any custom post type that your WordPress installation has registered. You are not limited to only posts and pages.


== Screenshots ==
1. Inline-Content social sharing buttons output
2. Floating Sidebar social share buttons output
3. Floating Sidebar social share buttons configuration page
4. Before and After Content social sharing buttons configuration page


== Changelog ==
[Looking for the changelog for Grow Social Pro? Click here to access it.](https://marketplace.mediavine.com/grow-social-pro/grow-social-pro-changelog/)

= 1.20.1 =
* Add post class to be targeted by Grow

= 1.20.0 =
* Add Inline Critical CSS to Inline Content Tool to reduce Layout Shift when CSS loading deferred
* Add Facebook share tooltip to explain why shares do not appear when under 100
* Add post and category IDs to Grow data
* Add status REST API endpoint
* Add the Grow saved class to the Trellis critical CSS bypass
* Fix inline content buttons showing up in WooCommerce product pages
* Fix outdated doc links in dashboard
* Fix inline content buttons not showing up before WPRM Jump to Recipe
* Fix Grow bookmark button not saving state
* Fix checking requirements on older WordPress versions causing fatal error

= 1.19.2 =
* FEATURE: Add notice of Share Count Removal
* FIX: Images prevented from being pinned
* FEATURE: Add more details to upgrade Call to Action

= 1.19.1 =
* FIX: Error when old networks present in tools

= 1.19.0 =
* FEATURE: New setting to allow a second render to fix issues with missing inline content buttons on theme conflicts
* FEATURE: Added new hook to allow plugin authors to exclude their custom post-types from being scraped by Facebook's API
* FEATURE: Add setting to hide Floating Sidebar when it reaches a certain element, configurable in the settings.
* FEATURE: Switches all users to optimized javascript with ability to roll back to jQuery until July 2021
* FEATURE: Add feature flag capabilities for beta program.
* FEATURE: Update Facebook Graph API Version
* FEATURE: Add Settings API
* FEATURE: Grow.me now available as a network for sharing
* FIX: Added wprm_recipe to post-type exclusion array to prevent calls to Facebook's API
* FIX: Fixes issue where missing Facebook token would cause 400 errors when updating share counts
* FIX: Fixes issue with broken Inline Content buttons on WooCommerce products
* FIX: Fixed an issue where individual share-counts were not displaying when the Minimum Share Count field was filled out.
* FIX: Trigger attributes for Floating Sidebar
* FIX: Hide Grow sharing elements from printers
* FIX: Resolved an issue where share count numbers were overflowing buttons on certain style variations.
* FIX: Resolve issue on some Trellis sites where mobile users needed to click twice on links to go to the target page.
* FIX: Resolves an issue where old networks could cause fatal errors during share count retrieval.
* COSMETIC: Remove Icon Font and use inline SVG for admin Icons
* COSMETIC: Fix Floating Sidebar Icon alignment issue
* COSMETIC: Fix gap between outline and icon on some button styles.
* ENHANCE: Add a notification for new users to check out setup documentation.


= 1.18.2 =
* FIX: Fixes issue where Pinterest shares were not being calculated
* FIX: Grow Social now checks if Facebook access token is invalid when used and marks it as expired
* FIX: Prevents PHP notices on unique server configurations
* FIX: Remove unneeded settings meta blocks

= 1.18.1 =
* FIX: Issues were some tools would not be active after upgrading.
* FIX: Prevent unnecessary calls to `wp_remote_get()` that were causing timeouts and high CPU spikes

= 1.8.0 =
* FEATURE: Recommended PHP and WordPress versions have been updated to PHP 7.4 and WordPress 5.2
* FEATURE: Add button styles and custom colors to share buttons
* FEATURE: Rewrite button markup to be more accessible
* FEATURE: Stronger indication of visual focus on buttons
* FEATURE: New lighter weight icon animation
* FEATURE: Add ability to set minimum global and individual share counts
* FEATURE: Switch to non-jQuery javascript to improve site performance
* FEATURE: Use inline SVG for icons instead of icon font, will improve load times and page speed scores
* FEATURE: Add integration with the Mediavine Trellis Theme framework
* FEATURE: Switched the Facebook App transient to use an option instead for better compatibility with some hosts.
* FEATURE: Add a body class to indicate to themes that the sidebar will show up on Mobile
* FEATURE: Don't load styles if Grow elements don't exist on page
* FIX: Better spacing for the inline links
* FIX: Twitter character count should be correct and accommodate for url and username
* FIX: Twitter links should open in new window
* FIX: Added title attribute to share and follow links for accessibility 
* FIX: Fixed an error in share count rounding that would cause too many numbers after the decimal point
* FIX: Sanitize Open Graph and Twitter tags on titles and descriptions
* FIX: Resolved an alignment issue with some themes when single column buttons are used
* FIX: When labels are not shown, ensure that the icons with and without counts line up with each other on 1, 2, and 3 column layouts
* FIX: Add async and noptimize to JavaScript assets to prevent compatibility issues with WP Rocket, Autopmize and other optimization plugins.
* FIX: Ensure Yoast OG Tags get removed if Grow tags are being used
* FIX: Ensure optimization plugins don't interfere with front end data

= 1.7.0 =
* FEATURE: Change name and branding to Grow Social by Mediavine
* FEATURE: Optimize Javascript
* FEATURE: Better accessibility for share buttons
* FIX: Ensure text remains visible during icon webfont load
* FIX: Better spacing for the inline links
* FIX: Total Shares won't wrap lines

= 1.6.2 =
* Fixed: Issue with Facebook social share counts not being pulled properly

= 1.6.1 =
* New: Added Facebook authentication to be able to pull Facebook social share counts
* New: Added sixth column for Inline Content buttons
* Fixed: Issue with ampersand breaking the email button

= 1.6.0 =
* New: Added Email and Print social sharing buttons
* New: Redesigned the admin interface
* New: Added button labels to the floating sidebar social sharing tool
* Misc: Removed Google+ social sharing button

= 1.5.3 =
* Fixed: Bullet point list item issues for social share buttons with certain themes

= 1.5.2 =
* Misc: Code clean-up and compatibility with latest WordPress

= 1.5.1 =
* New: Removed support for OpenShareCount and added support for TwitCount

= 1.5.0 =
* New: Add a Twitter handle to the tweet generated when clicking on the Twitter social sharing button.
* Fixed: Issue with inline social share button labels being added to Yoast meta descriptions.

= 1.4.9 =
* New: Removed support for NewShareCounts in favor of support for OpenShareCount to retrieve Twitter social share counts.

= 1.4.8 =
* Misc: Small admin user interface improvements to make the plugin more user friendly.

= 1.4.7 =
* New: Added social sharing buttons icon animation.

= 1.4.6 =
* New: Added support for the 5th column in the Inline Content Social Sharing tool.
* New: Added feedback form on plugin deactivation.

= 1.4.5 =
* New: Added social media share count statistics meta-box in admin post edit screen.
* Fixed: Issue with Facebook API not pulling social share counts.

= 1.4.4 =
* Misc: Modified the way social share counts are being pulled to improve performance.
* Misc: Added feedback form for admin users.
* Misc: Updated Facebook API version used by the plugin.

= 1.4.3 =
* Fixed: Issue with Twitter opening two pop-up share windows when Twitter's script is added to the page.

= 1.4.2 =
* Misc: Added translation support for strings that were missing it.

= 1.4.1 =
* Misc: Removed Google+ social share count support, due to Google removing it also.

= 1.4.0 =
* Misc: New design for social media buttons labels fields in admin panel to make them more visible

= 1.3.9 =
* New: Added LinkedIn button as a social sharing option
* Misc: Stylised the total social sharing counts for the inline content buttons

= 1.3.8 =
* Fixed: Display issues of the social sharing buttons on different themes
* Misc: Improved accessibility of the admin interface

= 1.3.7 =
* Misc: Code clean-up and small usability improvements in the admin area

= 1.3.6 =
* Misc: Added support for Twitter summary card with large image

= 1.3.5 =
* New: Added option to set a custom value for the mobile device screen width in order to display or hide the social media share buttons
* Misc: Updated the social media icon font

= 1.3.4 =
* New: Added Facebook App Secret field in the settings page in order to unlock Facebook's default limitations when trying to grab the share counts for a post

= 1.3.3 =
* New: Added Facebook App ID field in the settings page in order for posts to pass Facebook's App ID validation

= 1.3.2 =
* Fixed: Performed a security audit and fixed security issues

= 1.3.1 =
* Fixed: Fatal error on some websites.

= 1.3.0 =
* New: Social media share count values are pulled asynchronous for each post after the post loads
* Misc: Refactored social media share system

= 1.2.6 =
* Fixed: XSS vulnerability in plugin settings pages

= 1.2.5 =
* Misc: Small admin interface changes for improved user experience

= 1.2.4 =
* Misc: Added rel="nofollow" to all share buttons

= 1.2.3 =
* Misc: Updated the Facebook social share counts grabber, due to Facebook's recent changes

= 1.2.2 =
* Fixed: Mozilla Firefox users can now change the text labels of the social media buttons for the Inline Content share tool

= 1.2.1 =
* New: Added WooCommerce support for Inline Content buttons before and after the product's short description

= 1.2.0 =
* Misc: Code clean-up

= 1.1.9 =
* New: Share Text option for the Inline Content share buttons
* Misc: Under the hood improvements and refactoring

= 1.1.8 =
* New: Added Twitter tweet counts with the help of http://newsharecounts.com/

= 1.1.7 =
* Fixed: Bug that caused issues in the WordPress admin page in Safari browser
* Fixed: Issues with the Pinterest button on webpages served through HTTPS

= 1.1.6 =
* Misc: New optimised icon font for the icons, that is smaller in size

= 1.1.5 =
* New: Settings page with option to disable the Open Graph tags printed by Grow Social by Mediavine

= 1.1.4 =
* Misc: Code clean up and minor performance improvements

= 1.1.3 =
* New: New bigger Google+ icon

= 1.1.2 =
* Fixed: Needed minor CSS fixes

= 1.1.1 =
* Fixed: PHP notice when outputting the meta tags

= 1.1.0 =
* New: Redesigned the plugin's admin dashboard
* Misc: Minor performance improvements

= 1.0.4 =
* Fixed: Fetching Google+ share counts resulted in PHP warnings and counts were not fetched

= 1.0.3 =
* Fixed: CSS issue where buttons without labels and rounded corners did not get displayed correctly

= 1.0.2 =
* Fixed: Removed un-dismissable admin notification
* Misc: Changed textdomain from "socialpug" to "social-pug" to match the one on WordPress.org

= 1.0.1 =
* Fixed: Share window now opens in pop-up
* Fixed: Small bug that showed the buttons on posts when no post types where selected

= 1.0.0 =

* Initial release.
