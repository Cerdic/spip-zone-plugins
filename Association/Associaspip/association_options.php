<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault            *
 *  Copyright (c) 2010 Emmanuel Saint-James                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Le premier element indique un ancien membre
$GLOBALS['association_liste_des_statuts'] =
  array('sorti','prospect','ok','echu','relance');

$GLOBALS['association_styles_des_statuts'] = array(
	'echu' => 'impair',
	'ok' => 'valide',
	'prospect' => 'prospect',
	'relance' => 'pair',
	'sorti' => 'sortie'
);

define('_DIR_PLUGIN_ASSOCIATION_ICONES', _DIR_PLUGIN_ASSOCIATION.'img_pack/');

// gros lien=bouton+texte de raccourci dans la colonne gauche/droite
function association_icone($texte, $lien, $image, $sup='rien.gif')
{
	return icone_horizontale($texte, $lien, _DIR_PLUGIN_ASSOCIATION_ICONES. $image, $sup, false);
}

// boutons d'action (si page de script indiquee) dans les listing
function association_bouton($texte, $image, $script='', $args='', $img_attributes='')
{
	$res = ($script ? '<a href="'.generer_url_ecrire($script, $args).'">' : '' );
	$res .= '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.$image.'" alt="';
	$res .= ($texte ? _T('asso:'.$texte).'" title="'._T('asso:'.$texte) : ' ' );
	$res .= '" '.$img_attributes.' />';
	$res .= ($script?'</a>':'');
	return $res;
}

// bloc de raccourci constitue uniquement du bouton retour
function association_retour($adresse_retour='')
{
	return bloc_des_raccourcis(association_icone(_T('asso:bouton_retour'),  ($adresse_retour=='')?str_replace('&', '&amp;', $_SERVER['HTTP_REFERER']):$adresse_retour, 'retour-24.png'));
}

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

function association_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut']=='0minirezo' && $GLOBALS['connect_toutes_rubriques']) {
		$menu = 'naviguer';
		$icone = 'annonce.gif';
		if (isset($boutons_admin['bando_reactions'])){
			$menu = 'bando_reactions';
			$icone = 'annonce.gif';
		}
		$boutons_admin[$menu]->sousmenu['association']= new Bouton(
			_DIR_PLUGIN_ASSOCIATION_ICONES.$icone,  // icone
			_T('asso:titre_menu_gestion_association') //titre
			);
	}
	return $boutons_admin;
}

// recupere dans une chaine un champ d'une table spip_asso_XX pour un enregistrement identifie par son id_XX
function sql_asso1champ($table, $id, $champ) {
	$data = sql_fetsel($champ, "spip_asso_{$table}s", "id_$table=".intval($id));
	return $data[$champ];
}

// recupere dans un tableau associatif un enregistrement d'une table spip_asso_XX identifie par son id_XX
function sql_asso1ligne($table, $id) {
	$data = sql_fetsel('*', "spip_asso_{$table}s", "id_$table=".intval($id));
	return $data;
}

# ensemble de fonctions pour recuperer les donnees de l'exercice en cours
function exercice_intitule($exercice) {
	return sql_asso1champ('exercice', $exercice, 'intitule');
}
function exercice_date_debut($exercice) {
	return sql_asso1champ('exercice', $exercice, 'debut');
}
function exercice_date_fin($exercice) {
	return sql_asso1champ('exercice', $exercice, 'fin');
}

