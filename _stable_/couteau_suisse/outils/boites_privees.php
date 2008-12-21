<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip('inc/layer');

// compatibilite avec SPIP 1.91
if(!function_exists('block_parfois_visible')) {
	function block_parfois_visible($nom, $invite, $masque, $style='') {
		return "<div style='$style'>" . bouton_block_invisible($nom) . "$invite</div>"
		. debut_block_invisible($nom) . "<div id='$nom'>$masque</div>" . fin_block() . '</div>';
	}
}
// compatibilite SPIP SQL < 2.0
if(!defined('_SPIP19300')) {
	include_spip('base/abstract_sql');
	if(!function_exists('sql_select')) { function sql_select($s=array(),$f=array(),$w=array(),$g=array(),$o=array(),$l='',$h=array(),$sv='') {
		return spip_abstract_select($s,$f,$w,$g,$o,$l,'',$h,'','',$sv); } }
	if(!function_exists('sql_fetch')) { function sql_fetch($s) { return spip_fetch_array($s); } }
	if(!function_exists('sql_update')) { function sql_update($t, $e, $w=array()) {
		if(!is_array($t))$t=array($t); if(!is_array($e))$e=array($e); if(!is_array($w))$w=array($w);
		$q=$r =''; foreach($e as $i=>$v) $e[$i] = "$i=$v";
		return spip_query("UPDATE ".join(',',$t)." SET ".join(',',$e).(empty($w)?'':" WHERE ".join(' AND ',$w)));
	} }
}

function boites_privees_affiche_gauche($flux){
	if(defined('boites_privees_TRI_AUTEURS') && ($flux['args']['exec']=='articles')) 
		$flux['data'] .= action_rapide_tri_auteurs($flux['args']['id_article']);
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
			// pour cs_lien()
			include_spip('cout_fonctions');
			$flux['data'] .= debut_cadre_relief(find_in_path('img/couteau-24.gif'),true,'',_T('icone_statistiques_visites'))
				. "<p><b>"._T('couteau:derniers_connectes')."</b></p>"
				. cs_derniers_connectes()
				. "<p><b>"._T('couteau:non_confirmes')."</b></p>"
				. cs_non_confirmes()
				. fin_cadre_relief(true);
			break;
		}
		default:
			break;
	}
	return $flux;
}

