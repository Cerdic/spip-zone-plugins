<?php

// notre fonction de recherche de logo
// SPIP 2.0
function calcule_logo_ou_gravatar($email) {
	$a = func_get_args();
	$email = array_shift($a);

	// la fonction normale
	$c = call_user_func_array('calcule_logo',$a);

	// si elle repond pas, on va chercher le gravatar
	if (!$c[0])
		$c[0] = gravatar($email);

	return $c;
}

// pour 2.1 on se contente de produire un tag IMG
function gravatar_img($email) {
	if (!$email OR !$g = gravatar($email))
		return '';

	return '<img src="'.$g.'" alt="" class="spip_logos" />';

}

function gravatar_verifier_index($tmp) {
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

function gravatar($email) {
	static $nb=5; // ne pas en charger plus de 5 anciens par tour
	static $max=10; // et en tout etat de cause pas plus de 10 nouveaux

	if (!strlen($email)
	OR !email_valide($email))
		return '';

	$tmp = sous_repertoire(_DIR_VAR, 'cache-gravatar');

	$md5_email = md5(strtolower($email));
	$gravatar_cache = $tmp.$md5_email.'.jpg';

	if ((!file_exists($gravatar_cache)
	OR (
		(time()-3600*24 > filemtime($gravatar_cache))
		AND $nb > 0
	  ))
	) {
		lire_fichier($tmp.'vides.txt', $vides);
		$vides = @unserialize($vides);
		if ((!isset($vides[$md5_email])
		OR time()-$vides[$md5_email] > 3600*8
		) AND $max-- > 0) {

			$nb--;
			include_spip("inc/distant");
			if ($gravatar
			= recuperer_page('http://www.gravatar.com/avatar/'.$md5_email.'?d=404')
			) {
				spip_log('gravatar ok pour '.$email);
				ecrire_fichier($gravatar_cache, $gravatar);
				// si c'est un png, le convertir en jpg
				$a = @getimagesize($gravatar_cache);
				if ($a[2] == 3) // png
				{
					rename($gravatar_cache, $gravatar_cache.'.png');
					include_spip('inc/filtres_images');
					$img = imagecreatefrompng($gravatar_cache.'.png');
					// Compatibilite avec la 2.1
					if(function_exists('_image_imagejpg')){
						_image_imagejpg($img, $gravatar_cache);
					}
					else
						image_imagejpg($img, $gravatar_cache);
				}
			} else {
				$vides[$md5_email] = time();
				ecrire_fichier($tmp.'vides.txt', serialize($vides));
			}

			gravatar_verifier_index($tmp);
		}
	}

	// On verifie si le gravatar existe en controlant la taille du fichier
	if (@filesize($gravatar_cache))
		return $gravatar_cache;
	else
		return '';
}

?>
