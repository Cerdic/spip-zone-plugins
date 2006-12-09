<?php

/*
 * gestion_documents
 *
 * interface de gestion des documents
 *
 * Auteur : cedric.morin@yterium.com
 * © 2006 - Distribue sous licence GPL
 *
 */

if (!defined('_DIR_PLUGIN_GESTIONDOCUMENTS')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_GESTIONDOCUMENTS',(_DIR_PLUGINS.end($p)));
}

function exec_reparer_liens_documents(){
	global $connect_statut;
	
	include_spip ("inc/presentation");
	include_spip ("inc/documents");
	include_spip ("inc/logos");
	include_spip ("inc/session");
	include_spip ("inc/indexation");

	//
	// Recupere les donnees
	//

	debut_page(_T('gestdoc:reparer_liens'), "documents", "documents");
	debut_gauche();


	//////////////////////////////////////////////////////
	// Boite "voir en ligne"
	//

	debut_boite_info();

	echo propre(_T('gestdoc:info_reparer'));

	fin_boite_info();

	debut_raccourcis();
	icone_horizontale (_T('gestdoc:portfolio'), 
		generer_url_ecrire('portfolio'),
		"../"._DIR_PLUGIN_GESTIONDOCUMENTS."/img_pack/stock_broken_image.png");
	fin_raccourcis();
	debut_droite();

	global $connect_statut;
	if ($connect_statut != '0minirezo') {
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}
	
	$liste_tables = array('spip_articles'=>'spip_documents_articles','spip_breves'=>'spip_documents_breves','spip_rubriques'=>'spip_documents_rubriques');
	

	foreach($liste_tables as $table=>$lien){
		$primary = primary_index_table($table);
		$res = spip_query("SELECT * FROM $table");
		while ($row = spip_fetch_array($res,SPIP_ASSOC)){
			$liste_doc = array();
			$id_objet = $row[$primary];
			foreach($row as $field=>$value){
				if (preg_match_all(",<(img|doc|emb)([0-9]+)(\|[[:alnum:]])*>,",$value,$matches,PREG_SET_ORDER)){
					foreach($matches as $match){
						#echo "$primary=$id_objet champ $field : tag " . $match[1] .$match[2] ."<br/>";
						$liste_doc[intval($match[2])]=true;
					}
				}
			}
			if (count($liste_doc)){
				// reperer les liens deja existants
				$cond = calcul_mysql_in("id_document", implode(",",array_keys($liste_doc)));
				$res2 = spip_query("SELECT * FROM $lien WHERE $cond AND $primary=".spip_abstract_quote($id_objet));
				while ($row2 = spip_fetch_array($res2))
					unset($liste_doc[$row2['id_document']]);
				// et ne garder que les docs existants
				$cond = calcul_mysql_in("id_document", implode(",",array_keys($liste_doc)));
				$res2 = spip_query("SELECT id_document FROM spip_documents WHERE $cond");
				$temp = $liste_doc;
				$liste_doc = array();
				while ($row2 = spip_fetch_array($res2))
					$liste_doc[$row2['id_document']] = $temp[$row2['id_document']];
			}
			if (count($liste_doc)){
				foreach($liste_doc as $id_document=>$dummy){
					echo _T("gestdoc:lien_ajoute")." (id_document=$id_document,$primary=$id_objet) <br/>";
					spip_abstract_insert($lien,"(id_document,$primary)","(".spip_abstract_quote($id_document).",".spip_abstract_quote($id_objet).")");
				}
			}
		}
	}
	
	echo _T("gestdoc:mis_jour_liens");
	fin_page();
}

?>
