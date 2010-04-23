<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

if (!defined("_ECRIRE_INC_VERSION")) return;

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

define('_ASSOCIATION_INSCRIPTION2', true); // false si on sait s'en passer

define('_ASSOCIATION_AUTEURS_ELARGIS', 
       @spip_query("SELECT id_auteur FROM spip_auteurs_elargis LIMIT 1") ? 
       'spip_auteurs_elargis' : 'spip_asso_adherents');

function association_auteurs_elargis_select($select, $from='', $where='', $group='', $order='', $limit='')
{
	return sql_select($select, _ASSOCIATION_AUTEURS_ELARGIS . $from, $where, $group, $order, $limit);
}

function association_auteurs_elargis_updateq($couples, $where='')
{
	include_spip('base/association'); // pour avoir la description
	sql_updateq(_ASSOCIATION_AUTEURS_ELARGIS, $couples, $where);
}

function association_auteurs_elargis_delete($where='')
{
	sql_delete(_ASSOCIATION_AUTEURS_ELARGIS, $couples, $where);
}

	//-- Table des tables ----------------------------------------------------

	global $table_des_tables;
	$table_des_tables['asso_dons'] = 'asso_dons';
	$table_des_tables['asso_ventes'] = 'asso_ventes';
	$table_des_tables['asso_comptes'] = 'asso_comptes';
	$table_des_tables['asso_categories'] = 'asso_categories';
	$table_des_tables['asso_plan'] = 'asso_plan';
	$table_des_tables['asso_ressources'] = 'asso_ressources';
	$table_des_tables['asso_prets'] = 'asso_prets';
	$table_des_tables['asso_activites'] = 'asso_activites';
	$table_des_tables['asso_adherents'] = 'asso_adherents';

?>
