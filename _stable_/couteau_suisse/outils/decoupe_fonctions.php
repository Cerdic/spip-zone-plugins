<?php
@define('_decoupe_NB_CARACTERES', 60);

define('_onglets_CONTENU', '<div class="onglets_contenu"><h2 class="cs_onglet"><a href="#">');
define('_onglets_DEBUT', '<div class="onglets_bloc_initial">');

// aide le Couteau Suisse a calculer la balise #INTRODUCTION
$GLOBALS['cs_introduire'][] = 'decoupe_nettoyer_raccourcis';

function onglets_callback($matches) {
	$matches[1] = preg_replace(','.preg_quote(_decoupe_SEPARATEUR,',').'\s+,', _decoupe_SEPARATEUR, $matches[1]);
	// au cas ou on ne veuille pas d'onglets, on remplace les '++++' par un filet et on entoure d'une classe.
	if (defined('_CS_PRINT')) {
		@define(_decoupe_FILET, '<p style="border-bottom:1px dashed #666; padding:0; margin:1em 20%; font-size:4pt;" >&nbsp; &nbsp;</p>');
		$t = preg_split(',(\n\n|\r\n\r\n|\r\r),', $matches[1], 2);
		$texte = preg_replace(','.preg_quote(_decoupe_SEPARATEUR, ',').'(.*?)(\n\n|\r\n\r\n|\r\r),ms', _decoupe_FILET."<h4>$1</h4>\n\n", $t[1]);
		// on sait jamais...
		str_replace(_decoupe_SEPARATEUR, _decoupe_FILET, $texte);
		return '<div class="onglets_print"><h4>' . textebrut($t[0]) . "</h4>\n$texte</div>";
	}
	$onglets = $contenus = array();
	$pages = explode(_decoupe_SEPARATEUR, $matches[1]);
	foreach ($pages as $p) {
		$t = preg_split(',(\n\n|\r\n\r\n|\r\r),', $p, 2);
		$t = array(trim(textebrut(nettoyer_raccourcis_typo($t[0]))), cs_safebalises($t[1]));
		if(strlen($t[0].$t[1])) $contenus[] = _onglets_CONTENU.$t[0]."</a></h2>\n\n".$t[1].'</div>';
	}
	return _onglets_DEBUT.join('', $contenus).'</div>'._onglets_FIN;
}

// fonction appellee sur les parties du texte non comprises entre les balises : html|code|cadre|frame|script|acronym|cite
function decouper_en_onglets_rempl($texte) {
	// surcharge possible de _decoupe_SEPARATEUR par _decoupe_COMPATIBILITE
	$rempl = preg_quote(_decoupe_SEPARATEUR,',')
		. (defined('_decoupe_COMPATIBILITE')?'|'.preg_quote(_decoupe_COMPATIBILITE,','):'');
	$texte = preg_replace(",\s*($rempl)\s*,", "\n\n"._decoupe_SEPARATEUR."\n\n", $texte);
	// si pas de balise, on sort
	if (strpos($texte, '<')===false) return $texte;
	// compatibilite avec la syntaxe de Pierre Troll
	if (strpos($texte, '<onglet|')!==false) {
		$texte = str_replace('<onglet|fin>', '</onglets>', $texte);
		$texte = preg_replace(',<onglet\|debut[^>]*\|titre=([^>]*)>\s*,', "<onglets>\\1\n\n", $texte);
		$texte = preg_replace(',\s*<onglet\|titre=([^>]*)>\s*,', "\n\n++++\\1\n\n", $texte);
	}
	// il faut un callback pour analyser l'interieur du texte
	return preg_replace_callback(',<onglets>(.*?)</onglets>,ms', 'onglets_callback', $texte);
}

