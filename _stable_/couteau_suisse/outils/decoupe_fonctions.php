<?php
define('_decoupe_NB_CARACTERES', 60);

// Filtre local utilise par le filtre 'cs_imprimer' afin d'eviter la decoupe
// Exemple : lors d'une impression a l'aide du squelette imprimer.html,
// remplacer la balise #TEXTE par [(#TEXTE*|propre|cs_imprimer)].
function decoupe_imprimer($texte) {
	return str_replace(_decoupe_SEPARATEUR, '<p style="border-bottom:1px dashed #666; padding:0; margin:1em 20%; font-size:4pt;" >&nbsp; &nbsp;</p>', $texte);
}

// fonction appellee sur les parties du textes non comprises entre les balises : html|code|cadre|frame|script|acronym|cite
function decouper_en_pages_rempl($texte) {
	if (strpos($texte, _decoupe_SEPARATEUR)===false) return $texte;
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
	$self = self();//$GLOBALS['REQUEST_URI'];

	// images calculees par decoupe_installe()
	$images = unserialize($GLOBALS['meta']['cs_decoupe']);

	// images et liens pour la navigation sous forme : << < ... > >>
	// precedent
	$alt = _T('cout:page_precedente');
	$alt = "title=\"$alt\" alt=\"$alt\"";
	$precedent = '<a href="' . parametre_url($self,'artpage', $artpage - 1) . '">'; 
	$precedent = $artpage == 1?$images['precedent_off']." $alt />"
		:$precedent.$images['precedent']."$alt /></a>";
	// suivant
	$alt = _T('cout:page_suivante');
	$alt = "title=\"$alt\" alt=\"$alt\"";
	$suivant = '<a href="' . parametre_url($self,'artpage', $artpage + 1) . '">'; 
	$suivant = ($artpage == $num_pages)?$images['suivant_off']." $alt />"
		:$suivant.$images['suivant']."$alt /></a>";
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
			$page = trim(safehtml(cs_imprimer($pages[$i-1])));
			$title = preg_split("/[\r\n]+/", $page, 2);
			$title = attribut_html(propre(couper($title[0], _decoupe_NB_CARACTERES)));//.' (...)';
			$milieu[] = '<a href="' . parametre_url($self,'artpage', $i) . "\" title=\"$title\">$i</a>";
		}
	}
	$milieu = join(' ', $milieu);

	// s'il existe plus de trois pages on retourne la pagination << < 1 2 3 4 > >>
	// sinon une forme simplifiee : < 1 2 3 >
	$pagination = $num_pages>3?"$debut $precedent $milieu $suivant $fin":"$precedent $milieu $suivant";
	$pagination1 = "<div class='pagination decoupe_haut'>$pagination</div>";
	$pagination2 = "<div class='pagination decoupe_bas'>$pagination</div>";
	$page = trim($pages[$artpage-1]);
	if (isset($_GET['decoupe_recherche'])) {
		include_spip('inc/surligne');
		$page = surligner_mots($page, $_GET['decoupe_recherche']);
	}
	return $sommaire.$pagination1.$page.$pagination2;
}

function decouper_en_pages($texte){
	if (strpos($texte, _decoupe_SEPARATEUR)===false) return $texte;
	// verification des metas qui stockent les liens d'image
	if (!isset($GLOBALS['meta']['cs_decoupe']) || isset($GLOBALS['var_mode'])) {
		include_spip('outils/decoupe');
		decoupe_installe();
	}
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'decouper_en_pages_rempl', $texte);
}

?>