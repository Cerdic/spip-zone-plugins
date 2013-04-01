<?php
define('_TAILLE_MAX_GRAVATAR',80); // taille max des gravatars à récupérer sur le site 

/**
 *
 * Gravatar : Globally Recognized AVATAR
 *
 * @package     plugins
 * @subpackage  gravatar
 *
 * @author      Fil, Cedric, Thomas Beaumanoir
 * @license     GNU/GPL
 *
 * @version     $Id$
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * notre fonction de recherche de logo
 *
 * @deprecated obsolete, on la garde pour ne pas planter les squelettes non recalcules
 * @param  string $email  Le mail qui sert a recuperer l'image sur gravatar.com
 * @return Array          Le logo de l'utilisateur
 */
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

/**
 * Construit la balise HTML <img> affichant le gravatar
 *
 * @param  string $img    Chemin de l'image
 * @param  string $alt    Texte alternatif
 * @param  string $class  Classe facultativ
 * @return string         Le code HTML
 */
function gravatar_balise_img($img,$alt="",$class=""){
	$taille = taille_image($img);
	list($hauteur,$largeur) = $taille;
	if (!$hauteur OR !$largeur)
		return "";
	return
	"<img src='$img' width='$largeur' height='$hauteur'"
	  ." alt='".attribut_html($alt)."'"
	  .($class?" class='".attribut_html($class)."'":'')
	  .' />';
}


/**
 * pour 2.1 on se contente de produire une balise IMG
 *
 * @param  string $email        le mail qui sert a recuperer l'image sur gravatar.com
 * @param  string $logo_auteur  Le logo de l'auteur s'il existe
 * @return string               La balise IMG
 */
function gravatar_img($email, $logo_auteur='') {
	include_spip('inc/config');
	$config = function_exists('lire_config')?lire_config('gravatar'):unserialize($GLOBALS['meta']['gravatar']);
	$default = '404'; // par defaut rien si ni logo ni gravatar (consigne a passer a gravatar)
	$image_default = ''; // image

	if ($config
	  AND strlen($image_default=$config['image_defaut'])
		AND strpos($image_default,".")===FALSE){
		$default = $image_default; // c'est une consigne pour l'api gravatar
		$image_default = ($default=='404')?'':'images/gravatar.png'; // si pas d'email, fournir quand meme une image
	}

	// retrouver l'image du mieux qu'on peut :
	// logo_auteur si il existe
	// ou gravatar si on a un email et si on trouve le gravatar
	if (!$img = $logo_auteur){
		if (!$g = gravatar($email,$default)) // chercher le gravatar etendu pour cet email
			$img = '';
		else
			$img = gravatar_balise_img($g, "", "spip_logos photo avatar");
	}
	else {
		// changer la class du logo auteur
		$img = inserer_attribut($img, 'class', 'spip_logos photo avatar');
	}

	// si pas de config, retourner ce qu'on a
	if (!$config)
		return $img;
	
	// ensuite le mettre en forme si les options ont ete activees
	if (!$img
		AND $image_default
		AND $img = find_in_path($image_default))
		$img = gravatar_balise_img($img, "", "spip_logos photo avatar");

	if (!$img)
		return '';

	// mises en formes optionnelles du gravatar
	if ($config AND $t=$config['taille']){
		$img = filtrer('image_passe_partout',$img,$t);
		$img = filtrer('image_recadre',$img,$t,$t,'center');
		$img = filtrer('image_graver',$img);
	}

	return $img;
}

/**
 * Verifie (une fois) qu'un index index.php existe dans $tmp
 *
 * @staticvar boolean $done  True si la verif a deja ete faite
 * @param     string  $tmp   Le repertoire dans lequel on posera le gravatar
 * @return    null
 */
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

/**
 * Recupere l'image sur www.gravatar.com et la met en cache
 * 
 * @staticvar int         $nb       le nombre max d'anciens
 * @staticvar int         $max      le nombre max de nouveaux
 * @param     string      $email    le mail qui va servir pour calculer le gravatar
 * @param     int         $default  code de la page
 * @return    null|string           le chemin du fichier gravatar, s'il existe
 */
function gravatar($email, $default='404') {
	static $nb=5; // ne pas en charger plus de 5 anciens par tour
	static $max=10; // et en tout etat de cause pas plus de 10 nouveaux

	// eviter une requete quand on peut
	if (!strlen($email) OR !email_valide($email))
		return '';

	// si on demande un defaut identicon/monsterid/wavatar
	// faire d'abord une requete avec 404, cela permet de partager le cache
	// pour ceux qui ont vraiment un gravatar
	if ($default!=='404'){
		if ($gravatar_cache = gravatar($email))
			return $gravatar_cache;
	}

	$tmp = sous_repertoire(_DIR_VAR, 'cache-gravatar');

	$md5_email = md5(strtolower($email));
	$gravatar_id = $md5_email.($default=='404'?"":"-$default");
	$gravatar_cache = $tmp.$gravatar_id.'.jpg';

	// inutile de rafraichir souvent les identicon etc qui ne changent en principe pas
	$coeff_delai = ($default=='404' ? 1:10);
	if ((!file_exists($gravatar_cache)
	OR (
		(time()-3600*24*$coeff_delai > filemtime($gravatar_cache))
		AND $nb > 0
	  ))
	) {
		lire_fichier($tmp.'vides.txt', $vides);
		$vides = @unserialize($vides);
		if ((!isset($vides[$gravatar_id])
		OR time()-$vides[$gravatar_id] > 3600*8*$coeff_delai
		) AND $max-- > 0) {

			$nb--;
			include_spip("inc/distant");
			if ($gravatar
			= recuperer_page('http://www.gravatar.com/avatar/'.$md5_email.($default?"?d=$default":"")."&s="._TAILLE_MAX_GRAVATAR) 
			) {
				spip_log('gravatar ok pour '.$email);
				ecrire_fichier($gravatar_cache, $gravatar);
				// si c'est un png, le convertir en jpg
				$a = @getimagesize($gravatar_cache);
				if ($a[2] == 3) // png
				{
					if (!file_exists($gravatar_cache.'.png')) { // pour eviter un warning sous windows si le fichier existe deja
						rename($gravatar_cache, $gravatar_cache.'.png'); 
					}
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
				$vides[$gravatar_id] = time();
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
