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
function action_dupliquer_lettre() {
	$securiser_action = charger_fonction('securiser_action','inc');
	$copie_lettre = $securiser_action();


	if (intval($copie_lettre)
	  AND autoriser('editer', 'lettres')) {

		$lettre = new lettre(-1);
		$lettre->copier_lettre($copie_lettre);

		if ($redirect = _request('redirect'))
			$GLOBALS['redirect'] = parametre_url($redirect, 'id_lettre', $lettre->id_lettre);

	}

}


?>