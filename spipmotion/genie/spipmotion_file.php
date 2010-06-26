<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos et son directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 *
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

/**
 * Fonction appelée par le génie de SPIP à intervalle régulier
 *
 * Consulte la file d'attente pour savoir si des documents sont à encoder.
 * Vérifie en amont que la meta "spipmotion_casse" ne soit pas à "oui", si elle l'est
 * aucun encodage n'est lancé
 *
 * S'il existe au moins un document à encoder on lance le premier
 * Si ce document original n'existe plus, on supprime ses occurences dans la file d'attente
 * et on relance la même fonction
 *
 * @return
 * @param object $time
 */
function genie_spipmotion_file($time)  {
	$nb_encodages = sql_countsel('spip_spipmotion_attentes', "encode='non'");
	spip_log("Il y a $nb_encodages vidéo(s) à encoder","spipmotion");
	$en_cours = sql_getfetsel('id_spipmotion_attente','spip_spipmotion_attentes',"encode='en_cours'");
	if(($nb_encodages>0) && ($GLOBALS['meta']['spipmotion_casse'] != 'oui') && !intval($en_cours)){
		$doc_attente = sql_fetsel("*","spip_spipmotion_attentes","encode='non'","","id_spipmotion_attente ASC","1");
		$id_document = $doc_attente['id_document'];
		$id_doc_attente = $doc_attente['id_spipmotion_attente'];
		$document = sql_fetsel('*','spip_documents','id_document='.sql_quote($id_document));
		if($document['id_document']){
			spip_log('on encode le doc '.$id_document,'spipmotion');
			$encoder = charger_fonction('encodage','inc');
			$encoder($document,$id_doc_attente);
		}else{
			sql_delete('spip_spipmotion_attentes','id_document='.sql_quote($id_document));
			genie_spipmotion_file($time);
		}

	}else if(lire_config('spipmotion_casse') == 'oui'){
		spip_log('Attention, problème dans la configuration','spipmotion');
	}else if(intval($en_cours)){
		spip_log("L'id $en_cours de la file d'attente est en cours d'encodage",'spipmotion');
	}
	return 1;
}
?>