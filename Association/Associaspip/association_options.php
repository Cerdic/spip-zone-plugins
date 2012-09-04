<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;


/*****************************************
 * Initialisations
**/

/**
 * @global array $GLOBALS['association_liste_des_statuts']
 * @name $association_liste_des_statuts
 */
$GLOBALS['association_liste_des_statuts'] =
  array('sorti','prospect','ok','echu','relance'); // Le premier element indique un ancien membre

/**
 * @global array $GLOBALS['association_styles_des_statuts']
 * @name $association_styles_des_statuts
 */
$GLOBALS['association_styles_des_statuts'] = array(
	'echu' => 'impair',
	'ok' => 'valide',
	'prospect' => 'prospect',
	'relance' => 'pair',
	'sorti' => 'sortie'
);

/**
 * @var const _DIR_PLUGIN_ASSOCIATION_ICONES
 *   Repertoire de base des images (icones/logos/etc) d'Associaspip
 */
define('_DIR_PLUGIN_ASSOCIATION_ICONES', _DIR_PLUGIN_ASSOCIATION.'img_pack/');

/**
 * @global array $GLOBALS['spip_pipeline']['modules_asso']
 * @name $association_modules
 */
if ( !isset($GLOBALS['spip_pipeline']['modules_asso']) )
	$GLOBALS['spip_pipeline']['modules_asso'] = ''; // definir ce pipeline, sans ecraser sa valeur s'il existe


/*****************************************
 * @defgroup association_bouton
 * Affichage HTML : boutons d'action dans les listing
 *
** @{ */

/**
 * boutons d'action (si page de script indiquee) generique
 *
 * @param string $texte
 *   libelle du bouton
 * @param string $image
 *   nom du fichier de l'icone du bouton
 * @param string $script
 *   nom du fichier de traitement appele par le bouton (dans un lien "?exec=...")
 * @param string $exec_args
 *   autres parametres (outre le nom du script) passes a l'URL
 * @param string $img_attrs
 *   autres attributs passes a la balise affichant l'image
 * @return string $res
 *   code HTML du bouton
 *
 * @todo voir s'il est possible d'utiliser plutot la fonction bouton_action($libelle, $url, $class="", $confirm="", $title="") definie dans /ecrire/inc/filtres.php
 */
function association_bouton_faire($texte, $image, $script='', $exec_args='', $img_attrs='')
{
	$res = ($script ? '<a href="'.generer_url_ecrire($script, $exec_args).'">' : '' );
	$res .= '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.$image.'" alt="';
	$res .= ($texte ? _T('asso:'.$texte).'" title="'._T('asso:'.$texte) : ' ' );
	$res .= '" '.$img_attrs.' />';
	$res .= ($script?'</a>':'');
	return $res;
}

/**
 * @name association_bouton_<agir>
 * cas specifique de :
 *
 * @param string $objet
 *   nom de l'objet pour lequel on genere le bouton : c'est ce nom, prefixe
 *   d'un <mot> selon une convention, qui correspond au fichier d'execution
 *   appele par le lien du bouton
 * @param int|string $args
 *   identifiant de l'objet (le nom du parametre est alors "id")
 *   ou chaine des parametres passes a l'URL
 * @param string $tag
 *   balise-HTML encadrante (doit fonctionner par paire ouvrante et fermante) ;
 *   "TD" par defaut car dans Associaspip un tel bouton est genere dans une cellule de tableau
 * @return string $res
 *   code HTML du bouton
 */
//@{

/**
 * bouton de vue non modifiable ou apercu
 * <mot> = voir_
 */
function association_bouton_afficher($objet, $args='', $tag='td')
{
	$res = ($tag?"<$tag class='action'>":'');
	$res .= association_bouton_faire('bouton_voir', 'voir-12.png', "voir_$objet", is_numeric($args)?"id=$args":$args, 'width="12" height="12" alt="&#x2380;"');
	$res .= ($tag?"</$tag>":'');
	return $res;
}

/**
 * bouton d'edition (modification)
 * <mot> = edit_
 */
function association_bouton_modifier($objet, $args='', $tag='td')
{
	$res = ($tag?"<$tag class='action'>":'');
	$res .= association_bouton_faire('bouton_modifier', 'edit-12.gif', "edit_$objet", is_numeric($args)?"id=$args":$args, 'width="12" height="12" alt="&#x2380;"');
	$res .= ($tag?"</$tag>":'');
	return $res;
}

/**
 * bouton d'effacement (suppression)
 * <mot> = suppr_
 */
function association_bouton_supprimer($objet, $args='', $tag='td')
{
	$res = ($tag?"<$tag class='action'>":'');
	$res .= association_bouton_faire('bouton_supprimer', 'suppr-12.gif', "suppr_$objet", is_numeric($args)?"id=$args":$args, 'width="12" height="12" alt="&#x2327;"'); // 8 pluriel contre 3 singulier
	$res .= ($tag?"</$tag>":'');
	return $res;
}

//@}

/** @} */


/*****************************************
 * @defgroup association_calculer
 * Affichage HTML dans les listing d'une chaine calculee selon la configuration
 *
** @{ */

/**
 * Affichage d'un nom complet (de membre) suivant la configuration du plugin (i.e. champs geres ou non)
 *
 * @param string $civilite
 *   Civilite (M./Mme/Mle) ou titre (Dr./Pr./Mgr/Gle/etc.)
 * @param string $prenom
 *   Prenom(s)
 * @param string $nom
 *   Nom de famille
 * @param string $html_tag
 *   Indique la balise-HTML (paire ouvrante/fermante) servant a grouper le
 *   resultat. Sa presence (rien par defaut) indique d'appliquer le micro-
 *   formatage du groupe.
 * @return string $res
 *   Chaine du nom complet du membre, micro-formate ou non.
 */
function association_calculer_nom_membre($civilite, $prenom, $nom, $html_tag='')
{
	$res = '';
	if ($html_tag) {
		$res = '<'.$html_tag.' class="'. (($civilite || $prenom)?'n':'fn') .'">';
	}
	if ($GLOBALS['association_metas']['civilite'] && $civilite) {
		$res .= ($html_tag?'<span class="honorific-prefix">':'') .$civilite. ($html_tag?'</span>':'') .' ';
	}
	if ($GLOBALS['association_metas']['prenom'] && $prenom) {
		$res .= ($html_tag?'<span class="given-name">':'') .$prenom. ($html_tag?'</span>':'') .' ';
	}
	if ($nom) {
		$res .= ($html_tag?'<span class="family-name">':'') .$nom. ($html_tag?'</span>':'') .' ';
	}
	if ($html_tag) {
		$res .= '</'.$html_tag.'>';
	}
	return $res;
}

/**
 * Affichage du nom avec le lien vers la page correspondante
 *
 * En fait c'est pour les modules dons/ventes/activites/prets ou l'acteur (donateur/acheteur/inscrit/emprunteur)
 * peut etre un membre/auteur (son id_acteur est alors renseigne) mais pas
 * forcement son nom (qui peut etre different)
 * ou peut etre une personne exterieure a l'association (on a juste le nom alors
 * obligatoire)
 *
 * @param string $nom
 *   Nom complet affiche
 * @param int $id
 *   ID de l'objet lie
 * @param string $type
 *   Raccourci utilise pour faire le lien
 *   Par defaut : "membre"
 * @param string $html_tag
 *   Balise-HTML (paire ouvrante/fermante) encadrante
 * @return string $res
 *   Lien interne SPIP
 */
function association_calculer_lien_nomid($nom, $id, $type='membre', $html_tag='')
{
	$res = '';
	if ($html_tag) {
		$res = '<'.$html_tag.' class="fn">';
	}
	if ($id) {
		$res .= '[';
	}
	$res .= $nom;
	if ($id) {
		$res .= "->$type$id]";
	}
	if ($html_tag) {
		$res .= '</'.$html_tag.'>';
	}
	return propre($res);
}

/** @} */


/*****************************************
 * @defgroup association_formater
 * Affichage HTML d'une chaine localisee et micro-formatee.
 * La chaine initiale est (essentiellement) issue de la base de donnees apres
 * passage par un @ref association_recuperer si necessaire.
 *
 * @note association_formater_<quoi> s'appelait association_<quoi>fr ;
 * "fr" initialement pour FRanciser puis est devenu synonyme de FoRmat
 *
** @{ */

/**
 *  Affichage de date localisee et micro-formatee
 *
 * @param string $iso_date
 *   Date au format ISO-8601
 *   http://fr.wikipedia.org/wiki/ISO_8601#Date_et_heure
 * @param string $css_class
 *   Classe(s) CSS (separees par un espace) a rajouter
 *   Normalement : dtstart|dtend
 * @param string $html_tag
 *   Balise-HTML (paire ouvrante/fermante) encadrante
 *   Par defaut : "abbr"
 *   http://www.alsacreations.com/tuto/lire/1222-microformats-design-patterns.html#datetime-design-pattern
 *   Desactiver (chaine vide) pour ne pas micro-formater
 * @return string $res
 *   Date formatee
 */
function association_formater_date($iso_date, $css_class='', $htm_tag='abbr')
{
	$res = '';
	if ($html_tag)
		$res = "<$html_tag ". ($css_class?"class='$css_class' ":'') ."title='$iso_date'>";
	$res .= affdate_base($iso_date, 'entier'); // on fait appel a la fonction centrale des filtres SPIP... comme ca c'est traduit et formate dans les langues supportees ! si on prefere les mois en chiffres et non en lettre, y a qu'a changer les chaines de langue date_mois_XX
	if ($html_tag)
		$res .= ($html_tag?"</$htm_tag>":'');
	return $res;
}

/**
 * Affichage de nombre localise
 *
 * @param float $nombre
 *   Valeur numerique au format informatique standard
 * @param int $decimales
 *   Nombre de decimales affichees.
 *   Par defaut : 2
 * @param string $l10n
 *   Code ISO-639 de la langue voulue
 *   Par defaut : on tente de detecter la langue du navigateur sinon celle du site
 * @return string $res
 *   Nombre formatee
 *
 * @note Perfectible... Avis aux contributeurs motives...
 */
