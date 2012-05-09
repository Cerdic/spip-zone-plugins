<?php
include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_annonce_charger_dist($id_annonce='new',$id_auteur) {
	$valeurs = formulaires_editer_objet_charger('annonce', $id_annonce, '', '', $retour,'');
	// $valeurs = array();
	$valeurs['id_auteur'] = $id_auteur;
	if (($id_annonce=='oui') || ($id_annonce=='new')) $valeurs['date_debut'] = date('d/m/Y');
	if ($GLOBALS['meta']['moderation_annonce']=='non') $valeurs['statut'] = '1annonce_ok';
	else $valeurs['statut'] = '0nouvelle';

	if (($id_annonce !='oui') && ($id_annonce !='new')) {
		$adresse_auteur = sql_fetsel(array('adresse1','adresse2','code_postal','ville','pays'),spip_auteurs,'id_auteur='.sql_quote($id_auteur));

		if ($adresse_auteur['adresse1']== $valeurs['adresse1']) {

			$valeurs['adresse1']='';
			$valeurs['adresse2']='';
			$valeurs['code_postal']='';
			$valeurs['ville']='';
			$valeurs['pays']='';
		}
	}
	return $valeurs;
}

function formulaires_editer_annonce_verifier_dist($id_annonce='new') {

	$erreurs = formulaires_editer_objet_verifier('annonce',$id_annonce,array('titre','direction_echange','nature','date_debut','nom_referent','total_unite'),'',$retour,''); // tableau des request
	if (_request(total_fiduc) && ((_request(justification_fiduc)=='') || (is_null(_request(justification_fiduc)))))
	$erreurs['justification_fiduc']=_T('formerr_justif_fiduc_oblig'); // 
	// $erreurs = array();
	return $erreurs;
}

function formulaires_editer_annonce_traiter_dist($id_annonce='new') {
	
	return formulaires_editer_objet_traiter('annonce',$id_annonce,'', '', $retour='?page=nouvelle_annonce_theme', '');
}
?>
