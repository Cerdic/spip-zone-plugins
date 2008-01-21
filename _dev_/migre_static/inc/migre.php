<?php
#-----------------------------------------------------#
#  Plugin  : migre_static - Licence : GPL             #
#  File    : inc/migre - include                      #
#  Authors : Chryjs, 2007 - Beurt, 2006               #
#  Contact : chryjs¡@!free¡.!fr                       #
#-----------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!defined("_ECRIRE_INC_VERSION")) return;
if (defined("_INC_MIGRE")) return; else define("_INC_MIGRE", true);

include_spip("inc/distant");

if (!function_exists('plugin_get_infos')) include_spip('inc/plugin');
if (!function_exists('spip_query')) include_spip('inc/vieilles_defs'); // pour SPIP 1.9.3

include_spip("inc/migre_class"); // les objets


$infos=plugin_get_infos(_DIR_PLUGIN_MIGRESTATIC);
$GLOBALS['migrestatic_version'] = $infos['version']; // was 0.83
$GLOBALS['migrestatic'] = @unserialize($GLOBALS['meta']['migrestatic']);

// ------------------------------------------------------------------------------
// [fr] Initialisation des valeurs de meta du plugin aux defauts
// [en] Init plugin meta to default values
// ------------------------------------------------------------------------------
function migre_static_init_metas()
{
	global $migre_meta;
	migre_static_delete_metas(); // [fr] Nettoyage des traces [en] remove old metas
	unset($migre_meta);
	$migre_meta=array();
	$migre_meta['version']= $GLOBALS['migrestatic_version'];
	$migre_meta['migre_id_rubrique']= 0;
	$migre_meta['migreidmot']= "";
	$migre_meta['migre_liste_pages']= _T('migrestatic:liste_des_pages'); // from lang file
	$migre_meta['migre_test']= "checked";
	$migre_meta['migre_bcentredebut'] = ""; // was example : &lt;.{3,5}NAME.*index.{3,5}&gt
	$migre_meta['migre_bcentrefin'] = ""; // was example : &lt;.{3,5}END.*index.*&gt
	$migre_meta['migre_htos'] = get_list_htos();
	$migre_meta['migre_cs_decoupe']= "checked";
	if ($migre_meta!= $GLOBALS['meta']['migrestatic']) {
		include_spip('inc/meta');
		ecrire_meta('migrestatic', serialize($migre_meta));
		ecrire_metas();
		$GLOBALS['migrestatic'] = @unserialize($GLOBALS['meta']['migrestatic']);
		spip_log('migrestatic : migre_static_init_metas OK');
	}
} // migre_static_init_metas

// ------------------------------------------------------------------------------
// [fr] Supprimer les metas du plugin (desinstallation)
// [en] Delete plugin metas
// ------------------------------------------------------------------------------
function migre_static_delete_metas()
{
	if (isset($GLOBALS['meta']['migrestatic'])) {
		include_spip('inc/meta');
		effacer_meta('migrestatic');
		ecrire_metas();
		unset($GLOBALS['migrestatic']);
		unset($GLOBALS['meta']['migrestatic']);
		spip_log('migrestatic : migre_static_delete_meta OK');
	}
} // migre_static_delete_metas

// ------------------------------------------------------------------------------
// [fr] Mise à jour des metas du plugin
// [en] Update plugin metas
// ------------------------------------------------------------------------------
function migre_static_update_metas()
{
	$migre_meta = @unserialize($GLOBALS['meta']['migrestatic']);
	$migre_meta['version']= $GLOBALS['migrestatic_version'];
	include_spip('inc/meta');
	ecrire_meta('migrestatic', serialize($migre_meta));
	ecrire_metas();
	$GLOBALS['migrestatic'] = @unserialize($GLOBALS['meta']['migrestatic']);
	spip_log('migrestatic : migre_static_update_metas OK');
} // migre_static_update_metas

