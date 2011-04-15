<?php

function action_crayons_contactliaison_store_dist() {
	include_spip('action/crayons_store');
	// on donne une autre fonction de traitement des donnees
	return action_crayons_store_args('crayons_contactliaison_store');
}

function crayons_contactliaison_store() {
	$options = array(
			'f_get_valeur' => 'crayons_contactliaison_store_get_valeur',
			'f_set_modifs' => 'crayons_contactliaison_store_set_modifs');
	return  crayons_store($options);
}

function crayons_contactliaison_store_get_valeur($content, $regs) {
	list(,$crayon,$type,$modele,$id) = $regs;
	$id_contact = intval($id);
	$id_organisation = intval($modele);
	
	$val = sql_getfetsel('type_liaison', 'spip_organisations_contacts',
		array('id_contact='.$id_contact, 'id_organisation='.$id_organisation));
		
	return array('type_liaison' => $val);	
}


function crayons_contactliaison_store_set_modifs($modifs, $return) {
	foreach ($modifs as $modif) {
		list($type, $modele, $id, $content, $wid) = $modif;
			$id_contact = intval($id);
			$id_organisation = intval($modele);		
			sql_updateq('spip_organisations_contacts', $content, array(
				'id_contact=' . $id_contact,
				'id_organisation=' . $id_organisation
			));
	}
	return $return;
}


?>
