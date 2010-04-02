<?php
/**
* Plugin SPIPmotion
* par kent1 (http://kent1.sklunk.net)
*
* Copyright (c) 2007-2010
* Logiciel libre distribué sous licence GNU/GPL.
*
**/

/**
 * Fonction appelée par le génie de SPIP à intervalle régulier
 *
 * Consulte la file d'attente pour savoir si des documents sont à encoder.
 * S'il existe au moins un document à encoder on lance le premier et ainsi de suite
 *
 * @return
 * @param object $time
 */
function genie_spipmotion_file($time)  {
	$nb_encodages = sql_countsel('spip_spipmotion_attentes', "encode='non'");
	spip_log("Il y a $nb_encodages vidéo(s) à encoder","spipmotion");
	if($nb_encodages>0){
		$encoder = charger_fonction('encodage','inc');
		$doc_attente = sql_fetsel("*","spip_spipmotion_attentes","encode='non'","","maj DESC","1");
		$id_document = $doc_attente['id_document'];
		$id_doc_attente = $doc_attente['id_spipmotion_attente'];
		$document = sql_fetsel('*','spip_documents','id_document='.sql_quote($id_document));
		$encoder($document,$id_doc_attente);
	}
}
?>