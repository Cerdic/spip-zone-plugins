<?php
// ===================================================
// Filtre : hauteur_majoree
// ===================================================
// Auteur: S. Bellégo
// Fonction : Retourne la hauteur d'une image + 20. Sert pour
//                   afficher correctemnt le logo d'une rubrique
// ===================================================
//
function hauteur_majoree($img) {
	if (!$img) return;
	include_ecrire('inc_logos.php3');
	list ($h,$l) = taille_image($img);
	$h+=20;
	return $h;
}
// FIN du Filtre : hauteur_majoree

// ===================================================
// Filtre : paginer
// ===================================================
// Auteur: Smellup, inspiré du filtre original de James
// Fonction : affiche la liste des pages d'une boucle contenant
//                   un critère de limite du type {debut_xxx, yyy}
// ===================================================
//
function paginer($total, $position=0, $pas=1, $type_num='numero', $fonction='') {
	global $clean_link;
	global $pagination_item_avant, $pagination_item_apres, $pagination_separateur;

	// Personnalisation des items de pagination (a changer si besoin)
	$pagination_item_avant = '';
	$pagination_item_apres = '';
	$pagination_separateur = '&nbsp;|&nbsp;';

	tester_variable('pagination_separateur', '&nbsp;');
	if (ereg('^debut([-_a-zA-Z0-9]+)$', $position, $match)) {
		$debut_lim = "debut".$match[1];
		$position = intval($GLOBALS['HTTP_GET_VARS'][$debut_lim]);
	}

	$nombre_pages = floor(($total-1)/$pas)+1;
	$texte = '';
	if($nombre_pages>1) {
		$i = 0;
		while($i<$nombre_pages) {
			$clean_link->delVar($debut_lim);
			$clean_link->addVar($debut_lim, strval($i*$pas));
			$url = $clean_link->getUrl();
			if(function_exists($fonction)) $item = call_user_func($fonction, $i+1);
			else $item = strval($i+1);
			if(($i*$pas) != $position) {
				if ($type_num == 'numero' ) $item = strval((intval($item)-1)*$pas);
				$item = "<a href=\"".$url."\">".$item."</a>";
			}
			else {
				if ($type_num == 'numero' ) $item = strval((intval($item)-1)*$pas);
				$item = "<span class=\"on\">".$item."</span>";
			}
			$texte .= $pagination_item_avant.$item.$pagination_item_apres;
			if($i<($nombre_pages-1)) $texte .= $pagination_separateur;
			$i++;
		}

	//Correction bug: $clean_link doit revenir à son état initial
	$clean_link->delVar($debut_lim);
	if($position) $clean_link->addVar($debut_lim, $position);
		return $texte;
	}
	return '';
}
// FIN du Filtre : paginer

