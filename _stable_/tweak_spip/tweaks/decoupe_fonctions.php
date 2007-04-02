<?php

// fonction appellee sur les parties du textes non comprises entre les balises : html|code|cadre|frame|script|acronym|cite
function decouper_en_pages_rempl($texte) {
	if (strpos($texte, '++++')===false) return $texte;
	$artsuite = intval($_GET['artsuite']);
	$pages = explode('++++', $texte);
	$num_pages = count($pages);
	if ($num_pages == 1) return $texte;

	// si numero illegal ou si var_recherche existe, alors renvoyer toutes les pages, separees par une ligne <hr/>.
	// la surbrillance pourra alors fonctionner correctement.
	if (strlen($_GET['var_recherche']) || $artsuite < 0 || $artsuite >= $num_pages)
		return join("<hr/>", $pages);

	// images calculees par decoupe_installe()
	$images = unserialize($GLOBALS['meta']['tweaks_decoupe']);

	// images et liens pour la navigation sous forme : << < ... > >>
	$precedent = '<a href="' . parametre_url(self(),'artsuite', $artsuite - 1) . '">'; 
	$suivant = '<a href="' . parametre_url(self(),'artsuite', $artsuite + 1) . '">'; 
	$debut = '<a href="' . parametre_url(self(),'artsuite', 0) . '">'; 
	$fin = '<a href="' . parametre_url(self(),'artsuite', $num_pages-1) . '">';
	$alt = 'alt="'._T('tweak:page_precedente').'"';
	$precedent = $artsuite == 0?$images['precedent_off'].'/>'
		:$precedent.$images['precedent'].' alt="'._T('tweak:page_precedente').'"/></a>';
	$suivant = $artsuite == ($num_pages-1)?$images['suivant_off'].'/>'
		:$suivant.$images['suivant'].' alt="'._T('tweak:page_suivante').'"/></a>';
	$debut = $artsuite == 0?($temp=$images['precedent_off'].'/>').$temp
		:$debut.($temp=$images['precedent'].' alt="'._T('tweak:page_debut').'"/>').$temp.'</a>';
	$fin = $artsuite == $num_pages-1?($temp=$images['suivant_off'].'/>').$temp
		:$fin.($temp=$images['suivant'].' alt="'._T('tweak:page_fin').'"/>').$temp.'</a>';

	// liens des differentes pages sous forme : 1 2 3 4
	$milieu = array();
	for ($i = 0; $i < $num_pages; $i++) {
		if ($i == $artsuite) {
			$milieu[] = '<span style="color: lightgrey; font-weight: bold; text-decoration: underline;">' . ($i+1) . '</span>';
		} else {
			// isoler la premiere ligne non vide de chaque page pour les attributs alt et title
			$alt = preg_split("/[\r\n]+/", trim($pages[$i]), 2);
			$alt = attribut_html(propre(couper($alt[0], 60)));//.' (...)';
			$milieu[] = '<a href="' . parametre_url(self(),'artsuite', $i) . "\" alt=\"$alt\" title=\"$alt\">" . ($i+1) . '</a>';
		}
	}
	$milieu = join(' ', $milieu);
	
	// s'il existe plus de trois pages on retourne la pagination << < 1 2 3 4 > >>
	// sinon une forme simplifiee : < 1 2 3 >
	$pagination = $num_pages>3?"$debut $precedent $milieu $suivant $fin":"$precedent $milieu $suivant";
	$pagination = "<p align='center'><span class='pagination'>$pagination</span></p>";
	return $pagination.$pages[$artsuite].$pagination;
}

function decouper_en_pages($texte){
	if (strpos($texte, '++++')===false) return $texte;
	if (!isset($GLOBALS['meta']['tweaks_decoupe']) || $GLOBALS['var_mode'] == 'recalcul' || $GLOBALS['var_mode']=='calcul') {
		include_spip('tweaks/decoupe');
		decoupe_installe();
	}
	return tweak_exclure_balises('html|code|cadre|frame|script|acronym|cite', 'decouper_en_pages_rempl', $texte);
}

?>