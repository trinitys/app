<?php
// example: SERVER_ID=5915 php maintenance/wikia/GoogleWebmasterToolsSync/list_users.php --conf /usr/wikia/docroot/wiki.factory/LocalSettings.php

$freeSlotsWarning = 100;
$warningEmails = array("jacek@wikia-inc.com");

require_once( __DIR__."/configure_log_file.php" );
GWTLogHelper::notice( __FILE__ . " script starts.");
try {
	$gwt = new GWTService(null, null, null);
	$users = $gwt->getAvailableUsers();

	$limitPerAccout = $gwt->getMaxSitesPerAccount();


	$freeSlots = 0;

	foreach ( $users as $user ) { /* @var $user GWTUser */
		$freeSlots += $limitPerAccout - $user->getCount();
	}

	GWTLogHelper::notice( "FREE SLOTS: $freeSlots" );

	if ( $freeSlots < $freeSlotsWarning ) {

		foreach ( $warningEmails as $warningEmail ) {
			UserMailer::send(
				new MailAddress( $warningEmail ),
				new MailAddress( 'GoogleWebmasterToolsService@wikia-inc.com', 'GoogleWebmasterToolsService' ),
				'GA Webmaster Tools Accounts - '.$freeSlots.' slots left',
				'There is only '.$freeSlots.' slots left. Please add new accounts to our database.',
				null,
				'text/html; charset=ISO-8859-1'
			);
		}
	}
} catch ( Exception $ex ) {
	GWTLogHelper::error( __FILE__ . " script failed.", $ex);
}