function association_formater_nombre($nombre, $decimales=2, $l10n='')
{
	// recuperer le code des parametres regionnaux a utiliser
	// dans un premier temps, on essaye d'utiliser la langue puisque SPIP gere
	// bien cela et offre la possibilite d'en faire plus avec
	//  http://programmer.spip.org/Forcer-la-langue-selon-le-visiteur
	// Comme ce n'est pas suffisant (le code de localisation est de la forme
	// langue-pays ou langue_PAYS en utilisant les codes ISO), et recuperer le
	// pays n'est pas simple sans faire appel a l'IP-geolocalisation
	// http://stackoverflow.com/questions/2156231/how-do-you-detect-a-website-visitors-country-specifically-us-or-not
	// Ni SPIP ni PHP n'offrant de moyen "simple" d'arriver a nos fin bah...
	if (!$l10n) { // pas de localae specifiee
		$l10n = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		if (!$l10n) { // pas specifie par le navigateur non plus ?
			$l10n = array('french', 'fr_FR', 'fr_FR@euro', 'fr_FR.iso88591', 'fr_FR.iso885915@euro', 'fr_FR.utf8', 'fr_FR.utf8@euro'); // alors on s'impose...
		} else { // si specifie, on va transformer en tableau http://www.thefutureoftheweb.com/blog/use-accept-language-header
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $l10n, $lang_parse);
			if (count($lang_parse[1])) { // creer la liste locale=>preference
				$langues = array_combine($lang_parse[1], $lang_parse[4]);
				foreach ($langues as $langue => $taux) { // pour les taux de preferences non specifies, mettre a 100%
					if ($taux==='')
						$langues[$langue] = 1;
				}
				arsort($langues, SORT_NUMERIC); // ordonne par taux de preferences
				$l10n = array_keys($langues); // on recupere la liste des langues triees
			}

		}
	}
	// formater selon la langue choisie/recuperee
	// http://stackoverflow.com/a/437642
	setlocale(LC_NUMERIC, $l10n);
	$locale = localeconv();
    return number_format(floatval($nombre), $decimales, $locale['decimal_point'], $locale['thousands_sep']);
}

/**
 * Affichage de duree localisee et micro-formatee
 *
 * @param int|string $nombre
 *   Valeur numerique de la duree.
 * @param string $unite
 *   Lettre indiquant le type de duree affiche : Y|M|W|D|H
 *   respectivement pour annee|mois|semaine|jour|heures
 *   Noter qu'il est possible d'utiliser les equivalents francais : A|S|J
 *   Noter aussi qu'on peut avoir en prime T (horaire seule) ou I (date),
 *   et dans ce cas ce n'est un nombre entier qui est utilise mais une chaine du temps au format ISO
 * @param string $html_tag
 *   Balise-HTML (paire ouvrante/fermante) encadrante
 *   Par defaut : "abbr" avec la classe "duration"
 *   http://www.alsacreations.com/tuto/lire/1222-microformats-design-patterns.html#abbr-design-pattern
 *   Desactiver (chaine vide) pour ne pas micro-formater
 * @return string $res
 *   Duree formatee
 *
 * @note les cas de minutes/secondes doivent etre specifie comme des heures au format ISO...
 */
function association_formater_duree($nombre, $unite='', $htm_tag='abbr')
{
	$frmt_h = ''; // format human-readable
	$frmt_m = 'P'; // format machine-parsable
	switch(strtoupper($unite)) { // http://ufxtract.com/testsuite/documentation/iso-duration.htm
		case 'Y' : // year
		case 'A' : // annee
			$nombre = intval($nombre);
			$frmt_m .= $nombre.'Y';
			$valeur = association_formater_nombre($nombre,0);
			$unite = ($nombre<=1) ? _T('local:an') : _T('local:ans');
			break;
		case 'M' : // month/mois
			$nombre = intval($nombre);
			$frmt_m .= $nombre.'M';
			$valeur = association_formater_nombre($nombre,0);
			$unite = ($nombre<=1) ? _T('spip:date_un_mois') : _T('spip:date_mois');
			break;
		case 'W' : // week
		case 'S' : // semaine
			$nombre = intval($nombre);
			$frmt_m .= $nombre.'W';
			$valeur = association_formater_nombre($nombre,0);
			$unite = ($nombre<=1) ? _T('spip:date_une_semaine') : _T('spip:date_semaines');
			break;
		case 'D' : // day
		case 'J' : // jour
			$nombre = intval($nombre);
			$frmt_m .= $nombre.'D';
			$valeur = association_formater_nombre($nombre,0);
			$unite = ($nombre<=1) ? _T('local:jour') : _T('spip:date_jours');
			break;
		case 'H' : // hour/heure
			$frmt_m .= 'T'.str_replace('00M', '',  str_replace(':','H',$nombre.':00').'M' );
			$valeur = association_formater_nombre($nombre,0);
			if (intval($nombre)>1)
				$unite = _T('spip:date_heures');
			elseif (is_numeric($nombre))
				$unite = _T('spip:date_une_heure');
			elseif (strstr($nombre,'0:00'))
				$unite = _T('spip:date_une_minute');
			else {
				$nombre = explode(':',$nombre);
				$frmt_h = _T('spip:date_fmt_heures_minutes', array('h'=>$nombre[0],'m'=>$nombre[1]));
			}
			break;
		case 'T' : // (full) ISO Time : no check...
			$frmt_m .= 'T'.str_replace( array('HM','HS','MS','00H','00M'), array('H','H','M'), preg_replace('m:m','M',preg_replace('h:h','H',$nombre,1),1).'S' );
			$nombre = explode(':',$nombre,2);
			if ($nombre[0]>24) { // http://dev.mysql.com/doc/refman/4.1/en/time.html
				$nombre['-1'] = intval($nombre[0]/24);
				$nombre[0] = $nombre[0]%24;
			}
			switch($nombre['-1']) { // nombre de jours
				case 0:
				case '':
					$frmt_h = '';
					break;
				case 1:
					$frmt_h = _T('duree_temps', array('nombre'=>1,'unite'=>_T('local:jour')));
					break;
				default:
					$frmt_h =  _T('duree_temps', array('nombre'=>association_formater_nombre($nommbre['-1'],0),'unite'=>_T('spip:date_jours')));
					break;
			}
			if ($nombre[0])
				$frmt_h .= ', ';
			switch($nombre[0]) { // nombre d'heures
				case 0:
					$frmt_h .= '';
					break;
				case 1:
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>_T('spip:date_une_heure')));
					break;
				default:
					$frmt_h .= _T('duree_temps', array('nombre'=>association_formater_nombre($nombre[0],0),'unite'=>_T('spip:date_heures')));
					break;
			}
			if ($nombre[1])
				$frmt_h .= ', ';
			switch($nombre[1]) { // nombre de minutes
				case 0:
					$frmt_h .= '';
					break;
				case 1:
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>_T('spip:date_une_minute')));
					break;
				default:
					$frmt_h .= _T('duree_temps', array('nombre'=>association_formater_nombre($nombre[1],0),'unite'=>_T('spip:date_minutes')));
					break;
			}
			if ($nombre[2])
				$frmt_h .= ', ';
			switch($nombre[2]) { // nombre de secondes
				case 0:
					$frmt_h .= '';
					break;
				case 1:
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>_T('spip:date_une_seconde')));
					break;
				default:
					$frmt_h .= _T('duree_temps', array('nombre'=>association_formater_nombre($nombre[2],0),'unite'=>_T('spip:date_secondes')));
					break;
			}
			$frmt_h .= '. ';
			break;
		case 'I' : // (full) ISO DateTime or Date : no check !!!
		default :
			$frmt_m .= $nombre;
			$nombre = explode('T',$nombre,2);
			$ladate = explode(':',$nombre[0]);
			switch($ladate[0]) { // nombre d'annee
				case 0:
				case '':
					$frmt_h = '';
					break;
				case 1:
					$frmt_h = _T('duree_temps', array('nombre'=>1,'unite'=>_T('local:an')));
					break;
				default:
					$frmt_h =  _T('duree_temps', array('nombre'=>association_formater_nombre($ladate[0],0),'unite'=>_T('local:ans')));
					break;
			}
			if ($ladate[1])
				$frmt_h .= ', ';
			switch($ladate[1]) { // nombre de mois
				case 0:
					$frmt_h .= '';
					break;
				case 1:
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>_T('spip:date_un_mois')));
					break;
				default:
					$frmt_h .= _T('duree_temps', array('nombre'=>association_formater_nombre($ladate[1],0),'unite'=>_T('spip:date_mois')));
					break;
			}
			if ($ladate[2])
				$frmt_h .= ', ';
			switch($ladate[2]) { // nombre de jours
				case 0:
					$frmt_h .= '';
					break;
				case 1:
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>_T('local:jour')));
					break;
				default:
					$frmt_h .= _T('duree_temps', array('nombre'=>association_formater_nombre($ladate[2],0),'unite'=>_T('spip:date_jours')));
					break;
			}
			if (count($lheure))
				$frmt_h .= ', ';
			$lheure = explode(':',$nombre[1]);
			switch($lheure[0]) { // nombre d'heures
				case 0:
					$frmt_h .= '';
					break;
				case 1:
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>_T('spip:date_une_heure')));
					break;
				default:
					$frmt_h .=  _T('duree_temps', array('nombre'=>association_formater_nombre($lheure[0],0),'unite'=>_T('spip:date_heures')));
					break;
			}
			if ($lheure[1])
				$frmt_h .= ', ';
			switch($lheure[1]) { // nombre d'heures
				case 0:
					$frmt_h .= '';
					break;
				case 1:
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>_T('spip:date_une_minute')));
					break;
				default:
					$frmt_h .=  _T('duree_temps', array('nombre'=>association_formater_nombre($lheure[1],0),'unite'=>_T('spip:date_minutes')));
					break;
			}
			if ($lheure[2])
				$frmt_h .= ', ';
			switch($lheure[2]) { // nombre d'heures
				case 0:
					$frmt_h = '';
					break;
				case 1:
					$frmt_h = _T('duree_temps', array('nombre'=>1,'unite'=>_T('spip:date_une_seconde')));
					break;
				default:
					$frmt_h =  _T('duree_temps', array('nombre'=>association_formater_nombre($lheure[2],0),'unite'=>_T('spip:date_secondes')));
					break;
			}
			$frmt_h .= '. ';
			break;
	}
	if (!$frmt_h)
		$frmt_h = _T('asso:duree_temps', array('nombre'=>$valeur, 'unite'=>$unite) );
	return $html_tag ? "<$htm_tag class='duration' title='". htmlspecialchars($frmt_m, ENT_QUOTES, $GLOBALS['meta']['charset']). "'>$frmt_h</$htm_tag>" : $frmt_h;
}

