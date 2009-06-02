<?php 

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_marquepage_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	$id_forum = intval($arg);
	
	// Si id_forum n'est pas un nombre, c'est une création
	if (!$id_forum){
		$id_rubrique = intval(_request('id_rubrique'));
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
		if (!($id_rubrique and $id_auteur)){
			$message = _T('marquepages:erreur');
		}
		else{
			include_spip('inc/marquepages_api');
			$url = _request('mp_url');
			$titre = _request('mp_titre');
			$description = _request('mp_description');
			$statut = _request('mp_visibilite');
			$tags = _request('mp_etiquettes');
			$id_forum = marquepages_ajouter($id_rubrique, $url, $titre, $description, $statut, $tags);
			if ($id_forum){
				$message =  _T('marquepages:enregistre');
				//include_spip('inc/invalideur');
				//suivre_invalideur('id_forum/'.$id_forum);
			}
			else
				$message =  _T('marquepages:erreur');
		}
	}
	// Sinon c'est une modif
	else{
		include_spip('inc/marquepages_api');
		$url = _request('mp_url');
		$titre = _request('mp_titre');
		$description = _request('mp_description');
		$statut = _request('mp_visibilite');
		$tags = _request('mp_etiquettes');
		$ok = marquepages_modifier($id_forum, $titre, $description, $statut, $tags);
		if ($ok){
			$message =  _T('marquepages:modifie');
			//include_spip('inc/invalideur');
			//suivre_invalideur('id_forum/'.$id_forum);
		}
		else
			$message =  _T('marquepages:erreur');
	}
	
	if ($redirect = _request('redirect')){
		include_spip('inc/headers');
		redirige_par_entete(str_replace('&amp;', '&', rawurldecode($redirect)));
	}
	else
		return array($id_forum, $message);
	
}

?>
