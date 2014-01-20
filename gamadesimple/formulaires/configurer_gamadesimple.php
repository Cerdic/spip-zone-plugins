<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
 
include_spip('inc/actions');
include_spip('inc/editer');
 
 
function formulaires_editer_gamadesimple_charger_dist($id_gamadesimple='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
$valeurs = formulaires_editer_objet_charger('gamadesimple',$id_gamadesimple,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
return $valeurs;
}
 

function formulaires_editer_gamadesimple_identifier_dist($id_gamadesimple='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
return serialize(array(intval($id_gamadesimple)));
}
 
function formulaires_editer_gamadesimple_verifier_dist($id_gamadesimple='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
return formulaires_editer_objet_verifier('gamadesimple', $id_gamadesimple);
}
 
function formulaires_editer_gamadesimple_traiter_dist($id_gamadesimple='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
return formulaires_editer_objet_traiter('gamadesimple',$id_gamadesimple,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
}
?>