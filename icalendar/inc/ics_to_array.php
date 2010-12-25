<?php

function inc_ics_to_array($u) {
	include_spip('lib/iCalcreator.class');

	# on passe par un fichier temp car notre librairie fonctionne comme ca
	$tmp = _DIR_TMP . 'ics-'.md5($u);
	ecrire_fichier($tmp, $u);

	$v = new vcalendar();
	$v->setConfig( 'filename', $tmp );
	$v->parse();

	supprimer_fichier($tmp);

	return($v->components);
}

