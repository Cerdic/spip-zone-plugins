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
?>