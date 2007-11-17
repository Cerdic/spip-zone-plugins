<?


/*
 * Affiche le formulaire CFG de la vue (fond) demandee
 */

function balise_CFG_VUE($p){
	//return calculer_balise_dynamique($p, 'VUE_CFG', array());
	
	$vue = 			sinon(interprete_argument_balise(1,$p), "''"); // indispensable neanmmoins
	$id = 			sinon(interprete_argument_balise(2,$p), "''");
	//$aff_titre = 	sinon(interprete_argument_balise(3,$p), "''");
	$p->code = "calculer_CFG_VUE($vue, $id)";
	return $p;
}

function calculer_CFG_VUE($fond, $id){
	include_spip('inc/cfg');
	$cfg = cfg_charger_classe('cfg');
	$config = & new $cfg($fond, $fond, $id); 
		
	//$sortie = ($afficher_titre)
	//	? "<h2>$config->titre</h2>\n"
	//	: "";
	if ($config->autoriser()){
		$config->traiter();	
		return $config->formulaire();	
	} else
		return "NON!";
}

?>