// ------------------------------------------------------------------------------
// [fr] Tableau de correspondance par defaut des differentes balises html
// [fr] On ne peut pas faire en une seule passe car certaines balises spip ont une syntaxe proche de celles du html...
// [fr] D autre part il se peut qu on veuille adapter la conversion a ses propres choix...
// [fr] Comme on peut les modifier dans des champs (pas des textarea) les \n et \r sont transformes en @n et @r
// ------------------------------------------------------------------------------
function get_list_htos()
{
	$htos=array();
	
	$htos['prem']['filtre']		="";				$htos['prem']['spip']=""; // un premier filtre pour l utilisateur
	
	$htos['comment']['filtre']	="/<!--.*-->/iUms";		$htos['comment']['spip']="";
	$htos['script']['filtre']	="/<(script|style)\b.+?<\/\\1>/i";	$htos['script']['spip']="";
	$htos['italique']['filtre']	=",<(i|em)( [^>@r]*)?".">(.+)</\\1>,Uims";	$htos['italique']['spip']="{\\3}";
	$htos['bold']['filtre']		=",<(b|h[4-6])( [^>]*)?".">(.+)</\\1>,Uims";	$htos['bold']['spip']="@@b@@\\3@@/b@@"; 
	$htos['h']['filtre']		=",<(h[1-3])( [^>]*)?".">(.+)</\\1>,Uims";	$htos['h']['spip']="@r{{{ \\3 }}}@r";
	$htos['tr']['filtre']		=",<tr( [^>]*)?".">,Uims";	$htos['tr']['spip']="<br>@r";
	$htos['thtd']['filtre']		=",<t[hd]( [^>]*)?".">,Uims";	$htos['thtd']['spip']=" | ";
	//$htos['thtd']['filtre']	=",<t[hd]( [^>]*)?".">,Uims";	$htos['thtd']['spip']="";
	$htos['br']['filtre']		="/<br.*>/iUs";			$htos['br']['spip']="";
	$htos['tbody']['filtre']	="/<\/*tbody.*>/iUs";		$htos['tbody']['spip']="";
	$htos['table']['filtre']	="/<\/*table.*>/iUs";		$htos['table']['spip']="";
	$htos['font']['filtre']		="/<\/*font.*>/iUs";		$htos['font']['spip']="";
	$htos['span']['filtre']		="/<\/*span.*>/iUs";		$htos['span']['spip']="";
	$htos['ulol']['filtre']		="/<\/*[uo]l.*>/iUs";		$htos['ulol']['spip']="";
	$htos['blockquote']['filtre']	="/<\/*blockquote.*>/iUs";	$htos['blockquote']['spip']="";
	$htos['div']['filtre']		="/<\/*div.*>/iUs";		$htos['div']['spip']="";
	$htos['hr']['filtre']		="/<hr.*>/iUs";			$htos['hr']['spip']="";
	$htos['bull']['filtre']		="/&bull;/";			$htos['bull']['spip']="@r@r-*";
	$htos['li']['filtre']		="/<li.*>/iUs";			$htos['li']['spip']="@r@r-*";
	$htos['slashli']['filtre']	="/<\/li>/iUs";			$htos['slashli']['spip']="";
	$htos['nbsp']['filtre']		="/&nbsp;/iUs";			$htos['nbsp']['spip']=" ";
	$htos['slashtrtd']['filtre']	=",</t[rhd]>,Uims";		$htos['slashtrtd']['spip']="@r";
	$htos['p']['filtre']		=",</*p.*>,Uims";		$htos['p']['spip']="";
	
	$htos['dern']['filtre']		="";				$htos['dern']['spip']=""; // un dernier filtre pour l utilisateur
	
	return $htos;
} // get_list_htos