/**
 * Affichage de prix (montant et devise) localisee et micro-formatee
 *
 * @param float|int $montant
 *   Montant (valeur chiffree) correspondant au prix
 * @param string $devise_code
 *   Trigramme representant le code ISO-4217 de la devise
 *   http://fr.wikipedia.org/wiki/ISO_4217
 *   Par defaut : la code defini dans le fichier de langues, sinon EUR
 * @param string $devise_symb
 *   Symbole ou nom generique abrege de la devise
 *   Par defaut : le symbole defini dans le fichier de langues si defini, sinon le code.
 * @param string $html_span
 *   Balise-HTML (paire ouvrante/fermante) encadrant l'ensemble
 *   Par defaut : "span" avec les classes "money price"
 *   http://www.alsacreations.com/tuto/lire/1222-microformats-design-patterns.html#value-class-pattern
 *   Desactiver (chaine vide) pour ne pas micro-formater
 * @param string $html_abbr
 *   Balise-HTML (paire ouvrante/fermante) encadrant chaque sous-partie
 *   Par defaut : "abbr" avec la classe "duration"
 * @return string $res
 *   Duree formatee
 *
 * @note On n'utilise pas la fontcion PHP money_format() --qui ne fonctionne pas
 * sous Windows-- car on veut micro-formater avec une devise fixee par la
 * configuration (en fait les chaines de langue) du plugin
 */
function association_formater_prix($montant, $devise_code='', $devise_symb='', $htm_span='span', $htm_abbr='abbr')
{
	$res = '';
	if ($html_span)
		$res .= "<$htm_span class='money price'>"; // pour la reference est "price" <http://microformats.org/wiki/hproduct> (reconnu par les moteurs de recherche), mais "money" <http://microformats.org/wiki/currency-brainstorming> est d'usage courant aussi
	$montant = ($html_abbr?"<$htm_abbr class='amount' title='$montant'>":'') . association_formater_nombre($montant) . ($html_abbr?"</$htm_abbr>":'');
	if ( !$devise_code ) {
		$devise_code = _T('asso:devise_code_iso');
		if ( !$devise_code )
			$devise_code = 'EUR';
		$devise_symb = _T('asso:devise_symbole');
	}
	if ( !$devise_symb ) {
		if ( function_exists('formater_devise') ) // plugin "Devise" est actif
			$devis_symp = formater_devise($devise_code, '%N');
		else
			$devise_symb = $devise_code;
	}
	$devise = ($html_abbr ? "<$htm_abbr class='currency' title='". htmlspecialchars($devise_code, ENT_QUOTES, $GLOBALS['meta']['charset']) .'\'>' : '') . $devise_symb . ($html_abbr?"</$htm_abbr>" :'');
	$res .= _T('asso:devise_montant', array('montant'=>$montant, 'devise'=>$devise) );
	return $html_span ? "$res</$htm_span>" : $res;
}

/**
 * Affichage d'un texte formate
 *
 * @param string $texte
 *   Le texte brut initial
 * @param string $filtre
 *   Filtre SPIP a appliquer au texte
 * @param array $params
 *   Liste des parametres du filtre
 * @return string $res
 *   Texte formate
 * @note
 *   http://spipistrelle.clinamen.org/spip.php?article16
 */
function association_formater_texte($texte, $filtre='', $params=array() )
{
	if ( !is_array($params) )
		$params = array($params);
	$ok = array_unshift($params, $texte);
	return $filtre?call_user_func_array($filtre, $params):$texte;
}

/** @} */


/*****************************************
 * @defgroup association_recuperer
 * Transforme un champ de formulaire en vue de son insertion en base de donnees.
 * S'utilise donc sur un champ passe par le @ref association_verifier correspondant.
 * Assure donc un bon enregistrement et la restitution par le @ref association_formater correspondant.
 *
 * @param string $valeur
 *   Nom a recuperer (par GET ou POST ou Cookie) ...ou la valeur directement
 * @param bool $req
 *   Indique s'il s'agit du nom (vrai --par defaut) ou pas (faux, donc la valeur)
 *
** @{ */

/**
 * @return string $valeur
 *   Date au format ISO
 */
function association_recuperer_date($valeur, $req=false)
{
	if ($valeur!='') {
		$valeur = preg_replace('/\D/', '-', ($req?_request($valeur):$valeur), 2); // la limitation a 2 separateurs permet de ne transformer que la partie "date" s'il s'agit d'un "datetime" par exemple.
	}
	return $valeur;
}

/**
 * @return float $valeur
 *   Nombre decimal
 */
function association_recuperer_montant($valeur, $req=false)
{
	if ($valeur!='') {
		$valeur = str_replace(' ', '', ($req?_request($valeur):$valeur) ); // suppprime les espaces separateurs de milliers
		$valeur = str_replace(',', '.', $valeur); // convertit les , en .
		$valeur = floatval($valeur);
	} else
		$valeur = 0.0;
	return $valeur;
}

/** @} */


/*****************************************
 * @defgroup association_verifier
 * Verification du format de la valeur d'un champ de formulaire.
 * Permet d'appeler @ref association_recupere equivalent sur ce champ...
 *
 * @param string $valeur
 *   Nom a recuperer (par GET ou POST ou Cookie) ...ou la valeur directement
 * @param bool $rex
 *   Indique si la verification est plus lache (vrai) ou pas (faux --par defaut)
 *   [le nom de la variable signifie "RElaXed check"]
 * @param bool $req
 *   Indique s'il s'agit du nom (vrai --par defaut) ou pas (faux, donc la valeur)
 *   [le nom de la variable signifie "by REQuest"]
 * @return string
 *   Message d'erreur... (donc chaine vide si OK)
 *
** @{ */

/**
 * S'assurer que la valeur saisie est une chaine de date valide
 */
function association_verifier_date($valeur, $rex=FALSE, $req=TRUE)
{
	$date = $req ? _request($valeur) : $valeur;
	if ( $rex && ($date=='0000-00-00' || !$date) )
		return '';
	if (!preg_match('/^\d{4}\D\d{2}\D\d{2}$/', $date)) // annee sur 4 chiffres ; mois sur 2 chiffres ; jour sur 2 chiffres ; separateur est caractere non numerique quelconque...
#	if (!preg_match('/^\d{4}\D(\d|1[0-2])\D([1-9]|0[1-9]|[12]\d|3[01])$/', $date)) // annee sur 4 chiffres ; mois sur 1 ou 2 chiffres entre 1 et 12 ; jour sur 1 ou 2 chiffres eentre 1 et 31 ; separateur est n'importe quel caractere ne representant pas un chiffre arabe de la notation decimale standard...
		return _T('asso:erreur_format_date', array('date'=>$date) ); // ...c'est un petit plus non documente (la documentation et le message d'erreur stipulent AAAA-MM-JJ : mois et jours toujours sur deux chiffres avec donc zero avant si inferieur a 10, et separateur est tiret)
	list($annee, $mois, $jour) = preg_split('/\D/', $date);
	if (!checkdate($mois, $jour, $annee)) // la date doit etre valide : pas de 30 fevrier ou de 31 novembre par exemple.
		return _T('asso:erreur_valeur_date', array('date'=>$date) );
	return '';
}

/**
 * S'assurer que la valeur saisie est un flottant positif
 */
function association_verifier_montant($valeur, $req=TRUE)
{
	if (association_recuperer_montant($valeur,$req)<0)
		return _T('asso:erreur_montant');
	else
		return '';
}

/**
 * S'assurer que l'entier saisie correspond bien a un id_auteur
 * de la table spip_asso_membres (par defaut) ou spip_auteurs (si on elargi a tous
 * --ceci permet d'editer des membres effaces tant qu'ils sont references par SPIP)
 */
function association_verifier_membre($valeur, $rex=FALSE, $req=TRUE)
{
	$id_auteur = intval($req?_request($valeur):$valeur);
	if ($id_auteur) {
		if ( sql_countsel('spip_'.($rex?'auteurs':'asso_membres'), "id_auteur=$id_auteur")==0 ) {
			return _T('asso:erreur_id_adherent');
		}
	} else
		return '';
}

/** @} */


/*****************************************
 * @defgroup association_selectionner
 * Selecteur HTML (liste deroulante) servant a filtrer le listing affiche en milieu de page
 *
 * @param int $sel
 *   ID selectionne : conserve la valeur selectionnee
 * @param string $exec
 *   Nom du fichier de l'espace prive auquel le formulaire sera soumis.
 *   Si present, le formulaire complet (balise-HTML "FORM") est genere.
 *   Si absent (par defaut), seul le selecteur (et le code supplementaire fourni
 *   par $plus) est(sont) renvoye(s).
 * @param string $plus
 *   Source HTML rajoute a la suite.
 *   (utile si on genere tout le formulaire avec des champs caches)
 * @return string $res
 *   Code HTML du selecteur (ou du formulaire complet si $exec est indique)
 *
** @{ */

/**
 * Selecteur d'exercice comptable
 */
