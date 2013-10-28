<?php
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * b_b (http://http://www.weblog.eliaz.fr)
 *
 * © 2008/2012 - Distribue sous licence GNU/GPL
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
		$valeurs['self'] = _request('self') ? _request('self') : self();
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

function formulaires_em_supprimer_media_traiter_dist($id_document,$type='',$objet,$id_objet,$redirect='',$compteur='',$total=''){
	if(!intval($id_document)){
		$erreurs['id_document'] = 'Erreur';
		$invalider = false;
		return;
	}else{
		include_spip('action/dissocier_document');

		supprimer_lien_document($id_document, $objet, $id_objet,true);

		/**
		 * Si plus de documents on repasse l'article en statut "prepa"
		 */
		if(!$id_document = sql_getfetsel("id_document","spip_documents_liens","id_objet=$id_objet AND objet=".sql_quote($objet))){
			include_spip('action/editer_objet');
			include_spip('inc/config');
			$id_table = id_table_objet($objet);
			$table = table_objet_sql($objet);
			$statut = sql_getfetsel("statut","$table","$id_table=$id_objet");
			if(lire_config('emballe_medias/fichiers/statut_article_apres_dernier_document','prepa') == 'poubelle') {
				$c = array('statut' => 'poubelle');
				objet_instituer($objet,$id_objet, $c);
			} else if(in_array($statut,array('prop','publie'))){
				$c = array('statut' => 'prepa');
				objet_instituer($objet,$id_objet, $c);
			}
		}
		$res['message_ok'] = 'Fichier supprimé';
		$învalider = true;
	}

	if($invalider){
		include_spip('inc/invalideur');
		suivre_invalideur("1",true);
	}
	if($redirect)
		$res['redirect'] = $redirect;
	return $res;
}

?>