// ------------------------------------------------------------------------------
// [fr] Cette fonction verirife que :
// [fr] - la liste est bien sur le meme site (petit controle de copyright)
// [fr] retourne un tableau avec la liste des URI des pages a telecharger. 
// [fr] Le document attendu doit respecter la syntaxe definie !!!
// ------------------------------------------------------------------------------
function old_get_list_of_pages($uri_list="")
{
	include_spip("inc/distant");
	$uri_pages=array();
	if (!empty($uri_list)) {
		if (!preg_match(',^https?://[^.]+\.[^.]+.*/.*$,', $uri_list)) {
			spip_log("migrestatic:get_list_of_pages:'$uri_list' is not a valid url");
			return $uri_pages;
		}
		$site=parse_url($uri_list); // urlencode ?
		$site_uri= $site[host];
		$dochtml = recuperer_page($uri_list,true);
		// Pre-traitement : reperer le charset du document et le convertir
		// Bonus : supprime le BOM
		//include_spip('inc/charsets');
		//$dochtml = transcoder_page($dochtml);
		$dochtml = preg_replace(';\<[\t| |\s]*a[\t| |\s]*href[\t| |\s]*=[\t| |\s]*["\']?([^"\']*)["\']?.*?\>(.*?)<\/a>;i','\\1',$dochtml); // extract from a href=
		$prelist = preg_split("/\r\n|\n\r|\n|\r|\s| |\t/",$dochtml);
		reset($prelist);
		while (list($key,$val)=each($prelist)) {
			$val = preg_replace("/[\t| |\s]#.*/","",$val); // remove comments
			$val = preg_replace("/^#.*$/","",$val); // remove comments line

			if (!empty($val)) {
				if (preg_match(',^https?://[^.]+\.[^.]+.*/.*$,', $val)) {
					$site=parse_url($val);
					if ($site[host] == $site_uri) {
						$uri_pages[]=$val;
					}
					else spip_log("migrestatic:get_list_of_pages:'$val' is not in '$site_uri'") ;
				} // preg_match
				else spip_log("migrestatic:get_list_of_pages:'$val' is not an url");
			} // !empty
		} // while
	}
	return $uri_pages;
} // old_get_list_of_pages

/*
 * Page de description de la migration (sur site distant)
 * Anciens formats :
 * 
 * 1 / Liste d'articles separee par espace et retour à la ligne du style :
 * http://www.truc.com/monurl.html
 * 
 * 2 / Bloc rubrique sous forme :
 * <RUBRIQUE id='x' titre='aaa' id_parent='x' lang='ff'>
 * <a href='hhhhhhhhh'>titre</a>
 * </RUBRIQUE>
 * 
 * 3 / Evolution support d'une description au format XML
 *  Voir DTD base/migre_desc.dtd
 * 
 */

define('BALISE_RUBRIQUE','<RUBRIQUE');
define('BALISE_FIN_RUBRIQUE','</RUBRIQUE');

define('BALISE_XML',"<migre_desc>");

function get_list_of_pages($url_page_description,&$liste_rubriques,&$liste_articles)
{
	global $migre_meta;

	if (!empty($url_page_description)) {
		if (!preg_match(',^https?://[^.]+\.[^.]+.*/.*$,', $url_page_description)) {
			spip_log("migrestatic:get_list_of_pages:'$url_page_description' is not a valid url");
			return;
		}
		$site=parse_url($url_page_description); // urlencode ?
		$site_uri= $site[host];
		$page_description = recuperer_page($url_page_description,true);

		if (strpos($page_description,BALISE_XML)!==false) {
			$arbre = array();
			$strict = true;
			$clean = true;
			$profondeur = -1; // illimite
			$arbre = spip_xml_parse($page_description, $strict, $clean, $profondeur);
			analyser_xml($arbre, $site_uri,$liste_rubriques,$liste_articles);
		}
		else {
			// ancien format
			// Pre-traitement : reperer le charset du document et le convertir
			// Bonus : supprime le BOM
			include_spip('inc/charsets');
			$page_description = transcoder_page($page_description);
			analyser_ancien_format($page_description,$site_uri,$liste_rubriques,$liste_articles);
		}
	}
	else {
		spip_log("migrestatic:get_list_of_pages:url_page_description is empty");
	}
}