// Affichage micro-formate d'un nom complet (de membre) suivant la configuration du plugin (i.e. champs geres ou non)
function association_calculer_nom_membre($civilite, $prenom, $nom, $html_tag='') {
	$res = '';
	if ($html_tag) {
		$res = '<'.$html_tag.' class="'. (($civilite || $prenonm)?'n':'fn') .'">';
	}
	if ($GLOBALS['association_metas']['civilite']=='on' && $civilite) {
		$res .= ($html_tag?'<span class="honorific-prefix">':'') .$civilite. ($html_tag?'</span>':'') .' ';
	}
	if ($GLOBALS['association_metas']['prenom']=='on' && $prenom) {
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

// Affichage (dans un listing) du nom avec le lien vers la page correspondante
// En fait c'est pour les modules dons/ventes/activites/prets ou l'acteur (donateur/acheteur/inscrit/emprunteur)
// peut etre un membre/auteur (son id_acteur est alors renseigne) mais pas forcement son nom (qui peut etre different)
// ou peut etre une personne exterieure a l'association (on a juste le nom obligatoire alors)
function association_calculer_lien_nomid($nom, $id, $type='membre', $html_tag='') {
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

// Affichage de date localisee et micro-formatee
function association_datefr($iso_date, $css_class='', $htm_abbr='abbr')
{
	$res = ($css_class?"<$htm_abbr class='$css_class' title='$iso_date'>":'');
	$res .= affdate_base($iso_date, 'entier'); // on fait appel a la fonction centrale des filtres SPIP... comme ca c'est traduit et formate dans les langues supportees ! si on prefere les mois en chiffres et non en lettre, y a qu'a changer les chaines de langue date_mois_XX
	$res .= ($css_class?"</$htm_abbr>":'');
	return $res;
}

function association_verifier_date($date)
{
	if (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $date))
		return _T('asso:erreur_format_date');
	list($annee, $mois, $jour) = explode('-',$date);
	if (!checkdate($mois, $jour, $annee))
		return _T('asso:erreur_date');
	return;
}

// Affichage de duree localisee et micro-formatee
// Nota: les cas de minutes/secondes doivent etre specifie comme des heures au format ISO...
function association_dureefr($nombre, $unite='', $htm_abbr='abbr')
{
	$frmt_h = '';
	$frmt_m = 'P';
	switch(strtoupper($unite)) { // http://ufxtract.com/testsuite/documentation/iso-duration.htm
		case 'Y' : // year
		case 'A' : // annee
			$nombre = intval($nombre);
			$frmt_m .= $nombre.'Y';
			$valeur = association_nbrefr($nombre,0);
			$unite = ($nombre<=1) ? _T('spip:date_une_annee') : _T('spip:date_annees');
			break;
		case 'M' : // month/mois
			$nombre = intval($nombre);
			$frmt_m .= $nombre.'M';
			$valeur = association_nbrefr($nombre,0);
			$unite = ($nombre<=1) ? _T('spip:date_un_mois') : _T('spip:date_mois');
			break;
		case 'W' : // week
		case 'S' : // semaine
			$nombre = intval($nombre);
			$frmt_m .= $nombre.'W';
			$valeur = association_nbrefr($nombre,0);
			$unite = ($nombre<=1) ? _T('spip:date_une_semaine') : _T('spip:date_semaines');
			break;
		case 'D' : // day
		case 'J' : // jour
			$nombre = intval($nombre);
			$frmt_m .= $nombre.'D';
			$valeur = association_nbrefr($nombre,0);
			$unite = ($nombre<=1) ? _T('spip:date_un_jour') : _T('spip:date_jours');
			break;
		case 'H' : // hour/heure
			$frmt_m .= 'T'.str_replace('00M', '',  str_replace(':','H',$nombre.':00').'M' );
			$valeur = association_nbrefr($nombre,0);
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
					$frmt_h = _T('duree_temps', array('nombre'=>1,'unite'=>$_T('spip:date_un_jour')));
					break;
				default:
					$frmt_h =  _T('duree_temps', array('nombre'=>association_nbrefr($nommbre['-1'],0),'unite'=>$_T('spip:date_jours')));
					break;
			}
			if ($nombre[0])
				$frmt_h .= ', ';
			switch($nombre[0]) { // nombre d'heures
				case 0:
					$frmt_h .= '';
					break;
				case 1:
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>$_T('spip:date_une_heure')));
					break;
				default:
					$frmt_h .= _T('duree_temps', array('nombre'=>association_nbrefr($nombre[0],0),'unite'=>$_T('spip:date_heures')));
					break;
			}
			if ($nombre[1])
				$frmt_h .= ', ';
			switch($nombre[1]) { // nombre de minutes
				case 0:
					$frmt_h .= '';
					break;
				case 1:
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>$_T('spip:date_une_minute')));
					break;
				default:
					$frmt_h .= _T('duree_temps', array('nombre'=>association_nbrefr($nombre[1],0),'unite'=>$_T('spip:date_minutes')));
					break;
			}
			if ($nombre[2])
				$frmt_h .= ', ';
			switch($nombre[2]) { // nombre de secondes
				case 0:
					$frmt_h .= '';
					break;
				case 1:
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>$_T('spip:date_une_seconde')));
					break;
				default:
					$frmt_h .= _T('duree_temps', array('nombre'=>association_nbrefr($nombre[2],0),'unite'=>$_T('spip:date_secondes')));
					break;
			}
			$frmt_h .= '. ';
			break;
		default : // (full) ISO DateTime or Date : no check !!!
			$frmt_m .= $nombre;
			$nombre = explode('T',$nombre,2);
			$ladate = explode(':',$nombre[0]);
			switch($ladate[0]) { // nombre d'annee
				case 0:
				case '':
					$frmt_h = '';
					break;
				case 1:
					$frmt_h = _T('duree_temps', array('nombre'=>1,'unite'=>$_T('spip:date_une_annee')));
					break;
				default:
					$frmt_h =  _T('duree_temps', array('nombre'=>association_nbrefr($ladate[0],0),'unite'=>$_T('spip:date_annees')));
					break;
			}
			if ($ladate[1])
				$frmt_h .= ', ';
			switch($ladate[1]) { // nombre de mois
				case 0:
					$frmt_h .= '';
					break;
				case 1:
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>$_T('spip:date_un_mois')));
					break;
				default:
					$frmt_h .= _T('duree_temps', array('nombre'=>association_nbrefr($ladate[1],0),'unite'=>$_T('spip:date_mois')));
					break;
			}
			if ($ladate[2])
				$frmt_h .= ', ';
			switch($ladate[2]) { // nombre de jours
				case 0:
					$frmt_h .= '';
					break;
				case 1:
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>$_T('spip:date_un_jour')));
					break;
				default:
					$frmt_h .= _T('duree_temps', array('nombre'=>association_nbrefr($ladate[2],0),'unite'=>$_T('spip:date_jours')));
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
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>$_T('spip:date_une_heure')));
					break;
				default:
					$frmt_h .=  _T('duree_temps', array('nombre'=>association_nbrefr($lheure[0],0),'unite'=>$_T('spip:date_heures')));
					break;
			}
			if ($lheure[1])
				$frmt_h .= ', ';
			switch($lheure[1]) { // nombre d'heures
				case 0:
					$frmt_h .= '';
					break;
				case 1:
					$frmt_h .= _T('duree_temps', array('nombre'=>1,'unite'=>$_T('spip:date_une_minute')));
					break;
				default:
					$frmt_h .=  _T('duree_temps', array('nombre'=>association_nbrefr($lheure[1],0),'unite'=>$_T('spip:date_minutes')));
					break;
			}
			if ($lheure[2])
				$frmt_h .= ', ';
			switch($lheure[2]) { // nombre d'heures
				case 0:
					$frmt_h = '';
					break;
				case 1:
					$frmt_h = _T('duree_temps', array('nombre'=>1,'unite'=>$_T('spip:date_une_seconde')));
					break;
				default:
					$frmt_h =  _T('duree_temps', array('nombre'=>association_nbrefr($lheure[2],0),'unite'=>$_T('spip:date_secondes')));
					break;
			}
			$frmt_h .= '. ';
			break;
	}
	if (!$frmt_h)
		$frmt_h = _T('asso:duree_temps', array('nombre'=>$valeur, 'unite'=>$unite) );
	return "<$htm_abbr class='duration' title='$frmt_m'>$frmt_h</$htm_abbr>";
}

