<?php

function pubban_poubelle_pleine(){
	include_spip('base/abstract_sql');
	$trash = sql_countsel($GLOBALS['_PUBBAN_CONF']['table_pub'], "statut='5poubelle'", '', '', '', '', _BDD_PUBBAN) + sql_countsel($GLOBALS['_PUBBAN_CONF']['table_empl'], "statut='5poubelle'", '', '', '', '', _BDD_PUBBAN);
	return $trash;
}

// ---------------------
// FONCTIONS AFFICHAGE
// ---------------------

// On change vers 'pubban_documentation' (precedemment 'doc_pub_banner' via PhpDoc)
function pubban_lien_doc(){
	$div = "<br/><br/><hr>"._T('pubban:doc_info')
		. icone_horizontale(_T('pubban:see_doc'), generer_url_public('pubban_documentation'), "article-24.gif", "rien.gif", false)
		. "[<a href='".generer_url_public('pubban_documentation')."' target='_blank' title='"._T('pubban:see_doc_in_new_window')."'>"._T('pubban:new_window')."</a>]";
	return $div;
}

/**
 * Affichage des statistiques generales
 */
function afficher_statistiques_pubban($return=false, $div='', $plie='deplie') {
	include_spip('base/abstract_sql');

	$div = "<div id='$div' class='bloc_depliable bloc$plie'><table width='100%'>";

	// nbre d'emplacements
	$div .= "<tr><td style='border-top: 1px solid #808080;'><strong><a href=\"\">"._T('pubban:nb_emplacements')."&nbsp;:</a></strong></td><td style='border-top: 1px solid #808080;'><strong>";
	$div .= sql_getfetsel("COUNT(*)", $GLOBALS['_PUBBAN_CONF']['table_empl'], "statut='2actif'", '', '', '', '', _BDD_PUBBAN);
	$div .= "</strong></td></tr>";

	// nbre total de pubs
	$div .= "<tr><td style='border-top: 1px solid #808080;'><strong><a href=\"".generer_url_ecrire('pubban_pub')."\" title=\""._T('pubban:lien_page')."\">"._T('pubban:nb_pub')."&nbsp;:</a></strong></td><td style='border-top: 1px solid #808080;'><strong>";
	$div .= sql_getfetsel("COUNT(*)", $GLOBALS['_PUBBAN_CONF']['table_pub'], "statut!='5poubelle'", '', '', '', '', _BDD_PUBBAN);
	$div .= "</strong></td></tr>";

	// nbre de pubs actives
	$div .= "<tr><td>&nbsp;&nbsp;&nbsp;<i>-&nbsp;"._T('pubban:nb_pub_actives')."&nbsp;:</i></td><td><strong>";
	$div .= sql_getfetsel("COUNT(*)", $GLOBALS['_PUBBAN_CONF']['table_pub'], "statut='2actif'", '', '', '', '', _BDD_PUBBAN);
	$div .= "</strong></td></tr>";

	// nbre de pubs inactives
	$div .= "<tr><td>&nbsp;&nbsp;&nbsp;<i>-&nbsp;"._T('pubban:nb_pub_inactives')."&nbsp;:</i></td><td><strong>";
	$div .= sql_getfetsel("COUNT(*)", $GLOBALS['_PUBBAN_CONF']['table_pub'], "statut='1inactif'", '', '', '', '', _BDD_PUBBAN);
	$div .= "</strong></td></tr>";

	// nbre de pubs obsoletes
	$div .= "<tr><td>&nbsp;&nbsp;&nbsp;<i>-&nbsp;"._T('pubban:nb_pub_obsoletes')."&nbsp;:</i></td><td><strong>";
	$div .= sql_getfetsel("COUNT(*)", $GLOBALS['_PUBBAN_CONF']['table_pub'], "statut='3obsolete'", '', '', '', '', _BDD_PUBBAN);
	$div .= "</strong></td></tr>";

	// nbre total d'affichages
	$div .= "<tr><td style='border-top: 1px solid #808080;'><strong><a href=\"".generer_url_ecrire('pubban_stats')."\" title=\""._T('pubban:lien_page')."\">"._T('pubban:nb_affichages')."&nbsp;:</a></strong></td><td style='border-top: 1px solid #808080;'><strong>";
	$resultat = sql_select("Sum(affichages) as A", $GLOBALS['_PUBBAN_CONF']['table_pub'], '', '', '', '', '', _BDD_PUBBAN);
	while($row = sql_fetch($resultat)) {
		$global_nbaffi = $row['A'];
	}
	$div .= $global_nbaffi;
	$div .= "</strong></td></tr>";

	// nbre total de clics
	$div .= "<tr><td style='border-top: 1px solid #808080;'><strong><a href=\"".generer_url_ecrire('pubban_stats')."\" title=\""._T('pubban:lien_page')."\">"._T('pubban:nb_clics')."&nbsp;:</a></strong></td><td style='border-top: 1px solid #808080;'><strong>";
	$resultat = sql_select("Sum(clics) as B", $GLOBALS['_PUBBAN_CONF']['table_pub'], '', '', '', '', '', _BDD_PUBBAN);
	while($row = sql_fetch($resultat)) {
		$global_nbclic = $row['B'];
	}
	$div .= $global_nbclic;
	$div .= "</strong></td></tr>";

	// ratio
	$div .= "<tr><td style='border-top: 1px solid #808080;'>&nbsp;&nbsp;&nbsp;-&nbsp;<i>"._T('pubban:info_ratio')."&nbsp;:</i></td><td style='border-top: 1px solid #808080;'><strong>";
	if($global_nbaffi > 0)
		$global_ratio = round($global_nbclic / $global_nbaffi * 100, 1); 
	else $global_ratio = 0; 
	$div .= $global_ratio . " %";
	$div .= "</strong></td></tr>";

	$div .= "</table></div>";
	if($return) return $div;
	echo $div;
}