// a priori retourne un array d'objets Rubrique
// et on construit un tableau d'Articles passé en référence
function analyser_xml($arbre, $site_uri,&$tableau_rubrique,&$tableau_articles) {
	global $migre_meta;

	// controles de base
	if ( !is_array($arbre) OR !is_array($arbre['migre_desc']) OR !is_array($arbre['migre_desc'][0]) ) {
		spip_log('migre_static:analyser_xml erreur arbre');
		return;
	}

	$id_rubrique_racine = $migre_meta['migre_id_rubrique'];
	$racine = new Rubrique();
	$racine->id_rubrique_souhaite = $id_rubrique_racine; // a priori == reel
	if ( $racine->maj_infos() )
		$id_secteur = $racine->id_secteur;
	else
		die ("BUG : rubrique inconnue");
	$tableau_rubrique[$id_rubrique_racine] = &$racine;

	// deux types d'items possibles a la racine
	// $arbre['migre_desc'][0]['rubrique'] 
	// et
	// $arbre['migre_desc'][0]['article'] 

	while ( list (,$rubrique) = each($arbre['migre_desc'][0]['rubrique'] ) ) {
		// on cree la rubrique
		unset($rubrique_courante);
		$rubrique_courante = extraire_Rubrique_xml($rubrique,$racine->id_rubrique_reel,$id_secteur);
		// on verifie que la même n'existe pas
		$id_rubrique_souhaite=$rubrique_courante->id_rubrique_souhaite;
		if (is_object($tableau_rubrique[$id_rubrique_souhaite])) {
			unset($rubrique_courante); // destructeur _obligatoire ici_
			$rubrique_courante = &$tableau_rubrique[$id_rubrique_souhaite]; // reinit avec le precedemment trouve
		}
		else
			$tableau_rubrique[$id_rubrique_souhaite]=&$rubrique_courante; // affectation
		// on traite ses articles
		extraire_tableau_Articles_xml($rubrique,$rubrique_courante, $site_uri,$tableau_articles);
	}

	// on traite les articles dans la racine
	extraire_tableau_Articles_xml($arbre['migre_desc'][0],$racine, $site_uri,$tableau_articles);

}



// a priori retourne un array d'objets Rubrique
// et on construit un tableau d'Articles passé en référence
function analyser_ancien_format($texte, $site_uri,&$tableau_rubrique,&$tableau_articles) {
	global $migre_meta;
	$id_rubrique_racine = $migre_meta['migre_id_rubrique'];
	$racine = new Rubrique();
	$racine->id_rubrique_souhaite = $id_rubrique_racine; // a priori == reel
	if ( $racine->maj_infos() )
		$id_secteur = $racine->id_secteur;
	else
		die ("BUG : rubrique inconnue");
	$tableau_rubrique[$id_rubrique_racine] = &$racine;

	while (($position = strpos($texte, BALISE_RUBRIQUE)) !== false) {
		unset($rubrique_courante);
		$debut = substr($texte, 0, $position); // si debut n'est pas vide rattacher les URL a id_parent
		if ($debut) {
			extraire_tableau_Articles($debut,$racine, $site_uri,$tableau_articles);
		}

		$reste = substr($texte, $position);
		$coupure = strpos($reste, ">");
		// on cherche les elements de definition de cette rubrique
		$elem_rubrique = trim(substr($reste, strlen(BALISE_RUBRIQUE),  $coupure - strlen(BALISE_RUBRIQUE)));
		$rubrique_courante = extraire_Rubrique($elem_rubrique,$racine->id_rubrique_reel,$id_secteur);
		// on verifie que la même n'existe pas
		$id_rubrique_souhaite=$rubrique_courante->id_rubrique_souhaite;
		if (is_object($tableau_rubrique[$id_rubrique_souhaite])) {
			unset($rubrique_courante); // destructeur
			$rubrique_courante = &$tableau_rubrique[$id_rubrique_souhaite]; // reinit avec le precedemment trouve
		}
		else
			$tableau_rubrique[$id_rubrique_souhaite]=&$rubrique_courante; // affectation
		$reste = substr($reste, $coupure);
		// et on cherche son contenu
		$fin = BALISE_FIN_RUBRIQUE . ">";

		$position = strpos($reste, $fin);
		if ($position === false) {
			spip_log('migre analyser manque fin boucle');
			die('migre analyser manque fin boucle');
		}
		$suite = substr($reste, $position + strlen($fin));
		$milieu = substr($reste, 1, $position-1); // 1 car il faut prendre en compte la longueur de ">"
		// on extrait les URLs
		extraire_tableau_Articles($milieu,$rubrique_courante, $site_uri,$tableau_articles);

		// on continue avec le reste
		$texte=$suite;

	}
	if ($texte) {
		extraire_tableau_Articles($texte,$racine, $site_uri,$tableau_articles);
	}
}

