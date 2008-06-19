<?php
include_spip('public/interfaces');
global $table_des_traitements;
if (!isset($table_des_traitements["TEXTE"][0])){
$table_des_traitements["TEXTE"][0] = 'propre(GestionMetas_mots_strong(%s))';
}
?>