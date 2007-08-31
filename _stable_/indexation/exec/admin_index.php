<?php

/*
 * Recherche entendue
 * plug-in d'outils pour la recherche et l'indexation
 * Panneaux de controle admin_index et index_tous
 * Boucle INDEX
 * filtre google_like
 *
 *
 * Auteur :
 * cedric.morin@yterium.com
 * pdepaepe et Nicolas Steinmetz pour google_like
 * fil pour le panneau admin_index d'origine
 * © 2005 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
define('_SIGNALER_ECHOS', false); // horrible :p
//include_spip('inc/indexation'); inclus dans le corps de la fonction pour resetter les meta si besoin
//include_spip('inc/indexation_etendue');
include_spip('inc/presentation');

function jauge($couleur,$pixels) {
	if ($pixels)
	  echo http_img_pack("jauge-$couleur.gif", $couleur, "height='10' width='$pixels'");
}

function exec_admin_index_dist()
{
	global $connect_statut, $connect_toutes_rubriques, $couleur_claire;

	$INDEX_elements_objet = array();
	if (isset($GLOBALS['meta']['INDEX_elements_objet']))
		$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
	
	debut_page(_T('rechercheetendue:moteur_recherche'), "administration", "cache");
	
	debut_gauche();
	
	debut_boite_info();
	echo propre(_T('rechercheetendue:info_admin_index'));
	fin_boite_info();
	
	debut_raccourcis();
	echo "<p>";
	icone_horizontale (_T('rechercheetendue:vocabulaire_indexe'),  generer_url_ecrire("index_tous"), "../"._DIR_PLUGIN_RECHERCHEETENDUE."/img_pack/stock_book-alt.gif");
	echo "</p>";
	
	icone_horizontale (_T('rechercheetendue:indexation_forcer'), generer_url_ecrire("admin_index", "forcer_indexation=20"), "../"._DIR_PLUGIN_RECHERCHEETENDUE."/img_pack/stock_exec.gif");
	icone_horizontale (_T('rechercheetendue:indexation_relancer'), generer_url_ecrire("admin_index", "forcer_indexation=oui"), "../"._DIR_PLUGIN_RECHERCHEETENDUE."/img_pack/stock_exec.gif");
	echo "<div style='width: 100%; border-top: solid 1px white;background: url(".http_wrapper('rayures-danger.png').");'>";
	icone_horizontale (_T('rechercheetendue:indexation_purger'), generer_url_ecrire("admin_index", "purger=oui"), "effacer-cache-24.gif");
	icone_horizontale (_T('rechercheetendue:indexation_resetter'), generer_url_ecrire("admin_index", "resetmeta=oui"), "effacer-cache-24.gif");
	echo "</div>";
	
	fin_raccourcis();


	debut_droite();
	gros_titre(_T('rechercheetendue:moteur_recherche'));
	
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	if (_request('resetmeta')=='oui'){
		include_spip('inc/meta');
		effacer_meta('INDEX_elements_objet');
		effacer_meta('INDEX_objet_associes');
		effacer_meta('INDEX_elements_associes');
		effacer_meta('INDEX_critere_indexation');
		effacer_meta('INDEX_iteration_nb_maxi');
		ecrire_metas();
	}
	include_spip('inc/indexation');
	include_spip('inc/indexation_etendue');
	if (version_compare($GLOBALS['spip_version_code'],'1.92','<'))
		RechercheEtendue_verifier_base();
	
	
	if ($forcer_indexation = intval(_request('forcer_indexation')))
		effectuer_une_indexation ($forcer_indexation);
	
	if (_request('forcer_reindexation') == 'oui')
		creer_liste_indexation();
	
	if (_request('purger') == 'oui') {
		spip_query("DELETE FROM spip_syndication");
		creer_liste_indexation();
	}
	
	$liste_tables = array();
	$icone_type = array();
	update_index_tables();
	#update_index_tables_sql_from_meta();  // ??
	$liste_tables = liste_index_tables();
	asort($liste_tables);
	
	$icone_spec=array('spip_forum'=>'forum-public-24.gif','spip_syndic'=>'site-24.gif','spip_documents'=>'doc-24.gif','spip_mots'=>'mot-cle-24.gif','spip_signatures'=>'suivi-petition-24.gif');
	
	foreach($liste_tables as $table){
		$typ = preg_replace("{^spip_}","",$table);
		if (substr($typ,-1,1)=='s')
		  $typ = substr($typ,0,strlen($typ)-1);
		$icone = "$typ-24.gif";
		if (isset($icone_spec[$table]))
			$icone = $icone_spec[$table];
		$icone_table[$table] = $icone;
	}

	// graphe des objets indexes
	foreach($liste_tables as $table){
		$critere = critere_indexation($table);
		$id_table = id_index_table($table);
		$col_id = primary_index_table($table);
	
		// 
		$index_total[$table] = sql_countsel($table, array($critere));
		$indexes[$table]['oui'] = sql_countsel('spip_indexation', array('type='.$id_table));

		$indexes[$table]['non'] = $index_total[$table] - $indexes[$table]['oui'];
;


	}
	
	debut_cadre_relief();

	echo "<table>";
	foreach($liste_tables as $table){
		if ($ifond==0){
			$ifond=1;
			$couleur="$couleur_claire";
		}else{
			$ifond=0;
			$couleur="#FFFFFF";
		}
		echo "<tr style='background-color:$couleur;'>";
		echo "<td style='width:100;'>";
		echo "<span style='font:arial,helvetica,sans-serif;font-size:small;'>";
		echo $table;
		echo "</span><td>";
		if (isset($INDEX_elements_objet[$table])){
			if ($index_total[$table]>0) {
				if ($index_total[$table]>0) {
					jauge('rouge', $a = floor(300*$indexes[$table]['non']/$index_total[$table]));
					jauge('vert', $b = ceil(300*$indexes[$table]['oui']/$index_total[$table]));
					jauge('fond', 300-$a-$b);
				}
			}
			else{
				echo _T("rechercheetendue:indexer_aucun");
			}
		}
		else{
			echo _T("rechercheetendue:indexation_non_configuree");
		}
		echo "</td><td>";
		if ($index_total[$table]>0) {
			echo "<span style='font:arial,helvetica,sans-serif;font-size:small;'>";
			if (($n = $indexes[$table]['oui'])!='')
			  echo $n;
			else
			  echo '0';
			echo "/" . $index_total[$table];
			if (($n = $indexes[$table]['non'])!='')
				echo "[-" . $indexes[$table]['non'] . "]";
			echo "</span>";
		}
		echo "</td></tr>\n";
	}
	echo "</table>";
	
	fin_cadre_relief();
	
	
	echo "<br/>";
	
	fin_page();
}
?>
