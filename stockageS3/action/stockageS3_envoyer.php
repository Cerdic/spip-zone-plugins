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
			$LOCATION = $cfg['location'];

			switch ($cfg['provider']) {
				case 'gs':
					define('S3_DEFAULT_URL', 'commondatastorage.googleapis.com');
					break;
				case 's3':
				default:
					define('S3_DEFAULT_URL', 's3.amazonaws.com');
					break;
			}

			// charger la librairie Amazon S3
			// http://code.google.com/p/php-aws/source
			require_once find_in_path('cloudfusion/cloudfusion.class.php');			
			$s3 = new AmazonS3($cfg['s3publickey'], $cfg['s3secretkey']);

			// Creer le bucket s'il n'existe pas deja
			if (!$s3->if_bucket_exists($BUCKET)) {
				if (!$s3->create_bucket($BUCKET, $LOCATION)) {
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
			$dest =  $PATH . $size_image. "-id" .$id_document. "-" .time(). "." .$path_info['extension'];

			// on l'envoie
			if ($s3_url = $s3->store_remote_file($src_site, $BUCKET, $dest)) {

				// gs est gentil mais il passe en https
				$s3_url = preg_replace(',^https,', 'http', $s3_url);
				// gs est gentil mais il transforme / en %2F
				$s3_url = str_replace('%2F', '/', $s3_url);

				spip_log("Stockage document $id_document ".$t['fichier']." => ".$s3_url, 'stockage');
				$url_distante = $s3_url;
				include_spip('action/editer_document');
				rename (get_spip_doc($t['fichier']), _DIR_RACINE.fichier_copie_locale($url_distante));
				document_set($id_document, array(
					'fichier' => $url_distante,
					'distant' => 'oui'
				));
				//echo "<br><br>$src => $url_distante => ../".fichier_copie_locale($url_distante);
			} else {
				spip_log("Erreur upload stockage ($id_document)", 'stockage');
			}
		}
	}

}



?>