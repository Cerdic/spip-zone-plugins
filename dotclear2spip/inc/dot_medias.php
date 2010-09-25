<?php

function dot_lister_medias($texte){
	
	
	
	preg_match_all('#<img[(\S )]*/>#',$texte,$match);
	$analyse = array();
	$i = 0;
	foreach($match[0] as $img){
		$analyse[$i]['src']	= extraire_attribut($img,'src');
		$analyse[$i]['alt']	= extraire_attribut($img,'alt');
		$analyse[$i]['title']	= extraire_attribut($img,'title');
		$analyse[$i]['complet']	= $img;
		$i++;
	
	}
	

	return $analyse;
		
}

function dot_id_media_spip($id){
	return sql_getfetsel('id_document','spip_documents','`descriptif`='.sql_quote('DC:'.$id));
}
function dot_corriger_url($src){
	$src = str_replace('/.','/',$src);
	$src = str_replace('_m.','.',$src);
	$src = str_replace('_b.','.',$src);
	$src = str_replace('_l.','.',$src);
	$src = str_replace('_t.','.',$src);
	return $src;	
}

function dot_ajouter_medias($medias,$post_id){
	$numerotation = array();
	
	foreach($medias as $media){
		if (verifier_distant($media['src']) != 'oui' and verifier_image($media['src'])){
			$media['src'] = dot_corriger_url($media['src']);
			list($fichier,$dossier) 	= dot_decomposer_chemin_media($media['src']);		
			$media_dot = sql_fetsel('media_title,media_upddt,media_id','dc_media','`media_file`='.sql_quote($fichier)." AND `media_dir`=".sql_quote($dossier) );
			
			$media_id = $media_dot['media_id'];
			$id_doc		= dot_id_media_spip($media_id);
			if($id_doc==null and $fichier!='#'){
				
					
					$crud = charger_fonction('crud','action');
					$resultat = $crud('create','documents','',array(
						'titre'	=>$media_dot['media_title'],
						'date'	=>$media_dot['media_upddt'],
						'descriptif'=>'DC:'.$media_dot['media_id'],
						'source'=> realpath(preg_replace('#^/#','',$media['src'])),
						'id_document'=>'non',
						'statut'	=>'publie',
					));
					$id_doc=$resultat['result']['id'];
					spip_log("Ajout du document $id_doc (ex $media_id)","dot2");
				
			
			
			}
			
			$numerotation[$media['complet']] = $id_doc;
			
		}
		else{
			spip_log("Attention : document du post $post_id pas transvasé (difficile)","dot_attention");
		}
	}
	return $numerotation;
}
function verifier_image($src){
	if (match($src,'^/')){
		return true;
	}	
	else 
		return false;
}

function verifier_distant($src){
	if (match($src,'^http://')){
		return 'oui';
	}
	else{
		return 'non';
	}	
}

function dot_decomposer_chemin_media($media){
	$dossier = str_replace('/public','.',preg_replace("#/[\w.]*$#",'',$media));
	$fichier = str_replace('/','',str_replace(preg_replace("#/[\w.]*$#",'',$media),'',$media));
	return array($fichier,$dossier);
}

?>