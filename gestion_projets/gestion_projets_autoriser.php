<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function gestion_projets_autoriser(){}

// declarations d'autorisations


// création de projet
function autoriser_projet_creer_dist($faire, $type, $id="", $qui, $opt='') {

	$statut= $qui['statut'];	

	if($statut =='0minirezo') $retour=true;
	
	return $retour;

}


// édition des projets
function autoriser_projet_editer_dist($faire, $type, $id, $qui, $opt='') {
	
	$id_projet=_request('id_projet');
	
	$id_chef_projet= sql_getfetsel('id_chef_projet','spip_projets','id_projet='.sql_quote($id_projet));
	
	if($qui['statut'] =='0minirezo' or  $id_chef_projet==$qui['id_auteur']) $retour=true;
	
	return $retour;

}

// créer tâches
function autoriser_tache_creer_dist($faire, $type, $id, $qui, $opt='') {


	$participants= sql_getfetsel('participants','spip_projets','id_projet='.sql_quote($id));
	
	if(in_array($qui['id_auteur'],unserialize($participants))) $retour=true;

	return $retour;

}

// éditer tâches
function autoriser_tache_editer_dist($faire, $type, $id, $qui, $opt='') {

 $id = _request('id_tache');

	$participants= sql_getfetsel('participants','spip_projets_taches','id_tache='.sql_quote($id));
	
	if(in_array($qui['id_auteur'],unserialize($participants)) or $qui['statut']=='0minirezo') $retour=true;

	return $retour;

}

// éditer tâches
function autoriser_tache_voir_dist($faire, $type, $id, $qui, $opt='') {

	$participants= sql_getfetsel('participants','spip_projets_taches','id_tache='.sql_quote($id));
	
	if(in_array($qui['id_auteur'],unserialize($participants)) or $qui['statut']=='0minirezo') $retour=true;

	return $retour;

}

// Les rédacteurs peuvent voire le bouton
function autoriser_projets_dist($faire, $type, $id, $qui, $opt) {
    return true; 
}
?>