// ===================================================
// Filtre : typo_couleur
// ===================================================
// Auteur : Smellup, inspiré du filtre original de A. Piérard
// Fonction : permettant de modifier la couleur du texte ou 
//                   de l'introduction d'un article
// Utilisation :                  
// 	- pour le rédacteur : [rouge]xxxxxx[/rouge]
// 	- pour le webmaster : [(#TEXTE|couleur)]
// ===================================================
//
function typo_couleur($texte) {

	// Variables personnalisables par l'utilisateur
	$typo_couleur_active = 'oui';
	// --> Activation ou désactivation de la fonction
	// --> Nuances personnalisables par l'utilisateur
	$couleur = array(
		'noir' => "#000000",
		'blanc' => "#FFFFFF",
	    'rouge' => "#FF0000",
		'vert' => "#00FF00",
		'bleu' => "#0000FF",
		'jaune' => "#FFFF00",
		'gris' => "#808080",
		'marron' => "#800000",
		'violet' => "#800080",
		'rose' => "#FFC0CB",
		'orange' => "#FFA500"
	);

	$recherche = array(
		'noir' => "/(\[noir\])(.*?)(\[\/noir\])/",
		'blanc' => "/(\[blanc\])(.*?)(\[\/blanc\])/",
	    'rouge' => "/(\[rouge\])(.*?)(\[\/rouge\])/",
		'vert' => "/(\[vert\])(.*?)(\[\/vert\])/",
		'bleu' => "/(\[bleu\])(.*?)(\[\/bleu\])/",
		'jaune' => "/(\[jaune\])(.*?)(\[\/jaune\])/",
		'gris' => "/(\[gris\])(.*?)(\[\/gris\])/",
		'marron' => "/(\[marron\])(.*?)(\[\/marron\])/",
		'violet' => "/(\[violet\])(.*?)(\[\/violet\])/",
		'rose' => "/(\[rose\])(.*?)(\[\/rose\])/",
		'orange' => "/(\[orange\])(.*?)(\[\/orange\])/"
	);

	$remplace = array(
		'noir' => "<span style=\"color:".$couleur['noir'].";\">\\2</span>",
		'blanc' => "<span style=\"color:".$couleur['blanc'].";\">\\2</span>",
	    'rouge' => "<span style=\"color:".$couleur['rouge'].";\">\\2</span>",
		'vert' => "<span style=\"color:".$couleur['vert'].";\">\\2</span>",
		'bleu' => "<span style=\"color:".$couleur['bleu'].";\">\\2</span>",
		'jaune' => "<span style=\"color:".$couleur['jaune'].";\">\\2</span>",
		'gris' => "<span style=\"color:".$couleur['gris'].";\">\\2</span>",
		'marron' => "<span style=\"color:".$couleur['marron'].";\">\\2</span>",
		'violet' => "<span style=\"color:".$couleur['violet'].";\">\\2</span>",
		'rose' => "<span style=\"color:".$couleur['rose'].";\">\\2</span>",
		'orange' => "<span style=\"color:".$couleur['orange'].";\">\\2</span>"
	);

	$supprime = "\\2";


	if ($typo_couleur_active == 'non') {
		$texte = preg_replace($recherche, $supprime, $texte);
	}
	else {
		$texte = preg_replace($recherche, $remplace, $texte);
	}
	return $texte;
}

// ===================================================
// Balise : #VERSION_SPIP
// ===================================================
// Auteur: Smellup, inspiré de la balise originale de Scoty
// Fonction : affiche la version SPIP correspondant à la 
//                   variable globale $version_spip_affichee
// ===================================================
//
function balise_VERSION_SPIP($p) {
    $p->code = "\$GLOBALS['spip_version_affichee']";
	$p->statut = 'html';
	return $p;
}

// ===================================================
// Balise : #VERSION_SQUELETTE
// ===================================================
// Auteur: Smellup
// Fonction : affiche la version utilisée du squelette Sarka 
//                   variable globale $version_squelette
// ===================================================
//
$GLOBALS['version_squelette'] = '1.2.1';
function balise_VERSION_SQUELETTE($p) {
    $p->code = "\$GLOBALS['version_squelette']";
	$p->statut = 'html';
	return $p;
}

// ===================================================
// Balise : #VISITES_SITE
// ===================================================
// Auteur: Smellup
// Fonction : affiche le nombre de visites sur le site pour le
//                   jour courant, la veille ou dépuis le début
// Paramètre: aujourdhui, hier, depuis_debut (ou vide)
// ===================================================
//
function balise_VISITES_SITE($p) {

	if ($a = $p->param) {
		$sinon = array_shift($a);
		if  (!array_shift($sinon)) {
			$p->fonctions = $a;
			array_shift( $p->param );
			$jour = array_shift($sinon);
			$jour = ($jour[0]->type=='texte') ? $jour[0]->texte : '';
		}
	}
	else {
		$jour = 'depuis_debut';
	}

	if (substr($GLOBALS['spip_version_affichee'],0,3) == '1.9') {
		$p->code = 'calcul_visites_site19('.$jour.')';
	}
	else {
		$p->code = 'calcul_visites_site('.$jour.')';
	}
	$p->statut = 'php';
	return $p;
}

