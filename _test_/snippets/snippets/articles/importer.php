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
	$champs_jointures = array('auteur');
	$champs_defaut_values = array('statut'=>'prop');
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
				$auteur_connu = false ;
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
						AND !in_array($key,$champs_jointures) 
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
				
				if ( $objet['auteur'] AND $creation){
					$auteur_connu = true ;
					foreach($objet['auteur'] as $nom){
					// ajouter l'auteur
					$id_article = $id_objet ;
					$table_prefix = $GLOBALS['table_prefix'] ;
						$id_auteur = get_id_auteur($nom);
  				         if ($id_auteur) {  				                
        				 $sql="INSERT INTO ".$table_prefix."_auteurs_articles (id_auteur, id_article) VALUES ($id_auteur, $id_article)";
        				 spip_query($sql);                              				              	
        				}                   							
					}
				}	
				
				if($auteur_connu){
				// se virer soi-meme
        		$connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'] ;
        		$sql = "DELETE FROM ".$table_prefix."_auteurs_articles WHERE id_auteur = '$connect_id_auteur' AND id_article = '$id_article'";
        		spip_query($sql); 
        		}
        		
        		// statut de l'article
        		if($champs_defaut_values['statut'] != 'prepa'){
        		$sql = "UPDATE ".$table_prefix."_articles SET statut = '".$champs_defaut_values['statut']."' WHERE id_article = '$id_article'";
        		spip_query($sql); 
        		}
			}
		}
	return $translations;
}


// d'apres spip2spip par erationnal
// recupere id d'un auteur selon son nom ou le creer
function get_id_auteur($name) {
    if (trim($name)=="") return false;    
    $sql = "SELECT id_auteur FROM spip_auteurs WHERE nom='".addslashes($name)."'";
    $result = spip_query($sql);
    while ($row = spip_fetch_array($result)) {
       return $row['id_auteur'];
    }
    // auteur inconnu, on le cree ...
    $sql = "INSERT INTO spip_auteurs (nom, statut) VALUES (\"$name\", '1comite')";
    $result = spip_query($sql);
    return spip_insert_id();
}



?>