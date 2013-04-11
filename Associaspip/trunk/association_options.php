<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;


/*****************************************
 * Initialisations
**/

$GLOBALS['spip_pipeline']['associaspip'] = '';

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
 * recuperer la valeur du tableau par un appel de fonction pour pouvoir le faire depuis un squelette
 * @param enum statut
 */
function association_styles_des_statuts($statut='prospect') {
	return $GLOBALS['association_styles_des_statuts'][$statut];
}

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

/**
 * @var const _ASSOCIASPIP_LIMITE_SOUSPAGE
 *   Nombre de lignes maximales dans les listes de membres, operations comptables, activites...
 */
if (!defined('_ASSOCIASPIP_LIMITE_SOUSPAGE'))
	define('_ASSOCIASPIP_LIMITE_SOUSPAGE', 30);

/**
 * @var const _ASSOCIASPIP_AUJOURDHUI_HORAIRE
 *   Indique s'il faut afficher l'heure en plus de la date
 */
if (!defined('_ASSOCIASPIP_AUJOURDHUI_HORAIRE'))
	define('_ASSOCIASPIP_AUJOURDHUI_HORAIRE', FALSE);


/*****************************************
 * @defgroup association_bouton
 * Affichage HTML : boutons d'action dans les listing
 *
 * @param string $tag
 *   balise-HTML encadrante (doit fonctionner par paire ouvrante et fermante) ;
 *   "TD" par defaut car dans Associaspip un tel bouton est genere dans une cellule de tableau
 * @return string $res
 *   code HTML du bouton
** @{ */

/**
 * boutons act[ion|er] (si page de script indiquee) generique
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
 *
 * @todo voir s'il est possible d'utiliser plutot la fonction bouton_action($libelle, $url, $class="", $confirm="", $title="") definie dans /ecrire/inc/filtres.php
 */
function association_bouton_act($texte, $image, $script='', $exec_args='', $img_attrs='', $tag='td') {
	$chemin = _DIR_PLUGIN_ASSOCIATION_ICONES.$image; // icone Associaspip
	if ( !file_exists($chemin) )
		$chemin = find_in_path($image); // icone alternative
	if ($texte) {
		$texte = association_langue($texte);
		$texte = "\nalt=\"$texte\" title=\"$texte\"";
	}
	$res = "<img src=\"$chemin\"$texte $img_attrs />";
	if ($script) {
		$h = generer_url_ecrire($script, $exec_args);
		$res = "<a href='$h'>$res</a>";
	}
	return $tag ? "<$tag class='action'>$res</$tag>" : $res;
}

/**
 * @name association_bouton_<quoi>
 * cas specifique de :
 *
 * @param string $objet
 *   nom de l'objet pour lequel on genere le bouton : c'est ce nom, prefixe
 *   d'un <mot> selon une convention, qui correspond au fichier d'execution
 *   appele par le lien du bouton
 * @param int|string $args
 *   identifiant de l'objet (le nom du parametre est alors "id")
 *   ou chaine des parametres passes a l'URL
 */
//@{

/**
 * bouton affich[age|er] v[ue|oir] visualis[ation|er]
 * bouton list[ing|er] (car tres souvent on va mettre l'element en evidence au sein d'une liste)
 */
function association_bouton_list($objet, $args='', $tag='td') {
	switch ($objet) { // infobulles au cas par cas
		case 'adherent' :
			$titre = 'adherent_label_voir_membre';
			break;
		case 'comptes' :
			$titre = 'adherent_label_voir_operation';
			break;
		case 'inscrits_activite' :
			$titre = 'activite_bouton_voir_liste_inscriptions';
			break;
		case 'membres_groupe' :
			$titre = 'voir_membres_groupe';
			break;
		case 'prets' :
			$titre = 'prets_nav_gerer';
			break;
		default :
			$titre = 'bouton_voir';
			break;
	}
	$res = association_bouton_act($titre, 'voir-12.png', "$objet", is_numeric($args)?"id=$args":$args, 'width="12" height="12"', $tag);
	return $res;
}

/**
 * bouton edit[ion|er] (modifi[cation|er])
 */
function association_bouton_edit($objet, $args='', $tag='td') {
	$res = association_bouton_act('bouton_modifier', 'edit-12.gif', "edit_$objet", is_numeric($args)?"id_$objet=$args":$args, 'width="12" height="12"', $tag);
	return $res;
}

/**
 * bouton suppr[ession|imer] (efface[ment|r])
 */
function association_bouton_suppr($objet, $args='', $tag='td') {
	$res = association_bouton_act('bouton_supprimer', 'suppr-12.gif', "suppr_$objet", is_numeric($args)?"id_$objet=$args":$args, 'width="12" height="12" class="danger"', $tag);
	return $res;
}

/**
 * bouton paye[ment|r] cotis[ation|er], contribu[tion financiere|er financierement]
 */
function association_bouton_paye($objet, $args='', $tag='td') {
	switch ($objet) { // infobulles au cas par cas
		case 'ajout_cotisation' :
			$titre = 'adherent_label_ajouter_cotisation';
			break;
		case 'edit_activite' :
			$titre = 'activite_bouton_maj_inscription';
			break;
		default :
			$titre = ' '; // ??
			break;
	}
	$res = association_bouton_act($titre, 'cotis-12.gif', "$objet", is_numeric($args)?"id=$args":$args, 'width="12" height="12"', $tag); // "ajout_$objet" jusqu'a ce que ajout_participation fusionne avec edit_activite
	return $res;
}

//@}

/**
 * bouton coch[age de|er une] case
 *
 * Ce n'est pas un bouton a proprement parler mais il est dans la zone des
 * boutons et sert a transmettre une liste de valeurs au parametre d'un bouton
 * normalement situe au bas du tableau..
 *
 * @param string $champ
 *   Nom du champ pour lequel le bouton est genere
 *   (mettre une chaine vide pour generer un bouton desactive)
 * @param string $valeur
 *   Valeur a transmettre pour ce champ
 * @param string $plus
 *   Texte supplementaire rajoute
 *   (utile pour placer d'autres boutons caches dans la cellule)
 */
