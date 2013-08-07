<?php

/*! \brief filtre � utiliser dans les squelettes
 *
 *  D�finition de la fonction de filtre
 *  V�rifie que le plugin est activ� et qu'il n'existe pas ailleurs deja ce filtre
 *  Rappel : dans le cadre d'une utilisation SPIP, il n'y a pas de param�tre � donner. $url correspond � la balise appelant le filtre
 *  
 * \param $url_site url du site � consulter
 * \return url de l'image g�n�r�e par le serveur
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
if (!function_exists('url_thumbsite')) {
	function url_thumbsite($url_site) {
		$url_serveur = '';
		//determine le serveur de vignettes a utiliser, defaut apercite.fr
		include_spip("inc/config");
		$serveur = lire_config('thumbsites/serveur', 'apercite');
		//Charge le fichier de conf specifique au serveur
		include_spip('serveurs/'.$serveur);
		//execute la surcharge
		if ($url_site)
			$url_serveur = url_thumbsite_serveur($url_site);
		return $url_serveur;
	}
}

// fonction de recherche de logo
// SPIP 2.0
function calcule_logo_ou_thumbshot($url) {
	$a = func_get_args();
	$url = array_shift($a);

	// la fonction normale
	$c = call_user_func_array('calcule_logo',$a);

	// si elle repond pas, on va chercher la vignette
	if (!$c[0])
		$c[0] = thumbshot($url);

	return $c;
}


// fonction de recherche de logo
// SPIP 2.1 : on se contente de produire un tag IMG
function thumbshot_img($url) {
	if (!$url OR !$g = thumbshot($url))
		return '';

	return '<img src="'.$g.'" alt="" class="spip_logos" />';

}

// fonction de creation d'un index des vignettes
function creer_index_thumbshots($tmp) {
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

// Cree le fichier cache du thumbshot et renvoie le fichier
function thumbshot($url_site, $refresh=false) {
	static $nb=5; // ne pas en charger plus de 5 anciens par tour

	if (!strlen($url_site) OR !parse_url($url_site))
		return '';

	$tmp = sous_repertoire(_DIR_VAR, 'cache-thumbsites');
	$md5_url = md5(strtolower($url_site));
	$thumb_cache = $tmp.$md5_url.'.jpg';

	if( $refresh AND file_exists($thumb_cache)) {
		$ret=supprimer_fichier($thumb_cache);
		spip_log("thumbshot demande de rafraichissement url $url_site file $thumb_cache suppression reussie ? $ret");
	}
	
	include_spip("inc/filtres");
	include_spip('inc/config');
	$duree = intval(lire_config('thumbsites/duree_cache', 30)) ;

	if ((!file_exists($thumb_cache)	OR ((time()-3600*24*$duree > filemtime($thumb_cache)) AND $nb > 0))) {

		$nb--;
		include_spip("inc/distant");
		if ($thumb = recuperer_page(url_thumbsite($url_site))) {
			spip_log('thumbshot ok pour '.$url_site);
			ecrire_fichier($thumb_cache, $thumb);
			// si c'est un png, le convertir en jpg
			$a = @getimagesize($thumb_cache);
			if ($a[2] == 3) // png
			{
				rename($thumb_cache, $thumb_cache.'.png');
				include_spip('inc/filtres_images');
				$img = imagecreatefrompng($thumb_cache.'.png');
				if (function_exists('image_imagejpg')) {
					image_imagejpg($img, $thumb_cache);				
				} else {
					/* Depuis SPIP 2.1, les filtres images changent de nom */
					_image_imagejpg($img, $thumb_cache);
				}
			}

			creer_index_thumbshots($tmp);
		}
	}

	// On verifie si le thumbshot existe en controlant la taille du fichier
	if (@filesize($thumb_cache))
		return $thumb_cache;
	else
		return '';
}

?>
