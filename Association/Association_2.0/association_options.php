<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

if (!defined("_ECRIRE_INC_VERSION")) return;

// Le premier element indique un ancien membre
$GLOBALS['association_liste_des_statuts'] =
  array('sorti','prospect','ok','echu','relance');

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
	. '" title="'
	. $texte
	. '" /></a>';
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
	
function association_I2_cfg_form($flux) {
        $flux .= recuperer_fond('fonds/inscription2_association');
	    return ($flux);
}	
	
// raccourcis

function generer_url_asso_don($id, $param='', $ancre='') {
	return  generer_url_ecrire('edit_don', "id=" . intval($id));
}

function generer_url_don($id, $param='', $ancre='') {
	return  array('asso_don', $id);
}

function generer_url_asso_adherent($id, $param='', $ancre='') {
	return  generer_url_ecrire('voir_adherent', "id=" . intval($id));
}

function generer_url_adherent($id, $param='', $ancre='') {
	return  array('asso_adherent', $id);
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
	
function association_header_prive($flux){
		$flux .= '<link rel="stylesheet" type="text/css" href="'.direction_css(find_in_path('association.css')).'" />';
		return $flux;
	}

// Gestion de l'absence eventuelle du plugin Inscription2

if (!defined('_ASSOCIATION_INSCRIPTION2'))
    define('_ASSOCIATION_INSCRIPTION2', true); // false si on sait s'en passer

define('_ASSOCIATION_AUTEURS_ELARGIS', 
       @spip_query("SELECT id_auteur FROM spip_auteurs_elargis LIMIT 1") ? 
       'spip_auteurs_elargis' : 'spip_asso_adherents');

	//-- Table des tables ----------------------------------------------------

global $table_des_tables, $table_titre;
	$table_des_tables['asso_dons'] = 'asso_dons';
	$table_des_tables['asso_ventes'] = 'asso_ventes';
	$table_des_tables['asso_comptes'] = 'asso_comptes';
	$table_des_tables['asso_categories'] = 'asso_categories';
	$table_des_tables['asso_plan'] = 'asso_plan';
	$table_des_tables['asso_ressources'] = 'asso_ressources';
	$table_des_tables['asso_prets'] = 'asso_prets';
	$table_des_tables['asso_activites'] = 'asso_activites';
	$table_des_tables['asso_adherents'] = 'asso_adherents';

$table_titre['asso_adherents']= "nom_famille AS titre, '' AS lang";
$table_titre['asso_dons']= "CONCAT('don ', id_don) AS titre, '' AS lang";

?>
