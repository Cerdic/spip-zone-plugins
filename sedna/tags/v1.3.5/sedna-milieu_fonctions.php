<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// filtre de nettoyage XHTML strict d'un contenu potentiellement hostile
// |textebrut|lignes_longues|entites_html|antispam2|texte_script
function nettoyer_texte($texte) {
	return texte_script(
		antispam2(
		corriger_toutes_entites_html(
		entites_html(
		couper(
		lignes_longues(
		textebrut(
			$texte
		)), 600)
		))));
}
// tri maison : d'abord par jour de syndication,
// et a l'interieur du jour par date de maj
function critere_tri_sedna($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->order = array(
		"'date_format(syndic_articles.date,\\'%Y-%m-%d 00:00:00\\') DESC'", "'syndic_articles.maj DESC'", "'syndic_articles.date DESC'"
	);
}

// critere {contenu}
function critere_contenu($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];

	// un peu trop rapide, ca... le compilateur exige mieux
	$boucle->hash = '
	// RECHERCHE
	if ($r = addslashes($Pile[0]["recherche"]))
		$s = "(syndic_articles.descriptif LIKE \'%$r%\'
			OR syndic_articles.titre LIKE \'%$r%\'
			OR syndic_articles.url LIKE \'%$r%\'
			OR syndic_articles.lesauteurs LIKE \'%$r%\')";
		else $s = 1;
	';
	$boucle->where[] = '$s';
}
// identifiant d'un lien en fonction de son url et sa date, 4 chars
// 3ko = 500 * (5 caracteres + espace)
// 16**5 possibilites = suffisant pour eviter risque de doublons sur 500
function creer_identifiant ($url,$date) {
	return substr(md5("$date$url"),0,5);
}

// unicode 24D0 = caractere de forme "(a)"
function antispam2($texte) {
	return str_replace('@','&#x24d0;', $texte);
}

function afficher_lien($id_syndic_article,$id_lien,$id_syndic,$date,$url,$titre,$lesauteurs,$desc,$lang=false,$nom_site,$url_site) {
	static $vu, $lus, $ferme_ul, $id, $iddesc;
	global $ex_syndic, $class_desc;
	
	include_spip('inc/filtres');
	
	$ret = '';
	// Articles a ignorer
	if (!_request('id_syndic')
	AND $_COOKIE['sedna_ignore_'.$id_syndic])
		return;

	// initialiser la liste des articles lus
	if (!is_array($lus))
		$lus = array_flip(explode('-', '-'.$_COOKIE['sedna_lu']));

	if ($vu[$id_lien]++) return;

	// regler la classe des liens, en fonction du cookie sedna_lu
	$class_link = $lus[$id_lien] ? 'vu' : '';

	if (unique(substr($date,0,10)))
		$affdate = '<h1 class="date">'.jour($date).' '.nom_mois($date).'</h1>';


	// indiquer un intertitre si on change de source ou de date
	if ($affdate OR ($id_syndic != $ex_syndic)) {
		$ret .= $ferme_ul; $ferme_ul="</ul>\n";
		$ret .= $affdate;
	}

	// Suite intertitres
	if ($affdate OR ($id_syndic != $ex_syndic)) {
		$ret .= "<h2 id='site${id_syndic}_".(++$id)."'
		onmouseover=\"getElementById('url".$id."').className='urlsiteon';\"
		onmouseout=\"getElementById('url".$id."').className='urlsite';\"
		>";
		$link = parametre_url(self(),'id_syndic',$id_syndic);
		if ($age = intval($GLOBALS['age']))
			$link = parametre_url($link,'age',$age);
		$ret .= "<a href=\"$link\">".$nom_site."</a>";
		$ret .= " <a class=\"urlsite\" href=\"".$url_site.'" id="url'.$id.'">'
				.$url_site
				."</a>";
		$ret .= "</h2>\n<ul>\n";
		$ex_syndic = $id_syndic;
	}

	$ret .= "<li class='hentry'";
	if (!_request('id_syndic') AND !strlen(_request('recherche')))
		$ret .= " id='item${id_syndic}_${id_syndic_article}'";
	$ret .= " onmousedown=\"jai_lu('$id_lien');\">\n";
	$ret .= "<abbr class='published updated' title='".date_iso($date)."'>".affdate($date,'H:i')."</abbr>";
	$ret .= "<div class=\"titre\">";
	$ret .= "<a href=\"$url\" title=\"$url\" class=\"link$class_link\" id=\"news$id_lien\" rel=\"bookmark\"".($lang ? " hreflang=\"$lang\"" : "").">";
	$ret .= "<span class=\"entry-title\">$titre</span></a>";
	$ret .= $lesauteurs;
	$ret .= "\n<span class=\"source\"><a href=\"$url_site\">$nom_site</a></span>\n";
	$ret .= "</div>\n";

	if ($desc)
		$ret .= "<div class=\"desc\">
		<div class=\"$class_desc\" id=\"desc_".(++$iddesc)."\">\n
		<span class=\"entry-summary\">".$desc."</span>\n
		</div></div>";

	$ret .= "\n</li>\n";
	return $ret;
}
?>