<?php

function inc_ics_to_array($u) {
	include_spip('lib/iCalcreator.class');

	# on passe par un fichier temp car notre librairie fonctionne comme ca
	$tmp = _DIR_TMP . 'ics-'.md5($u);
	ecrire_fichier($tmp, $u);

	$cal = new vcalendar();
	$cal->setConfig( 'filename', $tmp );
	$cal->parse();

	supprimer_fichier($tmp);

	# noter les dates cles dans un format plus facile a recuperer
	foreach($cal->components as $k => &$v) {
		foreach(array('dtstart', 'dtend', 'dtstamp', 'lastmodified', 'created')
		as $champ) {
			if (isset($v->$champ)) {
				$w = &$v->$champ;
				$date = table_valeur($w, "/value");
				$w['str'] = sprintf('%04d-%02d-%02d %02d:%02d:%02d',
					$date['year'],
					$date['month'],
					$date['day'],
					$date['hour'],
					$date['min'],
					$date['sec']
				);
			}
		}
	}

	return($cal->components);
}

