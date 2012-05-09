<?php

$GLOBALS['dossier_squelettes'] = 'plugins/sel/squelettes';

// spip 2 $GLOBALS['meta'][version_installee] = 15828
// spip 3 $GLOBALS['meta'][version_installee] >= 18407

// include_spip('inc_version');
$spip_branche_principale = substr($GLOBALS[spip_version_branche], 0, 1);

// NOTE : spip_version_branche n'est pas trouvé depuis ce fichier
// défini dans /spip/ecrire/inc_version.php (ligne 412)
/*
session_set('es',array(
'nom_pays' => '<:sel:pays_espagne:>',
'nom_fiduc' => 'euro',
'iso_fiduc4217' => 'EUR',
));

session_set('ee',array(
'nom_pays' => 'estonie',
'nom_fiduc' => 'couronne estonienne',
'iso_fiduc4217' => 'EEK',
));

session_set('fi',array(
'nom_pays' => 'finlande',
'nom_fiduc' => 'euro',
'iso_fiduc4217' => 'EUR',
));

session_set('fr',array(
'nom_pays' => 'france',
'nom_fiduc' => 'euro',
'iso_fiduc4217' => 'EUR',
));
*/


?>