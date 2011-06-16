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
function action_renvoyer_lettre_dist() {
	$securiser_action = charger_fonction('securiser_action','inc');
	$id_lettre = $securiser_action();



	if ($lettre = intval($id_lettre)
	  AND autoriser('editer', 'lettres')) {

		if (_request('tous')==1) {
			$lettre = new lettre($id_lettre);
			$lettre->enregistrer_statut('envoi_en_cours');
		}
		elseif($email = _request('email_abonne')){
			$redirect = _request('redirect');
			$abonne = new abonne(0, $email);
			if ($abonne->existe) {
				$resultat = $abonne->renvoyer_lettre($id_lettre);
				$GLOBALS['redirect'] = parametre_url($redirect,'message','renvoi_'.($resultat ? 'ok' : 'ko'));
			}
			else {
				$GLOBALS['redirect'] = parametre_url($redirect,'message','abonne_inexistant');
			}
		}
	}

}


?>