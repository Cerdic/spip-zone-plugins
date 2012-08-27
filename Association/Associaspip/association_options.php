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

// cas des boutons de vue non modifiable ou apercu dans les listing
function association_bouton_afficher($objet, $args='', $tag='td')
{
	$res = ($tag?"<$tag class='action'>":'');
	$res .= association_bouton('bouton_voir', 'voir-12.png', "voir_$objet", is_numeric($args)?"id=$args":$args, 'width="12" height="12" alt="&#x2380;"');
	$res .= ($tag?"</$tag>":'');
	return $res;
}

// cas des boutons d'edition (modification) dans les listing
function association_bouton_modifier($objet, $args='', $tag='td')
{
	$res = ($tag?"<$tag class='action'>":'');
	$res .= association_bouton('bouton_modifier', 'edit-12.gif', "edit_$objet", is_numeric($args)?"id=$args":$args, 'width="12" height="12" alt="&#x2380;"');
	$res .= ($tag?"</$tag>":'');
	return $res;
}

// cas des boutons d'effacement (suppression) dans les listing
// ToDo: voir s'il est possible d'utiliser plutot la fonction bouton_action($libelle, $url, $class="", $confirm="", $title="") definie dans /ecrire/inc/filtres.php
function association_bouton_supprimer($objet, $args='', $tag='td')
{
	$res = ($tag?"<$tag class='action'>":'');
	$res .= association_bouton('bouton_supprimer', 'suppr-12.gif', "suppr_$objet", is_numeric($args)?"id=$args":$args, 'width="12" height="12" alt="&#x2327;"'); // 8 pluriel contre 3 singulier
	$res .= ($tag?"</$tag>":'');
	return $res;
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

// demande de confirmation dans
function bloc_confirmer_suppression($type,$id,$retour='')
{
	$res = '<p><strong>'. _T('asso:vous_aller_effacer', array('quoi'=>'<i>'._T('asso:objet_num',array('objet'=>$type,'num'=>$id)).'</i>') ) .'</strong></p>';
	$res .= '<p class="boutons"><input type="submit" value="'._T('asso:bouton_confirmer').'" /></p>';
	echo redirige_action_post("supprimer_{$type}s", $id, ($retour?$retour:$type.'s'), '', $res);

}

// recupere dans une chaine un champ d'une table spip_asso_XXs pour un enregistrement identifie par son id_XX
// un dernier drapeau mis a FALSE permet de traiter le cas des tables _plan|destination|destination_op qui n'ont pas de "s" final...
/* conversion d'anciennes fonctions :
 * exercice_intitule($exo) <=> sql_asso1champ('exercice', $exo, 'intitule')
 * exercice_date_debut($exercice) <=> sql_asso1champ('exercice', $exercice, 'debut')
 * exercice_date_fin($exercice) <=> sql_asso1champ('exercice', $exercice, 'fin')
 */
function sql_asso1champ($table, $id, $champ, $pluriel=TRUE)
{
	return sql_getfetsel($champ, "spip_asso_$table".($pluriel?'s':''), "id_$table=".intval($id));
}

// recupere dans un tableau associatif un enregistrement d'une table spip_asso_XX identifie par son id_XX
// un dernier drapeau mis a FALSE permet de traiter le cas des tables _plan|destination|destination_op qui n'ont pas de "s" final...
function sql_asso1ligne($table, $id, $pluriel=TRUE)
{
	return sql_fetsel('*', "spip_asso_$table".($pluriel?'s':''), "id_$table=".intval($id));
}

// Affichage micro-formate d'un nom complet (de membre) suivant la configuration du plugin (i.e. champs geres ou non)
function association_calculer_nom_membre($civilite, $prenom, $nom, $html_tag='')
{
	$res = '';
	if ($html_tag) {
		$res = '<'.$html_tag.' class="'. (($civilite || $prenom)?'n':'fn') .'">';
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

// Affichage de date localisee et micro-formatee
function association_datefr($iso_date, $css_class='', $htm_abbr='abbr')
{
	$res = ($css_class?"<$htm_abbr class='$css_class' title='$iso_date'>":'');
	$res .= affdate_base($iso_date, 'entier'); // on fait appel a la fonction centrale des filtres SPIP... comme ca c'est traduit et formate dans les langues supportees ! si on prefere les mois en chiffres et non en lettre, y a qu'a changer les chaines de langue date_mois_XX
	$res .= ($css_class?"</$htm_abbr>":'');
	return $res;
}

function association_recupere_date($valeur)
{
	if ($valeur!='') {
		$valeur = preg_replace('/\D/', '-', $valeur, 2); // la limitation a 2 separateurs permet de ne transformer que la partie "date" s'il s'agit d'un "datetime" par exemple.
	}
	return $valeur;
}

function association_verifier_date($date, $nullable=FALSE)
{
	if ( $nullable && ($date=='0000-00-00' || !$date) )
		return FALSE;
	if (!preg_match('/^\d{4}\D\d{2}\D\d{2}$/', $date)) // annee sur 4 chiffres ; mois sur 2 chiffres ; jour sur 2 chiffres ; separateur est caractere non numerique quelconque...
#	if (!preg_match('/^\d{4}\D(\d|1[0-2])\D([1-9]|0[1-9]|[12]\d|3[01])$/', $date)) // annee sur 4 chiffres ; mois sur 1 ou 2 chiffres entre 1 et 12 ; jour sur 1 ou 2 chiffres eentre 1 et 31 ; separateur est n'importe quel caractere ne representant pas un chiffre arabe de la notation decimale standard...
		return _T('asso:erreur_format_date', array('date'=>$date) ); // ...c'est un petit plus non documente (la documentation et le message d'erreur stipulent AAAA-MM-JJ : mois et jours toujours sur deux chiffres avec donc zero avant si inferieur a 10, separateur est tiret)
	list($annee, $mois, $jour) = preg_split('/\D/', $date);
	if (!checkdate($mois, $jour, $annee)) // la date doit etre valide : pas de 30 fevrier ou de 31 novembre par exemple.
		return _T('asso:erreur_valeur_date', array('date'=>$date) );
//	return FALSE;
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
			$unite = ($nombre<=1) ? _T('local:an') : _T('local:ans');
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
			$unite = ($nombre<=1) ? _T('local:jour') : _T('spip:date_jours');
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
					$frmt_h = _T('duree_temps', array('nombre'=>1,'unite'=>_T('local:jour')));
					break;
				default:
					$frmt_h =  _T('duree_temps', array('nombre'=>association_nbrefr($nommbre['-1'],0),'unite'=>_T('spip:date_jours')));
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
					$frmt_h .= _T('duree_temps', array('nombre'=>association_nbrefr($nombre[0],0),'unite'=>_T('spip:date_heures')));
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
					$frmt_h .= _T('duree_temps', array('nombre'=>association_nbrefr($nombre[1],0),'unite'=>_T('spip:date_minutes')));
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
					$frmt_h .= _T('duree_temps', array('nombre'=>association_nbrefr($nombre[2],0),'unite'=>_T('spip:date_secondes')));
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
					$frmt_h =  _T('duree_temps', array('nombre'=>association_nbrefr($ladate[0],0),'unite'=>_T('local:ans')));
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
					$frmt_h .= _T('duree_temps', array('nombre'=>association_nbrefr($ladate[1],0),'unite'=>_T('spip:date_mois')));
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
					$frmt_h .= _T('duree_temps', array('nombre'=>association_nbrefr($ladate[2],0),'unite'=>_T('spip:date_jours')));
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
					$frmt_h .=  _T('duree_temps', array('nombre'=>association_nbrefr($lheure[0],0),'unite'=>_T('spip:date_heures')));
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
					$frmt_h .=  _T('duree_temps', array('nombre'=>association_nbrefr($lheure[1],0),'unite'=>_T('spip:date_minutes')));
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
					$frmt_h =  _T('duree_temps', array('nombre'=>association_nbrefr($lheure[2],0),'unite'=>_T('spip:date_secondes')));
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
function association_prixfr($montant, $unite_code='', $unite_nom='', $htm_span='span', $htm_abbr='abbr')
{
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
function association_recupere_montant($valeur)
{
	if ($valeur!='') {
		$valeur = str_replace(' ', '', $valeur); /* suppprime les espaces separateurs de milliers */
		$valeur = str_replace(',', '.', $valeur); /* convertit les , en . */
		$valeur = floatval($valeur);
	} else
		$valeur = 0.0;
	return $valeur;
}

/* s'assurer que la valeur saisie est un float positif */
function association_verifier_montant($valeur)
{
	if (association_recupere_montant($valeur)<0)
		return _T('asso:erreur_montant');
//	else
//		return FALSE;
}

/* s'assurer que l'entier saisie correspond bien a un id_auteur de la table spip_asso_membres (par defaut) ou spip_auteurs (si on elargi a tous les autteurs --ceci permet d'editer des membres effaces tant qu'ils sont references par SPIP) */
function association_verifier_membre($id_auteur, $touslesauteurs=false)
{
	if ($id_auteur) {
		$id_auteur = intval($id_auteur);
		if (sql_countsel('spip_'.($touslesauteurs?'auteurs':'asso_membres'), "id_auteur=$id_auteur")==0) {
			return _T('asso:erreur_id_adherent');
		}
	} else
		return FALSE;
}

// Affichage du message indiquant la date
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

function association_header_prive($flux)
{
	$c = direction_css(find_in_path('association.css'));
	return "$flux\n<link rel='stylesheet' type='text/css' href='$c' />";
}

// Filtre pour "afficher" ou "cacher" un bloc div
// Utilise dans le formulaire cvt "editer_asso_comptes.html"
function affichage_div($type_operation,$list_operation)
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

/* selecteur d'exercice comptable */
function association_selectionner_exercice($exercice='', $exec='', $plus='') {
    $res = '<select name ="exercice" onchange="form.submit()">';
#    $res .= '<option value="0" ';
#    if (!$exercice) {
#		$res .= ' selected="selected"';
#    }
#    $res .= '>'. _L("choisir l'exercice ?") .'</option>';
    $sql = sql_select('id_exercice, intitule', 'spip_asso_exercices','', 'intitule DESC');
    while ($val = sql_fetch($sql)) {
		$res .= '<option value="'.$val['id_exercice'].'" ';
		if ($exercice==$val['id_exercice']) {
			$res .= ' selected="selected"';
		}
		$res .= '>'.$val['intitule'].'</option>';
    }
    $res .= '</select>'.$plus;
    return $exec ? generer_form_ecrire($exec, $res.'<noscript><input type="submit" value="'._T('asso:bouton_lister').'" /></noscript>') : $res;
}

/* selecteur d'exercice comptable */
function association_selectionner_destination($destination='', $exec='', $plus='') {
//    $res = '<select name ="destination[]" multiple="multiple" onchange="form.submit()">';
    $res = '<select name ="destination" onchange="form.submit()">';
    $res .= '<option value="0" ';
//    if ( !(array_search(0, $destinations)===FALSE) ) {
    if (!$destination) {
		$res .= ' selected="selected"';
    }
//    $res .= '>'. _T('asso:toutes_destinations') .'</option><option disabled="disabled">--------</option>';
    $res .= '>'. _T('asso:toutes_destinations') .'</option>';
    $intitule_destinations = array();
    $sql = sql_select('id_destination, intitule', 'spip_asso_destination','', 'intitule DESC');
    while ($val = sql_fetch($sql)) {
		$res .= '<option value="'.$val['id_destination'].'" ';
//		if (!(array_search($val['id_destination'], $destinations)===FALSE)) {
		if ($destination==$val['id_destination']) {
			$res .= ' selected="selected"';
		}
		$res .= '>'.$val['intitule'].'</option>';
//		$intitule_destinations[$val['id_destination']] = $val['intitule'];
    }
    $res .= '</select>'.$plus;
    if ($GLOBALS['association_metas']['destinations']){
		return $exec ? generer_form_ecrire($exec, $res.'<noscript><input type="submit" value="'._T('asso:bouton_lister').'" /></noscript>') : $res;
	} else {
		return FALSE;
	}
}

/* selecteur de grouoe de membres*/
function association_selectionner_groupe($id_groupe='', $exec='', $plus='') {
    $qGroupes = sql_select('nom, id_groupe', 'spip_asso_groupes', 'id_groupe>=100', '', 'nom');  // on ne prend en consideration que les groupe d'id >= 100, les autres sont reserves a la gestion des autorisations
    if ( $qGroupes && sql_count($qGroupes) ) { // ne proposer que s'il y a des groupes definis
		$res = '<select name="groupe" onchange="form.submit()">';
		$res .= '<option value="">'._T('asso:tous_les_groupes').'</option>';
		while ($groupe = sql_fetch($qGroupes)) {
			$res .= '<option value="'.$groupe['id_groupe'].'"';
			if ($id_groupe==$groupe['id_groupe'])
				$res .= ' selected="selected"';
			$res .= '>'.$groupe['nom'].'</option>';
		}
		$res .= '</select>'.$plus;
		return $exec ? generer_form_ecrire($exec, $res.'<noscript><input type="submit" value="'._T('asso:bouton_lister').'" /></noscript>') : $res;
	} else {
		return FALSE;
	}
}

/* selecteur de statut de membres*/
function association_selectionner_statut($statut_interne='', $exec='', $plus='') {
    $res = '<select name="statut_interne" onchange="form.submit()">';
    $res .= '<option value="%"'. (($statut_interne=='defaut' || $statut_interne=='%')?' selected="selected"':'') .'>'._T('asso:entete_tous').'</option>';
    foreach ($GLOBALS['association_liste_des_statuts'] as $statut) {
		$res .= '<option value="'.$statut.'"';
		if ($statut_interne==$statut)
			$res .= ' selected="selected"';
		$res .= '> '._T('asso:adherent_entete_statut_'.$statut).'</option>';
	}
	$res .= '</select>'.$plus;
    return $exec ? generer_form_ecrire($exec, $res.'<noscript><input type="submit" value="'._T('asso:bouton_lister').'" /></noscript>') : $res;
}

/* selecteur de statut de membres*/
function association_selectionner_id($id='', $exec='', $plus='') {
    $res = '<input type="text" name="id" onfocus=\'this.value=""\' size="5"  value="'. ($id?$id:_T('asso:entete_id')) .'" />'.$plus;
    return $exec ? generer_form_ecrire($exec, $res.'<noscript><input type="submit" value="'._T('asso:bouton_lister').'" /></noscript>') : $res;
}

/* selecteur d'annee parmi celles disponibles dans une table */
function association_selectionner_annee($annee='', $dtable, $dchamp, $exec='', $plus='') {
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
    if ($annee>$an_max || $annee<$an_min) { // a l'initialisation, l'annee courante est mise si rien n'est indique... or si l'annee n'est pas disponible dans la liste deroulante on est mal positionne et le changement de valeur n'est pas top
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

function encadre($texte,$avant='[',$apres=']')
{
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

function instituer_adherent_ici($auteur=array()){
	$instituer_adherent = charger_fonction('instituer_adherent', 'inc');
	return $instituer_adherent($auteur);
}
function instituer_statut_interne_ici($auteur=array()){
	$instituer_statut_interne = charger_fonction('instituer_statut_interne', 'inc');
	return $instituer_statut_interne($auteur);
}

// recupeer la liste des colonne=>libelle d'un objet etendu
function recuperer_iextras($ObjetEtendu)
{
	$champsExtrasVoulus = array();
	if (test_plugin_actif('IEXTRAS')) { // le plugin "Interfaces pour ChampsExtras2" est installe et active : on peut donc utiliser les methodes/fonctions natives...
		include_spip('inc/iextras'); // charger les fonctions de l'interface/gestionnaire (ce fichier charge les methode du core/API)
		$ChampsExtrasGeres = iextras_get_extras_par_table(); // C'est un tableau des differents "objets etendus" (i.e. tables principaux SPIP sans prefixe et au singulier -- par exemple la table 'spip_asso_membres' correspond a l'objet 'asso_membre') comme cle.
		foreach ($ChampsExtrasGeres[$ObjetEtendu] as $$ChampExtraRang => $ChampExtraInfos ) { // Pour chaque objet, le tableau a une entree texte de cle "id_objet" et autant d'entrees tableau de cles numerotees automatiquement (a partir de 0) qu'il y a de champs extras definis. Chaque champ extra defini est un tableau avec les cle=>type suivants : "table"=>string, "champ"=>string, "label"=>string, "precisions"=>string, "obligatoire"=>string, "verifier"=>bool, "verifier_options"=>array, "rechercher"=>string, "enum"=>string, "type"=>string, "sql"=>string, "traitements"=>string, "saisie_externe"=>bool, "saisie_parametres"]=>array("explication"=>string, "attention"=>string, "class"=> string, "li_class"]=>string,)
			if ( is_array($ChampExtraInfos)
				$champsExtrasVoulus[$ChampExtraInfos['champ']] = _TT($ChampExtraInfos['label']); // _TT est defini dans cextras_balises.php
		}
	} else { // le plugin "Interfaces pour ChampsExtras2" n'est pas actif :-S Mais peut-etre a-t-il ete installe ?
		$ChampsExtrasGeres = @unserialize(str_replace('O:10:"ChampExtra"', 'a', $GLOBALS['meta']['iextras'])); // "iextras (interface)" stocke la liste des champs geres dans un meta. Ce meta est un tableau d'objets "ChampExtra" (un par champ extra) manipules par "cextras (core)". On converti chaque objet en tableau
		if ( !is_array($ChampsExtrasGeres) )
			return array(); // fin : ChampsExtras2 non installe ou pas d'objet etendu.
		$TT = function_exists('_T_ou_typo') ? '_T_ou_typo' : 'T' ; // Noter que les <multi>...</multi> et <:xx:> sont aussi traites par propre() et typo() :  http://contrib.spip.net/PointsEntreeIncTexte
		foreach ($ChampsExtrasGeres as $ChampExtra) { // Chaque champ extra defini est un tableau avec les cle=>type suivants (les cles commencant par "_" initialisent des methodes de meme nom sans le prefixe) : "table"=>string, "champ"=>string, "label"=>string, "precisions"=>string, "obligatoire"=>string, "verifier"=>bool, "verifier_options"=>array, "rechercher"=>string, "enum"=>string, "type"=>string, "sql"=>string, "traitements"=>string, "_id"=>string, "_type"=>string, "_objet"=>string, "_table_sql"=>string, "saisie_externe"=>bool, "saisie_parametres"]=>array("explication"=>string, "attention"=>string, "class"=> string, "li_class"]=>string,)
			if ($ChampExtra['table']==$ObjetEtendu) // c'est un champ extra de la 'table' ou du '_type' d'objet qui nous interesse
				$champsExtrasVoulus[$ChampExtra['champ']] = $TT($ChampExtra['label']);
		}
	}
	return $champsExtrasVoulus;
}

// bloc infos integral (colonne gauche)
// Rem: une certaine similitude avec http://programmer.spip.org/boite_infos :)
function bloc_infos($TitreObjet, $NumObjet, $DesLignes=array(), $PrefixeLangue='asso', $ObjetEtendu='')
{
	$res = debut_boite_info(true);
	$res .= totauxinfos_intro($TitreObjet, $TitreObjet, $NumObjet, $DesLignes, $PrefixeLangu, $ObjetEtendu);
	$res .= association_date_du_jour();
	$res .= fin_boite_info(true);
	return $res;
}

// Rappels sur l'objet dans le bloc infos
// C'est un resume ou une petite presentation de l'objet en cours d'edition/lecture : ces informations permettent de situer le contexte de la page et n'apparaissent pas dans le bloc central !
function totauxinfos_intro($titre, $type='', $id=0, $DesLignes=array(), $PrefixeLangue='asso', $ObjetEtendu='')
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
/* Le code suivant fonctionne, mais :
-* il manque le formatage correct des donnees, surtout pour les listes (cas par exemple des : auteurs, mots cles, documents, enums definis dans l'interface).
-* seuls les champs extras crees manuellement (par l'interface donc) sont pris en compte, pas ceux rajoutes via pipeline par d'autres plugins.
		$champsExtras = recuperer_iextras($ObjetEtendu);
		if ( count($champsExtras) ) {
			$donneesExtras = sql_fetsel(array_keys($champsExtras), "spip_${ObjetEtendu}s", 'id_'.($type?$type:$ObjetEtendu).'='.intval($id) ); // on recupere les donnees... (il faut que le nom de la table soit le pluriel en "-s" de l'objet et que l'identifiant soit l'objet prefixe de "id_" :-S)
			foreach ($champsExtras as $col_name => $col_label) {
				$res .= '<dt>'. $col_label .'</dt><dd>'. propre($donneesExtras[$col_name]) .'</dd>'; // propre() encadre dans P... Cette presentation est propre a Associaspip. L'appel au pipeline "afficher_contenu_objet" remplace tout le "foreach" avec plutot : $res .= "<div class='$col_name'><strong>$col_label</strong> $donneesExtras[$col_name]</div>";
			}
		}
du coup, on readapte : */
		$res .= '<dt>+</dt><dd>'. pipeline('afficher_contenu_objet', array('args'=>array('type'=>$ObjetEtendu, 'id_objet'=>$id, 'contexte'=>array()), 'data'=>'',) ) .'</dd>';
	}
	if ( count($DesLignes) OR $ObjetEtendu )
		$res .= '</dl>';
	return $res;
}

// Tableau presentant les chiffres de synthese de la statistique descriptive dans le bloc infos
// On prend en entree : la table du plugin sur laquelle va porter les statistique, un tableau de item de langue decrivant la ligne et liste des champs sur lesquels calcuer les statistiques, le critere de selection SQL des lignes (sinon toutes)
// On renvoie pour chaque ligne : la moyenne arithmetique <http://fr.wikipedia.org/wiki/Moyenne#Moyenne_arithm.C3.A9tique> et  l'ecart-type <http://fr.wikipedia.org/wiki/Dispersion_statistique#.C3.89cart_type> ainsi que les extrema <http://fr.wikipedia.org/wiki/Crit%C3%A8res_de_position#Valeur_maximum_et_valeur_minimum> si on le desire (par defaut non car tableau debordant dans ce petit cadre)
function totauxinfos_stats($legende='',$sql_table_asso,$sql_champs,$sql_criteres='1=1',$decimales_significatives=1,$avec_extrema=false)
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
		$res .= '<td class="'.($decimales_significatives?'decimal':'integer').'">'. association_nbrefr($stats['valMoy'],$decimales_significatives) .'</td>';
		$res .= '<td class="'.($decimales_significatives?'decimal':'integer').'">'. association_nbrefr($stats['ekrTyp'],$decimales_significatives) .'</td>';
		if ($avec_extrema) {
			$res .= '<td class="'.($decimales_significatives?'decimal':'integer').'">'. association_nbrefr($stats['valMin'],$decimales_significatives) .'</td>';
			$res .= '<td class="'.($decimales_significatives?'decimal':'integer').'">'. association_nbrefr($stats['valMax'],$decimales_significatives) .'</td>';
		}
		$res .= '</tr>';
		$compteur++;
	}
	$res .= '</tbody></table>';
	return $res;
}

// Tableau des decomptes statistiques dans le bloc infos
// On prend en entree deux tableaux de taille egale (non controlee) --respectivement pour les intitules/libelles et les effectifs/occurences-- qui sont indexes par la classe CSS associee (parce-qu'elle doit etre unique pour chaque ligne)
function totauxinfos_effectifs($legende='',$table_textes,$table_nombres,$decimales_significatives=0)
{
	if (!is_array($table_textes) || !is_array($table_nombres) )
		return FALSE;
	$nombre = $nombre_total = 0;
	$res = '<table width="100%" class="asso_infos">';
	$res .= '<caption>'. _T('asso:totaux_nombres', array('de_par'=>_T("local:$legende"))) .'</caption><tbody>';
	foreach ($table_textes as $classe_css=>$libelle) {
		$res .= '<tr class="'.$classe_css.'">';
		$res .= '<td class"text">'._T('asso:'.$libelle).'</td>';
		$res .= '<td class="' .($decimales_significatives?'decimal':'integer') .'">'. association_nbrefr($table_nombres[$classe_css],$decimales_significatives) .'</td>';
		$nombre_total += $table_nombres[$classe_css];
		$res .= '</tr>';
	}
	$res .= '</tbody>';
	if (count($table_nombres)>1) {
		$res .= '<tfoot>';
		$res .= '<tr><th class="text">'._T('asso:liste_nombre_total').'</th>';
		$res .= '<th class="' .($decimales_significatives?'decimal':'integer') .'">'. association_nbrefr($nombre_total,$decimales_significatives) .'</th></tr>';
		$res .= '</tfoot>';
	}
	return $res.'</table>';
}

// Tableau des totaux comptables dans le bloc infos
// On prend en entree : le complement de titre du tableau puis les sommes cumulees des recettes et des depenses. (tous ces parametres sont facultatifs, mais attention qu'un tableau est quand meme genere dans tous les cas)
function totauxinfos_montants($legende='',$somme_recettes=0,$somme_depenses=0)
{
	$res = '<table width="100%" class="asso_infos">';
	$res .= '<caption>'. _T('asso:totaux_montants', array('de_par'=>_T("local:$legende"))) .'</caption><tbody>';
	if ($somme_recettes) {
		$res .= '<tr class="impair">'
		. '<th class="entree">'. _T('asso:bilan_recettes') .'</th>'
		. '<td class="decimal">' .association_prixfr($somme_recettes). ' </td>'
		. '</tr>';
	}
	if ($somme_depenses) {
		$res .= '<tr class="pair">'
		. '<th class="sortie">'. _T('asso:bilan_depenses') .'</th>'
		. '<td class="decimal">'.association_prixfr($somme_depenses) .'</td>'
		. '</tr>';
	}
	if ($somme_recettes && $somme_depenses) {
		$solde = $somme_recettes-$somme_depenses;
		$res .= '<tr class="'.($solde>0?'impair':'pair').'">'
		. '<th class="solde">'. _T('asso:bilan_solde') .'</th>'
		. '<td class="decimal">'.association_prixfr($solde).'</td>'
		. '</tr>';
	}
	return $res.'</tbody></table>';
}

// bloc affichant le formulaire pour genere le PDF de la/le liste/tableau
function bloc_listepdf($objet, $params=array(), $prefixeLibelle='', $champsExclus=array(), $coords=true)
{
	$res = '';
	if (test_plugin_actif('FPDF')) { // liste
		$res .= debut_cadre_enfonce('',true);
		$res .= '<h3>'. _T('plugins_vue_liste') .'</h3>';
		$res .= '<div class="formulaire_spip formulaire_asso_liste_'.$objet.'s">';
		$champsExtras = recuperer_iextras("asso_$objet");
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

// pour executer les squelettes comportant la balise Meta
include_spip('balise/meta');
// charger les metas donnees
$inc_meta = charger_fonction('meta', 'inc'); // inc_version l'a deja chargee
$inc_meta('association_metas');

// pouvoir utiliser les fonctions de coordonnees comme filtre
if (test_plugin_actif('COORDONNEES')) {
	include_spip('inc/association_coordonnees');
}

// Pour construire des menu avec SELECTED
function association_mySel($varaut,$variable, $option=NULL)
{
	if ( function_exists('mySel') ) //@ http://doc.spip.org/@mySel
		return mySel($varaut, $variable, $option);
	// la fonction mySel n'existe plus en SPIP 3 donc on la recree
	$res = ' value="'.$varaut.'"'. (($variable==$varaut) ? ' selected="selected"' : '');
	return  (!isset($option) ? $res : "<option$res>$option</option>\n");
}

?>