// cette fonction doit retourner un tableau d'objets Article
function extraire_tableau_Articles($texte,&$rub, $site_uri,&$tableau_articles) {
	// on transforme tous les a href en URI simple (en gros on vire les balises HTML)
	$texte = preg_replace(';\<[\t| |\s]*a[\t| |\s]*href[\t| |\s]*=[\t| |\s]*["\']?([^"\']*)["\']?.*?\>(.*?)<\/a>;i','\\1',$texte);
	// on eclate en une liste d'URIs (tableau)
	$prelist = preg_split("/\r\n|\n\r|\n|\r|\s| |\t/",$texte);
	reset($prelist);
	while (list($key,$val)=each($prelist)) {
		$val = preg_replace("/[\t| |\s]+#.*/","",$val); // remove comments
		$val = preg_replace("/^#.*$/","",$val); // remove comments line
		$val=trim($val);
		if (!empty($val)) {
			if (preg_match(',^https?://[^.]+\.[^.]+.*/.*$,', $val)) {
				$site=parse_url($val);
				if ($site[host] == $site_uri) {
					unset($nouvart);
					$nouvart=new Article($val,$rub->id_rubrique_souhaite,$rub->id_secteur);
					$rub->liste_articles[]= &$nouvart;
					$tableau_articles[] = &$nouvart;
				}
				else spip_log("migrestatic:extraire_tableau_Articles:'$val' is not in '$site_uri'") ;
			} // preg_match
			else spip_log("migrestatic:extraire_tableau_Articles:'$val' is not an url");
		} // !empty
	} // while
}

// cette fonction doit retourner un tableau d'objets Article
function extraire_tableau_Articles_xml($tab_rubrique,&$rub, $site_uri,&$tableau_articles) {
	if (!is_array($tab_rubrique['article'])) return;

	while (list(,$val)=each($tab_rubrique['article'])) {
		$url_article=trim($val['url'][0]);
		if (!empty($url_article)) {
			if (preg_match(',^https?://[^.]+\.[^.]+.*/.*$,', $url_article)) {
				$site=parse_url($url_article);
				if ($site[host] == $site_uri) {
					unset($nouvart);
					$nouvart=new Article($url_article,$rub->id_rubrique_souhaite,$rub->id_secteur);
					$nouvart->titre=$val['titre'][0];
					$rub->liste_articles[]= &$nouvart;
					$tableau_articles[] = &$nouvart;
				}
				else spip_log("migrestatic:extraire_tableau_Articles_xml:'$url_article' is not in '$site_uri'") ;
			} // preg_match
			else spip_log("migrestatic:extraire_tableau_Articles_xml:'$url_article' is not an url");
		} // !empty
	} // while
}


// retourne un objet Rubrique
function extraire_Rubrique($elem_rubrique,$id_parent,$id_secteur) {
	$res = new Rubrique($id_secteur);

	if ( preg_match(";id=['|\"](.*?)['|\"];i",$elem_rubrique,$matches) ) 
		$res->id_rubrique_souhaite=$matches[1];
	else
		$res->id_rubrique_souhaite=0;

	if ( preg_match(";titre=['|\"](.*?)['|\"];i",$elem_rubrique,$matches) ) 
		$res->titre=$matches[1];
	else
		$res->titre='';

	if ( preg_match(";id_parent=['|\"](.*?)['|\"];i",$elem_rubrique,$matches) ) 
		$res->id_parent_souhaite=$matches[1];
	else
		$res->id_parent_souhaite=$id_parent;

	if ( preg_match(";lang=['|\"](.*?)['|\"];i",$elem_rubrique,$matches) ) 
		$res->lang=$matches[1];
	else
		$res->lang=''; // valeur par defaut celle de la rubrique parente

	return $res;
}

// retourne un objet Rubrique
function extraire_Rubrique_xml($tab_rubrique,$id_parent,$id_secteur) {
	$res = new Rubrique($id_secteur);

	$res->id_rubrique_souhaite=$tab_rubrique['id'][0];
	$res->titre=$tab_rubrique['titre'][0];
	$res->id_parent_souhaite=$tab_rubrique['id_parent'][0];
	$res->lang=$tab_rubrique['lang'][0];

	return $res;
}

