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

// Gestion de l'absence du plugin Inscription2 (code pratiquement mort)

if (!defined('_ASSOCIATION_INSCRIPTION2'))
    define('_ASSOCIATION_INSCRIPTION2', false); // true si le veut

define('_ASSOCIATION_AUTEURS_ELARGIS', 
       (_ASSOCIATION_INSCRIPTION2 AND @spip_query("SELECT id_auteur FROM spip_auteurs_elargis LIMIT 1")) ? 
       'spip_auteurs_elargis' : 'spip_asso_membres');


// Le premier element indique un ancien membre
$GLOBALS['association_liste_des_statuts'] =
  array('sorti','prospect','ok','echu','relance');

// Est-il normal d'avoir deux listes de statuts ? 
$GLOBALS['association_liste_des_statuts2'] =
	!(_ASSOCIATION_INSCRIPTION2 AND function_exists('lire_config'))
	? $GLOBALS['association_liste_des_statuts']
	: array('sorti','ok','echu','relance', lire_config('inscription2/statut_interne'));

$GLOBALS['association_styles_des_statuts'] = array(
	"echu" => "impair",
	"ok" => "valide",
	"prospect" => "prospect",
	"relance" => "pair",
	"sorti" => "sortie"
);


define('_DIR_PLUGIN_ASSOCIATION_ICONES', _DIR_PLUGIN_ASSOCIATION.'img_pack/');

function association_icone($texte, $lien, $image, $sup='rien.gif')
{
	return icone_horizontale($texte, $lien, _DIR_PLUGIN_ASSOCIATION_ICONES. $image, $sup, false);
}

