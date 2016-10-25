<?php
/**
 * Plugin gabarits pour Spip 2.0
 * Licence GPL
 * 
 *
 */

/* pour que le pipeline ne rale pas ! */
function gabarits_autoriser($flux){return $flux;}

function autoriser_gabarit_modifier_dist($faire,$quoi,$id,$qui,$options) {
	// admin ok
	if ($qui['statut'] == '0minirezo')
		return true;
	// sinon auteur du gabarit ok
	$id_auteur = sql_getfetsel('id_auteur','spip_gabarits','id_gabarit='.intval($id));
	if ($qui['id_auteur'] != $id_auteur){
		return false;
	}else{
		return true;
	}
}

function autoriser_gabarit_voir_dist($faire,$quoi,$id,$qui,$options) {
	return true;
}

function autoriser_gabarit_supprimer_dist($faire,$quoi,$id,$qui,$options) {
	return autoriser_gabarit_modifier_dist($faire,$quoi,$id,$qui,$options);
}

function autoriser_gabarit_previsualiser_dist($faire, $type, $id, $qui, $opt) {
	return
		(test_espace_prive() AND autoriser('ecrire'));
}


function autoriser_gabarit_bouton_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

?>