<?php
#cf page exec configurer_montants

	include_spip('inc/cextras_autoriser');
	include_spip('inc/config');
	/* On verifie que la fonction existe bien */
	if (function_exists('lire_config')) {
		restreindre_extras('rubrique', 'prix', lire_config('montants/secteurs',0), 'secteur');
		restreindre_extras('article', 'prix', lire_config('montants/secteurs',0), 'secteur');
	}
?>