function calcul_visites_site($j) {

	if ( $j == 'hier' ) {
		$hier = date('Y-m-d',strtotime(date('Y-m-d')) - 3600*24);
		$query = "SELECT visites AS visites FROM spip_visites WHERE date='$hier'";
		$result = spip_query($query);
		$visites_hier = 0;
		if ($row = @spip_fetch_array($result)) {
			$visites_hier = $row['visites'];
		}
		$r = $visites_hier;
	}
	else {
		$query = "SELECT COUNT(DISTINCT ip) AS visites FROM spip_visites_temp";
		$result = spip_query($query);
		$visites_auj = 0;
		if ($row = @spip_fetch_array($result)) {
			$visites_auj = $row['visites'];
		}

		if ( $j == 'aujourdhui' ) {
			$r = $visites_auj;
		}
		else {
			$query = "SELECT SUM(visites) AS total_absolu FROM spip_visites";
			$result = spip_query($query);
			$total_jusqua_hier = 0;
			if ($row = @spip_fetch_array($result)) {
				$total_jusqua_hier = $row['total_absolu'];
			}
			$r = $total_jusqua_hier + $visites_auj;
		}
	}
	return $r;
}

function calcul_visites_site19($j) {

	if ( $j == 'aujourdhui' ) {
		$auj = date('Y-m-d',strtotime(date('Y-m-d')));
		$query = "SELECT visites AS visites FROM spip_visites WHERE date='$auj'";
		$result = spip_query($query);
		$visites_auj = 0;
		if ($row = @spip_fetch_array($result)) {
			$visites_auj = $row['visites'];
		}
		$r = $visites_auj;
	}
	else if ( $j == 'hier' ) {
		$hier = date('Y-m-d',strtotime(date('Y-m-d')) - 3600*24);
		$query = "SELECT visites AS visites FROM spip_visites WHERE date='$hier'";
		$result = spip_query($query);
		$visites_hier = 0;
		if ($row = @spip_fetch_array($result)) {
			$visites_hier = $row['visites'];
		}
		$r = $visites_hier;
	}
	else {
		$query = "SELECT SUM(visites) AS total_absolu FROM spip_visites";
		$result = spip_query($query);
		$visites_debut = 0;
		if ($row = @spip_fetch_array($result)) {
			$visites_debut = $row['total_absolu'];
		}
		$r = $visites_debut;
	}
	return $r;
}

// ===================================================
// Balise : #INTRODUCTION (surcharge)
// ===================================================
// Auteur: Smellup
// Fonction : Surcharge de la fonction standard de calcul de la 
//                   balise #INTRODUCTION. Permet d'en definir la
//                   taille en nombre de caractère
// ===================================================
//
function introduction ($type, $texte, $chapo='', $descriptif='') {

	// Personnalisable par l'utilisateur
	$taille_intro_article = 600;
	$taille_intro_breve = 300;
	$taille_intro_message = 600;
	$taille_intro_rubrique = 600;
	
    switch ($type) {
		case 'articles':
			if ($descriptif)
				return propre($descriptif);
			else if (substr($chapo, 0, 1) == '=')	// article virtuel
				return '';
			else
				return PtoBR(propre(supprimer_tags(couper_intro($chapo."\n\n\n".$texte, $taille_intro_article))));
			break;
		case 'breves':
			return PtoBR(propre(supprimer_tags(couper_intro($texte, $taille_intro_breve))));
			break;
		case 'forums':
			return PtoBR(propre(supprimer_tags(couper_intro($texte, $taille_intro_message))));
			break;
		case 'rubriques':
			if ($descriptif)
				return propre($descriptif);
			else
				return PtoBR(propre(supprimer_tags(couper_intro($texte, $taille_intro_rubrique))));
			break;
	}
}

// Personnalisation de la puce par défaut :utilisation de la classe spip
$GLOBALS['puce'] = '<li class="spip">';
?>