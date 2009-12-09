<?php
/*
 module mon_outil_action_rapide.php inclu :
 - dans la description de l'outil en page de configuration
 - apres l'appel de ?exec=action_rapide&arg=type_urls|argument
*/

function type_urls_action_rapide() {
	include_spip('inc/actions');
//cs_log($_POST, '==== type_urls_action_rapide :'); cs_log($_GET);
	include_spip('public/assembler'); // pour recuperer_fond()
	$fd = recuperer_fond(defined('_SPIP19300')?'fonds/type_urls':'fonds/type_urls_191', array(
		'type_urls' => $GLOBALS['type_urls'],
		'ar_num_objet' => _request('ar_num_objet'),
		'ar_type_objet' => _request('ar_type_objet'),
	));
	// au cas ou il y aurait plusieurs actions, on fabrique plusieurs <form>
	$fd = explode('@@CS_FORM@@', $fd);
	$res = "";
	$arg = defined('_SPIP19300')?'edit_urls2_':'edit_urls_';
	foreach($fd as $i=>$f) {
		// syntaxe : ajax_action_auteur($action, $id, $script, $args='', $corps=false, $args_ajax='', $fct_ajax='')
		$res .= ajax_action_auteur('action_rapide', $arg.$i, 'admin_couteau_suisse', "arg=type_urls|description_outil&modif=oui&cmd=descrip#cs_action_rapide", $f, '', 'function() { jQuery(\'#ar_chercher\', this).click();}')."\n";
	}
	return $res;
}

// Fonction appelee par exec/action_rapide : ?exec=action_rapide&arg=type_urls|URL_objet (pipe obligatoire)
// Renvoie les caracteristiques URLs d'un objet (cas SPIP >= 2.0)
function type_urls_URL_objet_exec() {
	$type = _request('type_objet');
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table(table_objet($type));
	$table = $desc['table'];
	$champ_titre = $desc['titre'];
	$col_id =  @$desc['key']["PRIMARY KEY"];
	if (!$col_id) return false; // Quand $type ne reference pas une table
	$id_objet = intval(_request('id_objet'));

	// chercher dans la table des URLS
	include_spip('base/abstract_sql');
	//  Recuperer une URL propre correspondant a l'objet.
	$row = sql_fetsel("U.url, O.$champ_titre", "$table AS O LEFT JOIN spip_urls AS U ON (U.type='$type' AND U.id_objet=O.$col_id)", "O.$col_id=$id_objet", '', 'U.date DESC', 1);

	if (!$row) return false; # Quand $id_objet n'est pas un numero connu
	list($champ_titre,) = explode(',', $champ_titre, 2);
	// Calcul de l'URL complete
	$url = generer_url_entite($id_objet, $type, '', '', true);
	$row2 = !strlen($url2 = $row['url'])
		// si l'URL n'etait pas presente en base, maintenant elle l'est !
		?sql_fetsel("url", "spip_urls", "id_objet=$id_objet AND type='$type'", '', 'date DESC', 1)
		:array('url'=>$url2);
	include_spip('inc/charsets');
	// url propre en base || titre || url complete || type d'URLs || URL recalculee
	echo $url2.'||'.charset2unicode($row[trim($champ_titre)]).'||'.$url.'||'.$type_urls.'||'.$row2['url'];
}

// Fonction appelee par exec/action_rapide : ?exec=action_rapide&arg=type_urls|URL_objet_191 (pipe obligatoire)
// Renvoie les caracteristiques URLs d'un objet (cas SPIP < 2.0)
function type_urls_URL_objet_191_exec() {
	$type = _request('type_objet');
	$table = $type.($type=='syndic'?'':'s');
	$id_objet = intval(_request('id_objet'));
	$r0 = "SELECT url_propre, titre FROM spip_$table WHERE id_$type=$id_objet";
	$r = spip_query($r0);
	if ($r AND $r = spip_fetch_array($r)) { $url_1 = $r['url_propre']; $titre = $r['titre']; }
	/*charger_generer_url();*/
	if(!function_exists($fct = 'generer_url_'.($type=='syndic'?'site':$type))) {
		if($f = include_spip('urls/'.$type_urls, false))
			include_once($f);
	}
	$url = function_exists($fct)?$fct($id_objet):'??';
	$r = spip_query($r0);
	if ($r AND $r = spip_fetch_array($r)) $url_2 = $r['url_propre'];
	// url propre en base || titre || url complete || type d'URLs || URL recalculee
	include_spip('inc/charsets');
	echo _request('format')=='iframe'
		?"<span style='font-family:Verdana,Arial,Sans,sans-serif; font-size:10px;'>[<a href='../$url' title='$url' target='_blank'>"._T('couteau:urls_propres_lien').'</a>]</span>'
		:$url_1.'||'.charset2unicode($titre).'||'.$url.'||'.$type_urls.'||'.$url_2;
}

?>