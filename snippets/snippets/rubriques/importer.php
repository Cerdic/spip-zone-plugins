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


function snippets_rubriques_importer($id_target,$arbre,$contexte){
	include_spip('base/serial');
	include_spip('base/abstract_sql');
	include_spip('inc/snippets');
	include_spip('snippets/articles/importer');
	
	$table_prefix = $GLOBALS['table_prefix'] ;

	$champs_non_importables = array('id_article',"id_rubrique","id_secteur","maj","export","visites","referers","popularite","id_trad","idx","id_version","url_propre");
	$champs_non_ajoutables = array('titre',"statut",'date','date_redac','lang');
	$champs_jointures = array('auteur','mot');
	$champs_defaut_values = array('statut'=>'prop');
	$table = 'spip_rubriques';
	$primary = 'id_rubrique';
	$fields = $GLOBALS['tables_principales']['spip_rubriques']['field'];
	$tag_objets="rubriques";
	$tag_objet="rubrique";
	$translations = array();
	
	if ($arbre && isset($arbre[$tag_objets]))
		foreach($arbre[$tag_objets] as $objets){
			foreach($objets[$tag_objet] as $objet){
				include_spip('action/editer_rubrique');
				// si c'est une creation, creer le formulaire avec les infos d'entete
				if (!($id_objet=intval($id_target))){
					if (preg_match(",id_rubrique=([0-9]*),i",$contexte,$regs))
						$id_rubrique=intval($regs[1]);
					$id_objet = insert_rubrique($id_rubrique);
					$creation = true;
				}
				// sinon on ajoute chaque champ, sauf le titre
				$row = spip_fetch_array(spip_query("SELECT * FROM $table WHERE $primary="._q($id_objet)));
				foreach (array_keys($row) as $key)
					if ( 	!in_array($key,$champs_non_importables) 
						AND !in_array($key,$champs_jointures) 
						AND ($creation OR !in_array($key,$champs_non_ajoutables) OR !$row[$key])
						AND isset($objet[$key])){
						$v=trim(spip_xml_aplatit($objet[$key]));
						$row[$key] = $creation?$v:($row[$key].$v);
					}
								
				revisions_rubriques($id_objet , $row);
				//$translations[] = array($table,$objet[$primary],$id_objet);
				// gerer l'import de liens eventuels
				
				if ( $objet['liste_articles']){
					snippets_articles_importer("",$objet['liste_articles'][0],"id_rubrique=$id_objet") ;              							
				}
				
				
				if ($objet['liste_rubriques']){
				snippets_rubriques_importer("",$objet['liste_rubriques'][0],"id_rubrique=$id_objet");
				}
				
        		
        		
        		
			}
		}
	return "";
}



?>