function cs_formatspip($id_article){
	$q = sql_select('descriptif,chapo,texte,ps', 'spip_articles', "id_article=$id_article");
	$row = sql_fetch($q);
	$txt = ''; $i = 0;
	if (strlen($row['descriptif'])>0)
		$txt .= '===== '._T('texte_descriptif_rapide')." =====\n\n"
			. $row['descriptif']."\n\n"; $i++;
	if (strlen($row['chapo'])>0)
		$txt .= '===== '._T('info_chapeau')." =====\n\n"
			. $row['chapo']."\n\n"; $i++;
	if (strlen($row['texte'])>0)
		$txt .= '===== '._T('info_texte')." =====\n\n"
			. $row['texte']."\n\n"; $i++;
	if (strlen($row['ps'])>0)
		$txt .= '===== '._T('info_post_scriptum')." =====\n\n"
			. $row['ps']."\n\n"; $i++;
	$titre =  _T('couteau:texte'.($i>1?'s':'').'_formatspip');
	// compatibilite SPIP < 2.0
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

function cs_derniers_connectes(){ 
	$query = sql_select('id_auteur,nom,statut,en_ligne', 'spip_auteurs', '', '', array('en_ligne DESC'), '10'); 
	$res = array(); 
    while ($row = sql_fetch($query)) $res[]=_T('couteau:stats_auteur', array(
		'icon' => '<a href="'.generer_url_ecrire("auteurs","statut=" . $row['statut']).'">' . bonhomme_statut($row) . '</a>',
		'nom' => cs_lien(generer_url_ecrire("auteur_infos","id_auteur=$row[id_auteur]"), $row['nom']),
		'date' => cs_date_long($row['en_ligne'])
	));
	return cs_listeulli($res);
} 

function cs_non_confirmes(){ 
	$query = sql_select('id_auteur,nom,maj', 'spip_auteurs', "statut='nouveau'", '', array('maj DESC')); 
	$res = array(); 
    while ($row = sql_fetch($query)) $res[]=_T('couteau:stats_auteur', array(
		'icon' => http_img_pack("aide.gif", '', '', _T('couteau:attente_confirmation')),
		'nom' => cs_lien(generer_url_ecrire("auteur_infos","id_auteur=$row[id_auteur]"), $row['nom']),
		'date' => cs_date_long($row['maj'])
	)); 
	return cs_listeulli($res);
} 

function cs_urls_propres($type, $id) {
	// SPIP < 2.0
	if(!defined('_SPIP19300')) return debut_cadre_relief(find_in_path('img/couteau-24.gif'), true)
		. "<div class='verdana1' style='text-align: left;'>"
		. block_parfois_visible('bp_urls', '<b>'._T('couteau:urls_propres_titre').'</b>', cs_urls_propres_descrip($type, $id), 'text-align: center;')
		. "</div>"
		. fin_cadre_relief(true);
	// SPIP >= 2.0
	return cadre_depliable(find_in_path('img/couteau-24.gif'),
		'<b>'._T('couteau:urls_propres_titre').'</b>',
		false,	// true = deplie
		cs_urls_propres_descrip($type, $id),
		'bp_urls_propres');
}

function cs_urls_propres_descrip($type, $id) {
	global $type_urls;
	$res = "";
	// SPIP >= 2.0
	if(defined('_SPIP19300')) {
		$url = generer_url_entite_absolue($id, $type, '', '', true);
		$s = sql_select("url", "spip_urls", "id_objet=$id AND type='$type'", '', 'date DESC');
		while ($t = sql_fetch($s)) $res .= "&bull;&nbsp;$t[url]\n";
	// SPIP < 2.0
	} else {
		// impossible de calculer l'url publique d'ici.
		$url = '';
		$table = $type.($type=='syndic'?'':'s');
		$r = spip_query("SELECT url_propre FROM spip_$table WHERE id_$type=$id");
		if ($r AND $r = spip_fetch_array($r)) $res .= "&bull;&nbsp;$r[url_propre]\n";
	}

	$format = in_array($type_urls, array('page', 'standard', 'html'))
		?_T('couteau:urls_propres_erreur')
		:_T('couteau:urls_propres_objet');
	$mem=$GLOBALS['class_spip_plus'];
	$GLOBALS['class_spip_plus']=' class="spip"';
	$res = propre(
		_T('couteau:urls_propres_format', array(
			'format'=>$type_urls,
			'url'=>generer_url_ecrire('admin_couteau_suisse', 'cmd=descrip&outil=type_urls#cs_infos')
		)). "\n\n"
		. $format . "\n\n"
		. '|{{'. _T('couteau:2pts', array(
			'objet'=>strtoupper(filtrer_entites(_T('couteau:objet_'.$type))).' '.$id
		))."}}|\n"
		. "|$res|\n"
		. (strlen($url)
			?"\n[<span>[". _T('couteau:urls_propres_lien'). "|{$url}->{$url}]</span>]\n\n"
			:'<iframe src="./?exec=action_rapide&arg=type_urls_spip&format=iframe&type_objet='.$type.'&id_objet='.$id.'" width="100%" style="border:none; height:4em;"></iframe>')
	);
	$GLOBALS['class_spip_plus']=$mem;
	return $res;
}

// fonction qui centralise : 
//	- 1er affichage : action_rapide_tri_auteurs($id_article)
//	- appel exec : action_rapide_tri_auteurs()
// 	- appel action : action_rapide_tri_auteurs($id_article, $id_auteur, $monter)
function action_rapide_tri_auteurs($id_article=0, $id_auteur=0, $monter=true) {
spip_log("action_rapide_tri_auteurs : $id_article, $id_auteur, $monter");
	// si appel action...
	 if($id_auteur) {
		$s = sql_select('id_auteur', 'spip_auteurs_articles', "id_article=$id_article");
		$i=0; $j=0;
		while ($a = sql_fetch($s)) {
			if($a['id_auteur']==$id_auteur) { $i = $a['id_auteur']; break; }
			$j = $a['id_auteur'];
		}
		if(!$monter && $i && ($a = sql_fetch($s))) $j = $a['id_auteur'];
		spip_log("action_rapide_tri_auteurs, article $id_article : echange entre l'auteur $i et l'auteur $j");
		if($i && $j) {
			sql_update("spip_auteurs_articles", array('id_auteur'=>-99), "id_article=$id_article AND id_auteur=$i");
			sql_update("spip_auteurs_articles", array('id_auteur'=>$i), "id_article=$id_article AND id_auteur=$j");
			sql_update("spip_auteurs_articles", array('id_auteur'=>$j), "id_article=$id_article AND id_auteur=-99");
		}
		return;
	 }
	$id = $id_article?$id_article:_request('id_article');
	include_spip('public/assembler'); // pour recuperer_fond(), SPIP < 2.0
	$texte = trim(recuperer_fond('fonds/tri_auteurs', array('id_article'=>$id)));
	// syntaxe : ajax_action_auteur($action, $id, $script, $args='', $corps=false, $args_ajax='', $fct_ajax='')
	if(strlen($texte))
		$texte = ajax_action_auteur('action_rapide', 'tri_auteurs', 'articles', "arg=boites_privees&fct=tri_auteurs&id_article=$id#bp_tri_auteurs_corps", $texte);
	// si appel exec...
	if(!$id_article) return $texte;
	// ici, 1er affichage !
	if(!strlen($texte)) return '';
	// SPIP < 2.0
	if(!defined('_SPIP19300')) return debut_cadre_relief(find_in_path('img/couteau-24.gif'), true)
		. "<div class='verdana1' style='text-align: left;'>"
		. block_parfois_visible('bp_ta', '<b>'._T('couteau:tri_auteurs').'</b>', "<div id='bp_tri_auteurs_corps'>$texte</div>", 'text-align: center;')
		. "</div>"
		. fin_cadre_relief(true);
	// SPIP >= 2.0
	return cadre_depliable(find_in_path('img/couteau-24.gif'),
		'<b>'._T('couteau:tri_auteurs').'</b>',
		false,	// true = deplie
		"<div id='bp_tri_auteurs_corps'>$texte</div>",
		'bp_tri_auteurs');
}

?>