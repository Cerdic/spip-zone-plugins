<?php
/**
 * Gestion du formulaire de d'édition de reservation
 *
 * @plugin     Réservation Événements
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_reservation
 *     Identifiant du reservation. 'new' pour un nouveau reservation.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_reservation_identifier_dist($id_reservation='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_reservation)));
}

/**
 * Chargement du formulaire d'édition de reservation
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_reservation
 *     Identifiant du reservation. 'new' pour un nouveau reservation.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_reservation_charger_dist($id_reservation='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('reservation',$id_reservation,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	if(isset($valeurs['langue']))$valeurs['lang']=$valeurs['langue'];
	if(isset($valeurs['reference']) AND !$valeurs['reference']){
		$fonction_reference = charger_fonction('reservation_reference', 'inc/');
		$valeurs['reference']=$fonction_reference();  	
	}
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de reservation
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_reservation
 *     Identifiant du reservation. 'new' pour un nouveau reservation.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_reservation_verifier_dist($id_reservation='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$email=_request('email');
	$obligatoire=array('reference');
	
	if(!_request('id_auteur') AND (_request('nom') OR $email)) $obligatoire=array_merge($obligatoire,array('nom','email'));
	else $obligatoire=array_merge($obligatoire,array('id_auteur'));	
		
		

	
	
    
    $erreurs=formulaires_editer_objet_verifier('reservation',$id_reservation,$obligatoire);
         if ($email){
            include_spip('inc/filtres');
            // un redacteur qui modifie son email n'a pas le droit de le vider si il y en avait un
            if (!email_valide($email)){
                $id_auteur_session=isset($GLOBALS['visiteur_session']['id_auteur'])?$GLOBALS['visiteur_session']['id_auteur']:'';
                $erreurs['email'] = (($id_auteur==$id_auteur_session)?_T('form_email_non_valide'):_T('form_prop_indiquer_email'));
                }
            elseif(!$id_auteur){
                if($email_utilise=sql_getfetsel('email','spip_auteurs','email='.sql_quote($email))) $erreurs['email']=_T('reservation:erreur_email_utilise');
                }
            }	   
    
     // verifier et changer en datetime sql la date envoyee
     $verifier = charger_fonction('verifier', 'inc');
     $champ = 'date_paiement';
     $normaliser = null;
     if ($erreur = $verifier(_request($champ), 'date', array('normaliser'=>'datetime'), $normaliser)) {
     $erreurs[$champ] = $erreur;
     // si une valeur de normalisation a ete transmis, la prendre.
     } elseif (!is_null($normaliser) and _request($champ)) {
     set_request($champ, $normaliser);
     }
     else set_request($champ,'0000-00-00 00:00:00');
 return $erreurs;

}

/**
 * Traitement du formulaire d'édition de reservation
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_reservation
 *     Identifiant du reservation. 'new' pour un nouveau reservation.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_reservation_traiter_dist($id_reservation='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
    
	return formulaires_editer_objet_traiter('reservation',$id_reservation,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>