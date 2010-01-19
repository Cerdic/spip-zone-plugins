<?php

// notre fonction de recherche de logo
function calcule_logo_ou_havatar($url) {
	$a = func_get_args();
	$url = array_shift($a);

	// la fonction normale
	$c = call_user_func_array('calcule_logo',$a);

	// si elle repond pas, on va chercher le havatar
	if (!$c[0])
		$c[0] = havatar($url);

	return $c;
}

function havatar_verifier_index($tmp) {
	static $done = false;
	if ($done) return;
	$done = true;
	if (!file_exists($tmp.'index.php'))
		ecrire_fichier ($tmp.'index.php', '<?php
	foreach(glob(\'./*.jpg\') as $i)
		echo "<img src=\'$i\' />\n";
?>'
		);
}

function havatar($url) {
	static $nb=5; // ne pas en charger plus de 5 anciens par tour
	static $max=10; // et en tout etat de cause pas plus de 10 nouveaux

	if (!strlen($url))
		return '';

	$tmp = sous_repertoire(_DIR_VAR, 'cache-havatar');

	$md5_url = md5(strtolower($url));
	$havatar_cache = $tmp.$md5_url.'.jpg';

	if ((!file_exists($havatar_cache)
	OR (
		(time()-3600*24 > filemtime($havatar_cache))
		AND $nb > 0
	  ))
	) {
		lire_fichier($tmp.'vides.txt', $vides);
		$vides = @unserialize($vides);
		if ((!isset($vides[$md5_url])
		OR time()-$vides[$md5_url] > 3600*8
		) AND $max-- > 0) {

			$nb--;
			include('hkit.class.php');
			$h = new hKit;
			//$h->tidy_mode = 'proxy'; // 'proxy', 'exec', 'php' or 'none'
			$hcards = $h->getByURL('hcard', $url);
			$url_image = '';
			if ($hcards) {
				foreach ($hcards as $hcard) {
					if (($url_image = $hcard['photo'])) {
						break;
					}
				}
			}
			include_spip("inc/distant");
			if ($url_image && ($havatar = recuperer_page($url_image))) {
				spip_log('havatar ok pour '.$url);
				ecrire_fichier($havatar_cache, $havatar);
				// si c'est un png, le convertir en jpg
				$a = @getimagesize($havatar_cache);
				if ($a[2] == 3) // png
				{
					rename($havatar_cache, $havatar_cache.'.png');
					include_spip('inc/filtres_images');
					$img = imagecreatefrompng($havatar_cache.'.png');
					// Compatibilite avec la 2.1
					if(function_exists('_image_imagejpg')){
						_image_imagejpg($img, $havatar_cache);
					}
					else
						image_imagejpg($img, $havatar_cache);
				}
			} else {
				$vides[$md5_url] = time();
				ecrire_fichier($tmp.'vides.txt', serialize($vides));
			}

			havatar_verifier_index($tmp);
		}
	}

	// On verifie si le havatar existe en controlant la taille du fichier
	if (@filesize($havatar_cache))
		return $havatar_cache;
	else
		return '';
}

?>