// fonction renvoyant l'image appellee dans img/decoupe
function decoupe_image($fich, $help, $self, $off, $val, &$images, $double=false) {
	$alt = _T('cout:'.$help);
	$alt = "title=\"$alt\" alt=\"$alt\"";
	if ($off) {
		$tmp = $images[$fich.'_off']." $alt />";
		return $double?$tmp.$tmp:$tmp;
	} else {
		$tmp=$images[$fich]." $alt />";
		return '<a href="'.parametre_url($self,'artpage', $val).'">'.($double?$tmp.$tmp:$tmp).'</a>';
	}
}

// fonction appellee sur les parties du textes non comprises entre les balises : html|code|cadre|frame|script|acronym|cite
function decouper_en_pages_rempl($texte) {
	if (strpos($texte, _decoupe_SEPARATEUR)===false) return $texte;

	// au cas ou on ne veuille pas de decoupe, on remplace les '++++' par un filet.
	if (defined('_CS_PRINT')) {
		@define(_decoupe_FILET, '<p style="border-bottom:1px dashed #666; padding:0; margin:1em 20%; font-size:4pt;" >&nbsp; &nbsp;</p>');
		return str_replace(_decoupe_SEPARATEUR, _decoupe_FILET, $texte);
	}
	// recherche du sommaire s'il existe
	if (defined('_sommaire_REM') && (substr_count($texte, _sommaire_REM)==2)) {
		$pages = explode(_sommaire_REM, $texte);
		$sommaire = $pages[0].$pages[1];
		$texte = $pages[2];
	} else $sommaire = ''; 

	// traitement des pages
	$pages = explode(_decoupe_SEPARATEUR, $texte);
	$num_pages = count($pages);
	if ($num_pages == 1) return $texte;
	$artpage = max(intval(artpage()), 1);
	$artpage = min($artpage, $num_pages);
/*
	// si numero illegal ou si var_recherche existe, alors renvoyer toutes les pages, separees par une ligne <hr/>.
	// la surbrillance pourra alors fonctionner correctement.
	if (strlen($_GET['var_recherche']) || $artpage < 1 || $artpage > $num_pages)
		return join("<hr/>", $pages);
*/
	$self = nettoyer_uri();//self();//$GLOBALS['REQUEST_URI'];

	// images calculees par decoupe_installe()
	$images = unserialize($GLOBALS['meta']['cs_decoupe']);

	// images et liens pour la navigation sous forme : << < ... > >>
	$precedent = decoupe_image('precedent', 'page_precedente', $self, $artpage==1, ($artpage - 1)."-$num_pages", $images);
	$suivant = decoupe_image('suivant', 'page_suivante', $self, $artpage==$num_pages, ($artpage + 1)."-$num_pages", $images);
	if ($num_pages>3) {
		$debut = isset($images['debut'])
			?decoupe_image('debut', 'page_debut', $self, $artpage==1, 0, $images)
			:decoupe_image('precedent', 'page_debut', $self, $artpage==1, 0, $images, true);
		$fin = isset($images['fin'])
			?decoupe_image('fin', 'page_fin', $self, $artpage==$num_pages, "{$num_pages}-$num_pages", $images)
			:decoupe_image('suivant', 'page_fin', $self, $artpage==$num_pages, "{$num_pages}-$num_pages", $images, true);
	}
	// liens des differentes pages sous forme : 1 2 3 4
	$milieu = array();
	for ($i = 1; $i <= $num_pages; $i++) {
		if ($i == $artpage) {
			$milieu[] = "<span style=\"color: lightgrey; font-weight: bold; text-decoration: underline;\">$i</span>";
		} else {
			// isoler la premiere ligne non vide de chaque page pour l'attribut title
			$page = supprimer_tags(cs_safebalises(cs_introduire($pages[$i-1])));
			$title = preg_split("/[\r\n]+/", trim($page), 2);
			$title = attribut_html(/*propre*/(couper($title[0], _decoupe_NB_CARACTERES)));//.' (...)';
			$title = _T('cout:page_lien', array('page' => $i, 'title' => $title));
			$milieu[] = '<a href="' . parametre_url($self,'artpage',"{$i}-$num_pages") . "\" title=\"$title\">$i</a>";
		}
	}
	$milieu = join(' ', $milieu);

	// s'il existe plus de trois pages on retourne la pagination << < 1 2 3 4 > >>
	// sinon une forme simplifiee : < 1 2 3 >
	$pagination = $num_pages>3?"$debut\n$precedent\n$milieu\n$suivant\n$fin":"$precedent\n$milieu\n$suivant";
	$pagination1 = "<div id='decoupe_haut' class='pagination decoupe_haut'>\n$pagination\n</div>\n";
	$pagination2 = "<div id='decoupe_bas' class='pagination decoupe_bas'>\n$pagination\n</div>\n";
	$page = cs_safebalises($pages[$artpage-1]);
	if (isset($_GET['decoupe_recherche'])) {
		include_spip('inc/surligne');
		$page = surligner_mots($page, $_GET['decoupe_recherche']);
	}
	decoupe_notes_orphelines($page);
	return $sommaire.$pagination1.$page.$pagination2;
}