function association_bouton_coch($champ, $valeur='', $plus='', $tag='td') {
	$res = ($tag?"<$tag class='action'>":'');
	$res .= $plus.'<input type="checkbox" ';
	if ( $champ )
		$res .= 'name="'.$champ.'[]" value="'.$valeur.'"';
	else
		$res .= 'disabled="disabled"';
	$res .= ' />'. ($tag?"</$tag>":'');
	return $res;
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
 * Affichage d'un nom complet (de membre) suivant la configuration du plugin (i.e. champs geres ou non)
 *
 * @param string $civilite
 *   Civilite (M./Mme/Mle) ou titre (Dr./Pr./Mgr/Gle/etc.)
 * @param string $prenom
 *   Prenom(s)
 * @param string $nom
 *   Nom de famille
 * @param string $html_span
 *   Indique la balise-HTML (paire ouvrante/fermante) servant a grouper le
 *   resultat. Sa presence (rien par defaut) indique d'appliquer le micro-
 *   formatage du groupe.
 * @param string $ps
 *   Chaine a rajouter entre le nom et le prenom (souvent ", " pour bien les
 *   distinguer/separer). Dans ce cas (au moins un caractere, meme espace) le
 *   formatage est a la francaise/chinoise (cas aussi dans de nombreux pays
 *   francophones) : le prenom est place apres le nom ! Dans le cas contraire,
 *   il ("prae nomen" en latin, et "first name" en anglais) il "pre"cede le nom
 *   (de famille/geniteur/origine...)
 * @return string $res
 *   Chaine du nom complet du membre, micro-formate ou non.
 */
function association_formater_nom($civilite, $prenom, $nom, $html_span='', $ps='') {
	$res = '';
	if ($html_span) {
		$res = '<'.$html_span.' class="'. (($civilite || $prenom)?'n':'fn') .'">';
	}
	if ($GLOBALS['association_metas']['civilite'] && $civilite) {
		$res .= ($html_span?'<span class="honorific-prefix">':'') .$civilite. ($html_span?'</span>':'') .' ';
	}
	if ($GLOBALS['association_metas']['prenom'] && $prenom) {
		$nom1 = ($html_span?'<span class="given-name">':'') .$prenom. ($html_span?'</span>':'');
	}
	$nom2 = ($html_span?'<span class="family-name">':'') .$nom. ($html_span?'</span>':'');
	$res .= ($ps?"$nom2$ps$nom1":"$nom1 $nom2");
	return $res. ($html_span?"</$html_span>":'');
}

/**
 *  Affichage de date localisee et micro-formatee
 *
 * @param string $iso_date
 *   Date au format ISO-8601
 *   http://fr.wikipedia.org/wiki/ISO_8601#Date_et_heure
 * @param string $css_class
 *   Classe(s) CSS (separees par un espace) a rajouter
 *   Normalement : dtstart|dtend
 * @param string $format
 *   Indique le formatage de date souhaite (cf filtre affdate_<format>)
 * @param string $html_abbr
 *   Balise-HTML (paire ouvrante/fermante) encadrante
 *   Par defaut : "abbr"
 *   http://www.alsacreations.com/tuto/lire/1222-microformats-design-patterns.html#datetime-design-pattern
 *   Desactiver (chaine vide) pour ne pas micro-formater
 * @return string $res
 *   Date formatee
 */
function association_formater_date($iso_date, $css_class='', $format='entier', $html_abbr='auto') {
	if ( !$iso_date || substr_count($iso_date, '0000-00-00') ) // date indeterminee
		return '';
	$res = '';
	if ( $html_abbr=='auto' )
		$html_abbr = (@$GLOBALS['meta']['html5']?'time':'abbr');
	if ( $html_abbr )
		$res = "<$html_abbr ". ($css_class?"class='$css_class' ":'') . ($html_abbr=='time'?'datetime':'title'). "='$iso_date'>";
	$res .= affdate_base($iso_date, $format?$format:'entier'); // on fait appel a la fonction centrale des filtres SPIP... comme ca c'est traduit et formate dans les langues supportees ! si on prefere les mois en chiffres et non en lettre, y a qu'a changer les chaines de langue date_mois_XX
	return $res. ($html_abbr?"</$html_abbr>":'');
}

/**
 * Affichage de nombre localise
 *
 * @param float $nombre
 *   Valeur numerique au format informatique standard
 * @param int $decimales
 *   Nombre de decimales affichees.
 *   Par defaut : 2
 * @param string $css_class
 *   Classe(s) CSS (separees par un espace) a rajouter
 * @param string $html_abbr
 *   Balise-HTML (paire ouvrante/fermante) encadrante
 * @return string $res
 *   Nombre formatee
 *
 * @note Perfectible... Avis aux contributeurs motives...
 */
function association_formater_nombre($nombre, $decimales=2, $css_class='', $html_abbr='') {
	if ( $html_abbr )
		$res = "<$html_abbr ". ($css_class?"class='$css_class' ":'') ."title='$iso_date'>";
	else
		$res = '';
	setlocale(LC_NUMERIC, utiliser_langue_visiteur() );
	$locale = localeconv();
    $res .= number_format(floatval($nombre), $decimales, $locale['decimal_point'], $locale['thousands_sep']);
	return $res. ($html_abbr?"</$html_abbr>":'');
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
 * @param string $html_abbr
 *   Balise-HTML (paire ouvrante/fermante) encadrante
 *   Par defaut : "abbr" avec la classe "duration"
 *   http://www.alsacreations.com/tuto/lire/1222-microformats-design-patterns.html#abbr-design-pattern
 *   Desactiver (chaine vide) pour ne pas micro-formater
 * @return string $res
 *   Duree formatee
 *
 * @note les cas de minutes/secondes doivent etre specifie comme des heures au format ISO...
 */
function association_formater_duree($nombre, $unite='', $html_abbr='abbr') {
	$frmt_h = ''; // format human-readable
	$frmt_m = 'P'; // format machine-parsable
	if ( is_numeric($unite) ) { // inversion...
		$pivot = $unite;
		$unite = $nombre;
		$nombre = $pivot;
	}
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
	return $html_abbr ? "<$html_abbr class='duration' title='". htmlspecialchars($frmt_m, ENT_QUOTES, $GLOBALS['meta']['charset']). "'>$frmt_h</$html_abbr>" : $frmt_h;
}

/**
 * Affichage de prix (montant et devise) localisee et micro-formatee
 *
 * @param float|int $montant
 *   Montant (valeur chiffree) correspondant au prix
 * @param string $type
 *   Nature du montant : non visible, est utilise comme classe semantique complementaire
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
 *   Par defaut : "abbr" avec les classes "amount" et "currency"
 * @return string $res
 *   Duree formatee
 *
 * @note On n'utilise pas la fontcion PHP money_format() --qui ne fonctionne pas
 * sous Windows-- car on veut micro-formater avec une devise fixee par la
 * configuration (en fait les chaines de langue) du plugin
 */
function association_formater_prix($montant, $type='', $devise_code='', $devise_symb='', $html_abbr='abbr', $html_span='span') {
	$res = '';
	if ($html_span)
		$res .= "<$html_span class='money price $type'>"; // la reference est "price" <http://microformats.org/wiki/hproduct> (reconnu par les moteurs de recherche), mais "money" <http://microformats.org/wiki/currency-brainstorming> est d'usage courant aussi
	$montant = ($html_abbr?"<$html_abbr class='amount' title='$montant'>":'') . association_formater_nombre($montant) . ($html_abbr?"</$html_abbr>":'');
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
	$devise = ($html_abbr ? "<$html_abbr class='currency' title='". htmlspecialchars($devise_code, ENT_QUOTES, $GLOBALS['meta']['charset']) .'\'>' : '') . $devise_symb . ($html_abbr?"</$html_abbr>" :'');
	$res .= _T('asso:devise_montant', array('montant'=>$montant, 'devise'=>$devise) );
	return $html_span ? "$res</$html_span>" : $res;
}

/**
 * Affichage d'un texte formate
 *
 * @param string $texte
 *   Le texte brut initial
 * @param string $filtre
 *   Filtre SPIP a appliquer au texte.
 *   Pour les filtres avec parametre, il faut utiliser une liste debutant par le
 *   nom du filtre suivi des parametres.
 * @param string $html_span
 *   Balise-HTML (paire ouvrante/fermante) encadrante
 * @param string $css_class
 *   Classe(s) CSS (separees par un espace) a rajouter
 *   N'est (ne sont) prise(nt) en compte que si un tag-HTML est specifie
 * @return string $res
 *   Texte formate
 * @note
 *   http://spipistrelle.clinamen.org/spip.php?article16
 */
function association_formater_texte($texte, $filtre='', $css_class='', $html_span='' ) {
	$res = '';
	if ( $css_class && !$html_span )
		$html_span = 'span';
	if ( $html_span )
		$res = "<$html_span". ($css_class?" class='$css_class' ":'') .'>';
	include_spip('inc/texte'); // pour nettoyer_raccourci_typo
	if ( is_array($filtre) ) {
		$params = $filtre;
		$filtre = array_shift($params);
	} else {
		$params = array();
	}
	$ok = array_unshift($params, $texte);
	$res .= $filtre?call_user_func_array($filtre, $params):$texte;
	return $res. ($html_span?"</$html_span>":'');
}

/**
 * Affiche une puce de couleur carree nommee puce-*.gif
 *
 * @param string $statut
 *   Valeur du "statut" a iconifier
 * @param string|array $icone
 *   Nom (couleur) de la puce parmis celles disponibles : orange, rouge, vert, poubelle...
 *   Tableau associant chaque statut a un nom de puce...
 * @param string $acote
 *   Legende placee a cote de l'icone
 * @return string
 *   Dessin et texte
 */
function association_formater_puce($statut, $icone,  $acote='', $img_attrs='') {
	if ( is_array($icone) )
		$icone = $icone[$statut];
	if (!$statut) $img_attrs .= " alt=' '";
	return association_bouton_act($statut, 'puce-'.$icone.'.gif', '', '', $img_attrs, '').' '. association_langue($acote) ; // c'est comme un bouton... sans action/lien...
}

/**
 *  Affichage de l'horodatage localisee et micro-formatee
 *
 * @param string $iso_date
 *   Date au format ISO-8601
 *   http://fr.wikipedia.org/wiki/ISO_8601#Date_et_heure
 * @param string $css_class
 *   Classe(s) CSS (separees par un espace) a rajouter
 *   Normalement : dtstart|dtend
 * @param string $html_abbr
 *   Balise-HTML (paire ouvrante/fermante) encadrante
 *   Par defaut : "abbr"
 *   http://www.alsacreations.com/tuto/lire/1222-microformats-design-patterns.html#datetime-design-pattern
 *   Desactiver (chaine vide) pour ne pas micro-formater
 * @return string $res
 *   Date formatee
 */
function association_formater_heure($iso_date, $css_class='', $html_abbr='auto') {
	$res = '';
	if ( $html_abbr=='auto' )
		$html_abbr = ($GLOBAL['meta']['html5']?'time':'abbr');
	if ( $html_abbr )
		$res = "<$html_abbr ". ($css_class?"class='$css_class' ":'') . ($html_abbr=='time'?'datetime':'title'). "='$iso_date'>";
	$res .= affdate_heure($iso_date); // on fait appel a la fonction centrale des filtres SPIP... comme ca c'est traduit et formate dans les langues supportees ! si on prefere les mois en chiffres et non en lettre, y a qu'a changer les chaines de langue date_mois_XX
	return $res . ($html_abbr?"</$html_abbr>":'');
}

/**
 * Affichage d'un caracteristique ou d'un code
 *
 * @param string $code
 *   La valeur de la caracteristique
 * @param string|array $type
 *   Le type de caracteristique, non affiche (utilise comme classe CSS)
 *   L'affichage a effectuer indice par le type de caracteristique
 * @param bool $p_v
 *   Indique s'il s'agit d'un numero de serie (faux) ou d'un autre genre de parametre (vrai)
 * @param string $html_span
 *   Balise-HTML (paire ouvrante/fermante) encadrante
 *   Par defaut "span"
 * @return string $res
 *   Texte formate
 * @note
 *   http://microformats.org/wiki/hproduct-proposal#Schema
 */
function association_formater_code($code, $type='x-associaspip', $p_v=TRUE, $html_span='span' ) {
	$res = $html_span  ? ("<$html_span class='". ($p_v?'p-v':'identifier') ."'>") : '';
	if ( is_string($type) ) { // label implied
		$res .= "<span class='". ($p_v?'property':'type') ."$type' title='$type'>$code</span>";
	} else { // label explicit
		$res .= "<abbr class='". ($p_v?'property':'type') ."' title='$type'>$type</abbr> <span class='value'>$code</span>";
	}
	return $res. ($html_span?"</$html_span>":'');
}

/**
 * Afficher le nom ou le lien sur un objet a partir de son id
 *
 * @param int $id
 *   Valeur de l'identifiant pour faire le lien ou recuperer le nom
 * @param string|array $nom
 *   Le pseudonyme a afficher (directement donnee) ; ou
 *   La liste de : la table dans laquelle la recuperer, le champ le contenant
 *   (par defaut "titre") et la cle primaire a utiliser pour la requete (par
 *   defaut "id_auteur"). Le cas particulier de la liste vide permet de generer
 *   le nom complet du membre ayant l'ID fourni.
 * @param string $lien
 *   Nom de l'objet pour lequel on genere le lien sous forme de raccourci SPIP
 * @param string $html_span
 *   Balise HTML encadrante (paire ouvrante/fermante) a utiliser pour encadrer
 *   l'ensemble. Par defaut : "span". Il faut mettre un chaine vide pour ne pas
 *   microformater...
 * @return string $res
 *   Code HTML correspondant
 *
 * @note : etait association_calculer_lien_nomid
 * En fait c'est pour les modules dons/ventes/activites/prets ou l'acteur (donateur/acheteur/inscrit/emprunteur)
 * peut etre un membre/auteur (son id_acteur est alors renseigne) mais pas
 * forcement son nom (qui peut etre different)
 * ou peut etre une personne exterieure a l'association (on a juste le nom alors
 * obligatoire)
 */
function association_formater_idnom($id, $nom='', $lien='', $html_span='span') {
	$res = '';
	if ( is_array($nom) ) { // requeter le nom... (rajoute de la charge sur la base de donnees)
		$table = ($nom[0] ? $nom[0] : ($nom['table'] ? $nom['table'] : ($nom['from'] ? $nom['from'] : ($nom['tables']?$nom['tables']:'spip_asso_membres') ) ) ) ; // on recupere le nom de la table a interroger
		if ( $table=='spip_asso_membres' || $table=='asso_membres' ) { // cas special d'un membre
			$membre = sql_fetsel('*', 'spip_asso_membres', "id_auteur=$id");
			$res = association_formater_nom($membre['sexe'], $membre['prenom'], $membre['nom_famille'], $html_span);
		} else { // cas general
			$champ = ($nom[1] ? $nom[1] : ($nom['field'] ? $nom['field'] : ($nom['select'] ? $nom['select'] : (($table=='spip_auteurs' || $table=='auteurs')?'nom':'titre') ) ) ) ; // on recupere le nom du champ contenant le nom recherche
			$clef = ($nom[2] ? $nom[2] : ($nom['pk'] ? $nom['pk'] : ($nom['id'] ? $nom['id'] : 'id_auteur' ) ) ) ; // on recupere le nom du champ contenant le nom recherche
			$nom = sql_getfetsel($champ, $table, "$clef=".sql_quote($id) );
			if ( $nom )
				$res = ($html_span?"<$html_span class='n'>":'') . $nom . ($html_span?"</$html_span>":'');
			elseif ( $lien=='membre')
				$res = association_formater_idnom($id, array(), '', $html_span); // on doit pouvoir faire mieux je pense
			else
				$res = _T('asso:objet_num', array('objet'=>_T("perso:$lien"), 'num'=>$id) );
		}
	} elseif ( $nom ) { // utiliser nom...
		$res = ($html_span?"<$html_span class='n'>":'') .$nom. ($html_span?"</$html_span>":'');
	}
	if ( $lien ) {
		$res = propre('['.$res."->$lien$id]");
	}
	return $res;
}

/**
 * Affichage micro-formate de liste de numeros de telephones
 *
 * @param array $id_objets
 *   Liste des (listes de) numeros de telephones a formater, ou
 *   liste des ID dont on doit formater les numeros (voir parametre suivant)
 * @param string $objet
 *   Indique le type d'objet dont les ID sont passes afin de recuperer les
 *   numeros associes a ces objets. Quand rien n'est indique c'est que c'est la
 *   liste de liste des numeros qui est directement fournie.
 *   (ceci est prevu pour etendre facilement l'usage de la fonction si necessaire,
 *   vaut "auteur" par defaut)
 * @param string $html_span
 *   Balise-HTML (paire ouvrante/fermante) encadrant l'ensemble
 *   Par defaut : "div" avec la classe "tel" (ne rien mettre pour desactiver)
 * @param string $href_pre
 *   Protocole a utiliser pour faire un lien cliquable sur le numero
 *   Par defaut : "tel:" comme preconise par la RFC 3966
 *   Ne rien mettre pour desactiver la creation de lien.
 * @param string $href_post
 *   Complement du precedant dans le cas de certains protocoles
 *   Par exemple, avec $href_pre='sip:' on a $href_post='@ip.ou.hote.passerelle;user=phone'
 * @param string $sep
 * @return array $telephones_string
 *   Liste des numeros formates en HTML.
 *   Cette fonction s'occupe surtout du balisage (micro-formate) ;
 *   la localisation "visuelle" du numero est confie au modele coordonnees_telephone
 * @note
 *   http://microformats.org/wiki/hcard-fr#adr_tel_email_types
 *   http://microformats.org/wiki/hcard-fr#valeurs_sous-propri.C3.A9t.C3.A9_type
 *   http://microformats.org/wiki/hcard-fr#Lisible_par_Humain_vs._Machine
 *   http://microformats.org/wiki/vcard-suggestions#TEL_Type_Definition
 */
function association_formater_telephones($id_objets, $objet='auteur', $html_span='div', $href_pre='tel:', $href_post='', $sep=' ') {
	$id_objets = association_recuperer_liste($id_objets, FALSE);
	if ($objet) { // ancien comportement : ce sont les id_auteur qui sont transmis
		$telephones_array = array(); // initialisation du tableau des donnees
		$trouver_table = charger_fonction('trouver_table', 'base');
		if ( $trouver_table('numeros') && $trouver_table('numeros_liens') ) { // le plugin "Coordonnees" est installe (active ou pas)
			foreach ($id_objets as $id_objet) { // prepare la structure du tableau renvoye
				$telephones_array[$id_objet] = array();
			}
			$query = sql_select('l.id_objet, l.type, n.*','spip_numeros AS n INNER JOIN spip_numeros_liens AS l ON l.id_numero=n.id_numero', sql_in('l.id_objet', $id_objets).' AND l.objet='. sql_quote($objet) );
			while ($data = sql_fetch($query)) { // on recupere tous les numeros dans un tableau de tableaux
				$telephones_array[$data['id_objet']][] = $data;
			}
			sql_free($query);
		}
	} else { // on a deja la liste des numeros !
		$telephones_array = $id_objets;
	}
	$telephones_string = array();  // initialisation du tableau renvoye
	foreach ($telephones_array as $id_objet => $telephones) { // on cree la liste de chaines de numeros
		foreach ($telephones as $telephone) { // formater chaque numero
			if ( !is_array($telephone) ) {
				$telephone['numero'] = $telephone;
			}
			if ($html_span) { // formatage HTML avec microformat
				$telephones_string[$id_objet] .=  "<$html_span class='tel'>". appliquer_filtre($telephone['type'], 'logo_type_tel');
				$tel_num = ($telephone['pays']?"+$telephone[pays]$telephone[region]$telephone[numero]":$telephone['numero']);
				$telephones_string[$id_objet] .=  ($href_pre?("<a title='". _T('asso:composer_le') ." $tel_num' href='$href_pre"):"<abbr title='"). preg_replace('/[^\d+]/', '', $tel_num) . ($href_pre?$href_post:'') ."' class='value'>";
				unset($telephone['type']); // ne devrait plus etre traite par le modele
				unset($telephone['id_objet']); // ne devrait plus etre traite par le modele
				unset($telephone['id_numero']); // ne devrait pas etre utilise par le modele
			}
			$telephone['_spc'] = $space; // parametre supplementaire pour le modele
			$telephones_string[$id_objet] .=  recuperer_fond("modeles/coordonnees_telephone", $telephone) .($html_span?('</'.($href_pre?'a':'abbr')."></$html_span>\n"):'') .$sep;
		}
	}
	return $telephones_string;
}

/**
 * Affichage micro-formate de liste d'adresses postales
 *
 * @param array $id_objets
 *   Liste des (listes de) numeros d'adresses a formater, ou
 *   liste des ID dont on doit formater les numeros (voir parametre suivant)
 * @param string $objet
 *   Indique le type d'objet dont les ID sont passes afin de recuperer les
 *   numeros associes a ces objets. Quand rien n'est indique c'est que c'est la
 *   liste de liste des numeros qui est directement fournie.
 *   (ceci est prevu pour etendre facilement l'usage de la fonction si necessaire,
 *   vaut "auteur" par defaut)
 * @param string $html_span
 *   Balise-HTML (paire ouvrante/fermante) encadrant l'ensemble
 *   Par defaut : "div" avec la classe "adr" (ne rien mettre pour desactiver)
 * @param string $newline
 *   Separateur de ligne utilise par le modele de presentation localisee (cf. note)
 *   Par defaut : "<br />" puisque le formatage est en HTML.
 * @param string $space
 *   Espaceur de blocs utilise par le modele de presentation localisee (cf. note)
 *   Par defaut : "&nbsp;" puisque le formatage est en HTML
 * @return array $adresses_string
 *   Liste des adresses formates en HTML.
 *   Cette fonction s'occupe surtout du balisage (micro-formate) ;
 *   la disposition des elements d'adresse est confie. au modele coordonnees_adresse
 * @note
 *   http://microformats.org/wiki/hcard-fr#adr_tel_email_types
 *   http://microformats.org/wiki/hcard-fr#valeurs_sous-propri.C3.A9t.C3.A9_type
 *   http://microformats.org/wiki/hcard-fr#Lisible_par_Humain_vs._Machine
 *   http://microformats.org/wiki/vcard-suggestions#TEL_Type_Definition
 *   http://microformats.org/wiki/adr
 *   http://microformats.org/wiki/adr-cheatsheet
 */
function association_formater_adresses($id_objets, $objet='auteur', $html_span='div', $newline='<br />', $espace='&nbsp;') {
	$id_objets = association_recuperer_liste($id_objets, FALSE);
	if ($objet) { // ancien comportement : ce sont les id_auteur qui sont transmis
		$adresses_array = array(); // initialisation du tableau des donnees
		$trouver_table = charger_fonction('trouver_table', 'base');
		if ( $trouver_table('adresses') && $trouver_table('adresses_liens') ) { // le plugin "Coordonnees" est installe (active ou pas)
			foreach ($id_objets as $id_objet) { // prepare la structure du tableau renvoye
				$adresses_array[$id_objet] = array();
			}
			$query = sql_select("l.id_objet, l.type, a.*, a.pays AS code_pays, '' AS nom_pays ",'spip_adresses AS a INNER JOIN spip_adresses_liens AS l ON l.id_adresse=a.id_adresse', sql_in('l.id_objet', $id_objets)." AND l.objet='$objet' ");
			while ($data = sql_fetch($query)) { // on recupere tous les numeros dans un tableau de tableaux
				$adresses_array[$data['id_objet']][] = $data;
			}
			sql_free($query);
		} elseif ( $trouver_table('gis') && $trouver_table('gis_liens') ) { // le plugin "GIS" est installe (active ou pas)
			foreach ($id_objets as $id_objet) { // prepare la structure du tableau renvoye
				$adresses_array[$id_objet] = array();
			}
			$query = sql_select("l.id_objet, l.type, a.*, a.pays AS nom_pays, adresse AS voie ",'spip_adresses AS a INNER JOIN spip_adresses_liens AS l ON l.id_gis=a.id_gis', sql_in('l.id_objet', $id_objets)." AND l.objet='$objet' ");
			while ($data = sql_fetch($query)) { // on recupere tous les numeros dans un tableau de tableaux
				$adresses_array[$data['id_objet']][] = $data;
			}
			sql_free($query);
		}
	} else { // on a deja la liste des adresses !
		$adresses_array = $id_objets;
	}
	$adresses_string = array();  // initialisation du tableau renvoye
	foreach ($adresses_array as $id_objet => $adresses) {  // on cree la liste de chaines d'adresses
		foreach ($adresses as $adresse) { // chaque adresse est forcement un tableau bien qu'on le verifie pas
			if ($html_span) { // formatage HTML avec microformat
				$adresses_string[$id_objet] .=  "<$html_span class='adr'>". appliquer_filtre($adresse['type'], 'logo_type_adr');
				if ($adresse['voie'])
					$adresse['voie'] = "<span class='street-address'>$adresse[voie]</span>";
				if ($adresse['ville'])
					$adresse['ville'] = "<span class='locality'>$adresse[ville]</span>";
				if ($adresse['complement'])
					$adresse['complement'] = "<span class='extended-address'>$adresse[complement]</span>";
				if ($adresse['region'])
					$adresse['region'] = "<span class='region'>$adresse[region]</span>";
				if ($adresse['code_postal'])
					$adresse['code_postal'] = "<span class='postal-code'>$adresse[code_postal]</span>";
				if ($adresse['boite_postale'])
					$adresse['boite_postale'] = "<span class='post-office-box'>$adresse[boite_postale]</span>";
				if ( !$adresse['nom_pays'] && $adresse['code_pays']!=$GLOBALS['association_metas']['pays'] )
					if ($adresse['code_pays']) {
						if ( is_numeric($adresse['code_pays']) && $trouver_table('geo_pays' ) ) // tenter de recuperer le nom avec le plugin "Geographie"
							$adresse['nom_pays'] = sql_getfetsel('nom', 'spip_geo_pays', "id_pays=$adresse[code_pays]");
						elseif ( $trouver_table('geo_pays') ) // tenter de recuperer le nom avec le plugin "Pays"
							$adresse['nom_pays'] = sql_getfetsel('nom', 'spip_pays', 'code='.sql_quote($adresse['code_pays']) );
						else // un code langue ?
							$adresse['nom_pays'] = _T($adresse['code_pays']);
					}
				if ($adresse['nom_pays'])
					$adresse['pays'] = "<span class='country-name'>$adresse[nom_pays]</span>";
				unset($adresse['type']); // ne devrait plus etre traite par le modele
				unset($adresse['id_objet']); // ne devrait plus etre traite par le modele
				unset($adresse['id_adresse']); // ne devrait pas etre utilise par le modele
			}
			$adresse['_nl'] = $newline; // parametre supplementaire pour le modele
			$adresse['_spc'] = $space; // parametre supplementaire pour le modele
			$adresses_string[$id_objet] .=  recuperer_fond('modeles/coordonnees_adresse', $adresse) .($html_span?"</$html_span>\n":'');
		}
	}
	return $adresses_string;
}

/**
 * Affichage micro-formate de liste d'adresses de courriel
 *
 * @param array $id_objets
 *   Liste des (listes de) emails/mels a formater, ou
 *   liste des ID dont on doit formater les mails (voir parametre suivant)
 * @param string $objet
 *   Indique le type d'objet dont les ID sont passes afin de recuperer les
 *   numeros associes a ces objets. Quand rien n'est indique c'est que c'est la
 *   liste de liste des numeros qui est directement fournie.
 *   (ceci est prevu pour etendre facilement l'usage de la fonction si necessaire,
 *   vaut "auteur" par defaut)
 * @param string $html_span
 *   Balise-HTML (paire ouvrante/fermante) encadrant l'ensemble avec lien cliquable
 *   Par defaut : "div" avec la classe "email" (ne rien mettre pour desactiver)
 * @return array $emails_string
 *   Liste des courriels formates en HTML.
 * @note
 *   http://microformats.org/wiki/hcard-fr#adr_tel_email_types
 *   http://microformats.org/wiki/hcard-fr#valeurs_sous-propri.C3.A9t.C3.A9_type
 *   http://microformats.org/wiki/hcard-fr#Lisible_par_Humain_vs._Machine
 *   http://microformats.org/wiki/vcard-suggestions#EMAL_Type_Definition
 *   http://en.wikipedia.org/wiki/Email#URI_scheme_mailto:
 *   http://www.remote.org/jochen/mail/info/address.html
 *   http://en.wikipedia.org/wiki/X.400#Addressing
 */
function association_formater_emails($id_objets, $objet='auteur', $html_span='div', $sep=' ') {
	$id_objets = association_recuperer_liste($id_objets, FALSE);
	if ($objet) { // ancien comportement : ce sont les id_objet qui sont transmis
		$emails_array = array(); // initialisation du tableau des donnees
		foreach ($id_objets as $id_objet) { // prepare la structure du tableau renvoye
			$emails_array[$id_objet] = array();
		}
		if ( $objet=='auteur' ) { // on commence par recuperer les emails de la table spip_auteurs
			$query = sql_select("id_auteur, email, CONCAT('0-', id_auteur) AS id_email, '' AS titre, 'pref' AS type", 'spip_auteurs', sql_in('id_auteur', $id_objets)." AND email <> ''"); // on peut prendre comme titre le champ "nom" qui peut etre different du nom de membre affiche (c'est un pseudo) mais ce n'est pas forcement pertinent ; on peut reprendre le champ "email" aussi mais cela empeche le reformatage automatique et on a une longue colonne disgracieuse...
			while ($auteur_info = sql_fetch($query))
				$emails_array[$auteur_info['id_auteur']][] = $auteur_info;
			sql_free($query);
		}
		$trouver_table = charger_fonction('trouver_table', 'base');
		if ( $trouver_table('emails') && $trouver_table('emails_liens') ) { // le plugin "Coordonnees" est installe (active ou pas)
			$query = sql_select('l.id_objet, l.type, e.*','spip_emails AS e INNER JOIN spip_emails_liens AS l ON l.id_email=e.id_email', sql_in('l.id_objet', $id_objets)." AND l.objet='$objet' ");
			while ($data = sql_fetch($query)) { // on recupere tous les numeros dans un tableau de tableaux
				$emails_array[$data['id_objet']][] = $data;
			}
			sql_free($query);
		}
	} else { // on a deja la liste des emails !
		$emails_array = $id_objets;
	}
	$emails_string = array();  // initialisation du tableau renvoye
	foreach ($emails_array as $id_objet => $courriels) {  // on cree la liste de chaines de courriels
		foreach ($courriels as $courriel) { // formater chaque mel
			$href = FALSE;
			if ( !is_array($courriel) ) {
				$courriel['email'] = $courriel;
			}
			if ($html_span) { // balisage HTML avec microformat
				$emails_string[$id_objet] .= "<$html_span class='email'>". appliquer_filtre($courriel['type'], 'logo_type_mel');
				if ( !in_array($courriel['format'],array('x400','ldap')) )
					$href = TRUE;
				$emails_string[$id_objet] .= ($href?("<a title='". _T('asso:ecrire_a') ." $courriel[email]' href='mailto:$courriel[email]'"):'<span') ." class='value'>";
				unset($courriel['type']); // ne devrait plus etre traite par le modele
				unset($courriel['id_objet']); // ne devrait plus etre traite par le modele
				unset($courriel['id_email']); // ne devrait pas etre utilise par le modele
				$courriel['email'] = ( $courriel['titre'] ? $courriel['titre'] : ucwords(str_replace('@', ' ['._T('perso:at').'] ', $courriel['email'])) ); // on affiche le titre si present sinon la valeur
			}
			$emails_string[$id_objet] .= $courriel['email']. ($html_span?('</'.($href?'a':'span')."></$html_span>\n"):'');
		}
		$emails_string[$id_objet] = $emails_string[$id_objet].$sep;
	}
	return $emails_string;
}

/**
 * Affichage micro-formate de liste de sites
 *
 * @param array $id_objets
 *   Liste des (listes de) URLs a formater, ou
 *   liste des ID dont on doit formater les URLs (voir parametre suivant)
 * @param string $objet
 *   Indique le type d'objet dont les ID sont passes afin de recuperer les
 *   numeros associes a ces objets. Quand rien n'est indique c'est que c'est la
 *   liste de liste des numeros qui est directement fournie.
 *   (ceci est prevu pour etendre facilement l'usage de la fonction si necessaire,
 *   vaut "auteur" par defaut)
 * @param bool $a
 *   Active (si oui) la creation d'un lien cliquable avec la classe "url" ou renvoit juste (si faux) l'adresse brute
 * @param string $sep
 *   Separateur entre les adresse.
 *   Par defaut l'espace.
 * @return array $urls_string
 *   Liste des liens formates en HTML.
 * @note
 *   http://microformats.org/wiki/vcard-suggestions#URL_Type_Definition
 * @note
 *   http://en.wikipedia.org/wiki/Instant_messaging#Interoperability
 *   http://en.wikipedia.org/wiki/Comparison_of_instant_messaging_protocols
 *   http://microformats.org/wiki/hcard-examples#AOL_Instant_Messenger_.28AIM.29
 *   http://tools.ietf.org/html/rfc4770
 *   http://rfc-ref.org/RFC-TEXTS/4770/kw-uri.html
 *   http://en.wikipedia.org/wiki/VCard#vCard_extensions
 *   http://en.wikipedia.org/wiki/Social_web
 *   http://fr.wikipedia.org/wiki/R%C3%A9seau_social#R.C3.A9seaux_sociaux_sur_Internet
 *   http://fr.wikipedia.org/wiki/R%C3%A9seautage_social#R.C3.A9seaux_ayant_plus_de_30_millions_d.27inscriptions
 *   http://fr.wikipedia.org/wiki/Uniform_Resource_Identifier
 *   http://fr.wikipedia.org/wiki/Hyperlien
 */
function association_formater_urls($id_objets, $objet='auteur', $a=TRUE, $sep=' ') {
	$id_objets = association_recuperer_liste($id_objets, FALSE);
	if ($objet) { // ancien comportement : ce sont les id_objet qui sont transmis
		$urls_array = array(); // initialisation du tableau des donnees
		foreach ($id_objets as $id_objet) { // prepare la structure du tableau renvoye
			$urls_array[$id_objet] = array();
		}
		if ( in_array($objet, array('auteur', 'breve', 'forum', 'syndic', 'signature')) ) { // on commence par recuperer les #NOM_SITE et #URL_SITE des tables natives de SPIP (pour les breves c'est plutot #LIEN_TITRE et #LIEN_URL ! pfff...)
			$query = sql_select("0 AS id_url, id_$objet AS id_objet, ". ($objet=='breve'?'lien_titre':'nom_site') .' AS titre, '.  ($objet=='breve'?'lien_url':'url_site') ." AS url, 'pref' AS type",
			"spip_{$objet}s",
			sql_in("id_$objet", $id_objets) .' AND '. ($objet=='breve'?'lien_url':'url_site'). "<>''");
			while ($site = sql_fetch($query))
				$urls_array[$site['id_objet']][] = $site;
			sql_free($query);
		}
		$trouver_table = charger_fonction('trouver_table', 'base');
		if ( $trouver_table('syndic') && $trouver_table('syndic_liens') ) { // le plugin "Coordonnees" est installe (active ou pas)
			$query = sql_select('l.id_syndic AS id_url, l.id_objet, l.type, s.url_site AS url, s.nom_site AS titre, s.id_syndic AS id_url','spip_syndic AS s INNER JOIN spip_syndic_liens AS l ON l.id_syndic=s.id_syndic', sql_in('l.id_objet', $id_objets)." AND l.objet='$objet' ");
			while ($data = sql_fetch($query)) { // on recupere tous les sites lies dans un tableau de tableaux
				$urls_array[$data['id_objet']][] = $data;
			}
			sql_free($query);
		}
	} else { // on a deja la liste des URLs !
		$urls_array = $id_objets;
	}
	$urls_string = array();  // initialisation du tableau renvoye
	foreach ($urls_array as $id_objet => $urls) { // on le transforme en liste de chaines formatees
		foreach ($urls as $lien) { // il y a la(s) URL(s)
			if ( !is_array($lien) ) {
				$lien['url'] = $lien;
			}
			if ($a) { // balisage HTML avec microformat
				$urls_string[$id_objet] .= appliquer_filtre($lien['type'], 'logo_type_mel') ." <a class='url' href='$lien[url]'>";
#				unset($lien['type']); // ne devrait plus etre traite par le modele
#				unset($lien['id_objet']); // ne devrait plus etre traite par le modele
#				unset($lien['id_url']); // ne devrait pas etre utilise par le modele
				$urls_string[$id_objet] .= ($lien['titre']?$lien['titre']:$lien['url']); // on affiche le titre si present sinon la valeur
			} else
				$urls_string[$id_objet] .= $lien['url'];
			$urls_string[$id_objet] .= ($a?'</a>':'') .$sep;
		}
	}
	return $urls_string;
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
function association_recuperer_date($valeur, $req=TRUE) {
	$valeur = ($req?_request($valeur):$valeur);
	if ( $valeur ) {
		$valeur = preg_replace('/\D/', '-', $valeur, 2); // la limitation a 2 separateurs permet de ne transformer que la partie "date" s'il s'agit d'un "datetime" par exemple.
	}
	return $valeur;
}

/**
 * @return float $valeur
 *   Nombre decimal
 * @note
 *   Bien qu'il s'agisse en fait de s'assurer que la valeur est un flottant, la fonction s'appelle _montant car elle est utilisee surtout pour les montants.
 */
function association_recuperer_montant($valeur, $req=TRUE) {
	$valeur = ($req?_request($valeur):$valeur);
	if ( $valeur ) {
		setlocale(LC_NUMERIC, utiliser_langue_visiteur() );
		$locale = localeconv(); // recuperer les parametres regionnaux
		$valeur = str_replace($locale['thousands_sep'], '', $valeur); // suppprime les separateurs de milliers
		$valeur = str_replace($locale['decimal_point'], '.', $valeur); // remplacer le separateur decimal par le point
		$valeur = floatval($valeur);
	}
	return floatval($valeur);
}

/**
 * @return int $valeur
 *   Nombre entier
 */
function association_recuperer_entier($valeur, $req=TRUE) {
	$valeur = ($req?_request($valeur):$valeur);
	return intval($valeur);
}

/**
 * @return array $valeur
 *   Liste de valeurs
 */
function association_recuperer_liste($valeur, $req=FALSE) {
	$valeur = ($req?_request($valeur):$valeur);
	return $valeur = (array)$valeur;
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
function association_verifier_date($valeur, $rex=FALSE, $req=TRUE) {
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
function association_verifier_montant($valeur, $req=TRUE) {
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
function association_verifier_membre($valeur, $rex=FALSE, $req=TRUE) {
	$id_auteur = intval($req?_request($valeur):$valeur);
	if ($id_auteur) {
		if ( sql_countsel('spip_'.($rex?'auteurs':'asso_membres'), "id_auteur=$id_auteur")==0 ) {
			return _T('asso:erreur_id_adherent');
		}
	} else
		return '';
}

/**
 * S'assurer que la somme des ventilations par destinations comptables correspond
 * au montant de l'operation.
 * le parametre d'entree est le montant total attendu, les montants des destinations
 * sont recuperes directement dans $_POST
 */
function association_verifier_destinations($valeur, $req=TRUE) {
	if (!$GLOBALS['association_metas']['destinations']) return FALSE;

	// verifier si besoin que le montant des destinations
	// correspond bien au montant de l'operation
	$montant_attendu = floatval($req?_request($valeur):$valeur);
	$err = '';
	$toutesDestinationsIds = _request('id_dest');
	$toutesDestinationsMontants = _request('montant_dest');
	$total_destination = 0;
	$id_inserted = array();
	if (count($toutesDestinationsIds)>1) { // on a plusieurs destinations
		foreach ($toutesDestinationsIds as $id => $id_destination) {
		  // on verifie qu'il n'y a pas plusieurs fois
		  // la meme destination, tout en recalculant le total
			if (!array_key_exists($id_destination,$id_inserted)) {
				$id_inserted[$id_destination] = 0;
			} else {
				$err = _T('asso:erreur_destination_dupliquee');
			}
			$total_destination += association_recuperer_montant($toutesDestinationsMontants[$id], FALSE); // les montants sont dans un autre tableau aux meme cles
		}
		if ( $montant_attendu!=$total_destination ) { // on verifie que la somme des montants des destinations correspond au montant attendu
				$err .= _T('asso:erreur_montant_destination');
		}
	} else { // une seule destination, le montant peut ne pas avoir ete precise, dans ce cas pas de verif, c'est le montant attendu qui sera entre dans la base
		if ($toutesDestinationsMontants[1]) { // quand on a une seule destination, l'id dans les tableaux est forcement 1 par contruction de l'editeur
			if ( $montant_attendu!=association_recuperer_montant($toutesDestinationsMontants[1], FALSE) ) { // on verifie que le montant indique correspond au montant attendu
			  $err = _T('asso:erreur_montant_destination');
			}
		}
	}
	return $err;
}

/** @} */


/*****************************************
 * @defgroup filtre_selecteur_asso
 * Selecteur HTML (liste deroulante) servant a filtrer le listing affiche en milieu de page
 *
 * @param int $sel
 *   Selection initiale (pour conserver la valeur selectionnee)
 * @return string $res
 *   Code HTML du selecteur (ou du formulaire complet si $exec est indique)
 *
** @{ */

/**
 * @name filtre_selecteur_asso_<liste1>
 * cas general de :
 *
 * @param int $sel
 *   ID selectionne : conserve la valeur selectionnee
 */
//@{

/**
 * Selecteur d'exercice comptable
 */
function filtre_selecteur_asso_exercice($sel='') {
    $res = "<select name ='exercice' onchange='form.submit()' id='asso_exercice'>\n";
#    $res .= '<option value="0" ';
#	$res .= (!$el?' selected="selected"':'');
#    $res .= '>'. _L("choisir l'exercice ?") ."</option>\n";
    $sql = sql_select('id_exercice, intitule', 'spip_asso_exercices', '', 'intitule DESC');
    while ($val = sql_fetch($sql)) {
		$res .= '<option value="'.$val['id_exercice'].'" ';
		$res .= ($sel==$val['id_exercice']?' selected="selected"':'');
		$res .= '>'.$val['intitule']."</option>\n";
    }
    sql_free($sql);
    return "$res</select>\n";
}

/**
 * Selecteur de destination comptable
 */
function filtre_selecteur_asso_destination($sel='') {
    if ( !$GLOBALS['association_metas']['destinations'])
 		return ''; // on n'affiche le selecteur que si l'utilisation des destinations est activee en configuration
   $res = "<select name ='destination' onchange='form.submit()' id='asso_destination'>n";
    $res .= '<option value="0" ';
	$res .= (!$sel?' selected="selected"':'');
    $res .= '>'. _T('asso:toutes_destinations') ."</option>\n";
    $intitule_destinations = array();
    $sql = sql_select('id_destination, intitule', 'spip_asso_destination','', 'intitule DESC');
    while ($val = sql_fetch($sql)) {
		$res .= '<option value="'.$val['id_destination'].'" ';
		$res .= ($sel==$val['id_destination']?' selected="selected"':'');
		$res .= '>'.$val['intitule']."</option>\n";
    }
    sql_free($sql);
    return "$sel</select>\n";
}

/**
 * Selecteur de groupe de membres
 */
function filtre_selecteur_asso_groupe($sel='') {
    $sql = sql_select('id_groupe, nom', 'spip_asso_groupes', 'id_groupe>=100', '', 'nom');  // on ne prend en consideration que les groupe d'id >= 100, les autres sont reserves a la gestion des autorisations
    if ( !$sql || !sql_count($sql) )
		return '';  // ne proposer que s'il y a des groupes definis
	$res = "<select name='groupe' onchange='form.submit()' id='asso_groupe'>\n";
	$res .= '<option value=""';
	$res .= (!$sel?' selected="selected"':'');
    $res .= '>'. _T('asso:tous_les_groupes') ."</option>\n";
	while ($val = sql_fetch($sql)) {
		$res .= '<option value="'.$val['id_groupe'].'"';
		$res .= ($sel==$val['id_groupe']?' selected="selected"':'');
		$res .= '>'.$val['nom']."</option>\n";
	}
	sql_free($sql);
	return "$res</select>\n";
}

/**
 * Selecteur de statut de membres
 *
 * @note
 *   Idem instituer_statut_interne_ici
 *   Idem instituer_adherent_ici
 */
function filtre_selecteur_asso_statut($sel='') {
    $res = "<select id='statut_interne' name='statut_interne' onchange='form.submit()' id='asso_statutinterne'>\n";
#    $res .= '<option value="tous"';
#    $res .= (($sel=='tous' || $sel=='%')?' selected="selected"':'');
#    $res .= '>'. _T('asso:entete_tous') ."</option>\n";
    $res .= '<option value=""';
    $res .= (($sel=='defaut' || $sel=='')?' selected="selected"':'');
    $res .= '>'. _T('asso:actifs') ."</option>\n";
    foreach ($GLOBALS['association_liste_des_statuts'] as $statut) {
		$res .= '<option value="'.$statut.'"';
		$res .= ($sel==$statut?' selected="selected"':'');
		$res .= '> '. _T('asso:adherent_entete_statut_'.$statut)
		. "</option>\n";
	}
	return "$res</select>\n";
}

/**
 * Zone de saisie de numero de membre
 */
function filtre_selecteur_asso_id($sel='') {
	    $res = '<input type="text" name="id" onfocus=\'this.value=""\' size="5"  value="'.$sel.'" placeholder="'. _T('asso:entete_id') .'" />';
    return "$res\n";
}

//@}

/**
 * @name filtre_selecteur_asso_<liste2>
 * cas general de :
 *
 * @param string $table
 *   Nom (sans prefixe "spip_") de la table concernee
 * @param string $url
 *   Adresse des liens (en son absence c'est une liste de selection)
 * @param bool $lst
 *   Indique s'il faut une liste de selection (vrai) ou des cases/boutons (faux)
 */
//@{

/**
 * Selecteur d'annee parmi celles disponibles dans une table donnee
 *
 * @param string $annee
 *   Annee selectionnee. (annee courante par defaut)
 * @param string $champ
 *   Nom (sans prefixe "date_") du champ contenant les annees recherchees
 */
function filtre_selecteur_asso_annee($annee='', $table, $champ, $url='') {
    $pager = '';
    if ( !$annee ) // annee non precisee (ou valant 0)
		$annee = date('Y'); // on prend l'annee courante
    $res = "<select name ='annee' onchange='form.submit()' id='annee_$champ'>\n";
    $an_max = sql_getfetsel("MAX(DATE_FORMAT(date_$champ, '%Y')) AS an_max", "spip_$table", '');
    $an_min = sql_getfetsel("MIN(DATE_FORMAT(date_$champ, '%Y')) AS an_min", "spip_$table", '');
    if ( $annee>$an_max || $annee<$an_min ) // si l'annee (courante) n'est pas disponible dans la liste deroulante on est mal positionne et le changement de valeur n'est pas top
		$res .= '<option value="'.$annee.'" selected="selected">'.$annee."</option>\n";
    $sql = sql_select("DATE_FORMAT(date_$champ, '%Y') AS annee", "spip_$table",'', 'annee DESC', 'annee');
    while ($val = sql_fetch($sql)) {
		$res .= '<option value="'.$val['annee'].'"';
		if ($annee==$val['annee']) {
			$res .= ' selected="selected"';
			$pager .= "\n<strong>$val[annee]</strong>";
		} else {
			$pager .= ' <a href="'.$url.'&annee='.$val['annee'] .'">'.$val['annee']."</a>\n";
		}
		$res .= '>'.$val['annee']."</option>\n";
    }
    sql_free($sql);
    return ($url?"<div class='choix'>$pager</div>\n":"$res</select>\n");
}

/**
 * Selecteur d'initiale parmi celles disponibles dans une table donnee
 *
 * @param string $lettre
 *   Initiale selectionnee. (aucune par defaut)
 * @param string $champ
 *   Nom du champ contenant les initiales recherchees
 */
function filtre_selecteur_asso_lettre($lettre='', $table, $champ, $url='') {
    $lettre = strtoupper($lettre);
    $pager = '';
    $res = "<select name ='lettre' onchange='form.submit()' id='lettre_champ'>\n";
	$res .= '<option value=""';
	$res .= ((!$lettre||$lettre=='%')?' selected="selected"':'');
	$res .='>'. _T('asso:entete_tous') ."</option>\n";
    $sql = sql_select("UPPER( LEFT( $champ, 1 ) ) AS init", "spip_$table", '',  'init ASC', "$champ"); // LEFT(field, n) ==  SUBSTRING(field, 1, n)
    while ($val = sql_fetch($sql)) {
		$res .= '<option value="'.$val['init'].'"';
		if ($lettre==$val['init']) {
			$res .= ' selected="selected"';
			$pager .= "\n<strong>$lettre</strong>";
		} else {
			$pager .= ' <a href="'.$url.'&lettre='.$val['init'].'">'.$val['init'].'</a>';
		}
		$res .= '>'.$val['init']."</option>\n";
    }
    sql_free($sql);
    if ( !$lettre || $lettre=='%' ) {
		$pager .= ' <strong>'. _T('asso:entete_tous') .'</strong>';
	} else {
		$pager .= ' <a href="'.$url.'">'. _T('asso:entete_tous') .'</a>';
	}
    return ($url?"<div class='choix'>$pager</div>\n":"$res</select>\n");
}

/**
 * Selecteur d'exercice ou d'annee parmi celles disponibles dans une table donnee
 *
 * @param int $periode
 *   Annee ou exercice selectionnee.
 *   (dernier exercice ou annee courante par defaut)
 * @param string $champ
 *   Nom (sans prefixe "date_") du champ contenant les annees recherchees
 *
 * @see association_selectionner_annee
 * @see association_selectionner_exercice
 */
function filtre_selecteur_asso_periode($periode, $table, $champ, $url='') {
	return $GLOBALS['association_metas']['exercices'] ? filtre_selecteur_asso_exercice($periode) : filtre_selecteur_asso_annee($periode, $table, $champ, $url) ;
}

/**
 * Selecteur de type de liaison/coordonnee parmi celles disponibles dans une table (de liaison) donnee
 *
 * @param string $type
 *   Type selectionnee. (aucune par defaut)
 * @param string $coord
 *   Coordonnee qui nous interessee :
 *   adresse|email|numero|syndic
 * @param string $objet
 *   Objet lie auquel restreindre la selection.
 */
function filtre_selecteur_asso_type($type='', $coord='adresse', $objet='auteur', $lst=FALSE) {
    $tbl = table_objet_sql($coord); // on le determine ici car 'site' est un alias de 'syndic' (dont la table n'a pas de S final et dont la boucle s'appelle 'syndication' !)
    switch ($coord) {
		case 'adresse':
			$abreviation = 'adr';
			break;
		case 'email':
			$abreviation = 'mel';
			break;
#		case 'impp':
#			$abreviation = 'mip';
#			break;
		case 'numero':
			$abreviation = 'tel';
			break;
		case 'site':
		case 'syndic':
			$coord = 'site';
			$abreviation = 'mel';
			break;
		default:
			if (!$tbl) // la requete plantera surement
				return;
			else // la requete reussira probablement
				$abreviation = $coord;
			break;
	}
	$res = $pager = '<em class="explication">'. _T("coordonnees:label_type_$coord") ."</em>\n";
    $res .= "<select name='type_$coord' id='type_$coord'>\n";
    $res .= '<option value="%"';
    $pager .= "<div class='choix'><input name='type_$coord' id='type_{$coord}_tous' type='radio' value='%'";
    $res .= (($type==' ' || $type=='%')?' selected="selected"':'');
    $pager .= (($type==' ' || $type=='%')?' checked="checked"':'');
    $res .= '>'. _T('asso:entete_tous') ."</option>\n";
    $pager .= " /><label for='type_{$coord}_tous'>". _T('asso:entete_tous') ."</label></div>\n";
    $sql = sql_select("type", $tbl.'_liens', ($objet?("objet=".sql_quote($objet)):''), 'objet, type', 'objet, type');
    while ($val = sql_fetch($sql)) {
		$res .= '<option value="'.$val['type'].'"'
		. ($type==$val['type']?' selected="selected"':'') .'> '. _T('coordonnees:type_'.$abreviation.'_'.$val['type']) . "</option>\n";
		$pager .= "<div class='choix'><input name='type_$coord' id='type_$coord_". preg_replace('/\W/', '', $val['type']) ."' type='radio' value='$val[type]'". ($type==$val['type']?' checked="checked"':'') ." /><label for='type_$coord_". preg_replace('/\W/', '', $val['type']) ."'>". appliquer_filtre($val['type'], "logo_type_$abreviation") ."</label></div>\n";
	}
	sql_free($sql);
    return ($lst?"$res</select>\n":$pager);
}

//@}

/**
 * Selecteur de destinations comptables
 *
 * @param array $sel
 *   Liste des ID de destination selectionnes
 * @param bool $lst
 *   Indique s'il faut afficher le resultat sous forme d'une liste de selections
 *   multiples (vrai) ou sous forme de cases a cocher (faux)
 * @note
 *   Il s'agit d'un selecteur maintenu par compatibilite (usage uniquement dans
 *   exec/bilan.php actuellement) et ne devrait plus etre utilise a l'avenir
 */
function filtre_selecteur_asso_destinations($sel='', $plus='', $lst=FALSE) {
	if (!$GLOBALS['association_metas']['destinations'])
		return FALSE;
    $res1 = "<select name ='destinations[]' multiple='multiple' onchange='form.submit()' id='asso_destinations'>";
    $res2 = '';
    $res1 .= '<option value="0" ';
    $res2 .= '<div class="choix"><input type="checkbox" name ="destinations[]" value="0" id="destination_0"';
    if ( !(array_search(0, $sel)===FALSE) ) {
		$res1 .= ' selected="selected"';
		$res2 .= ' checked="checked"';
    }
    $res1 .= '>'. _T('asso:toutes_destinations') ."</option>\n<option disabled='disabled'></option>\n";
    $res2 .= ' /><label for="destination_0">'._T('asso:toutes_destinations').'</label></div>';
    $res2 .= "<div class='choix'><hr /></div>\n";
    $intitule_destinations = array();
    $sql = sql_select('id_destination, intitule', 'spip_asso_destination','', 'intitule DESC');
    while ($val = sql_fetch($sql)) {
		$res1 .= '<option value="'.$val['id_destination'].'"';
		$res2 .= '<div class="choix"><input type="checkbox" name ="destinations[]" value="'.$val['id_destination'].'" id="destination_'.$val['id_destination'].'"';
		if ( !(array_search($val['id_destination'], $sel)===FALSE) ) {
			$res1 .= ' selected="selected"';
			$res2 .= ' checked="checked"';
		}
		$res1 .= '>'.$val['intitule']."</option>\n";
		$res2 .= ' /><label for="destination_'.$val['id_destination'].'">'.$val['intitule']."</label></div>\n";
		$intitule_destinations[$val['id_destination']] = $val['intitule'];
    }
	sql_free($sql);
    return ($lst?"$res1</select>\n":$res2);
}

/** @} */


/*****************************************
 * @defgroup generer_url
 * Raccourcis SPIP de lien rajoutes par ce plugin
 *
 * Les tables ayant deux prefixes ("spip_asso_"),
 * le raccourci "x" implique de declarer le raccourci "asso_x"
 *
** @{ */

/*c
 * [->asso_donN] = /?exec=edit_don&id=N
 */
function generer_url_asso_don($id, $param='', $ancre='') {
	return  generer_url_ecrire('edit_don', 'id='.intval($id));
}
/*c
 * [->donN] = [->asso_dontN]
 */
function generer_url_don($id, $param='', $ancre='') {
	return  array('asso_don', $id);
}

/*c
 * [->asso_membreN] = /?exec=adherent&id=N
 */
function generer_url_asso_membre($id, $param='', $ancre='') {
	return  generer_url_ecrire('adherent', 'id='.intval($id));
}
/*c
 * [->membreN] = [->asso_membreN]
 */
function generer_url_membre($id, $param='', $ancre='') {
	return  array('asso_membre', $id);
}

/*c
 * [->asso_venteN] = /?exec=edit_vente&id=N
 */
function generer_url_asso_vente($id, $param='', $ancre='') {
	return  generer_url_ecrire('edit_vente', 'id='.intval($id));
}
/*c
 * [->venteN] = [->asso_venteN]
 */
function generer_url_vente($id, $param='', $ancre='') {
	return  array('asso_vente', $id);
}

/*c
 * [->asso_ressourceN] = /?exec=prets&id=N
 */
function generer_url_asso_ressource($id, $param='', $ancre='') {
	return  generer_url_ecrire('prets', 'id='.intval($id));
}
/*c
 * [->ressourceN] = [->asso_ressouceN]
 */
function generer_url_ressource($id, $param='', $ancre='') {
	return  array('asso_ressource', $id);
}

/*c
 * [->asso_activiteN] = /?exec=inscrits_activite&id=N
 */
function generer_url_asso_activite($id, $param='', $ancre='') {
	return  generer_url_ecrire('inscrits_activite', 'id='.intval($id));
}
/*c
 * [->activiteN] = [->asso_activiteN]
 */
function generer_url_activite($id, $param='', $ancre='') {
	return  array('asso_activite', $id);
}

/** @} */


/*****************************************
 * @defgroup association_tablinfos
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
 *   'chaine_de_langue_du_titre' => "texte contenu/explication associe."
 * @param string $ObjetEtendu
 *   Nom de l'objet etendu dont on desire afficher les lignes des champs rajoutes par "Interface Champs Extras 2".
 *   Par defaut : rien
 * @return string $res
 *
 * @note
 *   Ce n'est pas redondant d'avoir a la fois $type et $ObjetEtendu qui peuvent
 *   avoir des valeurs differentes comme on peut le voir dans exec/adherent.php et exec/inscrits_activite.php !
 */
function association_tablinfos_intro($titre, $type='', $id=0, $DesLignes=array(), $ObjetEtendu='') {
	$res = '';
	if ($type) {
		$res .= '<div style="text-align: center" class="verdana1 spip_x-small">'. _T('asso:titre_num', array('titre'=>_T("local:$type"), 'num'=>$id) ) .'</div>'; // presentation propre a Associaspip qui complete par un autre titre (voir ci-apres). Dans un SPIP traditionnel on aurait plutot : $res .= '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'. association_langue($type) .'<br /><span class="spip_xx-large">'.$id.'</span></div>';
	}
	$res .= '<div style="text-align: center" class="verdana1 spip_medium">'.$titre.'</div>';
	if ( !is_array($DesLignes) )
		return $res;
	if ( count($DesLignes) OR $ObjetEtendu )
		$res .= "\n<dl class='verdana1 spip_xx-small'>";
	foreach ($DesLignes as $dt=>$dd) {
		$res .= "\n<dt>". association_langue($dt) ."</dt>\n<dd>". propre($dd) .'</dd>'; // propre() paragraphe (rajoute <p>)... mais ce comportement peut etre change en mettant "paragrapher" a FALSE dans mes_options.php : http://www.spip.net/fr_article889.html Cette presentation-ci est propre a Associaspip ; Habituellement on a : $res .= "<div class='$dt'><strong>". association_langue($dt) ."</strong> $dd</div>";
	}
	if ($ObjetEtendu) {
		$champsExtras = association_trouver_iextras($ObjetEtendu, $id); // on recupere les champs extras crees manuellement (i.e. via l'interface d'edition du prive, pas ceux rajoutes par les plugins !)
		if ( count($champsExtras) ) {
			foreach ($champsExtras as $champExtra) {
				$res .= "\n<dt>". $champExtra[0] .'</dt>';
				$res .= "\n<dd>". $champExtra[1] .'</dd>';
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
		$res .= "</dl>\n";
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
function association_tablinfos_stats($legende='', $sql_table_asso, $sql_champs, $sql_criteres='1=1',$decimales_significatives=1, $avec_extrema=FALSE) {
	if (!is_array($sql_champs) || !$sql_table_asso)
		return FALSE;
	$res = '<table width="100%" class="asso_infos"><caption>'
	. _T('asso:totaux_moyens', array('de_par'=>_T("local:$legende")))
	. "</caption>\n"
	. "\n<tr class='row_first'>\n<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>"
	. '<th scope="col" title="'. _T('entete_stats_moy') .'">'
	. '<span style="text-decoration:overline;">X</span>' . "</th>\n"
	. "<th  scope='col' title='". _T('entete_stats_mea') ."'>&sigma;</th>\n";
	// σ &sigma; &#963; &#x3C3;
	if ($avec_extrema) {
		$res .= '<th  scope="col" title="'. _T('entete_stats_min') .'">[&lt;</th>';
		$res .= '<th  scope="col" title="'. _T('entete_stats_max') .'">&gt;]</th>';
	}
	$res .= "</tr>\n";
	$compteur = 0;
	foreach ($sql_champs as $libelle=>$champs) {
		$stats = sql_fetsel("AVG($champs) AS valMoy, STDDEV($champs) AS ekrTyp, MIN($champs) AS valMin, MAX($champs) AS valMax ", "spip_asso_$sql_table_asso", $sql_criteres);
		$res .= '<tr class="'. ($compteur%2?'row_odd':'row_even') .'">';
		$res .= "\n<td class='text'>". association_langue((is_numeric($libelle)?$champs:$libelle)) ."</td>\n";
		$res .= '<td class="'.($decimales_significatives?'decimal':'integer').'">'. association_formater_nombre($stats['valMoy'],$decimales_significatives) ."</td>\n";
		$res .= '<td class="'.($decimales_significatives?'decimal':'integer').'">'. association_formater_nombre($stats['ekrTyp'],$decimales_significatives) ."</td>\n";
		if ($avec_extrema) {
			$res .= '<td class="'.($decimales_significatives?'decimal':'integer').'">'. association_formater_nombre($stats['valMin'],$decimales_significatives) ."</td>\n";
			$res .= '<td class="'.($decimales_significatives?'decimal':'integer').'">'. association_formater_nombre($stats['valMax'],$decimales_significatives) ."</td>\n";
		}
		$res .= '</tr>';
		$compteur++;
	}
	$res .= '</table>';
	return $res;
}

/**
 * Tableau des decomptes statistiques dans le bloc infos
 *
 * @param string $legende
 *   Complement du titre du tableau
 * @param array $lignes
 *   'classe_unique_css_de_la_ligne' => array( 'chaine_de_langue', effectif_occurence, "texte libre place avant la chaine de langue", "texte libre place apres la chaine de langue")
 * @param int $decimales_significatives
 *   Nombre de decimales affichees
 * @return string $res
 *   Table HTML de deux colonnes et une ligne par paire libelle/effectif
 *   puis une ligne totalisant les effectifs s'il y a plus d'une ligne.
 *
 * @note
 *   Les classes CSS sont utilisees comme cle des tables parce-qu'il ne doit y en avoir qu'une par ligne.
 */
function association_tablinfos_effectifs($legende='', $lignes, $decimales_significatives=0) {
	if (!is_array($lignes) OR !$lignes)
		return '';
	$nbr_actuel = $nbr_total = 0;
	$res = '<table width="100%" class="asso_infos">';
	$res .= "\n<caption>". _T('asso:totaux_nombres', array('de_par'=>_T("local:$legende"))) ."</caption>\n";
	foreach ($lignes as $classe_css=>$params) {
		$res .= "<tr class='$classe_css'>";
		$res .= '<td class="text">'. $params[2]. association_langue($params[0]) .$params[3]."</td>\n";
		$nbr_actuel = is_array($params[1]) ? call_user_func_array('sql_countsel', $params[1]) : $params[1] ;
		$res .= '<td class="' .($decimales_significatives?'decimal':'integer') .'">'. association_formater_nombre($nbr_actuel, $decimales_significatives) ."</td>\n";
		$nbr_total += $nbr_actuel;
		$res .= "</tr>\n";
	}
	if ( count($lignes)>1 ) {
		$res .= '<tr><th class="text">'._T('asso:liste_nombre_total')."</th>\n";
		$res .= '<th class="' .($decimales_significatives?'decimal':'integer') .'">'. association_formater_nombre($nbr_total, $decimales_significatives) .'</th></tr>';
	}
	return $res."</table>\n";
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
 */
function association_tablinfos_montants($legende='', $somme_recettes=0, $somme_depenses=0) {
	$res = '<table width="100%" class="asso_infos">';
	$res .= '<caption>'. _T('asso:totaux_montants', array('de_par'=>_T("local:$legende"))) ."</caption>\n";
	$recettes = is_array($somme_recettes) ? call_user_func_array('sql_getfetsel', $somme_recettes) : $somme_recettes ;
	if ($recettes) {
		$res .= "<tr class='impair'>"
		. '<th scope="row" class="entree">'. _T('asso:bilan_recettes') ."</th>\n"
		. '<td class="decimal">' .association_formater_prix($recettes). ' </td>'
		. "</tr>\n";
	}
	$depenses = is_array($somme_depenses) ? call_user_func_array('sql_getfetsel', $somme_depenses) : $somme_depenses ;
	if ($depenses) {
		$res .= '<tr class="pair">'
		. '<th scope="row" class="sortie">'. _T('asso:bilan_depenses') ."</th>\n"
		. '<td class="decimal">'.association_formater_prix($depenses) ."</td>\n"
		. "</tr>\n";
	}
	if (!$recettes && !$depenses) { // ne rien afficher si les deux sont a zero !
		return '';
	}
	if ($recettes && $depenses) { // on va afficher le solde si l'un des deux ne vaut pas zero
		$solde = $recettes-$depenses;
		$res .= '<tr class="'.($solde>0?'impair':'pair').'">'
		. '<th scope="row" class="solde">'. _T('asso:bilan_solde') ."</th>\n"
		. '<td class="decimal">'.association_formater_prix($solde)."</td>\n"
		. "</tr>\n";
	}
	return "$res</table>\n";
}

/** @} */


/*****************************************
 * @defgroup association_form
 *
 * @return string $res
 *   FORMulaire HTML dans un bloc (DIV ou TABLE ou autre)
** @{ */

/**
 * Demande de confirmation dans la suppression d'un objet
 *
 * @param string $type
 *   Type d'objet a supprimer
 * @param int $id
 *   ID de l'objet a supprimer
 * @param string $retour
 *   Nom du fichier d'action vers lequel le formulaire sera redirige, sans le prefixe "supprimer_".
 *   Par defaut, quand rien n'est indique, c'est l'objet prefixe de "asso" qui est utilise
 */
function association_form_suppression($type, $id, $retour='') {
	$res = _T('asso:objet_num', array('objet'=>$type,'num'=>$id));
	$res = _T('asso:vous_aller_effacer', array('quoi'=>'<i>'.$res.'</i>'));
	$res = '<p><strong>'. $res  .'</strong></p><p class="boutons"><input type="submit" value="'. _T('asso:bouton_confirmer') .'" /></p>';
	return redirige_action_post("supprimer_asso_$type", $id, ($retour?$retour:$type.'s'), '', $res);
}

/**
 * Bloc (tableau en ligne) d'affinage (filtrage) des resultats dans les pages principales... (ici il s'agit de la navigation au sein des donnees tabulaires --un grand listing-- d'un module...)
 *
 * @param array $liste_filtres
 *   Filtres natifs du plugin (i.e. "filtre_selecteur_asso_"quelquechose) :
 *   'identifiant_du_filtre'=>array('liste','des','parametres')
 * @param string $exec
 *   Nom du fichier "exec" auquel le formulaire sera soumis
 * @param string|array $supplements
 *   Utilisation d'autres filtres ou code supplementaire a rajourer a la fin
 *   - Chaine HTML a rajouter
 *   - Tableau des 'identifiant_filtre'=>"code HTML du filtre" a rajouter
 * @param bool $td
 *   Indique s'il faut generer un tableau (vrai, par defaut) ou une liste (faux)
 * @note
 *   Ici il s'agit d'un vrai formulaire qui influe sur les donnees affichees
 *   et non sur la fonctionnalite en cours (onglet), contrairement aux apparences
 *   (le passage de parametre se faisant par l'URL, celle-ci change)
 *   http://comments.gmane.org/gmane.comp.web.spip.devel/61824
 */
function association_form_filtres($liste_filtres, $exec='', $supplements='', $td=TRUE) {
	$res = '<form method="get" action="'. ($exec?generer_url_ecrire($exec):'') .'">';
	if ($exec)
		$res .= "\n<div><input type='hidden' name='exec' value='$exec' /></div>";
	$res .= "\n<". ($td?'table width="100%"':'ul') .' class="asso_tablo_filtres">'. ($td?'<tr>':'');
	foreach($liste_filtres as $selecteur =>$params) {
		$res .= ($td?'<td':'<li') ." class='filtre_$filtre_selection'>". call_user_func_array("filtre_selecteur_asso_$selecteur", association_recuperer_liste($params, FALSE) ) . ($td?"</td>\n":'</li>');
	}
	if ( is_array($supplements) ) {
		foreach ($supplements as $nom => $supplement) {
			$res .= ($td?'<td':'<li') ." class='filtre_$nom'>$supplement</". ($td?'td>':'li>');
		}
	} else {
		$res .= $supplements;
	}
	$res .= ($td?'<td':'<li') . ' class="boutons"><noscript><div class="boutons"><input type="submit" value="'. _T('asso:bouton_lister') .'" /></div></noscript>' . ($td?"</td>\n":'</li>');
	return $res. ($td?'</tr></table':'</ul>') .">\n</form>\n";
}

/**
 * Selecteur/bandeau de sous-pagination
 *
 * @param int|array $pages
 *   Nombre total de pages ou
 *   Liste des elements a passer a "sql_countsel"
 * @param string $exec
 *   Nom du fichier appelant
 * @param string $params
 *   Autres informations passees par l'URL
 * @param int $debut
 *   Numero du premier enregistrement (si $req est a faux)
 *   Nom du champ contenant ce numero (si $req est a vrai)
 * @param string $plus
 *   Autres cellules du tableau uniligne. (utilise dans comptess et adherents !)
 * avec d'autres (FALSE)
 * @param bool $req
 * 	Cf. $debut
 */
function association_form_souspage($pages, $exec='', $arg=array(), $plus='', $debut='debut', $req=TRUE) {
	$res = "<table width='100%' class='asso_tablo_filtres'><tr>\n" .'<td align="left">';
	if ( is_array($pages) ) {
		$nbr_pages = ceil(call_user_func_array('sql_countsel',$pages)/_ASSOCIASPIP_LIMITE_SOUSPAGE); // ceil() ou intval()+1 ?
	} else {
		$nbr_pages = intval($pages);
	}
	if ( $nbr_pages>1 ) {
		$debut = ($req?_request($debut):$debut);
		$exec = ($exec?$exec:_request($exec));
		if (!is_array($arg))
			$arg = array($arg);
		for ($i=0; $i<$nbr_pages; $i++) {
			$position = $i*_ASSOCIASPIP_LIMITE_SOUSPAGE;
			if ($position==$debut) { // page courante
				$res .= "\n<strong>".$position.' </strong> ';
			} else { // autre page
				$arg['debut']= 'debut='.$position;
				$h = generer_url_ecrire($exec, join('&', $arg));
				$res .= "<a href='$h'>$position</a>\n";
			}
		}
	}
	return "$res</td>\n$plus\n</tr></table>";
}

/**
 * Boite affichant le formulaire pour genere le PDF de la/le liste/tableau
 *
 * @param string $objet
 *   Nom de l'objet : il s'agit imperativement d'un objet du plugin,
 *   correspondant a une table avec le nom de l'objet suffixe de "s" et prefixe
 *   de "spip_asso" (cela exclu quand meme les tables du plugin qui n'ont pas de
 *   "s" final !)
 * @param array $params
 *   Tableau de cle=>valeur supplementaires transmis par le formulaire (champs caches).
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
 * @note
 *   Ce formulaire sera traite par l'action de l'objet prefixe de "pdf_"
 */
function association_form_listepdf($objet, $params=array(), $prefixeLibelle='', $champsExclus=array(), $coords=true) {
	if (!test_plugin_actif('FPDF'))
		return;
	$frm = '<div>'; //l2.1
	foreach ($params as $k => $v) { // on fait suivre les autres parametres dont la liste des auteurs a afficher
		$frm .= '<input type="hidden" name="'.$k.'" value="'. htmlspecialchars($v, ENT_QUOTES, $GLOBALS['meta']['charset']) .'" />'; // http://stackoverflow.com/questions/46483/htmlentities-vs-htmlspecialchars
	}
	$frm .= '</div>'; //l2.1
	$champsExtras = association_trouver_iextras("asso_$objet");
	$desc_table = charger_fonction('trouver_table', 'base'); // http://doc.spip.org/@description_table deprecier donc preferer http://programmer.spip.net/trouver_table,620
	$champsPresents = $desc_table("spip_asso_${objet}s");
	$frm .= '<ul><li class="edit_champs">'; //l2.2
	foreach ($champsPresents['field'] as $k => $v) { // donner le menu des choix
		if ( !in_array($k, $champsExclus) ) { // affichable/selectionnable (champ ayant un libelle declare et connu)
				$lang_clef = $prefixeLibelle.$k;
				$lang_texte = association_langue($lang_clef);
				if ( $lang_clef!=str_replace(' ', '_', $lang_texte) ) { // champ natif du plugin
					$frm .= "<div class='choix'><input type='checkbox' name='champs[$k]' id='liste_${objet}s_$k' /><label for='liste_${objet}s_$k'>$lang_texte</label></div>"; //l3.x
				} elseif( array_key_exists($k,$champsExtras) ) { // champs rajoute via cextra
					$frm .= "<div class='choix'><input type='checkbox' name='champs[$k]' id='liste_${objet}s_$k' /><label for='liste_${objet}s_$k'>$champsExtras[$k]</label></div>"; //l3.x
				}
			}
	}
	if ($coords) {
			$frm .= '<div class="choix"><input type="checkbox" name="champs[email]" id="liste_'.$objet.'s_email" /><label for="liste_'.$objet.'s_email">'. _T('asso:adherent_libelle_email') .'</label></div>'; // on ajoute aussi l'adresse electronique principale (table spip_auteurs ou spip_emails)
			if (test_plugin_actif('COORDONNEES')) {
				$frm .= '<div class="choix"><input type="checkbox" name="champs[adresse]" id="liste_'.$objet.'_s_adresse" /><label for="liste_'.$objet.'_s_adresse">'. _T('coordonnees:adresses') .'</label></div>'; // on ajoute aussi l'adresse postale (table spip_adresses)
				$frm .= '<div class="choix"><input type="checkbox" name="champs[telephone]" id="liste_'.$objet.'_s_telephone" /><label for="liste_'.$objet.'_s_telephone">'. _T('coordonnees:numeros') .'</label></div>'; // on ajoute aussi le numero de telephone (table spip_numeros)
			}
	}
	$frm .= '</li></ul>'; //l2.2
	$frm .= '<p class="boutons"><input type="submit" value="'. _T('asso:bouton_imprimer') .'" /></p>'; //l2.3

	$res = '<h3>'. _T('plugins_vue_liste') .'</h3>'; //l1.1
	$res .= '<div class="formulaire_spip formulaire_asso_liste_'.$objet.'s">'; //l1.2
	$res .= generer_action_auteur('pdf_'.$objet.'s', 0, '', $frm, '', '');
	$res .= '</div>'; //l1.2
	return debut_cadre_enfonce('', TRUE). $res. fin_cadre_enfonce(TRUE);
}

/**
 * Boite affichant le formulaire pour genere le PDF des etiquettes
 *
 * @param string $where_adherents
 *   Criteres de requete SQL sur "spip_asso_membres m"
 * @param string $jointure_adherents
 *   Possible jonction SQL sur une autre table
 * @param string $suffixe
 *   Partie du nom de fichier insere entre "etiquettes" et ".pdf"
 */
function association_form_etiquettes($where_adherents, $jointure_adherents='', $suffixe='') {
	if (!test_plugin_actif('FPDF') OR !test_plugin_actif('COORDONNEES') )
		return;
	$frm = '<div>'; //l2.1
	$frm .= '<input type="hidden" name="where_adherents" value="'. htmlspecialchars($where_adherents, ENT_QUOTES, $GLOBALS['meta']['charset']) .'" />';
	$frm .= '<input type="hidden" name="jointure_adherents" value="'. htmlspecialchars($jointure_adherents, ENT_QUOTES, $GLOBALS['meta']['charset']) .'" />';
	$frm .= '<input type="hidden" name="suffixe" value="'. $suffixe .'" />';
	$frm .= '</div>'; //l2.1
	$frm .= '<ul>'; //l2.2
	$frm .= '<li class="editer_filtre_email">'; //l3.1
	$frm .= filtre_selecteur_asso_type('%', 'adresse', 'auteur', 1);
	$frm .= '</li>'; //l3.1
	$frm .= '<li class="editer_filtre_email">'; //l3.2
	$frm .= '<em class="explication">'. _T('asso:eti_filtre_emails') .'</em>';
	$frm .= '<select name="filtre_email" id="filtre_email">';
	$frm .= '<option value="0">'. _T('asso:membres_bof_email') .'</option>';
	$frm .= '<option value="-1">'. _T('asso:membres_non_email') .'</option>';
	$frm .= '<option value="+1">'. _T('asso:membres_oui_email') .'</option>';
	$frm .= '</select>';
	$frm .= '</li>'; //l3.2
	$frm .= '</ul>'; //l2.2
	$frm .= '<p class="boutons"><input type="submit" value="'. _T('asso:bouton_imprimer') .'" /></p>'; //l2.3

	$res = '<h3>'. _T('asso:etiquettes') .'</h3>';  //l1.1
	$res .= '<div class="formulaire_spip formulaire_asso_etiquettes">'; //l1.2
	$res .= '<p class="legend">'. _T('asso:info_etiquette') .'</p>'; //l2.0
	$res .= generer_action_auteur('pdf_etiquettes', 0, '', $frm, '', '');
	$res .= '</div>'; //l1.2
	if ( autoriser('editer_profil', 'association') )
		$res .= '<div><a href="'. generer_url_ecrire('parametrer_etiquettes') .'">'. _T('asso:parametrage_des_etiquettes') .'</a></div>'; //l2
	return debut_cadre_enfonce('', TRUE). $res. fin_cadre_enfonce(TRUE);
}

/** @} */

/**
 * Listing sous forme de tableau HTML
 *
 * @param string $table
 *   nom de table SQL
 * @param ressource $reponse_sql
 *   Ressource de requete sql_select sur cette table (avec jointure eventuelle)
 *   http://doc.spip.org/@sql_select
 *   http://programmer.spip.net/sql_select,569
 * @param array $presentation
 *   Tableau decrivant les donnees affichees :
 *   'nom_ou_alias_du_champ' => array('chaine_de:langue_du_libelle_d_entete', 'nom_du_format', 'parametre1', ...)
 *   Le nom du format est celui de la fonction de formatage du meme nom prefixee de association_formater_
 * @param array $boutons
 *   array('bouton', 'parametre1', ...)
 *   Le nom du type de bouton est celui de la fonction d'action du meme nom prefixee de association_bouton_
 * @param string $cle1
 *   Nom (ou alias) de la colonne cle primaire,
 * @param array $extra
 *   Liste de classes supplemetaires appliquees alternativement aux lignes ;
 *   Ou tableau des valeur=>classe supplementaires appliquees aux lignes presentant la valeur
 * @param string $cle2
 *   Nom (ou alias) de la colonne dont les valeurs servent de cle de classe
 * @param int $selection
 *   ID de la cle primaire selectionnee
 * @return string $res
 *   Table-HTML listant les donnees formatees
 */
function association_bloc_listehtml2($table, $reponse_sql, $presentation, $boutons=array(), $cle1='', $extra=array(), $cle2='', $selection=0) {

	if ( $cle1 ) {
		if ( strpos($cle1, 'id_')===0 )
			$objet = substr($cle1, 3);
		else
			$objet = $cle1;
	}
	$res = '';
	foreach ($presentation as &$param) { // affecter le tableau au passage
		$entete = array_shift($param);
		$res .= '<th scope="col">'. ($entete ? association_langue($entete) : '&nbsp;' ) ."</th>\n";
	}
	$lignes = association_bloc_tr($reponse_sql, $extra, $cle1, $cle2, $objet, $presentation, $boutons, $selection);
	sql_free($reponse_sql);

	if (!$lignes) return _T('asso:aucun');

	if ( count($boutons) ) { // colonne(s) de bouton(s) d'action
		$res .= '<th scope="col" colspan="'. count($boutons) .'" class="actions">'. _T('asso:entete_action' .(count($boutons)-1?'s':'')) ."</th>\n";
	}

	$res =  '<table width="100%" class="asso_tablo"'. ($table ? " id='liste_$table'" : '') . ">\n<tr class='row_first'>$res</tr>\n$lignes</table>\n";


	if ( $cle1 && $selection ) {
// comme on ne peut placer un evenement "onLoad" que sur une ressource externe
// (IMG, FRAME, SCRIPT, BODY) ; il vaut mieux appliquer un SCRIPT inclus
// (tout juste apres ou dans HEAD si possible)
		$res .= '<script type="text/javascript"> document.getElementById("'.$objet.$selection.'").scrollIntoView(true); </script>' ;
	}
	return $res;
}

function association_bloc_tr($query, $extra, $cle1, $cle2, $objet, $presentation, $boutons, $selection) {
	$nbr_lignes = 0;
	$nbr_couleurs = count($extra);
	$class_sup = (is_array($extra) AND $nbr_couleurs);
	$res ='';
	while ($data = sql_fetch($query)) {
		if ($class_sup) { // on a  un tableau de classes supplementaires
			if ( $cle2 ) { // lignes colorees selon les valeurs d'un champ
				$tr_css = $extra[$data[$cle2]];
			} else { // simple alternance de couleurs
				$nbr_lignes++;
				$tr_css = $extra[$nbr_lignes%$nbr_couleurs];
			}
		} elseif ( $extra ) { // classe supplementaire appliquee inconditionnellement
				$tr_css = $extra;
		} else $tr_css = '';
		if ( $cle1 && $data[$cle1]==$selection ) {
			$tr_css = 'surligne';
		}
		$res .= '<tr'. ($cle1?' id="'.$objet.$data[$cle1].'"':'') . ($tr_css?' class="'.$tr_css.'"':'') .'>' .
		association_bloc_format($presentation, $data, $cle1, $selection).
		association_bloc_bouton($boutons, $data[$cle1]) .
		"</tr>\n";
	}
	return $res;
}

function association_bloc_format($presentation, $data, $cle1, $selection) {
	$res = '';
	foreach ($presentation as $champ=>$params) {
		$format = array_shift($params);
		switch ($format) {
				case 'date' :
				case 'heure' :
					$td_css = 'date';
					break;
				case 'duree' :
				case 'nombre' :
				case 'prix' :
					$td_css = 'decimal';
					break;
				case 'entier' :
					$td_css = 'integer';
					$format = 'nombre'; $params = array(0);
					break;
				case 'puce' :
				case 'logo' :
					$td_css = 'image';
					break;
				case 'code' :
				case 'texte' :
				default :
					$td_css = 'text';
					break;
		}
		if ( $data[$cle1]==$selection )
			$td_css .= ' surligne';
		array_unshift($params, $data[$champ]);
		$format = call_user_func_array("association_formater_$format", $params);
		$res .= '<td class="'.$td_css.'">'. $format ."</td>\n";
	}
	return $res;
}

function association_bloc_bouton($boutons, $champ) {
	$res = '';
	foreach ($boutons as $params) {
		$type = array_shift($params);
		foreach ($params as &$param) {
			$param = str_replace('$$', $champ, $param);
		}
		$res .= call_user_func_array("association_bouton_$type", $params);
	}
	return $res;
}


/*****************************************
 * @defgroup association_passeparam
 * Les champs passes aux "exec" par l'URL etant normalises pour les filtres,
 * ils partagent le meme code de passage de valeur et les memes noms de parametres
 * (ce qui n'est pas le cas avec association_recuperer_ !)
 *
 * @param string $type
 *   Type d'objet|page pour lequel on passe le parametre en question.
 * @param string $objet
 *   Nom de la table (sans prefixe "spip") contenant la collection d'objets.
 *   Sa presence indique de retourner des parametres supplementaires et/ou de
 *   faire des controles supplementaires. Ce parametre est surtout utlise dans les pages d'edition/suppression
 * @return string|array $res
 *   Valeur du request...
 *   Ou une liste comportant la valeur du parametre au debut et d'autres valeurs
 *   utiles induites.
 *
** @{ */


/**
 * &id=
 *
 * @return int $id
 *
 */
function association_passeparam_id($type='') {
	if ($type) // recuperer en priorite : id_compte, id_don, id_evenement, id_ressource, id_vente, etc.
		$id = intval(_request("id_$type", $_GET));
	else
		$id = 0;
	// si pas d'id_... alors c'est le nom generique qui est utilise
	return $id ? $id : intval(_request('id'));
}

/**
 * Retourne la ligne SQL correspondant a la table donnee et au ID dans l'URL
 * et controle l'autorisation
 *
 * @param string $type
 *   Nom de l'ID
 * @param string $table
 *   Nom de la table
 * @param string $controle
 *   Nom du verbe d'autorisation
 * @param string $controle
 *   Nom de l'objet d'autorisation
 * @return array(int, array) | array()
 *   Numero de l'ID et ligne correspondante dans la table si ok, vide sinon
 *
 */
function association_controle_id($type, $table, $controle='') {
	if ($id = association_passeparam_id($type)) {
		include_spip('base/association');
		$trouver_table = charger_fonction('trouver_table', 'base');
		$table = "spip_$table";
		$desc = $trouver_table($table, $serveur);
		$id_table = $desc['key']["PRIMARY KEY"];
		$type = sql_fetsel('*', $table, "$id_table=$id");
	}
	// Si ok, $type est devenu $type la ligne.
	if ($id AND $type AND (!$controle OR autoriser($controle, 'association', $id)))
		return array($id, $type);
	include_spip('inc/minipres');
	// $type est un tableau ssi autorisation fautive
	echo minipres(is_array($type) ? '' :  _T('zxml_inconnu_id', array('id'=>$id)));
	return array();
}

/**
 * &annee=
 *
 * @return int $an
 * @return array($an, $sql_where)
 */
function association_passeparam_annee($type='', $objet='', $id=0) {
	if ($type) // recuperer en priorite :
		$an = intval(_request("annee_$type", $_GET));
	else
		$an = 0;
	if (!$an) // pas d'annee_... alors c'est le nom generique qui est utilise
		$an = intval(_request('annee'));
	if (!$an) // annee non precisee
		$an = date('Y'); // on prend l'annee courante
	if ($type && $objet) {
//		$desc_table = charger_fonction('trouver_table', 'base');
		if ($id) { // on veut un enregistrement precis : on ne va pas tenir compte de la l'annee passee en requete...
			$an = sql_getfetsel("DATE_FORMAT(date_$type, '%Y')", table_objet_sql($objet), id_table_objet($objet).'='.sql_quote($id) ); // ...on recupere l'annee correspondante a l'enregistrement recherche
		} else { // on peut faire mieux que prendre l'annee courante ou une annee farfelue passee en parametre
			$an = min(sql_getfetsel("MAX(DATE_FORMAT(date_$type, '%Y')) AS an_max", table_objet_sql($objet), ''), $an);
			$an = max(sql_getfetsel("MIN(DATE_FORMAT(date_$type, '%Y')) AS an_min", table_objet_sql($objet), ''), $an);
		}
		if (!$an) // ID inexistant (donc annee non trouvee) ou table vide (du coup annee vide)
			$an = date('Y'); // on prend l'annee courante retomber sur nos pattes et surtout ne pas fausser la requete
		return array($an, "DATE_FORMAT(date_$type, '%Y')=$an");
	} else
		return $an;
}

/**
 * &exercice=
 *
 * @return int $exo
 * @return array($exo, $sql_where)
 */
function association_passeparam_exercice($type='', $objet='', $id=0) {
	$exo = intval(_request('exercice'));
	if (!$exo) // exercice non precise
		$exo = intval(sql_getfetsel('id_exercice','spip_asso_exercices','','','date_debut DESC')); // on recupere le dernier exercice en date
	if ($type && $objet) {
		if ($id) { // on veut un enregistrement precis : on ne va pas tenir compte de l'exercice passe en requete...
			$dt = sql_getfetsel("date_$type", table_objet_sql($objet), id_table_objet($objet).'='.sql_quote($id) ); // ...on recupere la date correspondante a l'enregistrement recherche
			$exercice = sql_fetsel('*','spip_asso_exercices', "date_debut<='$dt' AND date_fin>='$dt'", '','date_debut DESC'); // on recupere le dernier exercice correspondant
		}
		if (!$exercice) { // pas d'ID ou table vide ou mauvais ID
			$exercice = sql_fetsel('*','spip_asso_exercices', "id_exercice=$exo"); // on recupere l'exercice indique
		}
		return array($exercice['id_exercice'], "date_$type>='$exercicee[date_debut]' AND date_$type<='$exercice[date_fin]'");
	} else
		return $exo;
}

/**
 * &statut=
 *
 * @return string $statut
 * @return array($statut, $sql_where)
 * Pour l'instant, appele uniquement dans exec/adherents.php vers la ligne 25
 */
function association_passeparam_statut($type='', $defaut='') {
	if ($type) // recuperer en priorite :
		$statut = trim(_request("statut_$type", $_GET));
	else
		$statut = '';
	if (!$statut) // pas de statut_... alors c'est le nom generique qui est utilise
		$statut = trim(_request('statut'));
	if (!$statut) // statut non precise non precisee
		$statut = $defaut; // on prend celui par defaut (tous)
	if ($defaut && $type) {
		switch ($type) {
			case 'interne' :
				if (in_array($statut, $GLOBALS['association_liste_des_statuts'] ))
					$sql_where = 'statut_interne='. sql_quote($statut);
				elseif ($statut=='tous')
					$sql_where = "statut_interne LIKE '%'";
				else {
					set_request('statut_interne', $defaut);
					$statuts = $GLOBALS['association_liste_des_statuts'];
					$exclure = array_shift($statuts);
					$sql_where = sql_in('statut_interne', $statuts);
				}
				break;
		}
		return array($statut, $sql_where);
	} else
		return $statut;
}

/**
 * &exercice= | &annee=
 *
 * @see association_passeparam_annee
 * @see association_passeparam_exercice
 */
function association_passeparam_periode($type='', $objet='', $id=0) {
	$PeriodeCompatable = 'association_passeparam_'.($GLOBALS['association_metas']['exercices']?'exercice':'annee');
	return $PeriodeCompatable($type, $objet, $id);
}

/** @} */


/*****************************************
 * @defgroup association_chargeparam
 * Charger des parametres comptables dans le contexte d'un formulaire
 *
 * @param string $type
 * @param int $id
 * @param array &$contexte
 * @return array $contexte
 *   Valeur(s) a charger.
 *
** @{ */

function association_chargeparam_operation($type, $id, &$contexte) {
	if ( $id ) { // si c'est une modification, on charge ses id_compte et journal depuis la table asso_comptes
		list($id_compte, $journal) = sql_fetsel('id_compte, journal', 'spip_asso_comptes', 'imputation='. sql_quote($GLOBALS['association_metas']["pc_$type"]) .' AND id_journal='. sql_quote($id) ); // on recupere id_compte et journal dans la table des asso_compteS
	} else {  // si c'est une nouvelle operation, on charge id_compte et journal vides
		$id_compte = $journal = '';
	}
	$contexte['journal'] = $journal; // ajoute le  journal qui ne se trouve pas dans la table chargee par editer_objet_charger mais dans asso_comptes plutot
	$contexte['_hidden'] .= "<input type='hidden' name='id_compte' value='$id_compte' />"; // on concatene aux _hidden de $contexte , id_compte qui sera utilise dans l'action
	$contexte['id_compte'] = $id_compte; // sera utilise par association_chargeparam_destinations()
	return $contexte;
}

function association_chargeparam_destinations($type, &$contexte) {
	if ($GLOBALS['association_metas']['destinations'] AND $contexte['id_compte']) {
		// Recuperer les destinations associees a id_compte
		// pour ajouter au contexte : id_dest, montant_dest, defaut_dest
		// ces variables sont recuperees par la balise dynamique
		include_spip('inc/association_comptabilite');
		$dest = association_liste_destinations_associees($contexte['id_compte']);
		if ($dest) {
			$contexte['id_dest'] = array_keys($dest);
			$contexte['montant_dest'] = array_values($dest);
		} else {
			$contexte['id_dest'] = '';
			$contexte['montant_dest'] = '';
		}
		$contexte['defaut_dest'] = ($type ? $GLOBALS['association_metas']["dc_$type"] : '');
	}
	return $contexte;
}

/** @} */


/*****************************************
 * @defgroup divers
 * Inclassables
 *
** @{ */

/**
 * Affichage du message indiquant la date
 * (et l'heure si option activee)
 *
 * @param bool $phraser
 *   Indique si l'horodatage est insere dans la chaine de langue prevue a cet
 *   effet (vrai, par defaut) ou s'il est renvoye seul (faux)
 * @return string $res
 */
function association_date_du_jour($phraser=TRUE) {
	$frmt_m = date('Y-m-d'. (_ASSOCIASPIP_AUJOURDHUI_HORAIRE?'\TH:i:s':'') ); // format machine-parsable : idealement "\TH:i:s.uP" mais il faut PHP "up"date (plus precisement 5.1.0 pour "e" et 5.1.3 pour "P" et 5.2.0 pour "u")
	$format = 'affdate_'. (_ASSOCIASPIP_AUJOURDHUI_HORAIRE?'heure':'base');
	$frmt_h = $format($frmt_m, 'entier');  // format human-readable
	if ( $phraser )
		return '<p class="clear date">'. _T('asso:date_du_jour', array('date'=> (@$GLOBALS['meta']['html5']?'<time datetime="':'<abbr title="'). $frmt_m.'">'.$frmt_h. (@$GLOBALS['meta']['html5']?'</time>':'</abbr>') ) ) .'</p>';
	else
		return $frmt_h;
}

/**
 * Injection de "association.css" dans le "header" de l'espace prive
 * @param string $flux
 * @return string $c
 */
function association_header_prive($flux) {
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
function affichage_div($type_operation, $list_operation) {
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
function association_trouver_iextras($ObjetEtendu, $id=0) {
	$champsExtrasVoulus = array();
/*
	if (test_plugin_actif('IEXTRAS')) { // le plugin "Interfaces pour ChampsExtras2" est installe et active : on peut donc utiliser les methodes/fonctions natives...
		include_spip('inc/iextras'); // charger les fonctions de l'interface/gestionnaire (ce fichier charge les methode du core/API)
		include_spip('inc/cextras_gerer'); // semble necessaire aussi
		if ($id)
			include_spip('cextras_pipelines'); // pour eviter le "Fatal error : Call to undefined function cextras_enum()" en recuperant un fond utilisant les enum...
		$ChampsExtrasGeres = iextras_get_extras_par_table(); // C'est un tableau des differents "objets etendus" (i.e. tables principaux SPIP sans prefixe et au singulier -- par exemple la table 'spip_asso_membres' correspond a l'objet 'asso_membre') comme cle.
		foreach ($ChampsExtrasGeres[$ObjetEtendu] as $ChampExtraRang => $ChampExtraInfos ) { // Pour chaque objet, le tableau a une entree texte de cle "id_objet" et autant d'entrees tableau de cles numerotees automatiquement (a partir de 0) qu'il y a de champs extras definis.
			if ( is_array($ChampExtraInfos) ) { // Chaque champ extra defini est un tableau avec les cle=>type suivants : "table"=>string, "champ"=>string, "label"=>string, "precisions"=>string, "obligatoire"=>string, "verifier"=>bool, "verifier_options"=>array, "rechercher"=>string, "enum"=>string, "type"=>string, "sql"=>string, "traitements"=>string, "saisie_externe"=>bool, "saisie_parametres"]=>array("explication"=>string, "attention"=>string, "class"=> string, "li_class"]=>string,)
				$label = _TT($ChampExtraInfos['label']); // _TT est defini dans cextras_balises.php
				if ( $id ) {
					$desc_table = charger_fonction('trouver_table', 'base');
					$champs = $desc_table("spip_$ChampExtraInfos[table]s");
					$datum_raw = sql_getfetsel($ChampExtraInfos['champ'], table_objet_sql($ChampExtraInfos['table']), $champs['key']['PRIMARY KEY'].'='.sql_quote($id) ); // on recupere les donnees...
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
		} else */ { // le plugin "Interfaces pour ChampsExtras2" n'est pas actif :-S Mais peut-etre a-t-il ete installe ?

		$ChampsExtrasGeres = @unserialize(str_replace('O:10:"ChampExtra"', 'a', $GLOBALS['meta']['iextras'])); // "iextras (interface)" stocke la liste des champs geres dans un meta. Ce meta est un tableau d'objets "ChampExtra" (un par champ extra) manipules par "cextras (core)". On converti chaque objet en tableau
		if ( !is_array($ChampsExtrasGeres) )
			return array(); // fin : ChampsExtras2 non installe ou pas d'objet etendu.
		$TT = function_exists('_T_ou_typo') ? '_T_ou_typo' : '_T' ; // Noter que les <multi>...</multi> et <:xx:> sont aussi traites par propre() et typo() :  http://contrib.spip.net/PointsEntreeIncTexte
		foreach ($ChampsExtrasGeres as $ChampExtra) { // Chaque champ extra defini est un tableau avec les cle=>type suivants (les cles commencant par "_" initialisent des methodes de meme nom sans le prefixe) : "table"=>string, "champ"=>string, "label"=>string, "precisions"=>string, "obligatoire"=>string, "verifier"=>bool, "verifier_options"=>array, "rechercher"=>string, "enum"=>string, "type"=>string, "sql"=>string, "traitements"=>string, "_id"=>string, "_type"=>string, "_objet"=>string, "_table_sql"=>string, "saisie_externe"=>bool, "saisie_parametres"]=>array("explication"=>string, "attention"=>string, "class"=> string, "li_class"]=>string,)
			if ($ChampExtra['table']==$ObjetEtendu) {// c'est un champ extra de la 'table' ou du '_type' d'objet qui nous interesse
				$label = $TT($ChampExtra['label']);
				if ( $id ) {
					$datum_raw = sql_getfetsel($ChampExtra['champ'], $ChampExtra['_table_sql'], id_table_objet($ChampExtra['_type']).'='.intval($id) ); // on recupere les donnees...
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
						case 'asso_categorie' :
						case 'asso_compte' :
						case 'asso_exercice' :
						case 'asso_membre' :
						case 'asso_ressource' :
							$raccourci = substr($ChampExtra['type'], 4); // on vire le prefixe "asso_"
							if ( $ChampExtra['traitement'] )
								$datum_parsed = $ChampExtra['traitement']('[->'.$raccourci.$datum_raw.']');
							else { // il faut une requete de plus
								switch ($raccourci) { // $valeur prend ici le champ SQL contenant la valeur desiree.
									case 'categorie' :
										$valeur = 'libelle';
										break;
									case 'compte' :
										$valeur = 'justification';
										break;
									case 'exercice' :
										$valeur = 'intitule';
										break;
									case 'membre' :
										$valeur = 'nom_famille'; // il faudrait "concatener" : nom_famille, prenom, sexe ; le tout en fonction des metas... mais http://sql.1keydata.com/fr/sql-concatener.php
										break;
									case 'ressource' :
										$valeur = 'intitule';
										break;
									default :
										$valeur = 'titre'; // sauf coincidence heurese, on devrait avoir une erreur...
										break;
								}
								$datum_parsed = association_formater_idnom($datum_raw, array(table_objet_sql($ChampExtra[type]), $valeur, 'id_'.$raccourci) , ''); // on recupere la donnee grace a la cle etrangere... (il faut que la table soit suffixee de "s" et que l'identifiant soit l'objet prefixe de "id_" :-S)
							}
							break;
						case 'asso_activite' :
						case 'asso_don' :
						case 'asso_vente' :
							$raccourci = substr($ChampExtra['type'], 4); // on vire le prefixe "asso_"
							if ( $ChampExtra['traitement'] )
								$datum_parsed = $ChampExtra['traitement']('[->'.$raccourci.$datum_raw.']');
							else
								$datum_parsed = _T('asso:objet_num', array('objet'=>$raccourci, 'num'=>$datum_raw) );
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
	}
	return $champsExtrasVoulus;
}

/**
 * Encapsulation de _T()
 *
 * @param string|array $chaine
 *   Chaine de langue avec eventuellement le prefixe "asso" omis
 *   Liste comportant la chaine de langue (prefixe "asso" optionnel) et les parametres
 * @return string
 *   Libelle localise
 */
function association_langue($chaine) {
	if ( is_string($chaine) ) {
		$head = $chaine;
		$tail = array();
	} elseif ( is_array($chaine) ) {
		$head = array_shift($chaine);
		$tail = $chaine;
	} else
		return '';
	return _T((strpos($head,':') ? '' : 'asso:').$head, $tail );
}

/**
 * Encapsulation de autoriser()
 *
 * @param string|array $aut
 *   Valeur de l'autorisation
 *   Liste des composantes de l'autorisation
 * @return bool
 *   Autorisation d'acces
 */
function association_acces($aut) {
	if ( is_array($aut) && count($aut) ) { // autorisation a calculer
		return call_user_func_array('autoriser', $aut);
	} elseif ( is_scalar($aut) ) { // autorisation deja calculee (chaine ou entier ou booleen, evalue en vrai/faux...)
		return autoriser($aut);
	} else // pas d'autorisation definie = autorise pour tous
		return '';
}

function association_langue_index($index, $head) {
	return _T((strpos($head,':') ? '' : 'asso:'). $head . $index);
}

/** @} */


// pour executer les squelettes comportant la balise Meta
include_spip('balise/meta');

// charger les metas donnees
$inc_meta = charger_fonction('meta', 'inc'); // inc_version l'a deja chargee
$inc_meta('association_metas');



?>