function association_bouton($texte, $image, $script, $args='')
{
	return '<a href="'
	. generer_url_ecrire($script, $args)
	. '"><img src="'
	. _DIR_PLUGIN_ASSOCIATION_ICONES. $image 
	. '" alt=" " title="'
	. $texte
	. '" /></a>';
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

		$boutons_admin['naviguer']->sousmenu['association']= new Bouton(
			_DIR_PLUGIN_ASSOCIATION_ICONES."annonce.gif",  // icone
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

// recupere dans la table de comptes et celle des destinations la liste des destinations associees a une operation
// le parametre correspond a l'id_compte de l'operation dans spip_asso_compte (et spip_asso_destination)
function association_liste_destinations_associees($id_compte)
{
	if ($destination_query = sql_select('spip_asso_destination_op.id_destination, spip_asso_destination_op.recette, spip_asso_destination_op.depense, spip_asso_destination.intitule', 'spip_asso_destination_op RIGHT JOIN spip_asso_destination ON spip_asso_destination.id_destination=spip_asso_destination_op.id_destination', "id_compte=$id_compte", '', 'spip_asso_destination.intitule'))
	{
		$destination = array();
		while ($destination_op = sql_fetch($destination_query))	{
			/* soit recette soit depense est egal a 0, donc pour l'affichage du montant on se contente les additionner */
			$destination[$destination_op[id_destination]] = $destination_op[recette]+$destination_op[depense]; 
		}
		if (count($destination) == 0) $destination = '';
	}
	else
	{
		$destination='';
	}

	return $destination;
}

// retourne une liste d'option HTML de l'ensemble des destinations de la base, ordonee par intitule
function association_toutes_destination_option_list()
{
	$liste_destination = '';
	$sql = sql_select('id_destination,intitule', 'spip_asso_destination', "", "", "intitule");
	while ($destination_info = sql_fetch($sql)) {
		$id_destination = $destination_info['id_destination'];
	 	$liste_destination .= "<option value='$id_destination'>".$destination_info['intitule'].'</option>';
	}
	return $liste_destination;
}

// retourne dans un <div> le code HTML/javascript correspondant au selecteur de destinations dynamique
// le premier parametre permet de donner un tableau de destinations deja selectionnees(ou '' si on ajoute une operation)
// le second parametre (optionnel) permet de specifier si on veut associer une destination unique, par default on peut ventiler sur
// plusieurs destinations
// le troisieme parametre permet de regler une destination par defaut[contient l'id de la destination] - quand $destination est vide
function association_editeur_destinations($destination, $unique=false, $defaut='')
{
	// recupere la liste de toutes les destination dans un code HTML <option value="destinatio_id">destination</option>
	$liste_destination = association_toutes_destination_option_list();

	$res = '';

	if ($liste_destination)	{
		$res = "<script type='text/javascript' src='".find_in_path("javascript/jquery.destinations_form.js")."'></script>";
		$res .= '<label for="destination"><strong>'
		. _T('asso:destination')
		. '&nbsp;:</strong></label>'
		. '<div id="divTxtDestination">';

		$idIndex=1;
		if ($destination != '') { /* si on a une liste de destinations (on edite une operation) */
			foreach ($destination as $destId => $destMontant) {						
				$liste_destination_selected = preg_replace('/(value=\''.$destId.'\')/', '$1 selected="selected"', $liste_destination);
				$res .= '<p class="formo" id="row'.$idIndex.'"><select name="destination_id'.$idIndex.'" id="destination_id'.$idIndex.'" >'
				. $liste_destination_selected
				. '</select>';
				if ($unique==false) {
					$res .= '<input name="montant_destination_id'.$idIndex.'" value="'
					. association_nbrefr($destMontant)
					. '" type="text" id="montant_destination_id'.$idIndex.'" />'
					. "<button class='destButton' type='button' onClick='addFormField(); return false;'>+</button>";
					if ($idIndex>1)	{
						$res .= "<button class='destButton' type='button' onClick='removeFormField(\"#row".$idIndex."\"); return false;'>-</button>";
					}
				}
				$res .= '</p>';
				$idIndex++;
			}
		}
		else {/* pas de destination deja definies pour cette operation */
			if ($defaut!='') {
				$liste_destination = preg_replace('/(value=\''.$defaut.'\')/', '$1 selected="selected"', $liste_destination);
			}
			$res .= '<p id="row1" class="formo"><select name="destination_id1" id="destination_id1" >'
			. $liste_destination
			. '</select>';
			if ($unique==false) {
				$res .= '<input name="montant_destination_id1" value="'
				. ''
				. '" type="text" id="montant_destination_id1"/>'
				. "<button class='destButton' type='button' onClick='addFormField(); return false;'>+</button>";
			}
			$res .= '</p>';
		}

		if ($unique==false) $res .= '<input type="hidden" id="idNextDestination" value="'.($idIndex+1).'">';
		$res .= '</div>';
	}
	return $res;
}

/* callback pour filtrer tout $_POST et ne recuperer que les destinations */
function destination_post_filter($var)
{
	if (preg_match ('/^destination_id/', $var)>0) return TRUE;
	return FALSE;
}

/* Ajouter une operation dans spip_asso_comptes ainsi que si necessaire dans spip_asso_destination_op */
function association_ajouter_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, $id_journal)
{
	include_spip('base/association');		

	$id_compte = sql_insertq('spip_asso_comptes', array(
		    'date' => $date,
		    'imputation' => $imputation,
		    'recette' => $recette,
		    'depense' => $depense,
		    'journal' => $journal,
		    'id_journal' => $id_journal,
		    'justification' => $justification));

	/* Si on doit gerer les destinations */
	if ($GLOBALS['association_metas']['destinations']=="on")
	{
		association_ajouter_destinations_comptables($id_compte, $recette, $depense);
	}
}

/* Ajouter une operation dans spip_asso_comptes ainsi que si necessaire dans spip_asso_destination_op */
function association_modifier_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, $id_journal, $id_compte)
{
	include_spip('base/association');		
	
	// tester $id_journal, si il est null, ne pas le modifier
	if ($id_journal) {
		sql_updateq('spip_asso_comptes', array(
			    'date' => $date,
			    'imputation' => $imputation,
			    'recette' => $recette,
			    'depense' => $depense,
			    'journal' => $journal,
			    'id_journal' => $id_journal,
			    'justification' => $justification),
			    "id_compte=$id_compte");
	} else {
		sql_updateq('spip_asso_comptes', array(
			    'date' => $date,
			    'imputation' => $imputation,
			    'recette' => $recette,
			    'depense' => $depense,
			    'journal' => $journal,
			    'justification' => $justification),
			    "id_compte=$id_compte");

	}

	/* Si on doit gerer les destinations */
	if ($GLOBALS['association_metas']['destinations']=="on")
	{
		association_ajouter_destinations_comptables($id_compte, $recette, $depense);
	}
}

