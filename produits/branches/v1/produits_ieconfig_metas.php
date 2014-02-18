<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function produits_ieconfig_metas($table){
	$table['produits']['titre'] = _T('produits:produits_titre');
	$table['produits']['icone'] = 'prive/themes/spip/images/produits-24.png';
	$table['produits']['metas_serialize'] = 'produits';
	return $table;
}

?>