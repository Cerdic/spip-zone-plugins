<?php

/*
 *   +----------------------------------+
 *    Nom du Filtre :    get_auteur_infos
 *   +----------------------------------+
 *    Date : lundi 23 f�rier 2004
 *    Auteur :  Nikau (luchier@nerim.fr)
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *    Cette fonction permet d'obtenir toutes les infos
 *    d'un auteur avec son nom ou son id_auteur
 *    ATTENTION !! cette fonction ne s'utilise pas de
 *    fa�n classique !! voir explication dans la contrib'
 *    Fonction utilis� �alement dans la fonction
 *    'afficher_avatar'
 *   +-------------------------------------+
 *
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/article.php3?id_article=412
*/

function get_auteur_infos($id='', $nom='') {
if ($id) $query = "SELECT * FROM spip_auteurs WHERE id_auteur=$id";
if ($nom) $query = "SELECT * FROM spip_auteurs WHERE nom='$nom'";
$result = spip_query($query);

if ($row = spip_fetch_array($result)) {
$row=serialize($row);
}
return $row;
}


function liens_externes ($texte) {
	return ereg_replace("<a ([^>]*https?://[^>]*class=\"spip_(out|url)\")>", "<a \\1 onclick=\"this.target='_blank'\">", $texte);
}

// Pour les fils rss

function pasdecrochet($texte) {
  // replaces ">" if first character by "*"
	$first = substr($texte,0,1);
	if (ord($first)==ord('>')) {
	$texte = substr($texte,1);
	}
	return $texte;
}

function w3cdate($texte) {
	// sets date (from #DATE) to W3C format
	$texte = substr($texte,0,10)."T".substr($texte,11,8)."Z";
	return $texte;
}

function tagdate($texte) {
	// sets date (from #DATE) to W3C URI tag format
	$texte = substr($texte,0,10);
	return $texte;
}	

function supprimehttp($texte) {
	// removes "http://" from URL to build Atom tag
	$texte = substr($texte,7);
	return $texte;
}


function supprimetags($texte) {
   // supprime les tags html d'un texte de backend (appeler avant texte_backend).
   $texte = strip_tags($texte);
   return $texte;
}

// Toute personne censee se detournerait de la rfc 822... et pourtant
function date_rfc822($date_heure) {
        list($annee, $mois, $jour) = recup_date($date_heure);
        list($heures, $minutes, $secondes) = recup_heure($date_heure);
        $time = mktime($heures, $minutes, $secondes, $mois, $jour, $annee);
        $timezone = sprintf('%+03d',intval(date('Z')/3600)).'00';
        return date("D, d M Y H:i:s", $time)." $timezone";
}

// renvoie une couleur fonction de l'age du forum
function dec2hex($v) {
        return substr('00'.dechex($v), -2);
}

function age_style($date) {

        // $decal en secondes
        $decal = date("U") - date("U", strtotime($date));

        // 3 jours = vieux
        $decal = min(1.0, sqrt($decal/(3*24*3600)));

        // Quand $decal = 0, c'est tout neuf : couleur vive
        // Quand $decal = 1, c'est vieux : bleu pale
        $red = ceil(128+127*(1-$decal));
        $blue = ceil(130+60*$decal);
        $green = ceil(200+55*(1-$decal));

        $couleur = dec2hex($red).dec2hex($green).dec2hex($blue);

        return 'background-color: #'.$couleur.';';
}

// Pour le sitemap google
function pourcent($valeur) {
   return $valeur / 100;
}

function propre_perso ($texte) {
	return ereg_replace('"','&nbsp;', $texte);
}

function propre_perso_txt ($texte) {
	return ereg_replace('&','&amp;', $texte);
}

/*
 *   +----------------------------------+
 *    Nom du Filtre : decouper_en_page
 *   +----------------------------------+
 *    Date : Vendredi 6 juin 2003
 *    Auteur :  "gpl"  : gpl@macplus.org
 *              Aur�ien PIERARD : aurelien.pierard@sig.premier-ministre.gouv.fr
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *		Il sert a pr�enter un article sur plusieurs pages
 *   +-------------------------------------+
 *
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/article.php3?id_article=62
*/

function decouper_en_page($texte) {
        global $artsuite, $var_recherche, $num_pages;
		
        if (empty($artsuite)) $artsuite = 0;
	
	// on divise la page (s�arateur : "-----/")
        $page = split('-----/', $texte);
        // Nombre total de pages
        $num_pages = count($page);

        // Si une seule page ou num�o ill�al, alors retourner tout le texte.
        // Cas sp�ial : si var_recherche positionn� tout renvoyer pour permettre �la surbrillance de fonctionner correctement.
        if ($num_pages == 1 || !empty($var_recherche) || $artsuite < 0 || $artsuite > $num_pages) {
			// On place les ancres sur les intertitres
			$texte = $texte;
			$array = explode("#NB_TITRE_DE_MON_ARTICLE#" , $texte);
			$res =count($array);
			$i =1;
			$texte=$array[0];
			while($i<$res){
				$texte=$texte.$i.$array[$i];
				$i++;
			}
			return $texte;
        } 

        $p_prec = $artsuite - 1;
        $p_suiv = $artsuite + 1;
        $uri_art = generer_url_article($GLOBALS['id_article']);
        $uri_art .= strpos($uri_art, '?') ? '&' : '?';

		// On place les ancres sur les intertitres
		$page[$artsuite] = preg_replace("|\{\{\{(.*)\}\}\}|U","<a name=\"sommaire_#NB_TITRE_DE_MON_ARTICLE#\">$0</a>", $page[$artsuite]);
		$array = explode("#NB_TITRE_DE_MON_ARTICLE#" , $page[$artsuite]);
		$res =count($array);
		$i =1;
		$page[$artsuite]=$array[0];
		while($i<$res){
			$page[$artsuite]=$page[$artsuite].$i.$array[$i];
			$i++;
		}
		// Pagination
	    switch (TRUE) {
			case ($artsuite == 0):
				$precedent = "";
				$suivant = "<a href='" . $uri_art . "artsuite=" . $p_suiv . "#sommaire_1'>&gt;&gt;</a>";
				break;
			case ($artsuite == ($num_pages-1)):
				$precedent = "<a href='" . $uri_art . "artsuite=" . $p_prec . "#sommaire_1'>&lt;&lt;</a>";
				$suivant = "";
				break;
			default:
				$precedent = "<a href='" . $uri_art . "artsuite=" . $p_prec . "#sommaire_1'>&lt;&lt;</a>";
				$suivant = "<a href='" . $uri_art . "artsuite=" . $p_suiv . "#sommaire_1'>&gt;&gt;</a>";
				break;
        }

        for ($i = 0; $i < $num_pages; $i++) {
			$j = $i;
			if ($i == $artsuite) {
				$milieu .= " <strong>" . ++$j . "</strong> ";
            }
			else {
				$milieu .= " <a href='" . $uri_art . "artsuite=$i'#sommaire_1>" . ++$j . "</a> ";
			}
        }

        // Ici, on peut personnaliser la pr�entation
        $resultat .= $page[$artsuite];
        $resultat .= "<div class='pagination' style='text-align:center'><p class='pagination'>pages : $precedent $milieu $suivant</p></div>";
        return $resultat;
}
// FIN du Filtre decouper_en_page

?>