function association_selectionner_exercice($sel='', $exec='', $plus='')
{
    $res = '<select name ="exercice" onchange="form.submit()">';
#    $res .= '<option value="0" ';
#    if (!$exercice) {
#		$res .= ' selected="selected"';
#    }
#    $res .= '>'. _L("choisir l'exercice ?") .'</option>';
    $sql = sql_select('id_exercice, intitule', 'spip_asso_exercices','', 'intitule DESC');
    while ($val = sql_fetch($sql)) {
		$res .= '<option value="'.$val['id_exercice'].'" ';
		if ( $sel==$val['id_exercice'] ) {
			$res .= ' selected="selected"';
		}
		$res .= '>'.$val['intitule'].'</option>';
    }
    $res .= '</select>'.$plus;
    return $exec ? generer_form_ecrire($exec, $res.'<noscript><input type="submit" value="'._T('asso:bouton_lister').'" /></noscript>') : $res;
}

/**
 * Selecteur de destination comptable
 */
function association_selectionner_destination($sel='', $exec='', $plus='')
{
    $res = '<select name ="destination" onchange="form.submit()">';
    $res .= '<option value="0" ';
    if ( !$sel) {
		$res .= ' selected="selected"';
    }
    $res .= '>'. _T('asso:toutes_destinations') .'</option>';
    $intitule_destinations = array();
    $sql = sql_select('id_destination, intitule', 'spip_asso_destination','', 'intitule DESC');
    while ($val = sql_fetch($sql)) {
		$res .= '<option value="'.$val['id_destination'].'" ';
		if ( $sel==$val['id_destination'] ) {
			$res .= ' selected="selected"';
		}
		$res .= '>'.$val['intitule'].'</option>';
    }
    $res .= '</select>'.$plus;
    if ($GLOBALS['association_metas']['destinations']){
		return $exec ? generer_form_ecrire($exec, $res.'<noscript><input type="submit" value="'._T('asso:bouton_lister').'" /></noscript>') : $res;
	} else {
		return '';
	}
}

/**
 * Selecteur de grouoe de membres
 */
function association_selectionner_groupe($sel='', $exec='', $plus='')
{
    $qGroupes = sql_select('nom, id_groupe', 'spip_asso_groupes', 'id_groupe>=100', '', 'nom');  // on ne prend en consideration que les groupe d'id >= 100, les autres sont reserves a la gestion des autorisations
    if ( $qGroupes && sql_count($qGroupes) ) { // ne proposer que s'il y a des groupes definis
		$res = '<select name="groupe" onchange="form.submit()">';
		$res .= '<option value="">'._T('asso:tous_les_groupes').'</option>';
		while ($groupe = sql_fetch($qGroupes)) {
			$res .= '<option value="'.$groupe['id_groupe'].'"';
			if ( $sel==$groupe['id_groupe'] )
				$res .= ' selected="selected"';
			$res .= '>'.$groupe['nom'].'</option>';
		}
		$res .= '</select>'.$plus;
		return $exec ? generer_form_ecrire($exec, $res.'<noscript><input type="submit" value="'._T('asso:bouton_lister').'" /></noscript>') : $res;
	} else {
		return '';
	}
}

/**
 * Selecteur de statut de membres
 */
function association_selectionner_statut($sel='', $exec='', $plus='')
{
    $res = '<select name="statut_interne" onchange="form.submit()">';
    $res .= '<option value="%"'. (($sel=='defaut' || $sel=='%')?' selected="selected"':'') .'>'._T('asso:entete_tous').'</option>';
    foreach ($GLOBALS['association_liste_des_statuts'] as $statut) {
		$res .= '<option value="'.$statut.'"';
		if ( $sel==$statut )
			$res .= ' selected="selected"';
		$res .= '> '._T('asso:adherent_entete_statut_'.$statut).'</option>';
	}
	$res .= '</select>'.$plus;
    return $exec ? generer_form_ecrire($exec, $res.'<noscript><input type="submit" value="'._T('asso:bouton_lister').'" /></noscript>') : $res;
}

/**
 * Zone de saisie de numero de membre
 */
function association_selectionner_id($sel='', $exec='', $plus='')
{
    $res = '<input type="text" name="id" onfocus=\'this.value=""\' size="5"  value="'. ($sel?$sel:_T('asso:entete_id')) .'" />'.$plus;
    return $exec ? generer_form_ecrire($exec, $res.'<noscript><input type="submit" value="'._T('asso:bouton_lister').'" /></noscript>') : $res;
}

/**
 * Selecteur d'annee parmi celles disponibles dans une table donnee
 *
 * @param string $annee
 *   Annee selectionnee. (annee courante par defaut)
 * @param string $dtable
 *   Nom (sans prefixe) de la table concernee
 * @param string $dchamp
 *   Nom (sans prefixe "date_") du champ contenant les annees recherchees
 *
 */
function association_selectionner_annee($annee='', $dtable, $dchamp, $exec='', $plus='')
{
    if ($exec) {
		$res = '<form method="post" action="'. generer_url_ecrire($exec) .'"><div>';
		$res .= '<input type="hidden" name="exec" value="'.$exec.'" />';
    } else {
		$res = '';
    }
    $pager = '';
    $res .= '<select name ="annee" onchange="form.submit()">';
    $an_max = sql_getfetsel("MAX(DATE_FORMAT(date_$dchamp, '%Y')) AS an_max", "spip_$dtable", '');
    $an_min = sql_getfetsel("MIN(DATE_FORMAT(date_$dchamp, '%Y')) AS an_min", "spip_$dtable", '');
    if ( $annee>$an_max || $annee<$an_min ) { // a l'initialisation, l'annee courante est mise si rien n'est indique... or si l'annee n'est pas disponible dans la liste deroulante on est mal positionne et le changement de valeur n'est pas top
		$res .= '<option value="'.$annee.'" selected="selected">'.$annee.'</option>';

	}
    $sql = sql_select("DATE_FORMAT(date_$dchamp, '%Y') AS annee", "spip_$dtable",'', 'annee DESC', 'annee');
    while ($val = sql_fetch($sql)) {
		$res .= '<option value="'.$val['annee'].'"';
		if ($annee==$val['annee']) {
			$res .= ' selected="selected"';
			$pager .= "\n<strong>$val[annee]</strong>";
		} else {
			$pager .= ' <a href="'. generer_url_ecrire($exec, '&annee='.$val['annee']) .'">'.$val['annee']."</a>\n";
		}
		$res .= '>'.$val['annee'].'</option>';
    }
    $res .= '</select>'.$plus;
    if ($exec) {
		$res .= '<noscript><input type="submit" value="'._T('asso:bouton_lister').'" /></noscript>';
		$res .= '</div></form>';
    }
    return $res;
}

/**
 * Selecteur de destinations comptables
 */
function association_selectionner_destinations($sel='', $exec='', $plus='')
{
    $res = '<select name ="destination[]" multiple="multiple" onchange="form.submit()">';
    $res .= '<option value="0" ';
    if ( !(array_search(0, $sel)===FALSE) ) {
		$res .= ' selected="selected"';
    }
    $res .= '>'. _T('asso:toutes_destinations') .'</option><option disabled="disabled">--------</option>';
    $intitule_destinations = array();
    $sql = sql_select('id_destination, intitule', 'spip_asso_destination','', 'intitule DESC');
    while ($val = sql_fetch($sql)) {
		$res .= '<option value="'.$val['id_destination'].'" ';
		if ( !(array_search($val['id_destination'], $sel)===FALSE) ) {
			$res .= ' selected="selected"';
		}
		$intitule_destinations[$val['id_destination']] = $val['intitule'];
    }
    $res .= '</select>'.$plus;
    if ($GLOBALS['association_metas']['destinations']){
		return $exec ? generer_form_ecrire($exec, $res.'<noscript><input type="submit" value="'._T('asso:bouton_lister').'" /></noscript>') : $res;
	} else {
		return FALSE;
	}
}

/** @} */


/*****************************************
 * @defgroup generer_url
 * Raccourcis
 *
 * Les tables ayant deux prefixes ("spip_asso_"),
 * le raccourci "x" implique de declarer le raccourci "asso_x"
 *
** @{ */

function generer_url_asso_don($id, $param='', $ancre='') {
	return  generer_url_ecrire('edit_don', 'id='.intval($id));
}
function generer_url_don($id, $param='', $ancre='') {
	return  array('asso_don', $id);
}

function generer_url_asso_membre($id, $param='', $ancre='') {
	return  generer_url_ecrire('adherent', 'id='.intval($id));
}
function generer_url_membre($id, $param='', $ancre='') {
	return  array('asso_membre', $id);
}

function generer_url_asso_vente($id, $param='', $ancre='') {
	return  generer_url_ecrire('edit_vente', 'id='.intval($id));
}
function generer_url_vente($id, $param='', $ancre='') {
	return  array('asso_vente', $id);
}

function generer_url_asso_ressource($id, $param='', $ancre='') {
	return  generer_url_ecrire('edit_ressource', 'id='.intval($id));
}
function generer_url_ressource($id, $param='', $ancre='') {
	return  array('asso_ressource', $id);
}

function generer_url_asso_activite($id, $param='', $ancre='') {
	return  generer_url_ecrire('voir_activite', 'id='.intval($id));
}
function generer_url_activite($id, $param='', $ancre='') {
	return  array('asso_activite', $id);
}

/** @} */


/*****************************************
 * @defgroup instituer_
 *
 * @param array $auteur
 * @return string
 *
** @{ */

function instituer_adherent_ici($auteur=array()){
	$instituer_adherent = charger_fonction('instituer_adherent', 'inc');
	return $instituer_adherent($auteur);
}

function instituer_statut_interne_ici($auteur=array()){
	$instituer_statut_interne = charger_fonction('instituer_statut_interne', 'inc');
	return $instituer_statut_interne($auteur);
}

/** @} */


/*****************************************
 * @defgroup association_totauxinfos
 * Informations de synthese, sur un objet, destinees a etre presente dans le bloc
 * d'infos contextuel debutant la colonne de gauche
 *
** @{ */

