<?php


/**
 * SPIP-Lettres
 *
 * Copyright (c) 2006-2009
 * Agence Artégo http://www.artego.fr
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
 *
 **/


include_spip('lettres_fonctions');


function action_instituer_lettre_dist() {
	$securiser_action = charger_fonction('securiser_action','inc');
	$id_lettre = $securiser_action();

	if ($statut = _request('statut_nouv')
	  AND autoriser('instituer', 'lettre',$id_lettre)) {

		$lettre = new lettre($id_lettre);
		$lettre->enregistrer_statut($statut);

	}


}


?>