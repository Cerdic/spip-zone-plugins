<?php
/*
 *   +----------------------------------+
 *    Nom du Filtre : decouper_en_pages
 *   +----------------------------------+
 *    Date : mardi 28 janvier 2003
 *    Auteur :  "gpl"
 *    Serieuse refonte et integration en mars 2007 : Patrice Vanneufville
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     Presenter un article sur plusieurs pages
 *   +-------------------------------------+ 
 *
*/

// cette fonction est appelee automatiquement a chaque affichage de la page privee de Tweak SPIP
// et calcule a l'avance les images trouvees dans le repertoire img/decoupe/
function decoupe_installe() {
tweak_log('decoupe_installe()');
	$path = dirname(find_in_path('img/decoupe/test'));
	$images = array();
	$dossier=opendir($path);
	while ($image = readdir($dossier)) {
		if (preg_match(',^([a-z][a-z0-9_-]*)\.(png|gif|jpg),', $image, $reg)) { 
			list(,,,$size) = @getimagesize("$path/$reg[1].$reg[2]");
			$images[$reg[1]] = "<img class=\"no_image_filtrer\" src=\"".tweak_htmlpath($path)."/$reg[1].$reg[2]\" $size";
		}
	}
	ecrire_meta('tweaks_decoupe', serialize($images));
	ecrire_metas();
}

// fonction appellee sur les parties du textes non comprises entre les balises : html|code|cadre|frame|script|acronym|cite
function decouper_en_pages_rempl($texte) {
	if (strpos($texte, '++++')===false) return $texte;
	$artsuite = intval($_GET['artsuite']);
	$pages = explode('++++', $texte);
	$num_pages = count($pages);
	
	// si une seule page, alors retourner le texte d'origine.
	// si numero illegal ou si var_recherche existe, alors renvoyer toutes les pages, separees par une ligne <hr/>.
	// la surbrillance pourra alors fonctionner correctement.
	if ($num_pages == 1) return $texte;
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
	if (!isset($GLOBALS['meta']['tweaks_decoupe']) || $GLOBALS['var_mode'] == 'recalcul' || $GLOBALS['var_mode']=='calcul')
		decoupe_installe();
	return tweak_exclure_balises('html|code|cadre|frame|script|acronym|cite', 'decouper_en_pages_rempl', $texte);
}

?>