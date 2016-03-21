<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// indiquer qu'on peut s'authentifier via une auth PMB
$GLOBALS['liste_des_authentifications']['pmb'] = 'pmb';

$GLOBALS['pmb_statut_nouvel_auteur'] = '6forum';

// ne pas faire planter #URL_x dans une boucle (pmb:n)
$GLOBALS['exception_des_connect'][] = 'pmb';

// si la configuration l'autorise,
// on charge pmb partout, ce qui surcharge
// les fichiers de Zpip
// et met pmb systematiquement en colonne.
include_spip('inc/config');
if (lire_config('spip_pmb/pmb_partout', 'non') == 'oui') {
	_chemin(_DIR_PLUGIN_PMB . 'pmb_partout/');
}