// micro-Formatage des montants avec devise
// on n'utilise pas la fontcion PHP money_format() --qui ne fonctionne pas sous Windows-- car on veut micro-formater avec une devise fixee par la configuration (en fait les chaines de langue) du plugin
function association_prixfr($montant, $unite_code='', $unite_nom='', $htm_span='span', $htm_abbr='abbr') {
	$res = "<$htm_span class='money price'>"; // pour la reference est "price" <http://microformats.org/wiki/hproduct> (reconnu par les moteurs de recherche), mais "money" <http://microformats.org/wiki/currency-brainstorming> est d'usage courant aussi
	$montant = "<$htm_abbr class='amount' title='$montant'>". association_nbrefr($montant) ."</$htm_abbr>";
	$devise = "<$htm_abbr class='currency' title='". _T('asso:devise_code_iso') .'\'>'. _T('asso:devise_symbole') ."</$htm_abbr>";
	$res .= _T('asso:devise_montant', array('montant'=>$montant, 'devise'=>$devise) );
	$res .= ($unite_code?" <$htm_abbr class='unit' title='".($unite_nom?$unite_nom:$unite_code)."'>$unite_code</$htm_abbr>":'');
	return "$res</$htm_span>";
}

// Formatage des nombres selon la langue de l'interface
function association_nbrefr($montant, $decimales=2, $l10n='')
{
	/** recuperer le code des parametres regionnaux a utiliser
	 * dans un premier temps, on essaye d'utiliser la langue puisque SPIP gere bien cela et offre la possibilite d'en faire plus avec  http://programmer.spip.org/Forcer-la-langue-selon-le-visiteur
	 * comme ce n'est pas suffisant (le code de localisation est de la forme langue-pays ou langue_PAYS en utilisant les codes ISO), et recuperer le pays n'est pas simple sans faire appel a l'IP-geolocalisation http://stackoverflow.com/questions/2156231/how-do-you-detect-a-website-visitors-country-specifically-us-or-not
	 * ni SPIP ni PHP n'offrant de moyen "simple" d'arriver a nos fin bah...
	 **/
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
	/** formater selon la langue choisie/recuperee
	 * @: http://stackoverflow.com/a/437642
	 **/
	setlocale(LC_NUMERIC, $l10n);
	$locale = localeconv();
    return number_format(floatval($montant), $decimales, $locale['decimal_point'], $locale['thousands_sep']);
}

