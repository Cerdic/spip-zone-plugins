<?php

$GLOBALS['dossier_squelettes'] = 'plugins/snb_v2/squelettes';


// spip 2 $GLOBALS['meta'][version_installee] = 15828
// spip 3 $GLOBALS['meta'][version_installee] >= 18407

// include_spip('inc_version');
$spip_branche_principale = substr($GLOBALS[spip_version_branche], 0, 1);

// NOTE : spip_version_branche n'est pas trouv� depuis ce fichier
// d�fini dans /spip/ecrire/inc_version.php (ligne 412)
?>