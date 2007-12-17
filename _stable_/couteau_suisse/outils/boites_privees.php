<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function boites_privees_affiche_milieu($flux){
	switch($flux['args']['exec']) {
		case 'articles':
			$id_article = $flux['args']['id_article'];
			// le formulaire qu'on ajoute
			$flux['data'] .= formatspip_affiche_milieu($id_article);
			break;
		default:
			break;
	}
	return $flux;
}

function formatspip_affiche_milieu($id_article){
	include_spip('inc/presentation');
	
	$q = spip_query("SELECT descriptif, chapo, texte, ps FROM spip_articles WHERE id_article=$id_article");
	// compatibilite SPIP 1.92
	$row = function_exists('sql_fetch')?sql_fetch($q):spip_fetch_array($q);
	$txt = '';
	if (strlen($row['descriptif'])>0) {
		$txt .= "----- "._T('texte_descriptif_rapide')." -----\n\n";
		$txt .= $row['descriptif']."\n\n";
	}
	if (strlen($row['chapo'])>0) {
		$txt .= "----- "._T('info_chapeau')." -----\n\n";
		$txt .= $row['chapo']."\n\n";
	}
	if (strlen($row['texte'])>0) {
		$txt .= "----- "._T('info_texte')." -----\n\n";
		$txt .= $row['texte']."\n\n";
	}
	if (strlen($row['ps'])>0) {
		$txt .= "----- "._T('info_post_scriptum')." -----\n\n";
		$txt .= $row['ps']."\n\n";
	}

	$flux = '';
	// compatibilite SPIP < v1.93
	$compat = function_exists('bouton_block_depliable');
	$bouton = $compat?bouton_block_depliable(_T('cout:texte_formatspip'), 'invisible', "formatspip")
		:bouton_block_invisible("formatspip")._T('cout:texte_formatspip');
	$flux .= debut_cadre_enfonce(find_in_path('/img/formatspip-24.png'), true, '', $bouton);
	$flux .= $compat?debut_block_depliable(false, "formatspip")
		:debut_block_invisible("formatspip");
	$flux .= '<textarea cols="55" rows="20" disabled style="width:100%; color:black;" name="texte_formatspip">'.$txt.'</textarea>';
	$flux .= fin_block();
	$flux .= fin_cadre_enfonce(true);

	return $flux;
}

?>