/**
 * Moteur de rcherche Pubban
 */
function pubban_search($str){
	include_spip('base/abstract_sql');
	$results = array('pub', 'emp');
	$i=0;
	$j=0;
	if(substr_count($str, 'id_empl') != 0 AND is_numeric(substr($str, 8))) {
		$id_empl = substr($str, 8);
		$pub = sql_select("id_pub", $GLOBALS['_PUBBAN_CONF']['table_join'], "id_empl=".intval($id_empl), '', '', '', '', _BDD_PUBBAN);
		while ($row = spip_fetch_array($pub)) {
			$results['pub'][$i] = $row['id_pub'];
			$i++;
		}
	}
	if(is_integer(intval($str)) AND intval($str) != 0){
		$pub = sql_getfetsel("id_pub", $GLOBALS['_PUBBAN_CONF']['table_pub'], "id_pub=".intval($str), '', '', '', '', _BDD_PUBBAN);
		if($pub) {
			$results['pub'][$i] = $pub;
			$i++;
		}
		$empl = sql_getfetsel("id_empl", $GLOBALS['_PUBBAN_CONF']['table_empl'], "id_empl=".intval($str), '', '', '', '', _BDD_PUBBAN);
		if($empl) {
			$results['emp'][$j] = $empl;
			$j++;
		}
	}
	else{
		$pub = sql_select("id_pub", $GLOBALS['_PUBBAN_CONF']['table_pub'], "titre LIKE '%".$str."%'", '', '', '', '', _BDD_PUBBAN);
		if(sql_count($pub) > 0) {
			while($row = spip_fetch_array($pub)){
				$results['pub'][$i] = $row['id_pub'];
				$i++;
			}
		}
		$empl = sql_select("id_empl", $GLOBALS['_PUBBAN_CONF']['table_empl'], "titre LIKE '%".$str."%'", '', '', '', '', _BDD_PUBBAN);
		if(sql_count($empl) > 0) {
			while($row = spip_fetch_array($empl)){
				$results['emp'][$j] = $row['id_empl'];
				$j++;
			}
		}
	}
	return $results;
}

/**
 * Recuperateur d'extension
 * Recupere la chaine apres son dernier point.
 * Si cette chaine est plus longue qu'attendu pour une extension (par defaut 5 caracteres),
 * retourne FALSE. Sinon, retourne l'extension, SANS le point.
 * @return boolean/string FALSE | extension sans point
 * @param string $file Le fichier ou l'adresse dont on cherche l'extension
 * @param numeric $car Le nombre de caracteres a verifier (longueur de l'extension) | default : 5 caracteres
 */
function pubban_extension($file, $car='5') {
	if(!is_string($file) OR !strlen($file)) return false;
	$ext = substr(strrchr($file, '.'), 1);
	if(strlen($ext) <= $car) return $ext;
	return false;
}

/**
 * Valideur d'url
 */
function pubban_UrlOK($url) { 
	if( substr_count($url, 'localhost') ) return true;
//	return eregi("^http://[_A-Z0-9-]+\.[_A-Z0-9-]+[.A-Z0-9-]*(/~|/?)[/_.A-Z0-9#?&=+-]*$",$url); 
	return preg_match("/^[http|https]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i",$url);
} 

function pubban_url_reponse($url) { 
    $url = @parse_url($url);
    if (!$url) return false;
    $url = array_map('trim', $url);
    $url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
    $path = (isset($url['path'])) ? $url['path'] : '';
    if ($path == '') $path = '/';
    $path .= (isset($url['query'])) ? "?$url[query]" : '';
    if (isset($url['host']) AND $url['host'] != gethostbyname($url['host'])){
        if (PHP_VERSION >= 5)
            $headers = get_headers("$url[scheme]://$url[host]:$url[port]$path");
        else {
            $fp = @fsockopen($url['host'], $url['port'], $errno, $errstr, 30);
            if (!$fp) return false;
            fputs($fp, "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
            $headers = fread($fp, 4096);
            fclose($fp);
        }
        $headers = (is_array($headers)) ? implode("\n", $headers) : $headers;
        return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
    }
    return false;
}

?>