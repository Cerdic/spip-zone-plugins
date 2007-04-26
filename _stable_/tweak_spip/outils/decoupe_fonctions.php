<?php

define('_decoupe_NB_CARACTERES', 60);

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
	$artpage = max(intval($_GET['artpage']), 1);
	$pages = explode(_decoupe_SEPARATEUR, $texte);
	$num_pages = count($pages);
	if ($num_pages == 1) return $texte;
	// si numero illegal ou si var_recherche existe, alors renvoyer toutes les pages, separees par une ligne <hr/>.
	// la surbrillance pourra alors fonctionner correctement.
	if (strlen($_GET['var_recherche']) || $artpage < 1 || $artpage > $num_pages)
		return join("<hr/>", $pages);

	// images calculees par decoupe_installe()
	$images = unserialize($GLOBALS['meta']['tweaks_decoupe']);

	// images et liens pour la navigation sous forme : << < ... > >>
	$precedent = '<a href="' . parametre_url(self(),'artpage', $artpage - 1) . '">'; 
	$suivant = '<a href="' . parametre_url(self(),'artpage', $artpage + 1) . '">'; 
	$debut = '<a href="' . parametre_url(self(),'artpage', 0) . '">'; 
	$fin = '<a href="' . parametre_url(self(),'artpage', $num_pages-1) . '">';
	$alt = 'alt="'._T('cout:page_precedente').'"';
	$precedent = $artpage == 0?$images['precedent_off'].'/>'
		:$precedent.$images['precedent'].' alt="'._T('cout:page_precedente').'"/></a>';
	$suivant = $artpage == ($num_pages-1)?$images['suivant_off'].'/>'
		:$suivant.$images['suivant'].' alt="'._T('cout:page_suivante').'"/></a>';
	$debut = $artpage == 0?($temp=$images['precedent_off'].'/>').$temp
		:$debut.($temp=$images['precedent'].' alt="'._T('cout:page_debut').'"/>').$temp.'</a>';
	$fin = $artpage == $num_pages-1?($temp=$images['suivant_off'].'/>').$temp
		:$fin.($temp=$images['suivant'].' alt="'._T('cout:page_fin').'"/>').$temp.'</a>';

	// liens des differentes pages sous forme : 1 2 3 4
	$milieu = array();
	for ($i = 1; $i <= $num_pages; $i++) {
		if ($i == $artpage) {
			$milieu[] = "<span style=\"color: lightgrey; font-weight: bold; text-decoration: underline;\">$i</span>";
		} else {
			// isoler la premiere ligne non vide de chaque page pour les attributs alt et title
			$alt = preg_split("/[\r\n]+/", trim(safehtml($pages[$i-1])), 2);
			$alt = attribut_html(propre(couper($alt[0], _decoupe_NB_CARACTERES)));//.' (...)';
			$milieu[] = '<a href="' . parametre_url(self(),'artpage', $i) . "\" alt=\"$alt\" title=\"$alt\">$i</a>";
		}
	}
	$milieu = join(' ', $milieu);

	// s'il existe plus de trois pages on retourne la pagination << < 1 2 3 4 > >>
	// sinon une forme simplifiee : < 1 2 3 >
	$pagination = $num_pages>3?"$debut $precedent $milieu $suivant $fin":"$precedent $milieu $suivant";
	$pagination = "<p align='center'><span class='pagination'>$pagination</span></p>";
	return $sommaire.$pagination.$pages[$artpage-1].$pagination;
}

function decouper_en_pages($texte){
	if (strpos($texte, _decoupe_SEPARATEUR)===false) return $texte;
	// verification des metas qui stockent les liens d'image
	if (!isset($GLOBALS['meta']['tweaks_decoupe']) || isset($GLOBALS['var_mode'])) {
		include_spip('outils/decoupe');
		decoupe_installe();
	}
	return tweak_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'decouper_en_pages_rempl', $texte);
}

?>