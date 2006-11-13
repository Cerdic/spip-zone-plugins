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


function snippets_articles_importer($id_target,$arbre,$contexte){
	include_spip('base/serial');
	include_spip('base/abstract_sql');
	$champs_non_importables = array('id_article',"id_rubrique","id_secteur","maj","export","visites","referers","popularite","id_trad","idx","id_version","url_propre");
	$champs_non_ajoutables = array('titre',"statut",'date','date_redac','lang');
	$champs_defaut_values = array('statut'=>'redac');
	$table = 'spip_articles';
	$primary = 'id_article';
	$fields = $GLOBALS['tables_principales']['spip_articles']['field'];
	$tag_objets="articles";
	$tag_objet="article";
	$translations = array();
	
	if ($arbre && isset($arbre[$tag_objets]))
		foreach($arbre[$tag_objets] as $objets){
			foreach($objets[$tag_objet] as $objet){
				$creation = false;
				include_spip('action/editer_article');
				// si c'est une creation, creer le formulaire avec les infos d'entete
				if (!($id_objet=intval($id_target))){
					if (preg_match(",id_rubrique=([0-9]*),i",$contexte,$regs))
						$id_rubrique=intval($regs[1]);
					$id_objet = insert_article($id_rubrique);
					$creation = true;
				}
				// sinon on ajoute chaque champ, sauf le titre
				$row = spip_fetch_array(spip_query("SELECT * FROM $table WHERE $primary="._q($id_objet)));
				foreach (array_keys($row) as $key)
					if ( 	!in_array($key,$champs_non_importables) 
						AND ($creation OR !in_array($key,$champs_non_ajoutables) OR !$row[$key])
						AND isset($objet[$key])){
						$v=trim(applatit_arbre($objet[$key]));
						$row[$key] = $creation?$v:($row[$key].$v);
					}
				revisions_articles($id_objet , $row);
				$translations[] = array($table,$objet[$primary],$id_objet);
				// gerer l'import de liens eventuels
				if ($id AND isset($objet['liens'])){
					foreach($objet['liens'] as $liens){
						// A FAIRE
					}
				}
			}
		}
	return $translations;
}

?>