<?php
/**
 * Plugin Stockage S3
 * Licence GPL (c) 2010 Natxo, Cedric
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_stockageS3_envoyer_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action','inc');
		$arg = $securiser_action();
	}
	
	if ($id_document = intval($arg)
		AND autoriser('stocker','document',$id_document)
		AND $t = sql_fetsel('*','spip_documents','id_document='.intval($id_document))
		AND $t['distant'] == 'non'
		){

		$cfg = @unserialize($GLOBALS['meta']['stockage']);

		if (strlen($cfg['s3publickey'])
		  AND strlen($cfg['s3secretkey'])
		  AND strlen($cfg['s3bucket'])) {

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
			
			$src_site =  $GLOBALS['meta']['adresse_site']. "/" .substr(get_spip_doc($t['fichier']),strlen(_DIR_RACINE));
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
				include_spip('action/editer_document');
				rename (get_spip_doc($t['fichier']), _DIR_RACINE.fichier_copie_locale($url_distante));
				document_set($id_document, array(
					'fichier' => $url_distante,
					'distant' => 'oui'
				));
				//echo "<br><br>$src => $url_distante => ../".fichier_copie_locale($url_distante);
			}			
		}
	}

}



?>