/* fonction permettant d'ajouter/modifier les destinations comptables (presente dans $_POST) a une operation comptable */
function association_ajouter_destinations_comptables($id_compte, $recette, $depense)
{
	include_spip('base/association');

	/* on efface de la table destination_op toutes les entrees correspondant a cette operation  si on en trouve*/
	sql_delete("spip_asso_destination_op", "id_compte=$id_compte");

	if ($recette>0) {
		$attribution_montant = "recette";
	}
	else {
		$attribution_montant = "depense";
	}

	/* on recupere dans $_POST toutes les keys des entrees commencant par destination_id */
	$toutesDestinationsPOST = array_filter(array_keys($_POST), "destination_post_filter");
	
	/* on boucle sur toutes les cles trouvees, les montants ont des noms de champs identiques mais prefixes par montant_ */
	$total_destination = 0;
	$id_inserted = array();


	foreach ($toutesDestinationsPOST as $destination_id)
	{
		$id_destination = _request($destination_id);
			
		/* on verifie qu'on n'a pas deja insere une destination avec cette id */
		if (!array_key_exists($id_destination,$id_inserted)) {
			$id_inserted[$id_destination]=0;
		}
		else {/* on a deja insere cette destination: erreur */
			include_spip('inc/minipres');
			$url_retour = generer_url_ecrire('edit_compte','id='.$id_compte);
			echo minipres(_T('asso:erreur_titre'),_T('asso:erreur_destination_dupliquee').'<br/><h1><a href="'.$url_retour.'">Retour</a><h1>');
			exit;
		}
		/* si on a une seule destination, on insere meme sans montant avec directement la somme recette+depense vu qu'au moins
		une des deux est egale a 0 */
		if (count($toutesDestinationsPOST) == 1) {
			$montant = $recette+$depense;
		} else {
			$montant = floatval(preg_replace("/,/",".",_request('montant_'.$destination_id)));
		}
		$total_destination += $montant;
		sql_insertq('spip_asso_destination_op', array(
		    'id_compte' => $id_compte,
		    'id_destination' => $id_destination,
		    $attribution_montant => $montant));
	}
	/* on verifie que la somme des montants des destinations correspond au montant de l'operation($recette+$depense) dont l'un des deux est egal a 0 */
	if ($recette+$depense != $total_destination) {
		include_spip('inc/minipres');
		$url_retour = generer_url_ecrire('edit_compte','id='.$id_compte);
		echo minipres(_T('asso:erreur_titre'),_T('asso:erreur_montant_destination').'<br/><h1><a href="'.$url_retour.'">Retour</a><h1>');
		exit;
	}
}

//Conversion de date
function association_datefr($date) { 
		$split = explode('-',$date); 
		$annee = $split[0]; 
		$mois = $split[1]; 
		$jour = $split[2]; 
		return $jour.'/'.$mois.'/'.$annee; 
	} 
	
function association_nbrefr($montant) {
		$montant = number_format($montant, 2, ',', ' ');
		return $montant;
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


// Pour ne pas avoir a ecrire le prefixe "spip_" dans les squelettes etc
// (cf trouver_table)
global $table_des_tables;
$table_des_tables['asso_dons'] = 'asso_dons';
$table_des_tables['asso_ventes'] = 'asso_ventes';
$table_des_tables['asso_comptes'] = 'asso_comptes';
$table_des_tables['asso_categories'] = 'asso_categories';
$table_des_tables['asso_plan'] = 'asso_plan';
$table_des_tables['asso_ressources'] = 'asso_ressources';
$table_des_tables['asso_prets'] = 'asso_prets';
$table_des_tables['asso_activites'] = 'asso_activites';
$table_des_tables['asso_membres'] = 'asso_membres';
$table_des_tables['association_metas'] = 'association_metas';

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
