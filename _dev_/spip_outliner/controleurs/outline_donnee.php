<?php
// un controleur qui n'utilise que php et les inputs dŽfauts
function controleurs_outline_donnee_dist($regs) {
	list(,$crayon,$type,$champ,$id) = $regs;
	$res = spip_query("SELECT d.id_form,f.type_form FROM spip_forms_donnees AS d JOIN spip_forms AS f ON f.id_form=d.id_form WHERE d.id_donnee="._q($id));
	if( !$row = spip_fetch_array($res))
		return array("$type $id $champ: " . _U('crayons:pas_de_valeur'), 6);
	$id_form = $row['id_form'];
	
	include_spip('inc/forms');
	$valeurs = Forms_valeurs($id,$id_form,$champ);
	# autoriser la creation de valeurs !
	if (!count($valeurs))
		#return array("$type $id $champ: " . _U('crayons:pas_de_valeur'), 6);
		$valeurs = array($champ=>'');

	$n = new Crayon("$type-$champ-" . $id, $valeurs,
			array(/*'hauteurMini' => 234,*/
				  'controleur' => 'formulaires/forms_structure'));
    
	$contexte = array(
		'champ'=>$champ,
		'erreur'=>serialize(array()),
		'id_form' => $id_form,
		'id_donnee' => $id,
		'valeurs' => serialize($valeurs));
	$html = $n->formulaire($contexte);
	
	// retirer le fieldset et sa legend
	$html = preg_replace(",<fieldset>\s*<legend>.*</legend>(.*)</fieldset>,Uims","\\1",$html);
	$status = NULL;

	return array($html, $status);
}


