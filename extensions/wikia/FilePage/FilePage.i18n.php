<?php
/**
 * @addtogroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'filepage-desc' => 'Modification of the standard MediaWiki file page for video support',
	/* video page */
	'video-page-file-list-header' => 'Appears on these pages',
	'video-page-global-file-list-header' => 'Appears on these wikis',
	'video-page-from-provider' => 'From $1',
	'video-page-expires' => 'Content expires on $1',
	'video-page-views' => '$1 {{PLURAL:$1|View|Views}}',
	'video-page-see-more-info' => 'Show more info',
	'video-page-see-less-info' => 'Show less info',
	'video-page-description-heading' => 'Description',
	'video-page-description-zero-state' => 'There is no description yet.',
	'video-page-file-list-pagination' => '$1 of $2',

	/* file page */
	'file-page-replace-button' => 'Replace',
	'file-page-tab-about' => 'About',
	'file-page-tab-history' => 'File History',
	'file-page-tab-metadata' => 'Metadata',
	'file-page-more-links' => 'See full list',
);

/** Message documentation (Message documentation) */
$messages['qqq'] = array(
	'filepage-desc' => '{{desc}}',

	/* video page */
	'video-page-file-list-header' => 'Heading for file list on Video File Page',
	'video-page-global-file-list-header' => 'Heading for global usage list on Video File Page',
	'video-page-from-provider' => '$1 is the provider name. The provider is where we got the video content from.  Some current examples are IGN and Ooyala.',
	'video-page-expires' => '$1 is a date. After the date specified, the video content will no longer be available to view.',
	'video-page-see-more-info' => 'Label to uncollapse UI that shows more info',
	'video-page-see-less-info' => 'Label to collapse UI that shows more info',
	'video-page-description-heading' => 'Description heading',
	'video-page-description-zero-state' => 'Placeholder file page content that states there is no description',
	'video-page-file-list-pagination' => 'Pagination for file listing.  e.g. 1 of 2.  $1 is current page, $2 is total pages',
	'video-page-views' => 'Shows total number of views (plays) of the video. $1 is a number of views (integer)',

	/* file page */
	'file-page-replace-button' => 'Replace button label, hidden in menu button',
	'file-page-tab-about' => 'Navigation tab label for the "about" section on a File Page.',
	'file-page-tab-history' => 'Navigation tab label for the "File History" section on a File Page.',
	'file-page-tab-metadata' => 'Navigation tab label for the "Metadata" section on a File Page.',
	'file-page-more-links' => 'A link to the full list of pages that have links to the file on this file page',
);
