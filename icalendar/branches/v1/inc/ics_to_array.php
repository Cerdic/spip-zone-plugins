<?php


# hors de la fonction, de facon a ce que la class soit chargee
# meme si le resultat est deja dans le cache (sinon le cache est inexploitable).
# cf. iterateur/data.php
include_spip('lib/iCalcreator.class');

function inc_ics_to_array($u) {

	# on passe par un fichier temp car notre librairie fonctionne comme ca
	$tmp = _DIR_TMP . 'ics-'.md5($u);
	ecrire_fichier($tmp, str_replace("\r\n", "\n", $u));

	$cal = new vcalendar();
	$cal->setConfig( 'filename', $tmp );
	$cal->parse();

	supprimer_fichier($tmp);

	$table_valeur = function_exists('Iterateurs_table_valeur')
		? 'Iterateurs_table_valeur' : 'table_valeur';

	# noter les dates cles dans un format plus facile a recuperer
	foreach($cal->components as $k => &$v) {

		foreach(array('dtstart', 'dtend', 'dtstamp', 'lastmodified', 'created')
		as $champ) {
			if (isset($v->$champ)
			  AND $w = &$v->$champ
			  AND $date = $table_valeur($w, "value")) {
				$w['str'] = date('Y-m-d H:i:s', strtotime(sprintf("%04d-%02d-%02dT%02d:%02d:%02d%s",
					$date['year'],
					$date['month'],
					$date['day'],
					$date['hour'],
					$date['min'],
					$date['sec'],
					$date['tz']))
				);
			}
		}
	}

	return($cal->components);
}

