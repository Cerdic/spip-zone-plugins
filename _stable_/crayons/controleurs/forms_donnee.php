<?php
// un controleur qui n'utilise que php et les inputs dfauts
function controleurs_forms_donnee_dist($regs) {
    list(,$crayon,$type,$champ,$id) = $regs;
	$q = "SELECT valeur FROM spip_forms_donnees_champs WHERE champ="._q($champ)." AND id_donnee="._q($id);
	$s = spip_query($q);
	if (!$t = spip_fetch_array($s))
	    return array("$type $id $champ: " . _U('crayons:pas_de_valeur'), 6);

	$valeur = $t['valeur'];

	$n = new Crayon("$type-$champ-$id", $valeur);
    
    $return = array(
    	// html
	    $n->formulaire(),
    	// status
    	null);

	return $return;
}


