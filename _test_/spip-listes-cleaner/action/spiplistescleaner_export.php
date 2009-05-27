<?php

function action_spiplistescleaner_export_dist() {
	include_spip('base/abstract_sql');

    // Check that the user is authorised
    if (! autoriser('modifier', 'article') ) {
		spip_log(LOG_ERROR, 'Denied permission to do action = spiplistescleaner_export.');
        return;
    }
	
	// change the header to do a download file
	$filename = "deleted_emails_-_". date('d-m-y_H\hi') .".csv";
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=\"$filename\"");
	
	// Ask  the database
	$result = sql_select('*',  'spiplistescleaner_deleted_emails');
	
	// print the result
	while($r = sql_fetch($result)) {
		echo $r['email'] . ";" . $r['date'] . "\n";
	}
}

?>
