<?php
@define('_decoupe_NB_CARACTERES', 60);

define('_onglets_CONTENU', '<div class="onglets_contenu"><h2 class="cs_onglet"><a href="#">');
define('_onglets_DEBUT', '<div class="onglets_bloc_initial">');

// aide le Couteau Suisse a calculer la balise #INTRODUCTION
function decoupe_introduire($texte) {
	if (defined('_decoupe_COMPATIBILITE'))
		return str_replace(array(_decoupe_SEPARATEUR, _decoupe_COMPATIBILITE), '<p>&nbsp;</p>', $texte);
	return str_replace(_decoupe_SEPARATEUR, '<p>&nbsp;</p>', $texte);
}
$GLOBALS['cs_introduire'][] = 'decoupe_introduire';

// controle des 3 balises usuelles p|div|span eventuellement coupees
// simple traitement pour les balises non imbriquees
function decoupe_safebalises($texte) {
	$texte = trim($texte);
	// balises <p|span|div> a traiter
	foreach(array('span', 'div', 'p') as $b) {
		// ouvrante manquante
		if(($fin = strpos($texte, "</$b>")) !== false)
			if(!preg_match(",<{$b}[ >],", substr($texte, 0, $fin)))
				$texte = "<$b>$texte";
		// fermante manquante
		$texte = strrev($texte);
		if(preg_match(',[ >]'.strrev("<{$b}").',', $texte, $reg)) {
			$fin = strpos(substr($texte, 0, $deb = strpos($texte, $reg[0])), strrev("</$b>"));
			if($fin===false || $fin>$deb) $texte = strrev("</$b>").$texte;
		}
		$texte = strrev($texte);
	}
	return $texte;
}

function onglets_callback($matches) {
	// au cas ou on ne veuille pas d'onglets, on remplace les '++++' par un filet et on entoure d'une classe.
	if ($_GET['cs']=='print') {
		@define(_decoupe_FILET, '<p style="border-bottom:1px dashed #666; padding:0; margin:1em 20%; font-size:4pt;" >&nbsp; &nbsp;</p>');
		$t = preg_split(',(\n\n|\r\n\r\n|\r\r),', $matches[1], 2);
		$texte = preg_replace(','.preg_quote(_decoupe_SEPARATEUR, ',').'(.*?)(\n\n|\r\n\r\n|\r\r),ms', _decoupe_FILET."<h4>$1</h4>\n", $t[1]);
		// on sait jamais...
		str_replace(_decoupe_SEPARATEUR, _decoupe_FILET, $texte);
		return '<div class="onglets_print"><h4>' . textebrut($t[0]) . "</h4>$texte</div>";
	}
	$onglets = $contenus = array();
	$pages = explode(_decoupe_SEPARATEUR, $matches[1]);
	foreach ($pages as $p) {
		$t = preg_split(',(\n\n|\r\n\r\n|\r\r),', $p, 2);
		$t = array(trim(textebrut(nettoyer_raccourcis_typo($t[0]))), decoupe_safebalises($t[1]));
		$contenus[] = _onglets_CONTENU.$t[0].'</a></h2>'.$t[1].'</div>';
	}
	return _onglets_DEBUT.join('', $contenus).'</div>'._onglets_FIN;
}

// fonction appellee sur les parties du texte non comprises entre les balises : html|code|cadre|frame|script|acronym|cite
function decouper_en_onglets_rempl($texte) {
	// surcharge possible de _decoupe_SEPARATEUR par _decoupe_COMPATIBILITE
	if (defined('_decoupe_COMPATIBILITE'))
		$texte = str_replace(_decoupe_COMPATIBILITE,_decoupe_SEPARATEUR, $texte);
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

// fonction appellee sur les parties du textes non comprises entre les balises : html|code|cadre|frame|script|acronym|cite
function decouper_en_pages_rempl($texte) {
	if (strpos($texte, _decoupe_SEPARATEUR)===false) return $texte;

	// au cas ou on ne veuille pas de decoupe, on remplace les '++++' par un filet.
	if ($_GET['cs']=='print') {
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
	$artpage = max(intval($_GET['artpage']), 1);
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
	// precedent
	$alt = _T('cout:page_precedente');
	$alt = "title=\"$alt\" alt=\"$alt\"";
	$precedent = '<a href="' . parametre_url($self,'artpage', $artpage - 1) . '">'; 
	$precedent = $artpage == 1?$images['precedent_off']." $alt />"
		:$precedent.$images['precedent']." $alt /></a>";
	// suivant
	$alt = _T('cout:page_suivante');
	$alt = "title=\"$alt\" alt=\"$alt\"";
	$suivant = '<a href="' . parametre_url($self,'artpage', $artpage + 1) . '">'; 
	$suivant = ($artpage == $num_pages)?$images['suivant_off']." $alt />"
		:$suivant.$images['suivant']." $alt /></a>";
	// s'il existe plus de trois pages on calcule les liens << et >>
	if ($num_pages>3) {
		// debut
		$alt = _T('cout:page_debut');
		$alt = "title=\"$alt\" alt=\"$alt\"";
		$debut = '<a href="' . parametre_url($self,'artpage', 0) . '">'; 
		$debut = $artpage == 1?($temp=$images['precedent_off']." $alt />").$temp
			:$debut.($temp=$images['precedent']." $alt />").$temp.'</a>';
		// fin
		$alt = _T('cout:page_fin');
		$alt = "title=\"$alt\" alt=\"$alt\"";
		$fin = '<a href="' . parametre_url($self,'artpage', $num_pages) . '">';
		$fin = ($artpage == $num_pages)?($temp=$images['suivant_off']." $alt />").$temp
			:$fin.($temp=$images['suivant']." $alt />").$temp.'</a>';
	}
	// liens des differentes pages sous forme : 1 2 3 4
	$milieu = array();
	for ($i = 1; $i <= $num_pages; $i++) {
		if ($i == $artpage) {
			$milieu[] = "<span style=\"color: lightgrey; font-weight: bold; text-decoration: underline;\">$i</span>";
		} else {
			// isoler la premiere ligne non vide de chaque page pour l'attribut title
			$page = supprimer_tags(decoupe_safebalises(cs_introduire($pages[$i-1])));
			$title = preg_split("/[\r\n]+/", trim($page), 2);
			$title = attribut_html(/*propre*/(couper($title[0], _decoupe_NB_CARACTERES)));//.' (...)';
			$title = _T('cout:page_lien', array('page' => $i, 'title' => $title));
			$milieu[] = '<a href="' . parametre_url($self,'artpage', $i) . "\" title=\"$title\">$i</a>";
		}
	}
	$milieu = join(' ', $milieu);

	// s'il existe plus de trois pages on retourne la pagination << < 1 2 3 4 > >>
	// sinon une forme simplifiee : < 1 2 3 >
	$pagination = $num_pages>3?"$debut\n$precedent\n$milieu\n$suivant\n$fin":"$precedent\n$milieu\n$suivant";
	$pagination1 = "<div id='decoupe_haut' class='pagination decoupe_haut'>\n$pagination\n</div>\n";
	$pagination2 = "<div id='decoupe_bas' class='pagination decoupe_bas'>\n$pagination\n</div>\n";
	$page = decoupe_safebalises($pages[$artpage-1]);
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
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'decouper_en_pages_rempl', $texte);
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

?>