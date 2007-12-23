<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function boites_privees_affiche_milieu($flux){
	switch($flux['args']['exec']) {
		case 'articles': {
			// texte original au format spip
			if(defined('boites_privees_ARTICLES'))
				$flux['data'] .= cs_formatspip($flux['args']['id_article']);
			break;
		}
		default:
			break;
	}
	return $flux;
}

function boites_privees_affiche_droite($flux) {
	switch($flux['args']['exec']) {
		case 'auteurs':
		case 'auteur_infos': if (defined('boites_privees_AUTEURS') && cout_autoriser()) {
			// compatibilite SPIP 1.92
			$fetch = function_exists('sql_fetch')?'sql_fetch':'spip_fetch_array';
			// pour cs_lien()
			include_spip('cout_fonctions');
			$flux['data'] .= debut_cadre_relief(find_in_path('img/couteau-24.gif'),true,'',_T('icone_statistiques_visites'))
				. "<p><b>"._T('cout:derniers_connectes')."</b></p>"
				. cs_derniers_connectes($fetch)
				. "<p><b>"._T('cout:non_confirmes')."</b></p>"
				. cs_non_confirmes($fetch)
				. fin_cadre_relief(true);
			break;
		}
		default:
			break;
	}
	return $flux;
}

function cs_formatspip($id_article){
	include_spip('inc/presentation');

	$q = spip_query("SELECT descriptif,chapo,texte,ps FROM spip_articles WHERE id_article=$id_article");
	// compatibilite SPIP 1.92
	$row = function_exists('sql_fetch')?sql_fetch($q):spip_fetch_array($q);
	$txt = ''; $i = 0;
	if (strlen($row['descriptif'])>0) {
		$txt .= '===== '._T('texte_descriptif_rapide')." =====\n\n"
			. $row['descriptif']."\n\n"; $i++;
	}
	if (strlen($row['chapo'])>0) {
		$txt .= '===== '._T('info_chapeau')." =====\n\n"
			. $row['chapo']."\n\n"; $i++;
	}
	if (strlen($row['texte'])>0) {
		$txt .= '===== '._T('info_texte')." =====\n\n"
			. $row['texte']."\n\n"; $i++;
	}
	if (strlen($row['ps'])>0) {
		$txt .= '===== '._T('info_post_scriptum')." =====\n\n"
			. $row['ps']."\n\n"; $i++;
	}
	$titre =  _T('cout:texte'.($i>1?'s':'').'_formatspip');
	// compatibilite SPIP < v1.93
	$compat = function_exists('bouton_block_depliable');
	$bouton = $compat?bouton_block_depliable($titre, 'invisible', "formatspip")
		:bouton_block_invisible("formatspip").$titre;
	$bloc = $compat?debut_block_depliable(false, "formatspip")
		:debut_block_invisible("formatspip");
	return debut_cadre_enfonce(find_in_path('/img/formatspip-24.png'), true, '', $bouton)
			. $bloc
			. '<textarea readonly cols="55" rows="30" class="formo" style="width:100%; font-size:90%;" name="texte_formatspip">'.$txt.'</textarea>'
			. fin_block()
			. fin_cadre_enfonce(true);
}

function cs_listeulli($res) {
	if(!count($res)) $res[] = _T('cout:variable_vide');
	$li = '<li style="margin:0.2em 0.4em;">';
	return "<p><ul style='list-style-type:none; padding:0;margin:0;'>$li".join("</li>$li", $res).'</li></ul></p>';
}

function cs_derniers_connectes($fetch){ 
	$query = spip_query("SELECT nom,statut,email,en_ligne FROM spip_auteurs ORDER BY en_ligne DESC LIMIT 10"); 
	$res = array(); 
    while ($row = $fetch($query)) $res[]=_T('cout:stats_auteur', array(
		'icon' => '<a href="'.generer_url_ecrire("auteurs","statut=" . $row['statut']).'">' . bonhomme_statut($row) . '</a>',
		'nom' => cs_lien($row['email'], $row['nom']),
		'date' => $row['en_ligne']
	));
	return cs_listeulli($res);
} 

function cs_non_confirmes($fetch){ 
	$query = spip_query('SELECT nom,email,maj FROM spip_auteurs WHERE statut=\'nouveau\' ORDER BY maj DESC'); 
	$res = array(); 
    while ($row = $fetch($query)) $res[]=_T('cout:stats_auteur', array(
		'icon' => http_img_pack("aide.gif", '', '', _T('cout:attente_confirmation')),
		'nom' => cs_lien($row['email'], $row['nom']),
		'date' => $row['maj']
	)); 
	return cs_listeulli($res);
} 

?>