<?php

/**
 * Plugin Stockage S3
 * Licence GPL (c) 2010 Natxo, Cedric
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function s3_provider($service) {
	$ep = array(
		'google' => 'Google Storage',
		'archiveorg' => 'Archive.org',
		'amazon' => 'Amazon S3',
	);

	if (isset($ep[$service]))
		return $ep[$service];
	return false;


}

function s3_endpoint($service) {
	$ep = array(
		'google' => 'commondatastorage.googleapis.com',
		'archiveorg' => 's3.us.archive.org',
		'amazon' => 's3.amazonaws.com',
	);

	if (isset($ep[$service]))
		return $ep[$service];
	return false;

}


/*
 * envoyer un fichier, si OK renvoyer l'URL du fichier sur S3
 */
function stockage_sendfile($src, $dest) {

	$cfg = @unserialize($GLOBALS['meta']['stockage']);

	if (!(strlen($cfg['s3publickey'])
	AND strlen($cfg['s3secretkey'])
	AND strlen($cfg['s3bucket'])))
		return false;

	// on cree le bucket le cas echeant
	$BUCKET = $cfg['s3bucket'];
	$PATH = $cfg['s3path'] ? $cfg['s3path'].'/' : '';
	$LOCATION = $cfg['location'];

	if (!$endpoint = s3_endpoint($cfg['provider'])) {
		spip_log ("Service inconnu: ".$cfg['provider'], 'stockage');
		return false;
	}

	// charger la librairie Amazon S3
	// http://code.google.com/p/php-aws/source
	include_spip('lib/S3');
	$s3 = new S3($cfg['s3publickey'], $cfg['s3secretkey'], $useSSL = false, $endpoint);

	// Creer le bucket (en mode public) s'il n'existe pas deja
	if (!$buckets = $s3->listBuckets()
	OR !in_array($BUCKET, $buckets)) {
		if (!$s3->putbucket($BUCKET, S3::ACL_PUBLIC_READ, $LOCATION)) {
			echo ("<h3>Something went wrong! We couldn't create a new bucket for ".htmlspecialchars($BUCKET)."!</h3>");
			var_dump($s3->listBuckets());
			exit;
		}
	}

	// on envoie le fichier
	if ($s3->putObjectFile($src, $BUCKET, $dest, $acl = S3::ACL_PUBLIC_READ /*, $metaHeaders = array(), $contentType = 'image/jpeg' */))
		return 'http://'.$BUCKET.'.'.$endpoint.'/'.$dest;

	return false;
}



?>
