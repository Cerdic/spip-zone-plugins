<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Appelle l'élément du core pour chercher une rubrique, mais en lui donnant le "name" qu'on veut et compatible cfg
function marquepages_chercher_rubrique($msg, $id_rubrique, $name){
	
	$select = chercher_rubrique($msg, 0, $id_rubrique, 'article', 0, '', 0, 'form_simple');
	$select = preg_replace('/<select.*?>/is', '<select name="'.$name.'" id="'.$name.'">', $select);
	$select = preg_replace('/<input[[:blank:]]+type=[\'"]hidden[\'"].*?id=[\'"]id_parent[\'"].*?\/>/is', '<input type="hidden" name="'.$name.'" id="id_parent" value="'.$id_rubrique.'" />', $select);
	return $select;
	
}

?>
