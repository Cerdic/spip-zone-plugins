<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function boites_privees_affiche_gauche($flux){
	if(defined('boites_privees_URLS_PROPRES')) 
		switch($flux['args']['exec']) {
			case 'articles': $flux['data'] .= cs_urls_propres('article', $flux['args']['id_article']); break;
			case 'naviguer': $flux['data'] .= cs_urls_propres('rubrique', $flux['args']['id_rubrique']); break;
			case 'auteur_infos': $flux['data'] .= cs_urls_propres('auteur', $flux['args']['id_auteur']); break;
			case 'breves_voir': $flux['data'] .= cs_urls_propres('breve', $flux['args']['id_breve']); break;
			case 'mots_edit': $flux['data'] .= cs_urls_propres('mot', $flux['args']['id_mot']); break;
			case 'sites': $flux['data'] .= cs_urls_propres('syndic', $flux['args']['id_syndic']); break;
		}
	return $flux;
}

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
				. "<p><b>"._T('couteau:derniers_connectes')."</b></p>"
				. cs_derniers_connectes($fetch)
				. "<p><b>"._T('couteau:non_confirmes')."</b></p>"
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
	$titre =  _T('couteau:texte'.($i>1?'s':'').'_formatspip');
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
	if(!count($res)) $res[] = _T('couteau:variable_vide');
	$li = '<li style="margin:0.2em 0.4em;">';
	return "<p><ul style='list-style-type:none; padding:0;margin:0;'>$li".join("</li>$li", $res).'</li></ul></p>';
}

function cs_date($numdate) {
	$date_array = recup_date($numdate);
	if (!$date_array) return '?';
	list($annee, $mois, $jour, $heures, $minutes, $sec) = $date_array;
	return _T('couteau:stats_date', array('jour'=>$jour, 'mois'=>$mois, 'annee'=>substr($annee,2), 'h'=>$heures, 'm'=>$minutes, 's'=>$sec));
}

function cs_derniers_connectes($fetch){ 
	$query = spip_query("SELECT id_auteur,nom,statut,en_ligne FROM spip_auteurs ORDER BY en_ligne DESC LIMIT 10"); 
	$res = array(); 
    while ($row = $fetch($query)) $res[]=_T('couteau:stats_auteur', array(
		'icon' => '<a href="'.generer_url_ecrire("auteurs","statut=" . $row['statut']).'">' . bonhomme_statut($row) . '</a>',
		'nom' => cs_lien(generer_url_ecrire("auteur_infos","id_auteur=$row[id_auteur]"), $row['nom']),
		'date' => cs_date($row['en_ligne'])
	));
	return cs_listeulli($res);
} 

function cs_non_confirmes($fetch){ 
	$query = spip_query('SELECT id_auteur,nom,maj FROM spip_auteurs WHERE statut=\'nouveau\' ORDER BY maj DESC'); 
	$res = array(); 
    while ($row = $fetch($query)) $res[]=_T('couteau:stats_auteur', array(
		'icon' => http_img_pack("aide.gif", '', '', _T('couteau:attente_confirmation')),
		'nom' => cs_lien(generer_url_ecrire("auteur_infos","id_auteur=$row[id_auteur]"), $row['nom']),
		'date' => cs_date($row['maj'])
	)); 
	return cs_listeulli($res);
} 

function cs_urls_propres($type, $id) {
	// fonction reservee a SPIP 2.0
	if(!defined('_SPIP19200')) return '';

	return cadre_depliable(find_in_path('img/couteau-24.gif'),
		'<b>'._T('couteau:urls_propres_titre').'</b>',
		false,	// true = deplie
		cs_urls_propres_descrip($type, $id),
		'bp_urls_propres');
}

function cs_urls_propres_descrip($type, $id) {
	global $type_urls;
	$url = generer_url_entite_absolue($id, $type, '', '', true);
	$s = sql_select("url", "spip_urls", "id_objet=$id AND type='$type'", '', 'date DESC');
	$res = "";
	while ($t = sql_fetch($s)) $res .= "&bull;&nbsp;$t[url]\n";

//spip_log($row);
	$format = in_array($type_urls, array('page', 'standard', 'html'))
		?_T('couteau:urls_propres_erreur')
		:_T('couteau:urls_propres_objet');
	return propre(
		_T('couteau:urls_propres_format', array(
			'format'=>$type_urls,
			'url'=>generer_url_ecrire('admin_couteau_suisse', 'cmd=descrip&outil=type_urls#cs_infos')
		)). "\n\n"
		. $format . "\n\n"
		. '{{'. _T('couteau:2pts', array(
			'objet'=>strtoupper(unicode2charset(html2unicode(_T('couteau:objet_'.$type)))).' '.$id
		))."}}\n\n"
		. "$res\n\n[["
		. _T('couteau:urls_propres_lien'). "|{$url}->{$url}]]\n\n"
		
	);
}

?>