// supprime les notes devenues orphelines
function decoupe_notes_orphelines(&$texte) {
	if($GLOBALS['les_notes']=='') return;
	$notes = $GLOBALS['les_notes'];
	tester_variable('ouvre_note', '[');
	$ouvre = preg_quote($GLOBALS['ouvre_note']);
	$appel = "<p[^>]*>$ouvre<a [^>]*name=\"nb([0-9]+)\" class=\"spip_note\" [^>]+>[^<]+</a>.*?</p>";
	preg_match_all(",$appel,", $GLOBALS['les_notes'], $tableau);
	for($i=0;$i<count($tableau[0]);$i++) {
		if (!preg_match(",<a href=\"#nb{$tableau[1][$i]}\",",$texte)) 
			$notes = str_replace($tableau[0][$i], '', $notes);
	}
	$GLOBALS['les_notes'] = trim($notes);
}

// ici on est en pre_propre, tests d'installation requis
function cs_onglets($texte){
	// verification des metas qui stockent les liens d'image
	if (!isset($GLOBALS['meta']['cs_decoupe'])) {
		include_spip('outils/decoupe');
		decoupe_installe();
	}
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'decouper_en_onglets_rempl', $texte);
}

// ici on est en post_propre, tests d'installation non requis
function cs_decoupe($texte){
	if (strpos($texte, _decoupe_SEPARATEUR)===false) return $texte;
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite|table', 'decouper_en_pages_rempl', $texte);
}

// Compatibilite
function decouper_en_pages($texte){ return cs_decoupe($texte); }

// Balises pour des onglets en squelette
function balise_ONGLETS_DEBUT($p) {
	$arg = interprete_argument_balise(1,$p);
	eval("\$arg=_onglets_DEBUT._onglets_CONTENU.$arg.'</a></h2>';");
	$p->code = "'$arg'";
	return $p;
}
function balise_ONGLETS_TITRE($p) {
	$arg = interprete_argument_balise(1,$p);
	eval("\$arg='</div>'._onglets_CONTENU.$arg.'</a></h2>';");
	$p->code = "'$arg'";
	return $p;
}
function balise_ONGLETS_FIN($p) {
	$p->code = "'</div></div>'";
	return $p;
}

// decode le parametre artpage=page-total
// attention, artpage n'est pas toujours present
function artpage($t=false, $index=0) {
	if($t===false) $t=_request('artpage');
	$t=strlen($t)?explode('-', $t, 2):array('1','0');
	return $t[$index];
}
function artpage_fin($t=false) {
	if($t===false) $t=_request('artpage');
	$t=strlen($t)?explode('-', $t, 2):array('1','0');
	return $t[0]>0 && $t[0]==$t[1];
}
function artpage_debut($t=false) {
	return artpage($t)==1;
}

?>