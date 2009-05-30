<?php

function action_spiplistescleaner_export_delete_dist() {
	include_spip('base/abstract_sql');

    // Check that the user is authorised
    if (! autoriser('modifier', 'article') ) {
		spip_log(LOG_ERROR, 'Denied permission to do action = spiplistescleaner_export.');
        return;
    }
	
	// Delete all rows in the table
	sql_delete ('spiplistescleaner_deleted_emails');
	
	// Reset the counter of number of deleted emails
	ecrire_config('spiplistescleaner/nb_deleted_mails_last_export', 0);
}

?>
