<?php
/**
 * Plugin Canevas pour Spip 2.0
 * Licence GPL
 * 
 *
 */

/* pour que le pipeline ne rale pas ! */
function canevas_autoriser($flux){return $flux;}

function autoriser_canevas_modifier_dist($faire,$quoi,$id,$qui,$options) {
	// admin ok
	if ($qui['statut'] == '0minirezo')
		return true;
	// sinon auteur du canevas ok
	$id_auteur = sql_getfetsel('id_auteur','spip_canevas','id_canevas='.intval($id));
	if ($qui['id_auteur'] != $id_auteur){
		return false;
	}else{
		return true;
	}
}

function autoriser_canevas_voir_dist($faire,$quoi,$id,$qui,$options) {
	return true;
}

function autoriser_canevas_supprimer_dist($faire,$quoi,$id,$qui,$options) {
	return autoriser_canevas_modifier_dist($faire,$quoi,$id,$qui,$options);
}

function autoriser_canevas_previsualiser_dist($faire, $type, $id, $qui, $opt) {
	return
		(test_espace_prive() AND autoriser('ecrire'));
}


function autoriser_canevas_bouton_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

?>