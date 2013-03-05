<?php
/**
 * Plugin Selection d&#039;objets
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_selection_objet_identifier_dist($id_selection_objet='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_selection_objet)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_selection_objet_charger_dist($id_selection_objet='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('selection_objet',$id_selection_objet,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
    
    $contexte=array(
    'objet_dest'=>_request('objet_dest'),
    'id_objet_dest'=>_request('id_objet_dest'),
    'objet'=>_request('objet'), 
    'id_objet'=>_request('id_objet'),   
    'titre'=>_request('titre'), 
    'statut'=>_request('statut'), 
    'lang'=>_request('lang'),                      
    );

    $valeurs=array_merge($contexte,$valeurs);   
    
    $valeurs['_hidden'].='<input type="hidden" name="lang" value="'.$valeurs['lang'].'">';              
    $valeurs['_hidden'].='<input type="hidden" name="objet_dest" value="'.$valeurs['objet_dest'].'">';
    $valeurs['_hidden'].='<input type="hidden" name="id_objet_dest" value="'.$valeurs['id_objet_dest'].'">'; 
    $valeurs['_hidden'].='<input type="hidden" name="objet" value="'.$valeurs['objet'].'">';
    $valeurs['_hidden'].='<input type="hidden" name="id_objet" value="'.$valeurs['id_objet'].'">';     
    $valeurs['_hidden'].='<input type="hidden" name="statut" value="'.$valeurs['statut'].'">';        
        //Les types liens pour l'objet concerné
    if(!$types=lire_config('selection_objet/type_liens_'.$valeurs['objet_dest'],array()))$types=lire_config('selection_objet/type_liens',array());
    
    
    $types_lien=array();
    foreach($types as $cle => $valeur){
        $types_lien[$cle]=_T($valeur);
        }
    $valeurs['types_lien']=$types_lien;

	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_selection_objet_verifier_dist($id_selection_objet='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('selection_objet',$id_selection_objet);
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_selection_objet_traiter_dist($id_selection_objet='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('selection_objet',$id_selection_objet,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>