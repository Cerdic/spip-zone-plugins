<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V4
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_spipimmo_proprio_charger_dist($id_proprio='new', $retour=''){
        $valeurs = formulaires_editer_objet_charger('spipimmo_proprietaires', $id_proprio, '', '', $retour, '');
        return $valeurs;
}

function formulaires_editer_spipimmo_proprio_verifier_dist($id_proprio='new', $retour=''){
		$erreurs = formulaires_editer_objet_verifier('spipimmo_proprietaires', $id_proprio, array('civilite', 'nom', 'prenom', 'adresse_1', 'code_postal', 'ville', 'tel_fixe'));
        return $erreurs;
}

function formulaires_editer_spipimmo_proprio_traiter_dist($id_proprio='new', $retour=''){
        return formulaires_editer_objet_traiter('spipimmo_proprietaires', $id_proprio, '', '', $retour, '');
}
?>