/**
 * Rappels sur l'objet dans le bloc infos
 *
 * C'est un resume ou une petite presentation de l'objet en cours
 * d'edition/lecture : ces informations permettent de situer le contexte de la
 * page et n'apparaissent pas dans le bloc central !
 *
 * @param string $titre
 *   Titre affiche en gros dans le bloc.
 * @param string $type
 *   Nom du raccourci, affiche au dessus du titre.
 * @param int $id
 *   ID de l'objet, affiche au dessus du titre
 * @param array $DesLignes
 *   Tableau des lignes supplementaires a rajouter dans le bloc, sous la forme :
 *   chaine_de_langue_du_titre (sans prefixe) => texte contenu/explication associe.
 * @param string $PrefixeLangue
 *   Prefixe de langue associe aux chaines de langue des titres de lignes.
 *   Par defaut : asso
 * @param string $ObjetEtendu
 *   Nom de l'objet etendu dont on desire afficher les lignes des champs rajoutes par "Interface Champs Extras 2".
 *   Par defaut : rien
 * @return string $res
 *
 * @note
 *   Ce n'est pas redondant d'avoir a la fois $type et $ObjetEtendu qui peuvent
 *   avoir des valeurs differentes comme on peut le voir dans exec/adherent.php et exec/inscrits_activite.php !
 */
function association_totauxinfos_intro($titre, $type='', $id=0, $DesLignes=array(), $PrefixeLangue='asso', $ObjetEtendu='')
{
	$res = '';
	if ($type) {
		$res .= '<div style="text-align: center" class="verdana1 spip_x-small">'. _T('asso:titre_num', array('titre'=>_T("local:$type"), 'num'=>$id) ) .'</div>'; // presentation propre a Associaspip qui complete par un autre titre (voir ci-apres). Dans un SPIP traditionnel on aurait plutot : $res .= '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'. _T("$PrefixeLangue:$type") .'<br /><span class="spip_xx-large">'.$id.'</span></div>';
	}
	$res .= '<div style="text-align: center" class="verdana1 spip_medium">'.$titre.'</div>';
	if ( count($DesLignes) OR $ObjetEtendu )
		$res .= '<dl class="verdana1 spip_xx-small">';
	foreach ($DesLignes as $dt=>$dd) {
		$res .= '<dt>'. _T("$PrefixeLangue:$dt") .'</dt><dd>'. propre($dd) .'</dd>'; // propre() encadre dans P... Cette presentation est propre a Associaspip. Habituellement on a : $res .= "<div class='$dt'><strong>". _T("$PrefixeLangue:$dt") ."</strong> $dd</div>";
	}
	if ($ObjetEtendu) {
		$champsExtras = association_trouver_iextras($ObjetEtendu, $id); // on recupere les champs extras crees manuellement (i.e. via l'interface d'edition du prive, pas ceux rajoutes par les plugins !)
		if ( count($champsExtras) ) {
			foreach ($champsExtras as $champExtra) {
				$res .= '<dt>'. $champExtra[0] .'</dt>';
				$res .= '<dd>'. $champExtra[1] .'</dd>';
/*
				if ( strstr($champExtra[1], '<div')===0 ) { // c'est dans un "DIV" superflu
					$res .= substr_replace( substr_replace($chamExtra[1], '</dd>', strrpos($chamExtra[1],'</div>'), 6), 'dd', 1, 3);
				} else {
					$res .= '<dd>'. $champExtra[0] .'</dd>';
				}
*/
				$res .= '<!--dd>'. $champExtra[2] .'</dd-->'; // comparaison de controle
			}
		}
	}
	if ( count($DesLignes) OR $ObjetEtendu )
		$res .= '</dl>';
	return $res;
}

/**
 * Tableau presentant les chiffres de synthese de la statistique descriptive
 *
 * @param string $legende
 *   Titre du tableau
 * @param string $sql_table_asso
 *   La table du plugin (sans prefixe "spip_asso") sur laquelle va porter les statistique.
 * @param array $sql_champs
 *   'chaine_de_langue' (sans prefixe) => "liste, des, champs, sur, laquelle, calculer, les statistiques"
 * @param string $sql_criteres
 *   Critere(s) de selection/restriction SQL des lignes (sinon toutes)
 * @param int $decimales_significatives
 *   Nombre de decimales affichees
 * @param bool $avec_extrema
 *   Indique s'il faut afficher (vrai) ou non (faux) les valeurs extremes.
 *   http://fr.wikipedia.org/wiki/Crit%C3%A8res_de_position#Valeur_maximum_et_valeur_minimum
 *   Par defaut : non, car le tableau deborde de ce petit cadre.
 * @return string $res
 *   Table HTML avec pour chaque ligne ($sql_champs) :
 *   - le nom attribue au groupe de champs
 *   - la moyenne arithmetique <http://fr.wikipedia.org/wiki/Moyenne#Moyenne_arithm.C3.A9tique>
 *   - l'ecart-type <http://fr.wikipedia.org/wiki/Dispersion_statistique#.C3.89cart_type>
 *   - ainsi que les extrema si on le desire
 */
function association_totauxinfos_stats($legende='', $sql_table_asso, $sql_champs, $sql_criteres='1=1',$decimales_significatives=1, $avec_extrema=false)
{
	if (!is_array($sql_champs) || !$sql_table_asso)
		return FALSE;
	$res = '<table width="100%" class="asso_infos">';
	$res .= '<caption>'. _T('asso:totaux_moyens', array('de_par'=>_T("local:$legende"))) .'</caption><thead>';
	$res .= '<tr class="row_first"> <th>&nbsp;</th>';
	$res .= '<th title="'. _T('entete_stats_moy') .'">x&#772</th>'; // X <span style="font-size:75%;">X</span>&#772 <span style="text-decoration:overline;">X</span> X<span style="position:relative; bottom:1.0ex; letter-spacing:-1.2ex; right:1.0ex">&ndash;</span> x<span style="position:relative; bottom:1.0ex; letter-spacing:-1.2ex; right:1.0ex">&macr;</span>
	$res .= '<th title="'. _T('entete_stats_mea') .'">&sigma;</th>'; // σ &sigma; &#963; &#x3C3;
	if ($avec_extrema) {
		$res .= '<th title="'. _T('entete_stats_min') .'">[&lt;</th>';
		$res .= '<th title="'. _T('entete_stats_max') .'">&gt;]</th>';
	}
	$res .= '</tr>';
	$res .= '</thead><tbody>';
	$compteur = 0;
	foreach ($sql_champs as $libelle=>$champs) {
		$stats = sql_fetsel("AVG($champs) AS valMoy, STDDEV($champs) AS ekrTyp, MIN($champs) AS valMin, MAX($champs) AS valMax ", "spip_asso_$sql_table_asso", $sql_criteres);
		$res .= '<tr class="'. ($compteur%2?'row_odd':'row_even') .'">';
		$res .= '<td class"text">'. _T('asso:'.(is_numeric($libelle)?$champs:$libelle)) .'</td>';
		$res .= '<td class="'.($decimales_significatives?'decimal':'integer').'">'. association_formater_nombre($stats['valMoy'],$decimales_significatives) .'</td>';
		$res .= '<td class="'.($decimales_significatives?'decimal':'integer').'">'. association_formater_nombre($stats['ekrTyp'],$decimales_significatives) .'</td>';
		if ($avec_extrema) {
			$res .= '<td class="'.($decimales_significatives?'decimal':'integer').'">'. association_formater_nombre($stats['valMin'],$decimales_significatives) .'</td>';
			$res .= '<td class="'.($decimales_significatives?'decimal':'integer').'">'. association_formater_nombre($stats['valMax'],$decimales_significatives) .'</td>';
		}
		$res .= '</tr>';
		$compteur++;
	}
	$res .= '</tbody></table>';
	return $res;
}

/**
 * Tableau des decomptes statistiques dans le bloc infos
 *
 * @param string $legende
 *   Complement du titre du tableau
 * @param array $table_textes
 *   'classe_unique_css_de_la_ligne' => 'chaine_de_langue' (sans prefixe "asso")
 * @param array $table_nombres
 *   'classe_unique_css_de_la_ligne' => effectif/occurence
 * @param int $decimales_significatives
 *   Nombre de decimales affichees
 * @return string $res
 *   Table HTML de deux colonnes et une ligne par paire libelle/effectif
 *   puis une ligne totalisant les effectifs s'il y a plus d'une ligne.
 *
 * @note
 *   Les classes CSS sont utilisees comme cle des tables parce-qu'il ne doit y en avoir qu'une par ligne.
 */
function association_totauxinfos_effectifs($legende='', $table_textes, $table_nombres, $decimales_significatives=0)
{
	if (!is_array($table_textes) || !is_array($table_nombres) )
		return FALSE;
	$nombre = $nombre_total = 0;
	$res = '<table width="100%" class="asso_infos">';
	$res .= '<caption>'. _T('asso:totaux_nombres', array('de_par'=>_T("local:$legende"))) .'</caption><tbody>';
	foreach ($table_textes as $classe_css=>$libelle) {
		$res .= '<tr class="'.$classe_css.'">';
		$res .= '<td class"text">'._T('asso:'.$libelle).'</td>';
		$res .= '<td class="' .($decimales_significatives?'decimal':'integer') .'">'. association_formater_nombre($table_nombres[$classe_css],$decimales_significatives) .'</td>';
		$nombre_total += $table_nombres[$classe_css];
		$res .= '</tr>';
	}
	$res .= '</tbody>';
	if (count($table_nombres)>1) {
		$res .= '<tfoot>';
		$res .= '<tr><th class="text">'._T('asso:liste_nombre_total').'</th>';
		$res .= '<th class="' .($decimales_significatives?'decimal':'integer') .'">'. association_formater_nombre($nombre_total,$decimales_significatives) .'</th></tr>';
		$res .= '</tfoot>';
	}
	return $res.'</table>';
}

