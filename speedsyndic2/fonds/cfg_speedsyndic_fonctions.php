<?php
function cfg_speedsyndic_verifier(&$cfg){
$err = array();

if (!is_numeric($cfg->val['frequence'])) {
$err['frequence']= _T('speedsyndic:erreur')._T('speedsyndic:frequence_num');
}
elseif ($cfg->val['frequence'] < 30) {
$err['frequence']=_T('speedsyndic:erreur')._T('speedsyndic:frequence_trente');
}
return $cfg->ajouter_erreurs($err);

}
?>