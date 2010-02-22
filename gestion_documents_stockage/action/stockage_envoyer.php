<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@action_virtualiser_dist
function action_stockage_envoyer_dist() {
	if (!autoriser('stocker'))
		die ('non');

if ($id_document = intval(_request('arg'))
AND $s = spip_query('SELECT * FROM spip_documents WHERE id_document='.$id_document)
AND $t = sql_fetch($s)) {

	if ($t['distant'] == 'non') {

		$cfg = @unserialize($GLOBALS['meta']['stockage']);
		if (strlen($cfg['s3publickey'])
		AND strlen($cfg['s3secretkey'])
		AND strlen($cfg['s3bucket'])
		) {

			$BUCKET = $cfg['s3bucket'];
			$PATH = $cfg['s3path'] ? $cfg['s3path'].'/' : '';

			// charger la librairie Amazon S3
			// http://code.google.com/p/php-aws/source
			require_once find_in_path('cloudfusion/cloudfusion.class.php');
			
			
			$s3 = new AmazonS3($cfg['s3publickey'], $cfg['s3secretkey']);
			

			// Creer le bucket s'il n'existe pas deja
			if (!$s3->if_bucket_exists($BUCKET)) {
				if (!$s3->create_bucket($BUCKET)) {
					echo ("Something went wrong! We couldn't create a new bucket for ".htmlspecialchars($BUCKET)."!");
					var_dump($s3->getBuckets());
					exit;
				}
			}


			include_spip('inc/documents');
			include_spip('inc/distant');
			
			//Size image, for future thumbnails. Now set "original"
			
			$size_image= "original";

			// Ou doit-on deposer notre fichier ?
			
			$path_info = pathinfo(get_spip_doc($t['fichier']));
			
			
			$src_site =  $GLOBALS['meta']['adresse_site']. "/" .str_replace("../", "" ,get_spip_doc($t['fichier']));
			$src =  get_spip_doc($t['fichier']);
			$dest =  $size_image. "-id" .$id_document. "-" .time(). "." .$path_info['extension'];
			 
			

			// on l'envoie
			$s3_url = $s3->store_remote_file($src_site, $BUCKET, $dest);
			
			
			/*echo "path spip".$GLOBALS['PATH_SPIP']."<br>";
			echo "src_site: $src_site <br>";
			echo "src: $src <br>";
			echo "bucket: $BUCKET <br>";
			echo "dest: $dest <br>";
			echo "enviado $s3_url";
			*/
			
				
				
				if ($s3_url != ""){
				
					$url_distante = $s3_url;
					sql_updateq('spip_documents', array(
						'fichier' => $url_distante,
						'distant' => 'oui'
					), 'id_document='.$id_document);

					rename (get_spip_doc($t['fichier']), "../".fichier_copie_locale($url_distante));
					
					//echo "<br><br>$src => $url_distante => ../".fichier_copie_locale($url_distante);
				}
				
			
		} 
		}
		else {
		include_spip('inc/distant');
		echo $t['fichier'].' => '.fichier_copie_locale($t['fichier']);
		
			if ($local = copie_locale($t['fichier'], 'test')) {
				// remettre en distant S3 un fichier dont on dispose de la copie locale, et qu'on aurait supprime de S3 ?
			}
		}

}

	//echo "\n<hr />fini";
}



?>
