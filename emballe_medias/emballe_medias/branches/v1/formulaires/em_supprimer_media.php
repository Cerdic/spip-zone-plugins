<?php
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * Quentin Drouet (kent1@arscenic.info)
 *
 * © 2008/2011 - Distribue sous licence GNU/GPL
 *
 * Formulaire d'affichage et de suppression de médias
 **/

 if (!defined("_ECRIRE_INC_VERSION")) return;
 
function formulaires_em_supprimer_media_charger_dist($id_document,$type='',$objet,$id_objet,$redirect='',$compteur='',$total=''){
	global $visiteur_session;
	$valeurs = array();

	$vu = sql_getfetsel('vu','spip_documents_liens','id_document='.intval($id_document).' AND objet='.sql_quote($objet).' AND id_objet='.intval($id_objet));
	if(!$visiteur_session['id_auteur']){
		$valeurs['editable'] = false;
		$valeurs['message_erreur'] = _T('emballe_medias:droits_insuffisants').'<br />'._T('emballe_medias:connection_obligatoire');
	}
	else{
		include_spip('inc/autoriser');
		if(autoriser('modifier','document',$id_document,$visiteur_session) &&
		($vu == 'non')){
			if(sql_countsel('spip_documents_liens','id_document='.intval($id_document).' AND (id_objet !='.intval($id_objet).' OR objet != '.sql_quote($objet).')') > 0){
				$valeurs['contenu_bouton'] = filtrer_entites(_T('emballe_medias:bouton_delier_document'));
				$valeurs['message_bouton'] = filtrer_entites(_T('emballe_medias:message_delier_document'));
			}
			$valeurs['editable'] = true;
		}else{
			if($vu == 'oui')
				$valeurs['message_erreur_boutons'] = filtrer_entites(_T('emballe_medias:erreur_document_insere'));
			else
				$valeurs['message_erreur'] = filtrer_entites(_T('emballe_medias:droits_insuffisants'));
			$valeurs['editable'] = false;
		}
		$valeurs['objet'] = $objet;
		$valeurs['id_objet'] = $id_objet;
		$valeurs['id_document'] = $id_document;
		$valeurs['compteur'] = $compteur;
		$valeurs['total'] = $total;
		$valeurs['em_type'] = $type;
		$valeurs['redirect'] = $redirect ? $redirect : self();
	}
	return $valeurs;
}

function formulaires_em_supprimer_media_verifier_dist($id_document,$type='',$objet,$id_objet,$redirect='',$compteur='',$total=''){
	$erreurs = array();
	if(!intval($id_document)){
		$erreurs['id_document'] = 'Erreur';
	}
	if (count($erreurs)) $erreurs['message_erreur'] = _T('emballe_medias:verifier_formulaire');
	return $erreurs;
}

// http://doc.spip.org/@inc_editer_article_dist
function formulaires_em_supprimer_media_traiter_dist($id_document,$type='',$objet,$id_objet,$redirect='',$compteur='',$total=''){
	if(!intval($id_document)){
		$erreurs['id_document'] = 'Erreur';
		$invalider = false;
		return;
	}else{
		include_spip('action/documenter');

		supprimer_lien_document($id_document, $objet, $id_objet);

		/**
		 * Si plus de documents on repasse l'article en statut "prepa"
		 */
		if(!$id_document = sql_getfetsel("id_document","spip_documents_liens","id_objet=$id_objet AND objet=".sql_quote($objet))){
			$statut = sql_getfetsel("statut","spip_articles","id_article=$id_objet");
			if(in_array($statut,array('prop','publie'))){
				include_spip('action/editer_article');
				$c = array('statut' => 'prepa');
				instituer_article($id_objet, $c);
			}
		}
		$res['message_ok'] = 'Fichier supprimé';
		$învalider = true;
	}

	if($invalider){
		include_spip('inc/invalideur');
		suivre_invalideur("1",true);
		spip_log('EM : emballe_medias - invalider');
	}
	if($redirect){
		$res['redirect'] = $redirect;
	}
	return $res;
}

?>