/**
 * Tableau des totaux comptables
 *
 * @param string $legende
 *   Complement du titre du tableau
 * @param float $somme_recettes
 *   Total des recettes
 * @param float $somme_depenses
 *   Total des depenses
 * @return string $res
 *   Table HTML presentant les recettes (sur une ligne) et les depenses (sur une autre ligne), puis le solde (sur une derniere ligne)
 *
 * @attention
 *   Tous ces parametres sont facultatifs, mais un tableau est quand meme genere dans tous les cas !
 */
function association_totauxinfos_montants($legende='', $somme_recettes=0, $somme_depenses=0)
{
	$res = '<table width="100%" class="asso_infos">';
	$res .= '<caption>'. _T('asso:totaux_montants', array('de_par'=>_T("local:$legende"))) .'</caption><tbody>';
#	if ($somme_recettes) {
		$res .= '<tr class="impair">'
		. '<th class="entree">'. _T('asso:bilan_recettes') .'</th>'
		. '<td class="decimal">' .association_formater_prix($somme_recettes). ' </td>'
		. '</tr>';
#	}
#	if ($somme_depenses) {
		$res .= '<tr class="pair">'
		. '<th class="sortie">'. _T('asso:bilan_depenses') .'</th>'
		. '<td class="decimal">'.association_formater_prix($somme_depenses) .'</td>'
		. '</tr>';
#	}
	if ($somme_recettes && $somme_depenses) {
		$solde = $somme_recettes-$somme_depenses;
		$res .= '<tr class="'.($solde>0?'impair':'pair').'">'
		. '<th class="solde">'. _T('asso:bilan_solde') .'</th>'
		. '<td class="decimal">'.association_formater_prix($solde).'</td>'
		. '</tr>';
	}
	return $res.'</tbody></table>';
}

/** @} */


/*****************************************
 * @defgroup association_bloc
 *
 *
** @{ */

/**
 * Boite d'infos sur un objet (colonne gauche)
 *
 *
 * @note
 *   Une certaine similitude avec http://programmer.spip.org/boite_infos :)
  */
function association_bloc_infosgauche($TitreObjet, $NumObjet, $DesLignes=array(), $PrefixeLangue='asso', $ObjetEtendu='')
{
	$res = debut_boite_info(true);
	$res .= association_totauxinfos_intro($TitreObjet, $TitreObjet, $NumObjet, $DesLignes, $PrefixeLangu, $ObjetEtendu);
	$res .= association_date_du_jour();
	$res .= fin_boite_info(true);
	return $res;
}

/**
 * Demande de confirmation dans la suppression d'un objet
 *
 * @param string $type
 *   Type d'objet a supprimer
 * @param int $id
 *   ID de l'objet a supprimer
 * @param string $retour
 *   Nom du fichier d'action vers lequel le formulaire sera redirige, sans le prefixe "supprimer_".
 *   Par defaut, quand rien n'est indique, c'est l'objet suffixe de "s" qui est utilise
 */
function association_bloc_suppression($type, $id, $retour='')
{
	$res = '<p><strong>'. _T('asso:vous_aller_effacer', array('quoi'=>'<i>'._T('asso:objet_num',array('objet'=>$type,'num'=>$id)).'</i>') ) .'</strong></p>';
	$res .= '<p class="boutons"><input type="submit" value="'._T('asso:bouton_confirmer').'" /></p>';
	echo redirige_action_post("supprimer_{$type}s", $id, ($retour?$retour:$type.'s'), '', $res);

}

/**
 * Bloc (tableau en ligne) d'affinage (filtrage) des resultats dans les pages principales... (ici il s'agit de la navigation au sein des donnees tabulaires --un grand listing-- d'un module...)
 *
 * @param array $liste_filtres
 *   Filtres natifs du plugin (identifiant prefixe de "association_selectionner_") :
 *   'identifiant_du_filtre'=>array('liste','des','parametres')
 * @param string $exec
 *   Nom du fichier "exec" auquel le formulaire sera soumis
 * @param string|array $supplements
 *   Utilisation d'autres filtres ou code supplementaire a rajourer a la fin
 *   - Chaine HTML a rajouter
 *   - Tableau des 'identifiant_filtre'=>"code HTML du filtre" a rajouter
 * @param bool $td
 *   Indique s'il faut generer un tableau (vrai, par defaut) ou une liste (faux)
 * @return string $res
 *   Form-HTML des filtres
 * @note
 *   Ici il s'agit d'un vrai formulaire qui influe sur les donnees affichees
 *   et non sur la fonctionnalite en cours (onglet), contrairement aux apparences
 *   (le passage de parametre se faisant par l'URL, celle-ci change)
 *   http://comments.gmane.org/gmane.comp.web.spip.devel/61824
 */
function association_bloc_filtres($liste_filtres, $exec='', $supplements='', $td=TRUE)
{
	$res = '<form method="get" action="'. ($exec?generer_url_ecrire($exec):'') .'">';
	if ($exec)
		$res .= "\n<input type='hidden' name='exec' value='$exec' />";
	$res .= "\n<". ($td?'table width="100%"':'ul') .' class="asso_tablo_filtres">'. ($td?'<tr>':'');
	foreach($liste_filtres as $filtre_selection =>$params) {
		$res .= ($td?'<td':'<li') ." class='filtre_$filtre_selection'>". call_user_func_array("association_selectionner_$filtre_selection", (is_array($params)?$params:array($params)) ) . ($td?'</td>':'</li>');
	}
	if ( is_array($supplements) ) {
		foreach ($supplements as $nom => $supplement) {
			$res .= ($td?'<td':'<li') ." class='filtre_$nom'>$supplement</". ($td?'td>':'li>');
		}
	} else {
		$res .= $supplements;
	}
	$res .= ($td?'<td':'<li') . ' class="boutons"><noscript><input type="submit" value="'. _T('asso:bouton_lister') .'" /></noscript></td>' . ($td?'</td>':'</li>');
	return $res. ($td?'</tr></table':'</ul>') .">\n</form>\n";
}

/**
 * Boite affichant le formulaire pour genere le PDF de la/le liste/tableau
 *
 * @param string $objet
 *   Nom de l'objet : il s'agit imperativement d'un objet du plugin,
 *   correspondant a une table avec le nom de l'objet suffixe de "s" et prefixe
 *   de "spip_asso" (cela exclu quand meme les tables du plugin qui n'ont pas de
 *   "s" final !)
 * @param string $params
 * @param string $prefixeLibelle
 *   Prefixe rajoute au nom du champ pour former la chaine de langue (dont le
 *   nommage est systematise dans "Associaspip")
 * @param array $champsExclus
 *   Liste (seules les valeurs du tableau sont prises en compte) des champs a ne
 *   pas prendre en compte : tous les autres champs de la table sont recuperes
 *   (mais seuls les champs geres par "Associaspip" et "Interface Champs Extras 2"
 *   seront affiches/proposes dans le formulaire, d'ou pas d'exclu par defaut)
 * @param bool $coords
 *   Indique s'il faut (vrai) prendre en compte ou pas (faux) le plugin "Coordonnees"
 * @return string $res
 *   Form HTML complet dans un cadre. Ce formulaire sera traite par l'exec de
 *   l'objet prefixe de "pdf_"
 */
function association_bloc_listepdf($objet, $params=array(), $prefixeLibelle='', $champsExclus=array(), $coords=true)
{
	$res = '';
	if (test_plugin_actif('FPDF')) { // liste
		$res .= debut_cadre_enfonce('',true);
		$res .= '<h3>'. _T('plugins_vue_liste') .'</h3>';
		$res .= '<div class="formulaire_spip formulaire_asso_liste_'.$objet.'s">';
		$champsExtras = association_trouver_iextras("asso_$objet");
		$frm = '<ul><li class="edit_champs">';
		$desc_table = charger_fonction('trouver_table', 'base'); // http://doc.spip.org/@description_table deprecier donc preferer http://programmer.spip.net/trouver_table,620
		$champsPresents = $desc_table("spip_asso_${objet}s");
		foreach ($champsPresents['field'] as $k => $v) { // donner le menu des choix
			if ( !in_array($k, $champsExclus) ) { // affichable/selectionnable (champ ayant un libelle declare et connu)
				$lang_clef = $prefixeLibelle.$k;
				$lang_texte = _T('asso:'.$lang_clef);
				if ( $lang_clef!=str_replace(' ', '_', $lang_texte) ) { // champ natif du plugin
					$frm .= "<div class='choix'><input type='checkbox' name='champs[$k]' id='liste_${objet}s_$k' /><label for='liste_${objet}s_$k'>$lang_texte</label></div>";
				} elseif( array_key_exists($k,$champsExtras) ) { // champs rajoute via cextra
					$frm .= "<div class='choix'><input type='checkbox' name='champs[$k]' id='liste_${objet}s_$k' /><label for='liste_${objet}s_$k'>$champsExtras[$k]</label></div>";
				}
			}
		}
		if ($coords) {
			$frm .= '<div class="choix"><input type="checkbox" name="champs[email]" id="liste_'.$objet.'s_email" /><label for="liste_'.$objet.'_s_email">'. _T('asso:adherent_libelle_email') .'</label></div>'; // on ajoute aussi l'adresse electronique principale (table spip_auteurs ou spip_emails)
			if (test_plugin_actif('COORDONNEES')) {
				$frm .= '<div class="choix"><input type="checkbox" name="champs[adresse]" id="liste_'.$objet.'_s_adresse" /><label for="liste_'.$objet.'_s_adresse">'. _T('coordonnees:adresses') .'</label></div>'; // on ajoute aussi l'adresse postale (table spip_adresses)
				$frm .= '<div class="choix"><input type="checkbox" name="champs[telephone]" id="liste_'.$objet.'_s_telephone" /><label for="liste_'.$objet.'_s_telephone">'. _T('coordonnees:numeros') .'</label></div>'; // on ajoute aussi le numero de telephone (table spip_numeros)
			}
		}
		foreach ($params as $k => $v) { // on fait suivre les autres parametres dont la liste des auteurs a afficher
			$frm .= '<input type="hidden" name="'.$k.'" value="'. htmlspecialchars($v, ENT_QUOTES, $GLOBALS['meta']['charset']) .'" />'; // http://stackoverflow.com/questions/46483/htmlentities-vs-htmlspecialchars
		}
		$frm .= '</li></ul>';
		$frm .= '<p class="boutons"><input type="submit" value="'. _T('asso:bouton_imprimer') .'" /></p>';
		$res .= generer_form_ecrire("pdf_${objet}s", $frm, '', '');
		$res .= '</div>';
		$res .= fin_cadre_enfonce(true);
	}

	return $res;
}