// affiche le contenu parse a partir du tableau de Rubrique recupere
function afficher_arbre($tableau_rubrique) {
	reset($tableau_rubrique);
	while ( list($k,$rub) = each($tableau_rubrique) ) {
		if (is_object($rub)) {
			$aff .= "<h1>"
			//		.$rub->id_rubrique_souhaite.":"
					. $rub->titre."</h1>\n";
			$aff .= "<ul>\n";
			reset($rub->liste_articles);
			while ( list(,$art) = each($rub->liste_articles)) {
				if (is_object($art))
					$aff .= "<li><a href='".$art->url."'>"
					//		. $art->id_rubrique_souhaite.":"
					//		. $art->titre.":"
							. $art->url
							. "</a></li>\n";
			}
			$aff .="</ul>\n";
		}
	}
	return $aff;
}

// raccroche les rubriques aux parents (s'ils existent, sinon a la rubrique principale)
// construit un "arbre"
// si une rubrique n'a pas de parent ou si le parent souhaite 
function reorganiser_rubriques(&$liste_rubriques) {
	global $migre_meta;

	$idx_rubriques = $liste_rubriques; // on fait une copie pour les manip pendant la boucle
	reset($liste_rubriques);
	while ( list($k,) = each($liste_rubriques) ) {
		$rub=&$liste_rubriques[$k];
		if ($rub->id_rubrique_souhaite!=$migre_meta['migre_id_rubrique']) {
			$id_parent_souhaite = $rub->id_parent_souhaite;
			if ( empty($id_parent_souhaite) OR ! is_object( $idx_rubriques[$id_parent_souhaite] ) ) {
				$rub->id_parent_souhaite = $migre_meta['migre_id_rubrique'];
				$id_parent_souhaite = $rub->id_parent_souhaite;
			}
			$rub->rub_parent=&$idx_rubriques[$rub->id_parent_souhaite]; // on raccroche au parent
			$rub->rub_parent->liste_rub_enfants[]=&$rub; // on rajoute cet enfant à la liste des enfants
		}
	}
}

function maj_arbre_rubriques(&$rub) {
	global $migre_meta;
	$insere=empty($rub->id_rubrique_reel);
	if ( $insere
		AND 
		$rub->id_rubrique_souhaite 
		AND
		 empty($rub->titre)
		) {
			if (!$rub->maj_infos()) { //pas de rubrique a ce numero
				$rub->titre="Rubrique ".$rub->id_rubrique_souhaite;
			}
			else {
				// on doit mettre a jour toutes les rub filles avec id_parent_reel
				while (list($k,)=each($rub->liste_rub_enfants)) {
					$rub->liste_rub_enfants[$k]->id_parent_reel=$rub->id_rubrique_reel;
				}
				$insere=false;
			}
		}
	if ($insere) {
			// inserer
			// a revoir c'est faux a ce stade sauf pour la racine et les rubriques pre-existantes
			if ( empty($rub->id_parent_reel) ) $rub->id_parent_reel=$rub->id_secteur; // ne devrait jamais arriver
			if ( empty($rub->lang) and is_object($rub->rub_parent) ) $rub->lang=$rub->rub_parent->lang; // on prend la langue du parent
			if ( empty($rub->lang) ) $rub->lang=$GLOBALS['meta']['langue_site'];

			if ($migre_meta['migre_test']) {
				$rub->id_rubrique_reel = $rub->id_rubrique_souhaite;
			}
			else {
				$result = spip_query( "INSERT INTO spip_rubriques ( id_parent, id_secteur, titre, statut, lang ) "
					. " VALUES "
					. " ( '".$rub->id_parent_reel."','".$rub->id_secteur."','".$rub->titre."','".$rub->statut."','".$rub->lang."' ) ");
				$rub->id_rubrique_reel=spip_insert_id();
			}
	}

	// on met a jour les sous rubriques (s'il y en a)
	if (is_array($rub->liste_rub_enfants)) {
		$liste_rub_enfants=$rub->liste_rub_enfants;
		reset($rub->liste_rub_enfants);
		while ( list($k,)=each($liste_rub_enfants) ) {
			$rub->liste_rub_enfants[$k]->id_parent_reel=$rub->id_rubrique_reel;
			maj_arbre_rubriques($rub->liste_rub_enfants[$k]);
		}
	}

	// on met a jour les articles (s'il y en a)
	if (is_array($rub->liste_articles)) {
		$liste_articles=$rub->liste_articles;
		reset($rub->liste_articles);
		while ( list($i,)=each($liste_articles) ) {
			$rub->liste_articles[$i]->id_rubrique_reel = $rub->id_rubrique_reel;
			$rub->liste_articles[$i]->id_secteur = $rub->id_secteur; // pas vraiment necessaire
			if (empty($rub->liste_articles[$i]->lang))
				$rub->liste_articles[$i]->lang = $rub->lang;
		}
	}
}

