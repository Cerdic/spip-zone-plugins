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

/**
 * Dupliquer une lettre existante sur une autre
 */
function action_tester_lettre_dist() {
	$securiser_action = charger_fonction('securiser_action','inc');
	$id_lettre = $securiser_action();


	if (intval($id_lettre)
	  AND autoriser('editer', 'lettres')) {

		$lettre = new lettre($id_lettre);
		$res = $lettre->tester();

		if ($redirect = _request('redirect'))
			$GLOBALS['redirect'] = parametre_url($redirect, 'message', 'test_'.($res?'ok':'ko'));

	}

}


?>