<?php

// Syndication : ce plugin permet d'integrer les <enclosure>
// des flux RSS sous la forme de documents distants dans la
// table spip_documents
//
// Il renseigne aussi une table de jointure
// spip_documents_syndic (id_document, id_syndic, id_syndic_article)
// qui sera aussi prise en compte par le compilateur
//
// (par defaut, on se contente de conserver une trace de ces
// documents dans le champ #TAGS de l'article syndique).


//
// recupere les donnees du point d'entree 'post_syndication'
//

	function PodcastClient_podcast_client() {
		PodcastClient_verifier_table_documents_syndic();
	
		list($le_lien, $id_syndic, $data) = func_get_arg(0);
		//print_r($data);
		//mettre les documents dans un tableau

		// trouver des enclosures (podcast)
		$enclosure = $data['enclosures'] ;
		// href et type sont obligatoires
			if (($enc_regs_url = extraire_attribut($enclosure,'href')
			AND $enc_regs_type = extraire_attribut($enclosure,'type'))) {
				$url  = substr(urldecode($enc_regs_url), 0,255);
				$data['enc_enclosure']['url'] = addslashes(url_absolue($url, $le_lien));
				$data['enc_enclosure']['type'] = trim($enc_regs_type) ;
				$data['enc_enclosure']['length'] = extraire_attribut($enclosure,'length') ;
				if(!$data['enc_enclosure']['type']) $data['enc_enclosure']['type'] = substr($url, -3);
			}

		$data['enclosures_all'][] = $data['enc_enclosure'] ;	

		// analyser le descriptif de l'article syndiqu�
		// trouver une image dans le descriptif (flickr)

		// <img src="http://farm1.static.flickr.com/209/465887020_320ee68662_m.jpg" width="180" height="240" alt="Ap�ro Belleville 19/7/04" style="border: 1px solid #ddd;" />
		$enclosure = $data['descriptif'] ;
			if (($enc_regs_url = extraire_attribut($enclosure,'src'))) {
				$url  = substr(urldecode($enc_regs_url), 0,255);
				//var_dump($url);

				$ext = substr($url, -3);
				if(preg_match("/(jpg|gif|png)/",$ext)){
				$data['enc_image']['url'] = addslashes(url_absolue($url, $le_lien));
				$data['enc_image']['type'] = $ext ;

				}
			}
			
		$data['enclosures_all'][] = $data['enc_image'] ;

		//trouver un flv chez  dailymotion ou une image chez flickr
		# <media:content url="http://www.dailymotion.com/get/14/80x60/flv/3088827.flv?key=733d92b170af04321c5d108635a6d94512c133a.flv" type="video/x-flv" duration="215" width="80" height="60"/>

		# <media:content url="http://farm1.static.flickr.com/169/465892205_c6232dab5f_o.jpg" type="image/jpeg" height="1920" width="2560"/>
		//echo $item ;
		if (preg_match(',(<media:content[^>]*>),i',
		$data['item'], $match)) {
			$go=str_replace('media:content','mediacontent',$match[1]);
			$data['enc_media']['url'] = extraire_attribut($go, 'url');
			$data['enc_media']['duration'] = extraire_attribut($go, 'duration');
			$data['enc_media']['width'] = extraire_attribut($go, 'width');
			$data['enc_media']['height'] = extraire_attribut($go, 'height');
			$data['enc_media']['type'] = trim(extraire_attribut($go,'type'));
		}

		// trouver une vignette miniature du media
		// <media:thumbnail url="http://static-02.dailymotion.com/dyn/preview/320x240/2084714.jpg?20070218160100" height="240" width="320"/>	

		// <media:thumbnail url="http://farm1.static.flickr.com/169/465892205_c22c43b50d_s.jpg" height="75" width="75"/>		
		if (preg_match(',(<media:thumbnail[^>]+\/>),i',
		$data['item'], $match)) {
			$go=str_replace('media:thumbnail','mediathumbnail',$match[1]);
			$data['enc_thumbnail']['url'] = extraire_attribut($go, 'url');
			$data['enc_thumbnail']['type'] = 'jpg';
		}

		// si le media est un flv, on prend le flv et aussi la miniature (a terme la mettre dans la vignette du doc distant spip)
		if($data['enc_media']['type'] == 'video/x-flv'){	
			$data['enclosures_all'][] = $data['enc_media'] ;
			$data['enclosures_all'][] = $data['enc_thumbnail'] ;
		}
		
		
		//trouver une video utube ou dalilymotion
		
		# <embed width="480" height="390" src="http://www.dailymotion.com/swf/1mt6qo4nfRX3Np77L" 
		# type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always">
		
		//var_dump($data['item']);
		
		//echo $item ;
		if (preg_match(',(<embed[^>]*>),i',
		$data['item'], $match)) {
			$go=str_replace('media:content','mediacontent',$match[1]);
			$data['enc_emb']['url'] = extraire_attribut($go, 'src');
			$data['enc_emb']['duration'] = extraire_attribut($go, 'duration');
			$data['enc_emb']['width'] = extraire_attribut($go, 'width');
			$data['enc_emb']['height'] = extraire_attribut($go, 'height');
			$data['enc_emb']['type'] = trim(extraire_attribut($go,'type'));
			
			//var_dump($data['enc_emb']);
			$data['enclosures_all'][] = $data['enc_emb'] ;

		}


		
//var_dump($data['item']);var_dump($data['descriptif']);var_dump($data['enclosures_all']);die("coucou");

		/**/
		PodcastClient_traiter_les_enclosures_rss($data['enclosures_all'],$id_syndic,$le_lien);

		return func_get_arg(0); # remettre les infos dans le pipeline
	}

	function PodcastClient_delete_podcast_client() {
		spip_query("DROP TABLE spip_documents_syndic");
	}

	//
	// Verifie que la table spip_documents_syndic existe, sinon la creer
	//
	function PodcastClient_verifier_table_documents_syndic() {
		if (!spip_query("SELECT id_syndic, id_syndic_article, id_document FROM spip_documents_syndic")) {
			spip_log('creation de la table spip_documents_syndic');
			include_spip('base/create');
			include_spip('base/abstract_sql');
            creer_base();
		}
	}

	//
	// Inserer les references aux fichiers joints
	// presentes sous la forme microformat <a rel="enclosure">
	//
	function PodcastClient_traiter_les_enclosures_rss($enclosures,$id_syndic,$le_lien) {
	spip_log('podcast_client'.$le_lien.'\n\n');
		if (sizeof($enclosures) == 0) return false ;
		include_spip('inc/filtres'); # pour extraire_attribut
	
		list($id_syndic_article) = spip_fetch_array(spip_query(
		"SELECT id_syndic_article FROM spip_syndic_articles
		WHERE id_syndic=$id_syndic AND url='".addslashes($le_lien)."'"), SPIP_NUM);
	
		// Attention si cet article est deja vu, ne pas doubler les references 
		spip_query("DELETE FROM spip_documents_syndic
		WHERE id_syndic_article=$id_syndic_article");
		spip_log("efface");
		// Integrer les enclosures
		foreach ($enclosures as $enclosure) {

			$url = $enclosure['url'] ;
			$type = $enclosure['type'] ;
			// href et type sont obligatoires
			if ($enclosure['url'] AND $enclosure['type']) {
			spip_log("type : ".$enclosure['type']) ;
			// Verifier que le content-type nous convient
				$row = spip_fetch_array(spip_query("SELECT id_type
				FROM spip_types_documents WHERE mime_type='$type'"));
				$id_type = $row['id_type'] ;
				spip_log("id_type1 : ".$id_type) ;
				if (!$id_type) {
				list($id_type) = spip_fetch_array(spip_query("SELECT id_type
					FROM spip_types_documents WHERE extension='$type'"), SPIP_NUM);
				spip_log("id_type2 : ".$id_type) ;
				}
				
				
				if (!$id_type) {
					spip_log("podcast_client: enclosure inconnue ($type) $url");
					list($id_type) = spip_fetch_array(spip_query("SELECT id_type
					FROM spip_types_documents WHERE extension='bin'"), SPIP_NUM);
					spip_log("id_type3 : ".$id_type) ;
					// si les .bin ne sont pas autorises, on ignore ce document
					if (!$id_type) continue;
				}
				spip_log("id_type : ".$id_type) ;
				// length : optionnel (non bloquant)
				
				$taille = intval($enclosure['length']);
				$largeur = intval($enclosure['width']);
				$hauteur = intval($enclosure['height']);
				$duree = intval($enclosure['duration']);
				spip_log("taille : ".$taille." largeur : ".$largeur." hauteur : ".$hauteur." duree ".$duree);
				// Inserer l'enclosure dans la table spip_documents
				if ($t = spip_fetch_array(spip_query("SELECT id_document FROM
				spip_documents WHERE fichier='$url' AND distant='oui'"))){
					$id_document = $t['id_document'];
				spip_log("deja vu doc->".$id_document) ;
				} else {

					spip_query("INSERT INTO spip_documents
					(id_type, titre, fichier, date, distant, taille, mode, largeur, hauteur)
					VALUES ($id_type,'','$url',NOW(),'oui',$taille, 'document', $largeur, $hauteur)");
					$id_document = spip_insert_id();
					spip_log("podcast_client: '$url' => id_document=$id_document");
					
				}
	
				// lier avec l'article syndique
				spip_query("INSERT INTO spip_documents_syndic
				(id_document, id_syndic, id_syndic_article)
				VALUES ($id_document, $id_syndic, $id_syndic_article)");
				spip_log("lier doc->".$id_document."Sarticle".$id_syndic_article) ;
				$n++;
			}
		
		
		}
	
		return $n; #nombre d'enclosures integrees
	}


?>