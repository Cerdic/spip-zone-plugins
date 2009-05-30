<?php

// action/spiplistes_liste_des_abonnes.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/texte');
include_spip('inc/spiplistes_api');
include_spip('inc/spiplistes_agenda');

//CP-20080622
// retourne le bloc agenda des envois
function action_spiplistes_agenda_dist () {

	include_spip('inc/autoriser');
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	if(autoriser('moderer', 'liste')) {
		$periode = ($arg == _SPIPLISTES_AGENDA_PERIODE_HEBDO) ? $arg : _SPIPLISTES_AGENDA_PERIODE_MOIS;
		$redirect = rawurldecode(_request('redirect'));
		echo(spiplistes_boite_agenda_contenu($arg, $redirect, "/"._DIR_IMG_PACK));
	} 
	exit(0);

} //
?>