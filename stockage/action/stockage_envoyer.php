<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@action_virtualiser_dist
function action_stockage_envoyer_dist() {
	if (!autoriser('stocker'))
		die ('non');

if ($id_document = intval(_request('id_document'))
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
			require_once find_in_path('php-aws/class.s3.php');

			$s3 = new S3($cfg['s3publickey'], $cfg['s3secretkey']);
			$s3->_pathToCurl = 'curl';

			// Creer le bucket s'il n'existe pas deja
			if (!$s3->bucketExists($BUCKET)) {
				if (!$s3->createBucket($BUCKET)) {
					echo ("Something went wrong! We couldn't create a new bucket for ".htmlspecialchars($BUCKET)."!");
					var_dump($s3->getBuckets());
					exit;
				}
			}

			include_spip('inc/documents');
			include_spip('inc/distant');

			// Ou doit-on deposer notre fichier ?
			$src = get_spip_doc($t['fichier']);
			$dest = $PATH . $t['fichier'];

			// on l'envoie
			if ($s3->putObject($BUCKET,
				$dest,	# destination
				$src,  # objet source
				true)
			) {
				// si OK on le note comme distant, et on met
				// la copie locale au bon endroit
				$url_distante = $cfg['s3baseurl'].$dest;
				sql_updateq('spip_documents', array(
					'fichier' => $url_distante,
					'distant' => 'oui'
				), 'id_document='.$id_document);

				rename ($src, fichier_copie_locale($url_distante));
				echo "$src => $url_distante => ".fichier_copie_locale($url_distante);
			}
		}
		else {
			echo "s3 n'est pas configur&#233;";
			var_dump($cfg);
			exit;
		}

	} else {
		include_spip('inc/distant');
		echo $t['fichier'].' => '.fichier_copie_locale($t['fichier']);
		
		if ($local = copie_locale($t['fichier'], 'test')) {
			// remettre en distant S3 un fichier dont on dispose de la copie locale, et qu'on aurait supprime de S3 ?
		}
	}

}

	echo "\n<hr />fini";
}



?>
