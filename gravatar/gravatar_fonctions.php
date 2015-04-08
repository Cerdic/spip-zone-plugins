<?php
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

// taille max des gravatars à récupérer sur le site
if (!defined('_TAILLE_MAX_GRAVATAR')) define('_TAILLE_MAX_GRAVATAR',80);

// le host vers gravatar
if (!defined('_GRAVATAR_HOST')) define('_GRAVATAR_HOST','http://www.gravatar.com');

// les caches
if (!defined('_GRAVATAR_CACHE_DELAY_REFRESH')) define('_GRAVATAR_CACHE_DELAY_REFRESH',3600*24); // 24h pour checker un existant
if (!defined('_GRAVATAR_CACHE_DELAY_CHECK_NEW')) define('_GRAVATAR_CACHE_DELAY_CHECK_NEW',3600*8); // 8h pour re-checker un user sans gravatar
if (!defined('_GRAVATAR_CACHE_FALLBACK_COEFF')) define('_GRAVATAR_CACHE_FALLBACK_COEFF',10); // x10 si on utilise identicon etc.
if (!defined('_GRAVATAR_CACHE_DELAY_LOCK')) define('_GRAVATAR_CACHE_DELAY_LOCK',3600*24); // 24h si gravatar nous a locke

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
 * @param     int|string  $default  gravatar par defaut : 404 ou identicon/monsterid/wavatar
 * @return    null|string           le chemin du fichier gravatar, s'il existe
 */
function gravatar($email, $default='404') {
	static $nb=5; // ne pas en charger plus de 5 anciens par tour
	static $max=10; // et en tout etat de cause pas plus de 10 nouveaux

	// eviter une requete quand on peut
	if (!strlen($email) OR !email_valide($email))
		return '';

	$tmp = sous_repertoire(_DIR_VAR, 'cache-gravatar');
	$lock_file = $tmp."gravatar.lock";


	$md5_email = md5(strtolower($email));
	// privacy : http://archive.hack.lu/2013/dbongard_hacklu_2013.pdf
	// eviter de rendre les emails retrouvables par simple reverse sur le md5 de gravatar
	if (!isset($GLOBALS['meta']['gravatar_salt'])){
		include_spip('inc/acces');
		include_spip('auth/sha256.inc');
		ecrire_meta('gravatar_salt', _nano_sha256($_SERVER["DOCUMENT_ROOT"] . $_SERVER["SERVER_SIGNATURE"] . creer_uniqid()), 'non');
	}
	if (function_exists("sha1"))
		$gravatar_id = sha1(strtolower($email).$GLOBALS['meta']['gravatar_salt']);
	else
		$gravatar_id = md5(strtolower($email).$GLOBALS['meta']['gravatar_salt']);
	$gravatar_id .= ($default=='404'?"":"-$default");
	$gravatar_cache = $tmp.$gravatar_id.'.jpg';

	// inutile de rafraichir souvent les identicon etc qui ne changent en principe pas
	$coeff_delai = ($default=='404' ? 1:_GRAVATAR_CACHE_FALLBACK_COEFF);
	$duree = 0;

	
	if (!file_exists($lock_file) OR $_SERVER['REQUEST_TIME']-filemtime($lock_file)>_GRAVATAR_CACHE_DELAY_LOCK){

		if ((!file_exists($gravatar_cache)
			OR (
				(($duree = $_SERVER['REQUEST_TIME']-filemtime($gravatar_cache))>_GRAVATAR_CACHE_DELAY_REFRESH*$coeff_delai)
				AND $nb>0
			))
		){
			if ($duree){
				spip_log("Actualiser gravatar anciennete $duree s (cache maxi " . _GRAVATAR_CACHE_DELAY_REFRESH*$coeff_delai . "s)", "gravatar");
			}
			lire_fichier($tmp . 'vides.txt', $vides);
			$vides = @unserialize($vides);
			if ($vides===false) $vides = array();
			if ($duree
				OR (
					(!isset($vides[$gravatar_id]) OR (($duree_vide = time()-$vides[$gravatar_id])>_GRAVATAR_CACHE_DELAY_CHECK_NEW*$coeff_delai))
					AND $max-->0
				)
			){
				if ($duree_vide){
					spip_log("Actualiser gravatar vide $duree_vide s (cache maxi " . _GRAVATAR_CACHE_DELAY_CHECK_NEW*$coeff_delai . "s)", "gravatar");
				}

				$nb--;
				include_spip("inc/distant");
				spip_timer('gravatar');
				if ($gravatar
					= recuperer_page(_GRAVATAR_HOST . '/avatar/' . $md5_email . ($default ? "?d=$default" : "") . "&s=" . _TAILLE_MAX_GRAVATAR)
				){
					spip_log('gravatar ok pour ' . $email);
					ecrire_fichier($gravatar_cache, $gravatar);
					// si c'est un png, le convertir en jpg
					$a = @getimagesize($gravatar_cache);
					if ($a[2]==3) // png
					{
						if (!file_exists($gravatar_cache . '.png')){ // pour eviter un warning sous windows si le fichier existe deja
							rename($gravatar_cache, $gravatar_cache . '.png');
						}
						include_spip('inc/filtres_images');
						$img = imagecreatefrompng($gravatar_cache . '.png');
						// Compatibilite avec la 2.1
						if (function_exists('_image_imagejpg')){
							_image_imagejpg($img, $gravatar_cache);
						} else
							image_imagejpg($img, $gravatar_cache);
					}
					if (isset($vides[$gravatar_id])){
						unset($vides[$gravatar_id]);
						ecrire_fichier($tmp . 'vides.txt', serialize($vides));
					}
				} else {
					$dt = spip_timer('gravatar', true);
					// si ca a ete trop long, ne pas ressayer (IP serveur ban par gravatar ?)
					if ($dt>10000){
						$nb = 0;
						@touch($lock_file);
						spip_log("gravatar.com trop long a repondre, on lock $lock_file", "gravatar");
					}
					// si on a pas eu de reponse mais qu'un cache existe le prolonger pour eviter de rechecker tout le temps
					if ($duree){
						@touch($gravatar_cache);
					} else {
						$vides[$gravatar_id] = time();
						ecrire_fichier($tmp . 'vides.txt', serialize($vides));
					}
				}

				gravatar_verifier_index($tmp);
			}
		}

	}

	// On verifie si le gravatar existe en controlant la taille du fichier
	if (@filesize($gravatar_cache))
		return $gravatar_cache;
	else
		return '';
}

?>