// met a jour les articles dans la base (si necessaire)
function maj_articles(&$liste_articles) {
	global $migre_meta;

	// [fr] Récup des URIs du site statique et traitement
	// [en] Get all URIs and process them
	$res_list= _T('migrestatic:resultat_liste_pages'). "\n<div $dir_lang style='width:98%;height:4em;overflow:auto;border: 1px dashed #ada095;padding:2px;margin:2px;background-color:#eee;text-align:left;'>" ;

	$res  = "";
	$i=0;
	$table_uri=array();
	reset($liste_articles);
	while ( list($i,$art)=each($liste_articles)) {
		$res_list .= "<a href='".$art->url."'>".$art->url."</a><br />";
		$liste_articles[$i]->migre_article();
		$table_uri[$art->url]=$liste_articles[$i]->id_article;
		$res .= debut_cadre_relief('article-24.gif',true,'',
				_T('migrestatic:processing_page')." <a href='".$art->url."'>".$art->url."</a>")
			 . $liste_articles[$i]->affiche
			 .fin_cadre_relief(true);
		if ($migre_meta['migre_test'] AND $i>5) break; else $i++; // [fr] On ne teste que les 5 premieres pages [en] Test only first 5 pages
	}

	if ($migre_meta['migre_test']) {
		//print_r($table_uri);
		$res.="Pas de mise a jour des liens<br>";
	} else {
		// met a jour les liens au sein des articles
		$res.=  "<b>"._T('migrestatic:mis_a_jour').migre_parse_url($table_uri)."</b>";
	}
	$res .= "<div style='clear:both'>"._T('migrestatic:migre_fini')."</div>";
	$res_list.= "<br style='clear: both;' />\n</div>\n";
	return $res_list.$res;
}


// ------------------------------------------------------------------------------
// [fr] Parcourt la liste des uri/articles et essaie de remplace les URL locales en numeros d'articles
// ------------------------------------------------------------------------------
function migre_parse_url($table_uri="") {
	$cpt=0;
	if (empty($table_uri) or !is_array($table_uri)) return;
	//copie de la liste
	reset($table_uri);
	while (list(,$id)=each($table_uri)) $table_art[]=$id;
	//construction des remplacements
	reset($table_uri);
	while (list(,$uri)=each($table_uri)) $table_search[]="/\-\>".str_replace("/","\/",html_entity_decode($uri))."\]/";
	reset($table_uri);
	while (list(,$id)=each($table_uri)) $table_replace[]="->art$id]"; // conversion des URIs dans migre_html_to_spip

	while (list(,$id_article)=each($table_art)) {
		$sql = "SELECT texte FROM spip_articles WHERE id_article=$id_article";
		$result=spip_query($sql);
		if ($row = spip_fetch_array($result)) {
			$row['texte']=stripslashes($row['texte']);
			$texte = preg_replace($table_search,$table_replace,$row['texte']); 
			if ($texte!=$row['texte']) { // on ne fait d'update que s'il y a eu modification(s)
				$texte=addslashes($texte);
				$sql="UPDATE spip_articles SET texte='$texte' WHERE id_article=$id_article";
				$result=spip_query($sql);
				$cpt++;
			} // if modif
		} // if $row
	} // while
	return $cpt;
} // migre_parse_url


?>
