<?php
/**
 * Fonctions utiles au plugin Réservation Événements
 *
 * @plugin     Réservation Événements
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function champs_extras_reservation(){
    //les champs extras auteur
    include_spip('cextras_pipelines');
    
    if(function_exists('champs_extras_objet')){
        //Charger les définitions pour la création des formulaires
        $champs_extras_auteurs=champs_extras_objet(table_objet_sql('auteur'));

    }
    
    return $champs_extras_auteurs;
}

function nom_statuts($statuts){
    $liste_objets=lister_tables_objets_sql();
    
    $statuts_selectionnees=array();
    
    if(is_array($statuts)){
      foreach($liste_objets['spip_reservations']['statut_textes_instituer'] AS $statut=>$label){
         if(in_array($statut,$statuts))$statuts_selectionnees[$statut]=_T($label);
        }                  
    }
    return $statuts_selectionnees;
}

//retourne les statuts qui définissent si un événement est complet
function statuts_complet(){
	$statuts_complets=charger_fonction('complet','inc/statuts');
	$statuts=$statuts_complets();
	return $statuts;
}

function chercher_label($label, $champ_extra='') {
	
	if(!$champ_extra)	{
		//les champs extras auteur
	    include_spip('cextras_pipelines');
	    
	    if(function_exists('champs_extras_objet')){
	        //Charger les définitions pour la création des formulaires
	        $champ_extra=champs_extras_objet(table_objet_sql('auteur'));
	    }				
	}

	foreach($champ_extra as $value) {
		
		if(isset($value['options']['nom']) and $value['options']['nom'] == $label)$label=$value['options']['label'];
	}

	return $label;
	
}
