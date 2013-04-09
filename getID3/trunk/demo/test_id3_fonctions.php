<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores et vidéos directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2013 - Distribué sous licence GNU/GPL
 *
 * Fichier de fonctions spécifique au squelette demo/test_id3.html
 * 
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Récupération des informations contenues dans les id3
 * et affichage pour tester (les données sont enregistrées en base)
 * 
 * @param $id_document int 
 * 	identifiant numérique du document en base
 * @param $retour boolean
 * 	si true, retourne un affichage html
 * 	si false, retourne l'array des informations
 * @return $output string|array
 * 	l'ensemble des infos id3 du document en fonction du paramètre $retour ci dessus
 */
function recuperer_id3_doc($id_document,$retour=true){
	$recuperer_id3 = charger_fonction('getid3_recuperer_infos','inc');
	$fichier = sql_getfetsel('fichier','spip_documents','id_document='.intval($id_document));
	if($fichier){
		include_spip('inc/documents');
		$fichier = get_spip_doc($fichier);
		if(file_exists($fichier)){
			$id3_content = $recuperer_id3($fichier);
		
			if($retour){
				$output = '';
				foreach($id3_content as $cle => $val){
					if(preg_match('/cover/',$cle)){
						$output .= ($val) ? '<img src='.url_absolue($val).' /><br /><br />' : '';
					}else{
						$output .= ($val) ? _T('getid3:info_'.$cle).' '.$val.'<br />' : '';
					}
				}
			}else{
				$output = $id3_content;
			}
			return $output;
		}
	}
	return false;
}

?>