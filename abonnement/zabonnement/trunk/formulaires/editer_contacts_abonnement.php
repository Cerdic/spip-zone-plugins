<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');
//#ENV{id_contacts_abonnement,new},objet=abonnement,id_objet=#ENV{id_abonnement},id_auteur=#ENV{id_auteur},"javascript:ajaxReload('contenu');"})]</div>
//$type, $id='new', $id_parent=0, $lier_trad=0, $retour='', $config_fonc='articles_edit_config', $row=array(), $hidden=''){

function formulaires_editer_contacts_abonnement_charger_dist($id_contacts_abonnement='new', $objet='',$id_objet='', $id_auteur='', $retour='', $supp=''){
	$valeurs = formulaires_editer_objet_charger('contacts_abonnement', $id_contacts_abonnement, '', '', $retour, '');

	$valeurs['page_envoi'] = $supp;
	//uniquement en hidden dans la page modif auteur
	if($supp=='auteur_infos'){
		unset($valeurs['id_objet']);
		$valeurs['id_auteur'] = $id_auteur;
		$valeurs['objet'] = $objet;
		$valeurs['id_objet'] = $id_objet;
	}
	return $valeurs;
}

function formulaires_editer_contacts_abonnement_verifier_dist($id_contacts_abonnement='new', $objet='',$id_objet='', $id_auteur='', $retour=''){
	return formulaires_editer_objet_verifier('contacts_abonnement', $id_contacts_abonnement);
}

function formulaires_editer_contacts_abonnement_traiter_dist($id_contacts_abonnement='new', $objet='',$id_objet='', $id_auteur='', $retour=''){
	
	// si redirection demandee, on refuse le traitement en ajax
	if ($retour) refuser_traiter_formulaire_ajax();	
	
	return formulaires_editer_objet_traiter('contacts_abonnement', $id_contacts_abonnement, '', 0, $retour);	
		
}

?>
