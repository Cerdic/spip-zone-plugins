<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/simplecal_utils');

function formulaires_editer_evenement_charger_dist($id_evenement='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='evenements_edit_config', $row=array(), $hidden=''){
    // Recupere automatiquement les champs de la table (entre autres...)
    $valeurs = formulaires_editer_objet_charger('evenement',$id_evenement,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);

    // Modif pour avoir un affichage formate ('14/08/2011 00:00:00' => '14/08/2011')
    $valeurs["date_debut"] = date_sql2affichage($valeurs["date_debut"]);
    $valeurs["date_fin"] = date_sql2affichage($valeurs["date_fin"]);
    
    return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_evenement_identifier_dist($id_evenement='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='evenements_edit_config', $row=array(), $hidden=''){
	return serialize(array(intval($id_evenement),$lier_trad));
}


// Choix par defaut des options de presentation
function evenements_edit_config($row)
{
	global $spip_ecran, $spip_lang, $spip_display;

	$config = $GLOBALS['meta'];
	$config['lignes'] = ($spip_ecran == "large")? 8 : 5;
	$config['langue'] = $spip_lang;

	$config['restreint'] = ($row['statut'] == 'publie');
	return $config;
}

function formulaires_editer_evenement_verifier_dist($id_evenement='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='evenements_edit_config', $row=array(), $hidden=''){
	// Verifications automatiques
    // ---------------------------
    $champs_obligatoires = array('titre', 'date_debut');
    $erreurs = formulaires_editer_objet_verifier('evenement', $id_evenement, $champs_obligatoires);
    
    // Autres verifications
    // ---------------------
    
    // Date de debut saisie correctement ?
    $date_debut = trim(_request('date_debut'));
    if ($date_debut && date_saisie2sql($date_debut) == '0000-00-00 00:00:00'){
        $erreurs['date_debut'] = _T('simplecal:validation_date_format');
    }
        
    // Date de fin saisie correctement ?
    $date_fin = trim(_request('date_fin'));
    if ($date_fin && date_saisie2sql($date_fin) == '0000-00-00 00:00:00'){
        $erreurs['date_fin'] = _T('simplecal:validation_date_format');
    }

    // Chronologie : Date de fin >= Date de debut (si pas d'autres erreurs sur les dates)
    if (!$erreurs['date_debut'] && !$erreurs['date_fin']){
        if ($date_debut && $date_fin){
            $date_debut_sql = date_saisie2sql($date_debut);
            $date_fin_sql = date_saisie2sql($date_fin);
            
            if ($date_debut_sql != '0000-00-00 00:00:00' && $date_fin_sql != '0000-00-00 00:00:00'){
                if ($date_fin_sql < $date_debut_sql){
                    $erreurs['date_fin'] = _T('simplecal:validation_date_chronologie');
                }
            }
        }
    }    
    
	return $erreurs;
}

function formulaires_editer_evenement_traiter_dist($id_evenement='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='evenements_edit_config', $row=array(), $hidden=''){
	
    // On remet les dates au format SQL ('14/08/2011' => '14/08/2011 00:00:00')
    set_request("date_debut", date_saisie2sql(_request("date_debut")));
    set_request("date_fin", date_saisie2sql(_request("date_fin")));
    
    return formulaires_editer_objet_traiter('evenement',$id_evenement,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>
