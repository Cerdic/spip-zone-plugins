<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2008               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : https://contrib.spip.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

// compatibilite spip 1.9
if(!function_exists(ajax_retour)) { 
	function ajax_retour($corps) {
		$c = $GLOBALS['meta']["charset"];
		header('Content-Type: text/html; charset='. $c);
		$c = '<' . "?xml version='1.0' encoding='" . $c . "'?" . ">\n";
		echo $c, $corps;
		exit;
	}
}

function exec_action_rapide_dist() {
	global $type_urls;
	cs_minipres();
	$arg = _request('arg');
//	spip_log("exec 'action_rapide' du Couteau suisse : $arg / "._request('submit'));
//	cs_log($_POST, 'exec POST='); cs_log($_GET, 'exec GET=');

	switch ($arg) {
	// formulaires en partie privee
	case 'boites_privees':
cs_log("INIT : exec_action_rapide_dist() - Preparation du retour par Ajax (donnees transmises par GET)");
		$script = _request('script');
cs_log(" -- fonction = $fct - script = $script - arg = $arg");
		cs_minipres(!preg_match('/^\w+$/', $script));
		$fct = 'action_rapide_'._request('fct');
		include_spip('outils/boites_privees');
		$res = function_exists($fct)?$fct():'';
cs_log(" FIN : exec_description_outil_dist() - Appel maintenant de ajax_retour() pour afficher le formulaire de la boite privee");	
		ajax_retour($res);
		break;

	// pour gerer les packs de configuration : mode non ajax, rien a faire.
	case 'sauve_pack':
		break;

	// retour normal des action rapides du couteau suisse : affichage du bloc au sein de la description d'un outil
	case 'retour_normal':
cs_log("INIT : exec_action_rapide_dist() - Preparation du retour par Ajax (donnees transmises par GET)");
		$script = _request('script');
		$outil = _request('outil');
cs_log(" -- outil = $outil - script = $script - arg = $arg");
		cs_minipres(!preg_match('/^\w+$/', $script));
		include_spip('inc/cs_outils');
		$res = cs_action_rapide($outil);
cs_log(" FIN : exec_description_outil_dist() - Appel maintenant de ajax_retour() pour afficher le formulaire de la boite privee");	
		ajax_retour($res);
		break;

	// renvoie les caracteristiques URLs d'un objet (cas SPIP < 2.0)
	case 'type_urls_spip':
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
		break;
	// renvoie les caracteristiques URLs d'un objet (cas SPIP >= 2.0)
	case 'type_urls_spip2':
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
		break;

	}
}

?>