<?php

include_spip("inc/presentation");
include_spip('public/assembler');

function exec_ga(){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_GA',(_DIR_PLUGINS.end($p)));
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	$out = $commencer_page(_T('Gestion des Google Analytics'), "ga", "ga");

	$contexte = array();
	foreach($_GET as $key=>$val)
		$contexte[$key] = $val;

	
	// gestion de la suppression d'un code
	if (isset($_GET['delete'])) {
		sql_delete('spip_ga', 'id = ' . _q($_GET['id']));
	}
		
	// gestion de l'ajout d'un code
	$out .= debut_gauche("ga",true);
	if (isset($_POST['validation'])) {
		$out .= debut_boite_info(true);
		include_spip('base/abstract_sql');
		if ($_POST['id'] != "") {
			sql_updateq('spip_ga', array('id_objet' => $_POST['id_objet'], 'objet' => $_POST['objet'], 'code' => $_POST['code'], 'lien' => $_POST['lien']), 'id = ' . _q($_POST['id']));
			$out .= "Code modifi&eacute;";
		}
		else {
			sql_insertq('spip_ga', array('id_objet' => $_POST['id_objet'], 'objet' => $_POST['objet'], 'code' => $_POST['code'], 'lien' => $_POST['lien']));
			$out .= "Code Ajout&eacute;";
		}
		$out .= fin_boite_info(true);
	}
	$out .= ga_boite($contexte);
	$out .= debut_droite('ga',true);
	$out .= recuperer_fond("prive/ga",$contexte);
	$out .= fin_gauche('ga',true);
	$out .= fin_page();

	echo $out;
}


function ga_boite($contexte){
	$out = "";
	$out .= debut_cadre_relief("../"._DIR_PLUGIN_GA."img_pack/ga.png",true);
	if (isset($contexte['id_ga'])) {
		$out .= bouton_block_depliable(_T("Modifier un code"),true,"ga_add");
	}
	else {
		$out .= bouton_block_depliable(_T("Ajouter un code"),false,"ga_add");
	}
	$out .= debut_block_depliable(isset($contexte['id_ga']),"ga_add");
	$out .= recuperer_fond("prive/ga_form",$contexte);
	$out .= fin_block();
	$out .= fin_cadre_relief(true);
	return $out;
}

?>