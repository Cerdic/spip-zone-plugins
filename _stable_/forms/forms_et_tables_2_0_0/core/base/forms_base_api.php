<?php
/**
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 * Version 1 de l'api, Fonction depreciee
 * 
 */

// les fonctions a jour sont dans la v2 de l'api
include_spip('base/forms_base_api_v2');

/**
 * Depreciee, utiliser forms_inserer_table
 */
function forms_creer_table($structure_xml,$type=NULL, $unique = true, $c=NULL){
	return forms_inserer_table($structure_xml,$type,$unique,$c);
}

/**
 * Depreciee, utiliser forms_lister_tables
 */
function forms_liste_tables($type){
	spip_log("fonction forms_lister_tables depreciee");
	return forms_lister_tables($type);
}

/**
 * Depreciee, utiliser forms_inserer_champ
 */
function forms_creer_champ($id_form,$type,$titre,$c=NULL,$champ=""){
	return forms_inserer_champ($id_form,$type,$titre,$c,$champ);
}

/**
 * Depreciee, utiliser forms_inserer_donnee
 */
function forms_creer_donnee($id_form,$c = NULL, $rang=NULL){
	return forms_inserer_donnee($id_form,$c,$rang);
}

/**
 * Depreciee, utiliser forms_informer_donnee
 */
function forms_infos_donnee($id_donnee,$specifiant=true,$linkable=false){
	return forms_informer_donnee($id_donnee,$specifiant,$linkable);
}

/**
 * Depreciee, utiliser forms_informer_donnee qui retourne plus d'infos
 */
function forms_decrit_donnee($id_donnee,$specifiant=true,$linkable=false){
	list($id_form,$titreform,$type_form,$t) = forms_infos_donnee($id_donnee,$specifiant,$linkable);
	return $t;
}

/**
 * Depreciee, utiliser forms_lister_donnees_liees
 */
function forms_donnees_liees($id_donnee,$type_form_lie){
	return forms_lister_donnees_liees($id_donnee,$type_form_lie);
}

/**
 * Depreciee, utiliser forms_separer_donnees_liees
 */
function forms_delier_donnee($id_donnee_1,$id_donnee_2=0,$type_form_lie = ""){
	return forms_separer_donnees_liees($id_donnee_1,$id_donnee_2,$type_form_lie);
}

/**
 * Depreciee, utiliser forms_enumerer_les_valeurs_champs
 */
function forms_les_valeurs($id_form, $id_donnee, $champ, $separateur=",",$etoile=false, $traduit=true){
	return forms_enumerer_les_valeurs_champs($id_form,$id_donnee,$champ,$separateur,$etoile,$traduit);
}

/**
 * Depreciee, utiliser forms_arbre_lister_relations
 */
function forms_arbre_liste_relations($id_form,$id_parent,$position="enfant"){
	return forms_arbre_lister_relations($id_form,$id_parent,$position);
}
?>