/**
 * Listing sous forme de tableau HTML
 *
 * @param array $entetes
 *   Liste des chaines de langue des libelles d'entete
 * @param ressource $reponse_sql
 *   Resultat du "sql_select"
 * @param array $formats
 *   'nom_ou_alias_du_champ' => array('format', 'parametre1', ...)
 *   Le nom du format est celui de la fonction de formatage du meme nom prefixee de association_formater_
 * @param array $boutons
 *   array('bouton', 'parametre1', ...)
 *   Le nom du type de bouton est celui de la fonction d'action du meme nom prefixee de association_bouton_
 * @param array $extra
 *   Elements annexes optionnel :
 *   - 'table' => 'nom_de_la_table_sql_sans_prefixe',
 *   - 'key' => 'nom_de_la_colonne_cle_primaire',
 *   - 'colors' => array('couleur1', ...)
 * @return string $res
 *   Table-HTML listant les donnees formatees
 */
function association_bloc_listehtml($entetes, $reponse_sql, $formats, $boutons=array(), $extra=array() )
{
	$res =  '<table width="100%" class="asso_tablo'. ($extra['table']?'" id="liste_'.$extra['table']:'').'">';
	$res .= "\n<thead>\n<tr>";
	foreach ($entetes as $entete) {
		$res .= '<th>'. _T($entete) .'</th>';
	}
	if ( count($boutons) ) {
		$res .= '<th colspan="'. count($boutons) .'" class="actions">'. _T('asso:entete_actions') .'</th>';
	}
	$res .= "</tr>\n</thead><tbody>";
	if ( !$reponse_sql && $extra['table'] ) {
		$reponse_sql = sql_select('*', 'spip_'.$extra['table'], $extra['where'], $extra['order']) ;
	}
	$nbr_lignes = 0;
	while ($data = sql_fetch($reponse_sql)) {
		$res .= '<tr'. ($extra['key']?' id="'.$data[$extra['key']].'"':'') .'>';
		foreach ($formats as $champ=>$params) {
			$format = array_shift($params);
			switch ($format) {
				case 'date' :
				case 'heure' :
					$classes = 'date';
					break;
				case 'duree' :
				case 'nombre' :
				case 'prix' :
					$classes = 'decimal';
					break;
				case 'entier' :
					$classes = 'integer';
					$format = 'nombre'; $params = array(0);
					break;
				case 'texte' : // ajouter : propre()
				default :
					$classes = 'text';
					break;
			}
			if ( is_array($extra['colors']) && $nbr_couleurs=count($extra['colors']) ) {
				$nbr_lignes++;
				$classes .= ' '.$extra['colors'][$nbr_lignes%$nbr_couleurs];
			}
			$ok = array_unshift($params,$data[$champ]);
			$res .= '<td class="'.$classes.'">'. call_user_func_array("association_formater_$format", $params) .'</td>';
		}
		foreach ($boutons as $params) {
			$type = array_shift($params);
			foreach ($params as &$param) {
				$param = str_replace('$$', $data[$extra['key']], $param);
			}
			$res .= call_user_func_array("association_bouton_$type", $params);
		}
		$res .= "</tr>\n";
	}
	return $res."</tbody>\n</table>\n";
}

/** @} */


/*****************************************
 * @defgroup sql_asso1
 * Extension de l'API SQL pour Associaspip (operations qui reviennent souvent)
 *
 * @param string $table
 *   Le nom de l'objet : correspond a la table sans prefixe "spip_asso" et sans le "s" final
 * @param int $id
 *   ID de la ligne a recuperer
 * @param bool $pluriel
 *   Indique qu'il s'agit d'une table avec (vrai, par defaut) ou sans (faux) un
 *   suffixe "s". Mis a FALSE, permet de traiter le cas des tables _plan|destination|destination_op !
 *
** @{ */

/**
 * Recupere dans une chaine un champ d'une table spip_asso_XXs pour un enregistrement identifie par son id_XX
 *
 * @param string $champ
 *   Nom du champ recherche
 * @return string
 *   Valeur du champ recherche
 *
 * @note Conversion d'anciennes fonctions :
 * - exercice_intitule($exo) <=> sql_asso1champ('exercice', $exo, 'intitule')
 * - exercice_date_debut($exercice) <=> sql_asso1champ('exercice', $exercice, 'debut')
 * - exercice_date_fin($exercice) <=> sql_asso1champ('exercice', $exercice, 'fin')
 */
function sql_asso1champ($table, $id, $champ, $pluriel=TRUE)
{
	return sql_getfetsel($champ, "spip_asso_$table".($pluriel?'s':''), "id_$table=".intval($id));
}

/**
 * Recupere dans un tableau associatif un enregistrement d'une table spip_asso_XX identifie par son id_XX
 *
 * @return array
 *   Tableau des champs sous forme : 'nom_du_champ'=>"contenu du champ"
 */
function sql_asso1ligne($table, $id, $pluriel=TRUE)
{
	return sql_fetsel('*', "spip_asso_$table".($pluriel?'s':''), "id_$table=".intval($id));
}

/** @} */


/*****************************************
 * @defgroup divers
 * Inclassables
 *
** @{ */

/**
 * Cree le critere SQL Where portant sur le champ "statut_interne"
 *
 * Pour l'instant, appele uniquement dans exec/adherents.php vers la ligne 25
 */
function request_statut_interne()
{
	$statut_interne = _request('statut_interne');
	if (in_array($statut_interne, $GLOBALS['association_liste_des_statuts'] ))
		return 'statut_interne='. sql_quote($statut_interne);
	elseif ($statut_interne=='tous')
		return "statut_interne LIKE '%'";
	else {
		set_request('statut_interne', 'defaut');
		$a = $GLOBALS['association_liste_des_statuts'];
		array_shift($a);
		return sql_in('statut_interne', $a);
	}
}

/**
 * Affichage du message indiquant la date
 *
 * @param bool $heure
 *   Indique s'il faut afficher (vrai) ou pas (faux, par defaut) l'heure.
 * @return string $res
 */
function association_date_du_jour($heure=false)
{
	$ladate = affdate_jourcourt(date('d/m/Y'));
	$hr = ($heure?date('H'):'');
	$mn = ($heure?date('i'):'');
	$res = '<p class="'. ($heure?'datetime':'date');
	$res .= '" title="'. date('Y-m-d') . ($heure?"T$hr:$mn":'');
	$lheure = ($heure? _T('spip:date_fmt_heures_minutes', array('h'=>$hr,'m'=>$mn)) :'');
	$res .= '">'.( $heure ? _T('asso:date_du_jour_heure', array('date'=>$ladate)) : _T('asso:date_du_jour',array('date'=>$ladate,'time'=>$lheure)) ).'</p>';
	return $res;
}

/**
 * Injection de "association.css" dans le "header" de l'espace prive
 * @param string $flux
 * @return string $c
 */
function association_header_prive($flux)
{
	$c = direction_css(find_in_path('association.css'));
	return "$flux\n<link rel='stylesheet' type='text/css' href='$c' />";
}

/**
 * Filtre pour "afficher" ou "cacher" un bloc div
 *
 * Utilise dans le formulaire cvt "editer_asso_comptes.html"
 *
 * @param string $type_operation
 * @param string $list_operation
 * @return string $res
 */
function affichage_div($type_operation, $list_operation)
{
	if(strpos($list_operation, '-')) {
		$operations = explode('-', $list_operation);
		$res = 'cachediv';
		for($i=0;$i<count($operations);$i++) {
			$operation = $GLOBALS['association_metas']['classe_'.$operations[$i]];
			if($type_operation===$operation) {
				$res = '';
				break;
			}
		}
	} else {
		$res = ($type_operation===$GLOBALS['association_metas']['classe_'.$list_operation])?'':'cachediv';
	}
	return $res;
}

/**
 * ??
 *
 * @param string $texte
 * @param string $avant
 * @param string $apres
 * @return string
 */
function encadre($texte,$avant='[',$apres=']')
{
    return ($texte=='')?'':$avant.$texte.$apres;
}

/**
 * Pour construire des menu avec SELECTED
 *
 * @param string $varaut
 *   La valeur de l'option
 * @param string $variable
 *   La variable (passee par valeur) contenant la selection courante
 * @param mixed $option
 *   Quand cette variable est definie, indique de renvoyer un code partiel.
 *   Par defaut c'est le code complet de l' Option HTML qui est retourne
 * @return string
 *   Option de select HTML
 *
 * @note
 *   Utilise dans inc/instituer_statut_interne.php et inc/instituer_adherent.php
 */
function association_mySel($varaut, $variable, $option=NULL)
{
	if ( function_exists('mySel') ) //@ http://doc.spip.org/@mySel
		return mySel($varaut, $variable, $option);
	// la fonction mySel n'existe plus en SPIP 3 donc on la recree
	$res = ' value="'.$varaut.'"'. (($variable==$varaut) ? ' selected="selected"' : '');
	return  (!isset($option) ? $res : "<option$res>$option</option>\n");
}

/**
 * Recupere la liste des champs extras manuellement rajoutes a un objet
 *
 * @param string $ObjetEtendu
 *   Nom de l'objet dont on veut recuperer les champs etendus
 * @param int $id
 *   ID de l'objet dont il veut recuperer aussi les donnees
 *   Par defaut : aucun (i.e. 0)
 * @return array $champsExtrasVoulus
 *   - si on ne veut pas de donnee :
 *     'nom_de_la_colonne'=>"Libelle du champ"
 *   - si on veut aussi les donnees :
 *     'nom_de_la_colonne'=>array( "Libelle du champ", "Donnee formatee", "Donnee brute SQL")
 */
