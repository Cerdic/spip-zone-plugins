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

function xmpphp_editer_contenu_objet($flux){
	$type_form = $flux['args']['type'];
	$id_document = $flux['args']['id'];
	if(is_array($flux['args']) && (in_array($type_form,array('illustrer_document','case_document','document')))){
		$document = sql_fetsel("docs.id_document, docs.id_orig, docs.extension,docs.mode,docs.distant, L.vu,L.objet,L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".intval($id_document));
		$extension = $document['extension'];
		$type = $document['objet'];
		$id = $document['id_objet'];
		if(in_array($type_form,array('case_document','document'))){
			if(($document['distant'] != 'oui') && in_array($extension,lire_config('xmpphp/extensions',array('ai','psd','pdf')))){
				$ajouts = '';
				if(extension_loaded('xmpPHPToolkit')){
					$infos_fichiers = charger_fonction('xmpphp_infos_fichiers', 'inc');
					$ajouts .= $infos_fichiers($id,$id_document,$type);
				}
				if($type_form == 'case_document'){
					$flux['data'] .= $ajouts;
				}else{
					if(preg_match(",<li [^>]*class=[\"']editer_infos.*>(.*)<\/li>,Uims",$flux['data'],$regs)){
						$infos_doc = recuperer_fond('prive/prive_infos_fichier', $contexte=array('id_document'=>$id_document));
						$flux['data'] = preg_replace(",($regs[1]),Uims","\\1".$infos_doc,$flux['data']);
					}
				}
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline document_desc_actions (Mediathèque)
 * On ajoute un lien pour récupérer le logo et relancer les encodages
 * 
 * @param array $flux Le contexte du pipeline
 * @return $flux Le contexte du pipeline complété
 */
function xmpphp_document_desc_actions($flux){
	$id_document = $flux['args']['id_document'];
	$document = sql_fetsel('*','spip_documents','id_document='.intval($id_document));
	if(($document['distant'] != 'oui') && in_array($document['extension'],lire_config('xmpphp/extensions',array('ai','psd','pdf')))){
		$texte = _T('xmpphp:lien_recuperer_infos');
		$redirect = ancre_url(self(),"doc".$id_document);
		$action = generer_action_auteur('xmpphp_infos', "0/article/$id_document", $redirect);
		$flux['data'] .= " | <a href='$action'>$texte</a>";
	}
	return $flux;
}
?>