<?php
/**
 * Gestion de l'action supprimer_pensebete
 *
 * @plugin Pensebetes
 * @copyright  2019
 * @author     Vincent CALLIES
 * @licence    GNU/GPL
 * @package SPIP\Pensebetes\Actions
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action pour supprimer un Pense-bête
 *
 * @param  int    $id_pensebete    Identifiant de l'objet
 * @return void
**/
 
function action_supprimer_pensebete_dist($id_pensebete=null){

	if (is_null($id_pensebete)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_pensebete = $securiser_action();
	}

	if (!autoriser('pensebete_supprimer', 'pensebete', $id_pensebete)) {
		include_spip('inc/minipres');
		minipres(_T('pensebete:erreur_titre'),_T('pensebete:erreur_suppression'));
		exit;
	}	
	
	// on va faire
	spip_log("Demande de suppression du pense-bête n°$id_pensebete par l'auteur n°".
			$GLOBALS['auteur_session']['id_auteur']." (".$GLOBALS['auteur_session']['nom'].")",
					'pensebetes.' . _LOG_INFO_IMPORTANTE);

	// on peut pas faire
	if (empty($id_pensebete)) {
		spip_log("action_supprimer_pensebete_dist : $id_pensebete est vide",
					'pensebetes.' . _LOG_ERREUR);

		return;
	}

	// cas suppression
	if ($id_pensebete) {
		$espace_prive = test_espace_prive();
		// avant de supprimer, si on est dans l'espace public, on garde la liaison
		if (!$espace_prive){
			$liens = sql_fetsel("id_objet,objet", "spip_pensebetes_liens", "id_pensebete=$id_pensebete");
			if (!$liens) return;
		}

		sql_delete('spip_pensebetes',  'id_pensebete=' . intval($id_pensebete));
		// si l'on est en train de visualiser le contenu du pense-bête
		// sa suppression ne permet pas la redirection prévue :
		$espace_prive = test_espace_prive();
		if (_request('exec')=='pensebete' and $espace_prive) {
			// Nous sommes dans l'espace privé et l'on regarde le contenu d'un pensebete
			// On le supprimer, il faut revenir vers la liste des pensesbetes
			include_spip('inc/headers');
			$redirect = generer_url_ecrire('pensebetes');
			redirige_par_entete($redirect);
		} elseif(!$espace_prive) {
			// nous sommes dans l'espace public (identifié) et l'on supprime le pensebete
			// il faut invalider le cache
			// pour que la page et le modèle puissent être rafraîchis.
			include_spip('inc/invalideur');
			suivre_invalideur("id='".$liens['objet']."/".$liens['id_objet']."'");
		}
	}
	else {
		spip_log("action_supprimer_pensebete_dist : suppession d'$id_pensebete pas possible",
					'pensebetes.' . _LOG_ERREUR);

	}
}

?>