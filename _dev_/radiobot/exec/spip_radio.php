<?php
function exec_spip_radio()
{
include_spip("exec/sites");
include_spip("inc/scan");


global $page_dist;

	if($page_dist){
	
	//scanner la page
	$item = scan_it($page_dist);
	print_r($item);
	echo "<hr>";
	
	$nom_site=$item['nom_site'];
	$url_site = $item['url_site'];
	$nom_syndic_article =$item['nom_syndic_article'] ; 
	$url_syndic_article =$item['url_syndic_article'] ; 
	
	$parse=parse_url($page_dist);
	$host=$parse['host'];
	
	echo "<h2>$nom_site</h2><textarea>$url_site</textarea>";
	
	$row=spip_fetch_array(spip_query("SELECT nom_site, id_syndic  FROM spip_syndic WHERE url_site LIKE '%".$host."%' AND statut='publie' AND syndication='oui'"));
	
	print_r($row);
	
		if($row) {
		echo "<h1>". $row['nom_site']." </h1>" ;
		//echo $row['id_syndic'] ;
		$id_syndic=$row['id_syndic'] ;
		}else{
		echo "site <a href='$url_site'> $nom_site </a> inconnu au bataillon" ;
		
		//referencer le site
		/**/
		$id_rubrique=82;
		$id_secteur=18;
		$nom_site=spip_abstract_quote($nom_site);
		$url_site=spip_abstract_quote($url_site);

		spip_query("INSERT INTO spip_syndic
							(nom_site, url_site, id_rubrique, id_secteur, date, date_syndic, statut, syndication, moderation)
							VALUES ($nom_site, $url_site, $id_rubrique, $id_secteur, NOW(), NOW(), 'publie', 'non', 'non')");
							$id_syndic = spip_insert_id();
							echo $id_syndic."<-<br>";

		
		
		
		echo "->id_syndic=$id_syndic, url->$url_syndic_article<-<br>";
		//merci
		}
	
	
	echo "<hr>";
	$row=spip_fetch_array(spip_query("SELECT *  FROM spip_syndic_articles WHERE id_syndic='$id_syndic' AND url='$url_syndic_article'"));
	
	if($row) {
	echo "déjà syndic" ;
	echo "voir <a href='../index.php?id_syndic=".$id_syndic."'>le site en ligne</a>";
	
	//mettre à jour
	}else{
	echo "La page n'est pas  encore référencée<br>";
	
	$data2=array();
	$data2['url'] = $url_syndic_article ;
	$data2['date'] = time() ;
	$data2['titre'] = $nom_syndic_article ;
	$data2['description'] = $description_syndic_article ;
	
		if(sizeof($item['documents']['urls'])>0){
		echo "Création de l'article $nom_syndic_article...";
		inserer_article_syndique ($data2, $id_syndic, 'publie', '', $page_dist,'','') ;
		$track_url=$item['documents']['urls'];
		$track_titre=$item['documents']['titres'];
			
			//echo "<hr>";
			//echo sizeof($track_url);
			//print_r($track_titre);
			
			list($id_syndic_article) = spip_fetch_array(spip_query(
			"SELECT id_syndic_article FROM spip_syndic_articles
			WHERE id_syndic='$id_syndic' AND url='$page_dist'"),SPIP_NUM);
		
			//echo "<< $maj" ;
			
			// deja vu ?
			if (spip_num_rows(spip_query("SELECT id_document FROM spip_documents_syndic
			WHERE id_syndic_article='$id_syndic_article' ")) > 0 AND $maj != 'oui' )
				return;
				
			if(sizeof($track_url)>0){
			$k = 0;
			
			foreach ($track_url as $enclosure) {
		
					$url = urldecode($enclosure);
					$type = "audio/mpeg"; //a preciser
		
					// Verifier que le content-type nous convient
					list($id_type) = spip_fetch_array(spip_query("SELECT id_type
					FROM spip_types_documents WHERE mime_type='$type'"),SPIP_NUM);
					if (!$id_type) {spip_log("ps de type");}#continue;
		
						if($url != "http://www.mp3"){
			//echo $url;
						// Inserer l'enclosure dans la table spip_documents
						if ($t = spip_fetch_array(spip_query("SELECT id_document FROM
						spip_documents WHERE fichier='$url' AND distant='oui'"))){
							$id_document = $t['id_document'];
							spip_query("UPDATE spip_documents SET actif='oui' WHERE id_document=$id_document");
							echo "deja ref-->$id_document<br>"; 
							}
						else {
						
							$d = spip_fetch_array(spip_query("SELECT titre, descriptif FROM
						spip_syndic_articles WHERE id_syndic_article='$id_syndic_article'"));
							
							if($track_titre[$k] AND $track_titre[$k] ) {
							
							$titre = supprimer_tags(addslashes($track_titre[$k])) ;
							$descriptif = supprimer_tags(addslashes($d['descriptif'])) ;
							} else { 
							$titre = supprimer_tags(addslashes($d['titre'])) ;
							$descriptif = supprimer_tags(addslashes($d['descriptif'])) ;
							}
							
							spip_query("INSERT INTO spip_documents
							(id_type, titre, fichier, date, distant, taille, mode)
							VALUES ($id_type,'$titre','$url',NOW(),'oui','0', 'document')");
							$id_document = spip_insert_id();
							echo $id_document."<-<br>";
						}
		
		
					echo "lions l article ($id_document, $id_syndic, $id_syndic_article)";
					// lier avec l'article syndique
					spip_query("INSERT INTO spip_documents_syndic
					(id_document, id_syndic, id_syndic_article)
					VALUES ($id_document, $id_syndic, $id_syndic_article)");
					
					echo "article : ".$d['titre']."<br>";
					$enf = spip_fetch_array(spip_query("SELECT id_document FROM spip_documents_syndic WHERE id_syndic_article='$id_syndic_article'"));
					print_r($enf);
					while($enf){echo "doc : ".$enf['id_document']."<br>";}
					//indexer le doc
					include_ecrire('inc_index.php3');
					marquer_indexer('document', $id_document);
					}
					$k++;
				
			}
			}
		
		echo "voir <a href='../index.php?id_syndic=".$id_syndic."&var_mode=recalcul'>le site en ligne</a>";
		}else{ 
		echo "pas de document à importer dans la page $nom_syndic_article" ;
		}
	
	}
}

}
?>