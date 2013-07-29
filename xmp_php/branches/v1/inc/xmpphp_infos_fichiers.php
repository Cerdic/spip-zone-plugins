<?php
/**
 * XMP php
 * Récupération des métadonnées XMP
 *
 * Auteur : kent1
 * ©2011 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/presentation');
function inc_xmpphp_infos_fichiers_dist($id, $id_document,$type,$script='',$ignore_flag = false) {

	$corps = recuperer_fond('prive/prive_infos_fichier', $contexte=array('id_document'=>$id_document));

	// Si on a le droit de modifier les documents, on affiche les icones pour récupérer les infos et le logo
	if(autoriser('joindredocument',$type, $id)){
		$texte = _T('xmpphp:lien_recuperer_infos');
		$script = $type.'s';
		$redirect =  generer_url_ecrire($script,"id_$type=$id#portfolio_documents");
		$infos_doc = sql_fetsel("extension", "spip_documents","id_document=".intval($id_document));

		// Inspire de inc/legender
		if (test_espace_prive()){
			$redirect = str_replace('&amp;','&',$redirect);
			$action = ajax_action_auteur('xmpphp_infos', "$id/$type/$id_document", $script, "type=$type&id_$type=$id&show_infos_docs=$id_document#xmpphp_infos_plus-$id_document", array($texte));
		}
		else{
			$redirect = str_replace('&amp;','&',$redirect);
			$action = generer_action_auteur('xmpphp_infos', "$id/$type/$id_document", $redirect);
			$action = "<a href='$action'>$texte</a>";
		}
		$icone = find_in_path('images/xmpphp-24.png');
		$corps .= icone_horizontale($texte, $action, $icone, "creer.gif", false);
	}
	return ajax_action_greffe("xmpphp_infos_plus", $id_document, $corps);
}
?>