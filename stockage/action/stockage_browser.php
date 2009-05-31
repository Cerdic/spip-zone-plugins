<?php

	function action_stockage_browser_dist() {


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

			if ($suppr = _request('supprimer')) {
				$s3->deleteObject($BUCKET, $suppr);
			}

#			$s3->deleteBucket($BUCKET);


			$l = $s3->getBucketContents($BUCKET);

			include_spip('inc/filtres');

			echo "<ul>";
			foreach($l as $file) {
				echo "<li>";

				echo $file['name']. ' ('.taille_en_octets($file['size']).')';
				echo "<a href='"
					.
					parametre_url(
					parametre_url(self(),
						'action', _request('action')
						),
						'supprimer', $file['name'])
				."'>supprimer</a>";

				echo "</li>";
			}
			echo "</ul>";

		}
		else {
			echo "s3 n'est pas configur&#233;";
			var_dump($cfg);
			exit;
		}
	}