/* prend en parametre le nom de l'argument a chercher dans _request et retourne un float */
function association_recupere_montant ($valeur) {
	if ($valeur!='') {
		$valeur = str_replace(' ', '', $valeur); /* suppprime les espaces separateurs de milliers */
		$valeur = str_replace(',', '.', $valeur); /* convertit les , en . */
		$valeur = floatval($valeur);
	} else
		$valeur = 0.0;
	return $valeur;
}

	//Affichage du message indiquant la date
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

function association_header_prive($flux){
	$c = direction_css(find_in_path('association.css'));
	return "$flux\n<link rel='stylesheet' type='text/css' href='$c' />";
}

function association_delete_tables($flux){
  spip_unlink(cache_meta('association_metas'));
}

// Filtre pour "afficher" ou "cacher" un bloc div
// Utilise dans le formulaire cvt "editer_asso_comptes.html"
function affichage_div($type_operation,$list_operation) {
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

function encadre($texte,$avant='[',$apres=']') {
    return ($texte=='')?'':$avant.$texte.$apres;
}

// Raccourcis
// Les tables ayant 2 prefixes ("spip_asso_")
// le raccourci "don" implique de declarer le raccourci "asso_don" etc.

function generer_url_asso_don($id, $param='', $ancre='') {
	return  generer_url_ecrire('edit_don', 'id='.intval($id));
}
function generer_url_don($id, $param='', $ancre='') {
	return  array('asso_don', $id);
}

function generer_url_asso_membre($id, $param='', $ancre='') {
	return  generer_url_ecrire('voir_adherent', 'id='.intval($id));
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

function instituer_adherent_ici($auteur=array()){
	$instituer_adherent = charger_fonction('instituer_adherent', 'inc');
	return $instituer_adherent($auteur);
}
function instituer_statut_interne_ici($auteur=array()){
	$instituer_statut_interne = charger_fonction('instituer_statut_interne', 'inc');
	return $instituer_statut_interne($auteur);
}


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