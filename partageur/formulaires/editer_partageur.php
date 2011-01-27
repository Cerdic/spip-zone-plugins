<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_partageur_charger_dist($id_partageur='new', $retour=''){
	$valeurs = formulaires_editer_objet_charger('partageur', $id_partageur, '', '', $retour, '');
	return $valeurs;
}

function formulaires_editer_partageur_verifier_dist($id_partageur='new', $retour=''){
  /*if (_request("url_site")== "http://") {
              
  } */
	$erreurs = formulaires_editer_objet_verifier('partageur', $id_partageur, array('titre','url_site'));
	return $erreurs;
}

function formulaires_editer_partageur_traiter_dist($id_partageur='new', $retour=''){
 	return formulaires_editer_objet_traiter('partageur', $id_partageur, '', '', $retour, '');  // voir action/editeur_partageur
}

?>