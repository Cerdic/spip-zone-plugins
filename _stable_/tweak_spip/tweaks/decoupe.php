<?php
/*
 *   +----------------------------------+
 *    Nom du Filtre : decouper_en_page
 *   +----------------------------------+
 *    Date : mardi 28 janvier 2003
 *    Auteur :  "gpl"
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     Il sert a presenter un article sur plusieurs pages
 *   +-------------------------------------+ 
 *
*/

// integration 2007 : Patrice Vanneufville

// cette fonction est appelee automatiquement a chaque affichage de la page privee de Tweak SPIP
function decoupe_installe() {
//tweak_log('decoupe_installe()');
echo 'installe !!';
	$path = dirname(find_in_path('img/decoupe/test'));
	$images = array();
	$dossier=opendir($path);
	while ($image = readdir($dossier)) {
		if (preg_match(',^([a-z][a-z0-9_-]*\).(png|gif|jpg),', $image, $reg)) { 
			list(,,,$size) = @getimagesize("$path/$reg[1].$reg[2]");
			$images[$reg[1]] = "<img class=\"no_image_filtrer\" alt=\"$reg[1]\" title=\"$reg[1]\" src=\"".tweak_htmlpath($path)."/$reg[1].$reg[2]\" $size/>";
		}
	}
	ecrire_meta('tweaks_decoupe', serialize($images));
	ecrire_metas();
}

function decouper_en_pages_rempl($texte) {
	if (strpos($texte, '++++')===false) return $texte;
	$artsuite = intval($_GET['artsuite']);
	$pages = explode('++++', $texte);
	$num_pages = count($pages);
	
	// Si une seule page ou numero illegal, alors retourner tout le texte.
	// Cas special : si var_recherche existe, alors tout renvoyer pour permettre a la surbrillance
	// de fonctionner correctement.
	if ($num_pages == 1 || strlen($_GET['var_recherche']) || $artsuite < 0 || $artsuite > $num_pages)
		return $texte;

	$images = unserialize($GLOBALS['meta']['tweaks_decoupe']);
print_r($images);
	
	$uri_prec = '<a href="' . parametre_url(self(),'artsuite', $artsuite - 1) . '">&gt;&gt;</a>'; 
	$uri_suiv = '<a href="' . parametre_url(self(),'artsuite', $artsuite + 1) . '">&lt;&lt;</a>'; 
	$suivant = $artsuite == ($num_pages-1)?"":$uri_suiv;
	$precedent = $artsuite == 0?"":$uri_prec;
	
	for ($i = 0; $i < $num_pages; $i++) {
		$j = $i;
		if ($i == $artsuite) {
				$milieu .= " <b>" . ++$j . "</b> ";
		} else {
				$milieu .= ' <a href="' . parametre_url(self(),'artsuite', $i) . '">' . ++$j . "</a> ";
		}
	}

	// Ici, on peut personnaliser la présentation
	$resultat = "<p><div class='pagination'>$precedent $milieu $suivant</div></p>";
	$resultat .= $pages[$artsuite];
	$resultat .= "<P class='pagination'><div class='pagination'>$precedent $milieu $suivant</div></p>";
	return $resultat;
}

function decouper_en_pages_rempl0($texte){
	if (strpos($texte, '++++')===false) return $texte;

	$trouve = array(
		'/\s*=\s*"(.*?)"/', // 1. Echappement des guillemets a l'interieur de balises
		'/"\s*(.*?)\s*"/', // 2. Remplacement des autres paires de guillemets (et suppression des espaces apres/avant)
		'/@@GUILLEMETS_ECHAPPES@@/' // 3. Restitution des guillemets a l'interieur de balises
	
				);
	$remplace = array
				(
		'=@@GUILLEMETS_ECHAPPES@@$1@@GUILLEMETS_ECHAPPES@@', // 1
		$guilles, // 2
		'"' // 3
				);
	return preg_replace($trouve, $remplace, $texte);
}

function decouper_en_pages($texte){
	if (strpos($texte, '++++')===false) return $texte;
	if (!isset($GLOBALS['meta']['tweaks_decoupe']) || $GLOBALS['var_mode'] == 'recalcul' || $GLOBALS['var_mode']=='calcul')
		decoupe_installe();
	return tweak_exclure_balises('html|code|cadre|frame|script|acronym|cite', 'decouper_en_pages_rempl', $texte);
}

?>