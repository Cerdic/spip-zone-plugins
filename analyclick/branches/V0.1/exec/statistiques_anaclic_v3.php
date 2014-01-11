<?php
/**
* Plugin Analyclick
*
* @author: Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2011
* Logiciel distribue sous licence GNU/GPL.
*
* Affichage des statistiques
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/statistiques');

function exec_statistiques_anaclic_v3_dist()
{
	$id_document = intval(_request('id_document'));
	$type = _request('type');
	if (!preg_match('/^\w+$/', $type)) $type = 'day';
	$duree = intval(_request('duree'));
	if (!$duree) $duree = 105;
	$interval = intval(_request('interval'));
	if (!$interval) {
	  if ($type == 'day')
	    $interval = 3600*24;
	  else $interval = 3600;
	}

	// nombre de referers a afficher
	$limit = intval(_request('limit'));
	if ($limit == 0) $limit = 100;

	if (!autoriser('voirstats', $id_document ? 'document':'', $id_document)) {
		include_spip('inc/minipres');
		echo minipres();
	} else exec_statistiques_anaclic_args($id_document, $duree, $interval, $type, $limit);
}

/** Affichage des documents les plus telecharges
*/
function aff_statistiques_comptage_document($info, $serveur, $id_document, $where, $max)
{	// Compteur par document
	$result = sql_select("SUM(telechargement) AS c, id_document", "spip_doc_compteurs", $where, 'id_document', 'c DESC', $max,'', $serveur);
	
	$out = "";
	
	$classement = 0;
	while ($r = sql_fetch($result,$serveur)) 
	{	$id = $r['id_document'];
		$row = sql_fetsel("titre", "spip_documents", "id_document=$id", '', '', '','', $serveur);
		$nom = $row['titre'] ? $row['titre'] : _T('document')." #$id";
		$count = $r['c'];
		$titre = "";
		$classement++;
		//
		if ($id_document == $id) $out .= "<li class='on'><em>$classement.</em>$nom</li>";
		else $out .= "<li><em>$classement.</em><a href='" 
			. generer_url_ecrire("statistiques_anaclic_v3","id_document=$id") 
			. "' title='"
			._T('info_popularite_3', array('popularite' => $count, 'visites' => $visites))
			."'>$nom</a></li>";
	}

	return !$out ? '' : (
		debut_cadre_enfonce("",true)
		.typo($info)
		."<ul class='classement'>"
		.$out
		."</ul>"
		.fin_cadre_enfonce(true));
}

function exec_statistiques_anaclic_args($id_document, $duree, $interval, $type, $limit,$serveur='')
{	// Decompte
	if ($id_document)
	{	$row = sql_fetsel("SUM(telechargement) AS total_absolu", "spip_doc_compteurs","id_document=$id_document",'','','','',$serveur);
		$total_absolu = $row ? $row['total_absolu'] : 0;
		$row = sql_fetsel("titre", "spip_documents","id_document=$id_document",'','','','',$serveur);
		$titre = typo($row['titre']);
	} 
	else 
	{	$row = sql_fetsel("SUM(telechargement) AS total_absolu", "spip_doc_compteurs",'','','','','',$serveur);
		$total_absolu = $row ? $row['total_absolu'] : 0;
	}

	$pour = $titre ? " "._T('info_pour')." &laquo; $titre &raquo;" : "";
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('anaclic:statistiques_documents').$pour, "statistiques_analic", "analic");
	
	echo '<div id="haut">'.barre_onglets("statistiques", "statistiques_anaclic_v3").'</div>';
	//echo gros_titre(_T('anaclic:statistiques_documents'),'', false);
	if ($titre) echo gros_titre($titre,'', false);

	echo debut_gauche('', true);

	// Statistiques sur tout le site
	echo debut_cadre_enfonce("",true);
	echo typo(_T('anaclic:info_afficher'));
	echo "<ul>";
		if ($id_document>0) {
			echo "<li><b><a href='" . generer_url_ecrire("statistiques_anaclic_v3","") . "'>"._T('info_tout_site')."</a></b></li>";
		} else {
			echo "<li><b>"._T('titre_page_articles_tous')."</b></li>";
		}
	echo "</ul>";
	echo fin_cadre_enfonce(true);

	// Afficher le document
	if ($id_document)
	{	/*
		$docs = sql_allfetsel("*", "spip_documents", "id_document=$id_document", '', '');
		// $documenter = charger_fonction('documenter', 'inc');
		echo debut_cadre('e','doc-24.gif', '', $cadre, "document$id_document")
			//.$documenter ($docs, 'document', 'document')
			.fin_cadre($style);
		*/
	}
	
	$classement = array();
	$liste = 0;
	// Decompte depuis 1 mois
	echo aff_statistiques_comptage_document(_T('anaclic:info_telechargement_30'), $serveur, $id_document, "date > DATE_SUB(NOW(),INTERVAL 30 DAY)", 20);

	echo creer_colonne_droite('', true);
	
	if (autoriser('configurer','anaclic'))
	{	$res = icone_horizontale(_T('icone_configuration_site'), generer_url_ecrire("anaclic_config"), "statistiques-24.gif","rien.gif", false);
		echo bloc_des_raccourcis($res);
	}

	// Decompte depuis le debut
	$result = aff_statistiques_comptage_document(_T('anaclic:info_telechargement'), $serveur, $id_document, "", 20);
	echo $result;

	echo debut_droite('', true);
	
	// Limiter a un document
	$duree = intval(_request('duree'));
	if (!$duree) $duree = 90;
	$duree_mois = intval(_request('duree_mois'));
	if (!$duree_mois) $duree_mois = 12;
	$where = "date > DATE_SUB(".sql_quote(date('Y-m-d H:i:s')).",INTERVAL $duree day)";
	if ($id_document) $where .= " AND id_document=$id_document";
	
	// Calcul des statistiques / jour
	$result = sql_select("date, SUM(telechargement) AS total_absolu", "spip_doc_compteurs",$where,'date','date','','',$serveur);
	$statj = array();
	while ($row = sql_fetch($result)) 
	{	array_push ($statj, array ( $row['date'], $row['total_absolu'] ));
	}
	// Calcul des statistiques / mois
	$where = "date > DATE_SUB(".sql_quote(date('Y-m-1')).",INTERVAL $duree_mois month)";
	if ($id_document) $where .= " AND id_document=$id_document";
	$result = sql_select("YEAR(date) as year, MONTH(date) as month, SUM(telechargement) AS total_absolu", "spip_doc_compteurs",$where,'year,month','date','','',$serveur);
	$statm = array();
	while ($row = sql_fetch($result)) 
	{	array_push ($statm, array ( $row[year]."-".($row['month']<10?'0':'').$row['month'], $row['total_absolu'] ));
	}
	// Affichage
	echo debut_cadre_sous_rub('statistique-24.png',true,'','', 'statistiques');
	echo recuperer_fond ('fonds/statistiques_anaclic_v3', array('id_document'=>$id_document, 'duree'=>$duree, 'duree_mois'=>$duree_mois, 'stat_jour' => json_encode($statj), 'stat_mois' => json_encode($statm) ));
	echo fin_cadre_relief(true);

	echo fin_gauche(), fin_page();	
}

?>
