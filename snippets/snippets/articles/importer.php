<?php
/*
 * snippets
 * Gestion d'import/export XML de contenu
 *
 * Auteurs :
 * Cedric Morin
 * � 2006 - Distribue sous licence GNU/GPL
 *
 */


function snippets_articles_importer($id_target,$arbre,$contexte){
	include_spip('base/serial');
	include_spip('base/abstract_sql');
	include_spip('inc/snippets');
	
	$table_prefix = $GLOBALS['table_prefix'] ;

	$champs_non_importables = array("id_article","id_rubrique","id_secteur","maj","export","visites","referers","popularite","id_trad","idx","id_version","url_propre");
	$champs_non_ajoutables = array('titre','date','date_redac','lang');
	$champs_jointures = array('auteur','mot');
	$champs_defaut_values = array('statut'=>'lememe');
	$table = 'spip_articles';
	$primary = 'id_article';
	$fields = $GLOBALS['tables_principales']['spip_articles']['field'];
	$tag_objets="articles";
	$tag_objet="article";
	$translations = array();
	
	$forcer_id = false ; // mettre true pour garder les meme id que dans le XML
	$forcer_maj = false ; // mettre true pour mettre a jour un lot d'articles de meme titre que dans le spip cible
	
	
	if ($arbre && isset($arbre[$tag_objets]))
		foreach($arbre[$tag_objets] as $objets){
			foreach($objets[$tag_objet] as $objet){
			spip_log($objet['titre'],"snippets");
				$creation = false;
				$auteur_connu = false ;
				
				// mettre a jour des articles deja en bdd avec le xml fournit
				if($forcer_maj){
				$id_target = '';
				$id_article_trouve = sql_fetsel("id_article","spip_articles","titre=".sql_quote($objet['titre'][0])); // ajouter la rub courrante
				$id_target = $id_article_trouve['id_article'] ;
				if(!intval($id_target))
					spip_log($objet['titre'][0].$id_target,"snippets_titres_erreur");
				}
				
				include_spip('action/editer_article');
				// si c'est une creation, creer le formulaire avec les infos d'entete
				if (!($id_objet=intval($id_target))){
					if (preg_match(",id_rubrique=([0-9]*),i",$contexte,$regs))
						$id_rubrique=intval($regs[1]);
					$id_objet = insert_article($id_rubrique);
					
					// forcer l'id
					if($forcer_id){
					$champs_non_importables = array("id_rubrique","id_secteur","maj","export","visites","referers","popularite","id_trad","idx","id_version","url_propre");
					$sql = "UPDATE ".$table_prefix."_articles SET id_article = '".$objet['id_article'][0]."' WHERE id_article = '$id_objet'";
        			spip_query($sql); 
        			$id_objet = $objet['id_article'][0] ;
					}
					
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
						$row[$key] = ($creation or $forcer_maj)?$v:($row[$key].$v);
					}
								
				revisions_articles($id_objet , $row);
				$translations[] = array($table,$objet[$primary],$id_objet);
				// gerer l'import de liens eventuels
				if ($id AND isset($objet['liens'])){
					foreach($objet['liens'] as $liens){
						// A FAIRE
					}
				}
				
				$id_article = $id_objet ; 
				
				if ( $objet['auteur'] AND ($creation OR $forcer_maj)){
					$auteur_connu = true ;
					sql_delete("spip_auteurs_articles","id_article=".sql_quote($id_article));
					foreach($objet['auteur'] as $nom){
					// ajouter l'auteur
						spip_log($nom,"snippets");
						$id_auteur = get_id_auteur($nom);
  				         if ($id_auteur) {  
  				         spip_log($nom.$id_auteur,"snippets");
        				 $sql="INSERT INTO ".$table_prefix."_auteurs_articles (id_auteur, id_article) VALUES ($id_auteur, $id_article)";
        				 spip_query($sql);                              				              	
        				}                   							
					}
				}	
				
				if($auteur_connu){
				// se virer soi-meme
        		$connect_id_auteur = $GLOBALS['visiteur_session']['id_auteur'] ;
        		$sql = "DELETE FROM ".$table_prefix."_auteurs_articles WHERE id_auteur = '$connect_id_auteur' AND id_article = '$id_article'";
        		spip_query($sql); 
        		}
        		
        		// statut de l'article
        		if($champs_defaut_values['statut'] != 'prepa'){
        		$sql = "UPDATE ".$table_prefix."_articles SET statut = '".$objet['statut'][0]."' WHERE id_article = '$id_article'";
        		spip_query($sql); 
        		}
        		
        		
        		if ( $objet['mot'] AND ($creation OR $forcer_maj)){
			
					sql_delete("spip_mots_articles","id_article=".sql_quote($id_article));
					foreach($objet['mot'] as $mot){
					spip_log($mot,"snippets");
					// ajouter le mot cle
					$id_article = $id_objet ;
					$table_prefix = $GLOBALS['table_prefix'] ;
						$id_mot  = get_id_mot($mot);
  				         if ($id_mot) {  				                
        				 $sql="INSERT INTO ".$table_prefix."_mots_articles (id_mot, id_article) VALUES ($id_mot, $id_article)";
        				 spip_query($sql);                              				              	
        				}                   							
					}
				}	
        		
        		
        		
        		if ( $objet['document'] AND ($creation OR $forcer_maj)){
			
					sql_delete("spip_documents_liens","id_objet=".sql_quote($id_article)." and objet='article'");
					foreach($objet['document'] as $doc){
					spip_log("--> ".$doc,"snippets");
					// ajouter le doc
					list($id,$fichier,$extension,$titre,$descriptif) = explode('|',$doc) ;
					spip_log("--- $id,$fichier,$extension,$titre","snippets");
					spip_log("hop ".$fichier,"snippets");
					$table_prefix = $GLOBALS['table_prefix'] ;
						 $id_doc  = get_id_doc($fichier);
  				         if ($id_doc) {  				                
        				 spip_log("hop doc".$fichier."trouve".$id_doc,"snippets");
        				 $sql="INSERT INTO ".$table_prefix."_documents_liens (id_document, id_objet, objet) VALUES ($id_doc, $id_article,'article')";
        				 spip_query($sql);                              				              	
        				 }else{
        				 // au cas ou, on creer un doc, mais il vaut mieux transferer les doc avec toutes les infos avant de snippet
        				 $a =  array(
							'date' => 'NOW()',
							'distant' => 'non',
							'mode' => 'image',
							'titre'=> $titre,
							'descriptif'=> $descriptif,
							'extension'=> $extension,
							'fichier' => $fichier );
							
							//'largeur' => $largeur,
							//'hauteur' => $hauteur,
							//'taille' => $taille,
							
						if($forcer_id) $a['id_document'] = $id ;	
			 	    	
			 	    	$id = sql_insertq("spip_documents", $a);
        				spip_log ("ajout du document $fichier $titre  (M 'image' T 'article' L '$id_article' D '$id')","snippets");
        				
        				sql_insertq("spip_documents_articles",
   	                    	array('id_document' => $id,
    	                    'id_article' => $id_article));
        				 
        				 }
        				
					}
				}
        	
        		
        		
			}
		}
	return $translations;
}



?>