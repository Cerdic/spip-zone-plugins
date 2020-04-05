<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1), BoOz
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions'); // *action_auteur

function inc_infos_son_dist($id, $id_document,$type,$extension,$script='',$ignore_flag = false) {
	global $connect_id_auteur, $connect_statut, $visiteur_session;

	if(_AJAX){
		include_spip('public/assembler');
		include_spip('inc/presentation');
	}
	$c = (is_array($visiteur_session)
		AND is_array($visiteur_session['prefs']))
				? $visiteur_session['prefs']['couleur']: 1;
	$couleurs = charger_fonction('couleurs', 'inc');
	$couleur_foncee = parametre_url($couleurs($c),'couleur_foncee');
	$corps = recuperer_fond('prive/prive_infos_son', $contexte=array('id_document'=>$id_document,'couleur_foncee'=>$couleur_foncee));

	// Si on a le droit de modifier les documents, on affiche les icones pour récupérer les infos et le logo
	if(autoriser('joindredocument',$type, $id)){
		$texte = _T('getid3:recuperer_infos');
		$script = $type.'s';
		$redirect =  generer_url_ecrire($script,"id_$type=$id#portfolio_documents");

		// Inspire de inc/legender
		if (test_espace_prive()){
			$redirect = str_replace('&amp;','&',$redirect);
			$action = ajax_action_auteur('getid3_infos', "$id/$type/$id_document", $script, "type=$type&id_$type=$id&show_infos_docs=$id_document#infosdoc-$id_document", array($texte));
		}
		else{
			$redirect = str_replace('&amp;','&',$redirect);
			$action = generer_action_auteur('getid3_infos', "$id/$type/$id_document", $redirect);
			$action = "<a href='$action'>$texte</a>";
		}
		if(!_AJAX){
			$corps .= icone_horizontale($texte, $action, find_in_path('images/id3v2-24.png'), "rien.gif", false);
		}
		$sons_metas = lire_config('getid3_write',array());
		if(in_array($extension,$sons_metas)){
			$texte_write = _T('getid3:lien_modifier_id3');
			if (test_espace_prive()){
				$redirect = str_replace('&amp;','&',$redirect);
				$action = parametre_url(generer_url_ecrire('document_id3_editer', "id_document=".$id_document),"redirect",$redirect);
			}
			if(!_AJAX){
				$corps .= icone_horizontale($texte_write, $action, find_in_path('images/id3v2-24.png'), "edit.gif", false);
			}
		}
	}
	return $corps;
}
?>