<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');
function formulaires_editer_chant_charger_dist($id_chant='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
        $valeurs = formulaires_editer_objet_charger('chant',$id_chant,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
        return $valeurs;
}
/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_chant_identifier_dist($id_chant='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
        return serialize(array(intval($id_chant)));
}
function formulaires_editer_chant_verifier_dist($id_chant='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
        return formulaires_editer_objet_verifier('chant', $id_chant);
}
function formulaires_editer_chant_traiter_dist($id_chant='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
        return formulaires_editer_objet_traiter('chant',$id_chant,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
}

?>