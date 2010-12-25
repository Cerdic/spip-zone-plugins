<?php

function inc_ics_to_array($u) {
	include_spip('lib/iCalcreator.class');

	$tmp = _DIR_TMP . 'ics-'.md5($u);
	ecrire_fichier($tmp, $u);

	$v = new vcalendar();
	  // create a new calendar instance
	$v->setConfig( 'unique_id', 'icaldomain.com' );
	  // set Your unique id, required if any component UID is missing
	
	$v->setProperty( 'method', 'PUBLISH' );
	  // required of some calendar software
	$v->setProperty( "x-wr-calname", "Calendar Sample" );
	  // required of some calendar software
	$v->setProperty( "X-WR-CALDESC", "Calendar Description" );
	// required of some calendar software
	$v->setProperty( "X-WR-TIMEZONE", "Europe/Stockholm" );
	  // required of some calendar software
	
	/* start parse of local file */
	$v->setConfig( 'directory', 'calendar' );
	  // set directory
	$v->setConfig( 'filename', $tmp );
	  // set file name
	$v->parse();

	return($v->components);
}