function association_trouver_iextras($ObjetEtendu, $id=0)
{
	$champsExtrasVoulus = array();
	if (test_plugin_actif('IEXTRAS')) { // le plugin "Interfaces pour ChampsExtras2" est installe et active : on peut donc utiliser les methodes/fonctions natives...
		include_spip('inc/iextras'); // charger les fonctions de l'interface/gestionnaire (ce fichier charge les methode du core/API)
		if ($id)
			include_spip('cextras_pipelines'); // pour eviter le "Fatal error : Call to undefined function cextras_enum()" en recuperant un fond utilisant les enum...
		$ChampsExtrasGeres = iextras_get_extras_par_table(); // C'est un tableau des differents "objets etendus" (i.e. tables principaux SPIP sans prefixe et au singulier -- par exemple la table 'spip_asso_membres' correspond a l'objet 'asso_membre') comme cle.
		foreach ($ChampsExtrasGeres[$ObjetEtendu] as $ChampExtraRang => $ChampExtraInfos ) { // Pour chaque objet, le tableau a une entree texte de cle "id_objet" et autant d'entrees tableau de cles numerotees automatiquement (a partir de 0) qu'il y a de champs extras definis.
			if ( is_array($ChampExtraInfos) ) { // Chaque champ extra defini est un tableau avec les cle=>type suivants : "table"=>string, "champ"=>string, "label"=>string, "precisions"=>string, "obligatoire"=>string, "verifier"=>bool, "verifier_options"=>array, "rechercher"=>string, "enum"=>string, "type"=>string, "sql"=>string, "traitements"=>string, "saisie_externe"=>bool, "saisie_parametres"]=>array("explication"=>string, "attention"=>string, "class"=> string, "li_class"]=>string,)
				$label = _TT($ChampExtraInfos['label']); // _TT est defini dans cextras_balises.php
				if ( $id ) {
					$desc_table = charger_fonction('trouver_table', 'base');
					$champs = $desc_table("spip_$ChampExtraInfos[table]s");
					$datum_raw = sql_getfetsel($ChampExtraInfos['champ'], "spip_$ChampExtraInfos[table]s", $champs['key']['PRIMARY KEY'].'='.intval($id) ); // on recupere les donnees... (il faut que la table ait le nom de l'objet et le suffixe "s" :-S)
					$datum_parsed = recuperer_fond('extra-vues/'.$ChampExtraInfos['type'], array (
						'champ_extra' => $ChampExtraInfos['champ'],
						'label_extra' => '', // normalement : _TT($ChampExtraInfos['label']), avec la chaine vide on aura juste "<strong></strong> " a virer...
						'valeur_extra' => $ChampExtraInfos['traitement']?$ChampExtraInfos['traitement']($datum_raw):$datum_raw,
						'enum_extra' => $ChampExtraInfos['enum'], // parametre indispensable pour les champs de type "option"/"radio"/"case" http://forum.spip.net/fr_245942.html#forum245980
					)); // resultat du pipeline "affiche_contenu_objet" altere (prive du libelle du champ qui est envoye separement)
					$champsExtrasVoulus[$ChampExtraInfos['champ']] = array( $label, str_ireplace('<strong></strong>', '', $datum_parsed), $datum_raw );
				} else {
					$champsExtrasVoulus[$ChampExtraInfos['champ']] = $label;
				}
			}
		}
	} else { // le plugin "Interfaces pour ChampsExtras2" n'est pas actif :-S Mais peut-etre a-t-il ete installe ?
		$ChampsExtrasGeres = @unserialize(str_replace('O:10:"ChampExtra"', 'a', $GLOBALS['meta']['iextras'])); // "iextras (interface)" stocke la liste des champs geres dans un meta. Ce meta est un tableau d'objets "ChampExtra" (un par champ extra) manipules par "cextras (core)". On converti chaque objet en tableau
		if ( !is_array($ChampsExtrasGeres) )
			return array(); // fin : ChampsExtras2 non installe ou pas d'objet etendu.
		$TT = function_exists('_T_ou_typo') ? '_T_ou_typo' : '_T' ; // Noter que les <multi>...</multi> et <:xx:> sont aussi traites par propre() et typo() :  http://contrib.spip.net/PointsEntreeIncTexte
		foreach ($ChampsExtrasGeres as $ChampExtra) { // Chaque champ extra defini est un tableau avec les cle=>type suivants (les cles commencant par "_" initialisent des methodes de meme nom sans le prefixe) : "table"=>string, "champ"=>string, "label"=>string, "precisions"=>string, "obligatoire"=>string, "verifier"=>bool, "verifier_options"=>array, "rechercher"=>string, "enum"=>string, "type"=>string, "sql"=>string, "traitements"=>string, "_id"=>string, "_type"=>string, "_objet"=>string, "_table_sql"=>string, "saisie_externe"=>bool, "saisie_parametres"]=>array("explication"=>string, "attention"=>string, "class"=> string, "li_class"]=>string,)
			if ($ChampExtra['table']==$ObjetEtendu) // c'est un champ extra de la 'table' ou du '_type' d'objet qui nous interesse
				$label = $TT($ChampExtra['label']);
				if ( $id ) {
					$datum_raw = sql_getfetsel($ChampExtra['champ'], $ChampExtra[_table_sql], "id__$ChampExtra[_type]=".intval($id) ); // on recupere les donnees... (il faut que l'identifiant soit l'objet prefixe de "id_" :-S)
					switch ( $ChampExtra['type'] ) { // Comme on n'est pas certain de pouvoir trouver "inc/iextra.php" et "inc/cextra.php" on a des chance que foire par moment. On va donc gerer les cas courants manuellement.
						case 'case' : // "<select type='checkbox' .../>..."
						case 'option' : // "<select ...>...</select>"
						case 'radio' : // "<select type='radio' .../>..."
							$valeurs = array();
							$enum = explode("\r\n", $ChampExtra['enum']);
							foreach ($enum as $pair) {
								list($key, $value) = explode(',', $pair, 1);
								$valeurs[$key] = $value;
							}
							$datum_parsed = $ChampExtra['traitement']?$ChampExtra['traitement']($valeurs[$datum_raw]):$valeurs[$datum_raw];
							break;
						case 'oui_non' :
							$datum_parsed = _T("item:$datum_raw");
							break;
//						case 'asso_activite' :
						case 'asso_categorie' :
						case 'asso_compte' :
//						case 'asso_don' :
						case 'asso_exercice' :
						case 'asso_membre' :
						case 'asso_ressource' :
//						case 'asso_vente' :
							$raccourci = substr($ChampExtra['type'], 4); // on vire le prefixe "asso_"
							if ( $ChampExtra['traitement'] )
								$datum_parsed = $ChampExtra['traitement']('[->'.$raccourci.$datum_raw.']');
							else { // il faut une requete de plus
								switch ($raccourci) { // $valeur prend ici le champ SQL contenant la valeur desiree.
//									case 'activite' :
									case 'categorie' :
										$valeur = 'libelle';
										break;
									case 'compte' :
										$valeur = 'justification';
										break;
//									case 'don' :
									case 'exercice' :
										$valeur = 'intitule';
										break;
									case 'membre' :
										$valeur = 'nom_famille'; // il faudrait "concatener" : nom_famille, prenom, sexe ; le tout en fonction des metas... mais http://sql.1keydata.com/fr/sql-concatener.php
										break;
									case 'ressource' :
										$valeur = 'intitule';
										break;
//									case 'vente' :
									default :
										$valeur = 'titre'; // sauf coincidence heurese, on devrait avoir une erreur...
										break;
								}
								$datum_parsed = sql_getfetsel($valeur, "spip_$ChampExtra[type]s", 'id_'.($raccourci=='membre'?'auteur':$raccourci).'='.intval($datum_raw) ); // on recupere la donnee grace a la cle etrangere... (il faut que la table soit suffixee de "s" et que l'identifiant soit l'objet prefixe de "id_" :-S)
							}
							break;
						case 'article' :
						case 'auteur' :
						case 'breve' :
						case 'document' :
						case 'evenement' :
						case 'rubrique' :
						case 'site' :
							if ( $ChampExtra['traitement'] )
								$datum_parsed = $ChampExtra['traitement']('[->'.$ChampExtra['type'].$datum_raw.']');
							else { // il faut une requete de plus
								$datum_parsed = sql_getfetsel($ChampExtra['type']=='auteur'?'nom':'titre', "spip_$ChampExtra[type]s", "id_$ChampExtra[type]=".intval($datum_raw) );
							}
							break;
						case 'auteurs' :
							if ( $ChampExtra['traitement'] ) {
								$valeurs = explode($datum_raw, ',');
								foreach ($valeurs as $rang=>$valeur)
									$valeurs[$rang] = '[->auteur'.$valeurs[$rang].']';
								$datum_parsed = implode(';', $valeurs);
							} else { // il faut une requete de plus
								$valeurs = sql_fetchall('nom', "spip_auteurs", "id_auteur IN (".sql_quote($datum_raw).')' );
								$datum_parsed = implode(';', $valeurs);
							}
							break;
						case 'bloc' : // "<textarea...>...</textarea>"
						case 'ligne' : // "<input type='text' .../>"
						default :
							$ChampExtra['traitement']?$ChampExtra['traitement']($datum_raw):$datum_raw;
					}
					$champsExtrasVoulus[$ChampExtra['champ']] = array( $label, $print, $datum );
				} else {
					$champsExtrasVoulus[$ChampExtra['champ']] = $label;
				}
		}
	}
	return $champsExtrasVoulus;
}

/** @} */


// pour executer les squelettes comportant la balise Meta
include_spip('balise/meta');

// charger les metas donnees
$inc_meta = charger_fonction('meta', 'inc'); // inc_version l'a deja chargee
$inc_meta('association_metas');

// pouvoir utiliser les fonctions de coordonnees comme filtre
if (test_plugin_actif('COORDONNEES')) {
	include_spip('inc/association_coordonnees');
}


?>