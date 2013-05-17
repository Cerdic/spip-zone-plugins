<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault            *
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


define('_DIR_PLUGIN_ASSOCIATION_ICONES', _DIR_PLUGIN_ASSOCIATION.'img_pack/');

function association_icone($texte, $lien, $image, $sup='rien.gif') {
	return icone_horizontale($texte, $lien, _DIR_PLUGIN_ASSOCIATION_ICONES. $image, $sup, false);
}

function association_bouton($texte, $image, $script, $args='', $img_attributes='') {
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

function association_retour() {
	return bloc_des_raccourcis(association_icone(_T('asso:bouton_retour'),  str_replace('&', '&amp;', $_SERVER['HTTP_REFERER']), "retour-24.png"));
}

function request_statut_interne() {
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

function association_mode_de_paiement($journal, $label) {
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

function association_flottant($s) {
	return number_format(floatval($s), 2, ',', ' ');
}

function association_telfr($n) {
	$n = preg_replace('/\D/', '', $n);
	if (!intval($n)) return '';
	return preg_replace('/(\d\d)/', '\1&nbsp;', $n);
}

function association_header_prive($flux){
	$c = direction_css(find_in_path('association.css'));
	return "$flux\n<link rel='stylesheet' type='text/css' href='$c' />";
}

function association_delete_tables($flux){
  spip_unlink(cache_meta('association_metas'));
}


// Pour ne pas avoir a ecrire le prefixe "spip_" dans les squelettes etc
// (cf trouver_table)
global $table_des_tables;
$table_des_tables['asso_dons'] = 'asso_dons';
$table_des_tables['asso_ventes'] = 'asso_ventes';
$table_des_tables['asso_comptes'] = 'asso_comptes';
$table_des_tables['comptes'] = 'asso_comptes';
$table_des_tables['asso_categories'] = 'asso_categories';
$table_des_tables['asso_plan'] = 'asso_plan';
$table_des_tables['asso_ressources'] = 'asso_ressources';
$table_des_tables['asso_prets'] = 'asso_prets';
$table_des_tables['asso_activites'] = 'asso_activites';
$table_des_tables['asso_membres'] = 'asso_membres';
$table_des_tables['association_metas'] = 'association_metas';
$table_des_tables['asso_destination'] = 'asso_destination';
$table_des_tables['asso_destination_op'] = 'asso_destination_op';

// Pour que les raccourcis ci-dessous heritent d'une zone de clic pertinente
global $table_titre;
$table_titre['asso_membres']= "nom_famille AS titre, '' AS lang";
$table_titre['asso_dons']= "CONCAT('don ', id_don) AS titre, '' AS lang";

// Toujours charger la description des tables (a ameliorer)
include _DIR_PLUGIN_ASSOCIATION . 'base/association.php';

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


// pour executer les squelettes comportant la balise Meta
include_spip('balise/meta');
// charger les metas donnees
$inc_meta = charger_fonction('meta', 'inc'); // inc_version l'a deja chargee
$inc_meta('association_metas');

?>