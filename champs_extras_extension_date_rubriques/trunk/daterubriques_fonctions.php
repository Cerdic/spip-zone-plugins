<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('inc/cextras_autoriser');
	include_spip('inc/config');
	/* Pas trouvé pourquoi mais ca plante (fonction lire_config non existante) lors d'une demande de 
		previsualisation meme avec cfg et Bonux actifs.
		Donc on verifie que la fonction existe bien */
	if (function_exists('lire_config')) {
		restreindre_extras('rubrique', 'date_utile', lire_config('daterubriques/secteurs',0), 'secteur');
	}
?>