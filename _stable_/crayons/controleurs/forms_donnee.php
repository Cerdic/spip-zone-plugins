<?php
// un controleur qui n'utilise que php et les inputs dŽfauts
function controleurs_forms_donnee_dist($regs) {
	list(,$crayon,$type,$champ,$id) = $regs;
	$res = spip_query("SELECT id_form FROM spip_forms_donnees WHERE id_donnee="._q($id));
	if( !$row = spip_fetch_array($res))
		return array("$type $id $champ: " . _U('crayons:form_introuvable'), 6);
	$id_form = $row['id_form'];
	
	include_spip('inc/forms');
	$valeurs = Forms_valeurs($id,$id_form,$champ);
	if (!count($valeurs))
		return array("$type $id $champ: " . _U('crayons:pas_de_valeur'), 6);

	$n = new Crayon("$type-$champ-" . $id, $valeurs,
			array('hauteurMini' => 234,
				  'controleur' => 'formulaires/forms_structure'));
    
	$contexte = array(
		'champ'=>$champ,
		'erreur'=>serialize(array()),
		'id_form' => $id_form,
		'id_donnee' => $id,
		'valeurs' => serialize($valeurs));
	$html = $n->formulaire($contexte);
	$status = NULL;

	return array($html, $status);
}


