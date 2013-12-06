<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function cameras_autoriser(){}



// -----------------
// Objet cameras


// bouton de menu
function autoriser_cameras_menu_dist($faire, $type, $id, $qui, $opts){
	return autoriser('creer', 'camera', '', $qui, $opts);
} 

// bouton d'outils rapides
function autoriser_cameracreer_menu_dist($faire, $type, $id, $qui, $opts){
	return autoriser('creer', 'camera', '', $qui, $opts);
} 

// creer
function autoriser_camera_creer_dist($faire, $type, $id, $qui, $opt) {
	return (in_array($qui['statut'], array('0minirezo', '1comite')) AND (!lire_config('cameras/auth_camedit'))); 
}

// voir le contenu 
function autoriser_voir($faire, $type, $id, $qui, $opt) {
	//Seuls les admins peuvent voir les auteurs et on autorise un auteur de voir ça propre fiche
	 if ($type == 'auteur')
		 return ($qui['statut'] == '0minirezo') OR ($qui['id_auteur']==$id);
	return true;
}

//On masque l'entrée auteur dans le menu pour les non admins
function autoriser_auteurs_menu($faire, $type, $id, $qui, $opt){
	return ($qui['statut'] == '0minirezo');
}


// modifier
function autoriser_camera_modifier_dist($faire, $type, $id, $qui, $opt) {
	return
		in_array($qui['statut'], array('0minirezo'))
		OR auteurs_camera($id, "id_auteur=".$qui['id_auteur']);
}

//Changer le statut : seuls les admins peuvent publier
function autoriser_camera_instituer_dist($faire, $type, $id, $qui, $opt){
	return ($qui['statut'] == '0minirezo');
}

// supprimer
function autoriser_camera_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// creer dans une rubrique
 function autoriser_rubrique_creercameradans_dist($faire, $type, $id, $qui, $opt) {
	return true;
} 

// associer (lier / delier)
function autoriser_associercameras_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}




// liste des auteurs, cf: ecrire/inc/autoriser
function auteurs_camera($id, $cond=''){
	return sql_allfetsel("id_auteur", "spip_auteurs_liens", "objet='camera' AND id_objet=$id". ($cond ? " AND $cond" : ''));
}

?>