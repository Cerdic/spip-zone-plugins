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

if (!defined("_ECRIRE_INC_VERSION")) return;

// Le premier element indique un ancien membre
$GLOBALS['association_liste_des_statuts'] =
  array('sorti','prospect','ok','echu','relance');

$GLOBALS['association_styles_des_statuts'] = array(
	"echu" => "impair",
	"ok" => "valide",
	"prospect" => "prospect",
	"relance" => "pair",
	"sorti" => "sortie"
);

$GLOBALS['association_classes'] = array(
	"capital" => "1",
	"immob" => "2",
	"stock" => "3",
	"tier" => "4",
    "financier" => "5",
    "charge" => "6",
    "produit" => "7",
    "contribution_volontaire" => "8"
 );

define('_DIR_PLUGIN_ASSOCIATION_ICONES', _DIR_PLUGIN_ASSOCIATION.'img_pack/');

function association_icone($texte, $lien, $image, $sup='rien.gif')
{
	return icone_horizontale($texte, $lien, _DIR_PLUGIN_ASSOCIATION_ICONES. $image, $sup, false);
}

function association_bouton($texte, $image, $script, $args='', $img_attributes='')
{
	return '<a href="'
	. generer_url_ecrire($script, $args)
	. '"><img src="'
	. _DIR_PLUGIN_ASSOCIATION_ICONES. $image 
	. '" alt=" " title="'
	. $texte
	. '" '
	. $img_attributes
	.' /></a>';
}

function association_retour()
{
	return bloc_des_raccourcis(association_icone(_T('asso:bouton_retour'),  str_replace('&', '&amp;', $_SERVER['HTTP_REFERER']), "retour-24.png"));
}

function request_statut_interne()
{
	$statut_interne = _request('statut_interne');
	if (in_array($statut_interne, $GLOBALS['association_liste_des_statuts'] ))
		return "statut_interne=" . sql_quote($statut_interne);
	elseif ($statut_interne == 'tous')
		return "statut_interne LIKE '%'";
	else {
		set_request('statut_interne', 'defaut');
		$a = $GLOBALS['association_liste_des_statuts'];
		array_shift($a);
		return sql_in("statut_interne", $a);
	}
}

function association_ajouterBoutons($boutons_admin) {
		// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		$menu = "naviguer";
		$icone = "annonce.gif";
		if (isset($boutons_admin['bando_reactions'])){
			$menu = "bando_reactions";
			$icone = "annonce.gif";
		}
		$boutons_admin[$menu]->sousmenu['association']= new Bouton(
			_DIR_PLUGIN_ASSOCIATION_ICONES.$icone,  // icone
			_T('asso:titre_menu_gestion_association') //titre
			);
			
	}
	return $boutons_admin;
}
	

function association_mode_de_paiement($journal, $label)
{
	$sel = '';
	$sql = sql_select("code,intitule", "spip_asso_plan", "classe=".sql_quote($GLOBALS['association_metas']['classe_banques']), '', "code") ;
	while ($banque = sql_fetch($sql)) {
		$c = $banque['code'];
		$sel .= "<option value='$c'"
		. (($journal==$c) ? ' selected="selected"' : '')
		. '>' . $banque['intitule'] ."</option>\n";
	}

	return '<label for="journal"><strong>'
	  . $label
	  . "&nbsp;:</strong></label>\n"
	  . (!$sel 
	      ? "<input name='journal' id='journal' class='formo' />"
	      : "<select name='journal' id='journal' class='formo'>$sel</select>\n");
}

// affichage du nom des membres
function association_calculer_nom_membre($civilite, $prenom, $nom) {
	$res = ($GLOBALS['association_metas']['civilite']=="on")?$civilite.' ':'';
	$res .= ($GLOBALS['association_metas']['prenom']=="on")?$prenom.' ':'';
	$res .= $nom;
	return $res;
}

//Conversion de date
function association_datefr($date) { 
		$split = explode('-',$date); 
		$annee = $split[0]; 
		$mois = $split[1]; 
		$jour = $split[2]; 
		return $jour.'/'.$mois.'/'.$annee; 
	}

function association_verifier_date($date) {
	if (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $date)) return _T('asso:erreur_format_date');
	list($annee, $mois, $jour) = explode("-",$date);
	if (!checkdate($mois, $jour, $annee)) return _T('asso:erreur_date');
	return;
}
	
function association_nbrefr($montant) {
		$montant = number_format(floatval($montant), 2, ',', ' ');
		return $montant;
	}

/* prend en parametre le nom de l'argument a chercher dans _request et retourne un float */
function association_recupere_montant ($valeur) {
	if ($valeur != '') {
		$valeur = str_replace(" ", "", $valeur); /* suppprime les espaces separateurs de milliers */
		$valeur = str_replace(",", ".", $valeur); /* convertit les , en . */
		$valeur = floatval($valeur);
	} else $valeur = 0.0;
	return $valeur;
}

	//Affichage du message indiquant la date 
function association_date_du_jour($heure=false) {
		return '<p>'.($heure ? _T('asso:date_du_jour_heure') : _T('asso:date_du_jour')).'</p>';
	}
	
function association_flottant($s)
{
	return number_format(floatval($s), 2, ',', ' ');
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
			$operation = $GLOBALS['association_classes'][$operations[$i]];
			if($type_operation===$operation) {
				$res = '';
				break;
			}
		}
	}
	else {
		$res = ($type_operation===$GLOBALS['association_classes'][$list_operation])?'':'cachediv';
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
	return  generer_url_ecrire('edit_don', "id=" . intval($id));
}

function generer_url_don($id, $param='', $ancre='') {
	return  array('asso_don', $id);
}

function generer_url_asso_membre($id, $param='', $ancre='') {
	return  generer_url_ecrire('voir_adherent', "id=" . intval($id));
}

function generer_url_membre($id, $param='', $ancre='') {
	return  array('asso_membre', $id);
}

function generer_url_asso_vente($id, $param='', $ancre='') {
	return  generer_url_ecrire('edit_vente', "id=" . intval($id));
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
?>
