<?php
/*
 * snippets
 * Gestion d'import/export XML de contenu
 *
 * Auteurs :
 * Cedric Morin
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */


function snippets_articles_importer($id_target,$source){
	include_spip('inc/forms');
	include_spip('inc/xml');
	include_spip('base/serial');
	include_spip('base/abstract_sql');
	$champs_non_importables = array('id_article',"id_rubrique","id_secteur","maj","export","visites","referers","popularite","id_trad","idx","id_version","url_propre");
	$champs_non_ajoutables = array('titre',"statut",'date','date_redac');
	$table = 'spip_articles';
	$primary = 'id_article';
	$fields = $GLOBALS['tables_principales']['spip_articles']['field'];
	$tag_objets="articles";
	$tag_objet="article";
	
	$arbre = spip_xml_load($source);
	if ($arbre && isset($arbre[$tag_objets]))
		foreach($arbre[$tag_objets] as $objets){
			foreach($objets[$tag_objet] as $objet){
				$names = array();
				$values = array();
				foreach (array_keys($fields) as $key)
					if (!in_array($key,$champs_non_importables) AND isset($objet[$key])){
						$values[$key] = trim(applatit_arbre($objet[$key]));
					}
				// si c'est une creation, creer le formulaire avec les infos d'entete
				if (!($id_objet=intval($id_target))){
					spip_abstract_insert($table,"(".implode(",",array_keys($names)).")","(".implode(",",array_map('_q',$values)).")");
					$id = spip_insert_id();
				}
				else { // sinon on ajoute chaque champ, sauf le titre
					$row = spip_fetch_array(spip_query("SELECT * FROM $table WHERE $primary="._q($id_objet)));
					$set = "";
					foreach ($values as $key=>$val)
						if (!in_array($key,$champs_non_ajoutables))
							$set .= "$key="._q((isset($row[$key])?$row[$key]:"").$val).",";
					if (strlen($set)){
						$set = substr($set,0,strlen($set)-1);
						spip_query("UPDATE $table SET $set WHERE $primary="._q($id_objet));
					}
				}
				// gerer l'import de liens eventuels
				if ($id AND isset($objet['liens'])){
					foreach($objet['liens'] as $liens){
						// A FAIRE
					}
				}
			}
		}
}

?>