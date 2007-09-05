<?php

/* le fichier de reference */
$GLOBALS['xmlRef']='http://jquery.com/api/data/jquery-docs-xml.xml';

/* la langue de ce fichier */
$GLOBALS['langRef']= 'en';

/* la table dans laquelle on stocke tout ça */
$GLOBALS['tablejq']= 'docjq';

// fonction appelee par le code genere par balise_TRAITEMENT
function appelerTraitement($fi, $fc) {
	if(!include_spip($fi)) return;
	if($fc) {
		return $fc;
	}
	$fc= strrchr($fi, '/');
	if($fc) {
		return 'traitement_'.substr($fc, 1);
	}
	return 'traitement_'.$fi;
}

?>
