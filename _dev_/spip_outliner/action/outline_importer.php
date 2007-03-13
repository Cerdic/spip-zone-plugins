<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/forms');
include_spip('base/forms_base_api');
function action_outline_importer_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (($val = $_FILES['fichier_opml']) AND (isset($val['tmp_name']))) {
		$nom = $val['name'];
		$source = $val['tmp_name'];
		$unlink = true;
	}
	if (strlen($source)){
		$outline_importer = charger_fonction('outline_importer','inc');
		include_spip('inc/xml');
		$arbre = spip_xml_load($source, false);
		$id_table = $outline_importer($arbre,$nom);
	}
	if ($unlink)
		@unlink($source);

	if ($redirect = urldecode(_request('redirect'))){
		include_spip('inc/headers');
		redirige_par_entete(str_replace('&amp;','&',$redirect));
	}	
}

?>