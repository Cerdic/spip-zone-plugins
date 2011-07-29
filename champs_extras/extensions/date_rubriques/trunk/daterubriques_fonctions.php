<?php
	include_spip('inc/cextras_autoriser');
	include_spip('inc/config');
	restreindre_extras('rubrique', 'date_utile', lire_config('daterubriques/secteurs',0), 'secteur');
?>