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
	if(($nb_encodages>0) && (lire_config('spipmotion_casse') != 'oui')){
		$doc_attente = sql_fetsel("*","spip_spipmotion_attentes","encode='non'","","maj DESC","1");
		$id_document = $doc_attente['id_document'];
		$id_doc_attente = $doc_attente['id_spipmotion_attente'];
		$document = sql_fetsel('*','spip_documents','id_document='.sql_quote($id_document));
		if($document['id_document']){
			$encoder = charger_fonction('encodage','inc');
			$encoder($document,$id_doc_attente);
			spip_log('on encode le doc '.$id_document,'spipmotion');
		}else{
			spip_log("Le document $id_document n'existe plus",'spipmotion');
			sql_delete('spip_spipmotion_attentes','id_document='.sql_quote($id_document));
			spip_log("On relance la file","spipmotion");
			genie_spipmotion_file($time);
		}

	}else if(lire_config('spipmotion_casse') == 'oui'){
		spip_log('Attention, problème dans la configuration','spipmotion');
	}
}
?>