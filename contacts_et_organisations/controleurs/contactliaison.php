<?php

function controleurs_contactliaison_dist($regs) {
	list(,$crayon,$type,$champ,$id) = $regs;
	
	$id_contact = intval($id);
	$id_organisation = intval($champ);
	
	$val = sql_getfetsel('type_liaison', 'spip_organisations_contacts',
		array('id_contact='.$id_contact, 'id_organisation='.$id_organisation));
		
    $valeur = array('type_liaison' => $val);
	$n = new Crayon($crayon, $valeur);
	
	$contexte = array();
    if (is_string($val) and preg_match(",[\n\r],", $val))
		$contexte['type_liaison'] = array('type'=>'texte');
	else
		$contexte['type_liaison'] = array('type'=>'ligne');

    $html = $n->formulaire($contexte);
    include_spip('action/crayon_html');
    $html = crayons_formulaire($html, 'crayons_contactliaison_store');
    
    $status = NULL;

	return array($html, $status);
}
?>
