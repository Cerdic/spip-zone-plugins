<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2012 - cmtmt, BoOz, kent1
 * Licence GPL v3
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Function déterminant les champs à utiliser dans le formulaire en fonction de la configuration du plugin
 *
 * @param int $id_auteur[optional] 
 * 	Dans le cas ou cette option est présente, on ne retourne que les champs autorisé à être modifiés dans la configuration
 * @return array $valeurs 
 * 	Un array contenant l'ensemble des champs
 */
function inc_inscription3_champs_formulaire_dist($id_auteur=null,$type_formulaire=null) {
	include_spip('inc/config');
	$config_i3 = lire_config('inscription3',array());
	$valeurs = array();

	/**
	 * Les champs à ne pas prendre en compte :
	 * -  Les options du formulaires qui ne créent pas de champs
	 * -  Les champs créés en base qui ne doivent pas être chargés ni écris
	 */
	$exceptions_des_champs_chargement = pipeline('i3_exceptions_chargement_champs_auteurs_elargis',array());
	$exceptions_des_champs_creation = pipeline('i3_exceptions_des_champs_auteurs_elargis',array());
	$exceptions_des_champs = array_merge($exceptions_des_champs_creation,$exceptions_des_champs_chargement);
	
	/**
	 * On liste l'ensemble des options du formulaire de configuration que l'on a en méta pour les trier
	 */
	foreach ($config_i3 as $clef => $valeur) {
		/**
		 * Définition du $suffixe que l'on souhaite :
		 * - _fiche_mod si l'id_auteur est renseigné (c'est une modification de données d'une fiche)
		 * - rien si pas d'id_auteur, c'est la création d'un nouvel auteur
		 * - _fiche_mod_nocreation ou _nocreation pour les champs non gérés par inscription3 (extras 2)
		 */
		$suffixe = '';
		if(is_numeric($id_auteur)){
			$suffixe = '_fiche_mod';
		}
		if(preg_match("/_(nocreation)/i", $clef)){
			$suffixe = $suffixe.'_nocreation';
		}
		
		/**
		 * On vire les suffixes potentiels des valeurs pour ne retourner que les champs réels
		 */
		$clef = preg_replace('/(_fiche_mod|_fiche|_nocreation|_obligatoire|_table)/','',$clef);
		
		/**
		 * Pour qu'un champ soit retourné, il doit :
		 * -* être configuré à on $clef.$suffixe
		 * -* ne pas être déjà présent dans l'array qui sera retourné
		 * -* ne pas être dans les pipelines de restrictions au dessus
		 */
		if(($config_i3[$clef.$suffixe] == 'on') && !in_array($clef,$valeurs) && !in_array($clef,$exceptions_des_champs)) {
			$valeurs[] = $clef;
		}
	}
	if($type_formulaire == "inscription"){
		$valeurs[] = 'mail_inscription';
		$valeurs[] = 'nom_inscription';
	}
	
	return $valeurs;
}
?>