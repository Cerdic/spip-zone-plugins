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
if (!defined('_GRAVATAR_CACHE_DELAY_LOCK')) define('_GRAVATAR_CACHE_DELAY_LOCK',3600*23); // 24h si gravatar nous a locke

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
			$img = gravatar_balise_img($g, "", "spip_logo spip_logos photo avatar");
	}
	else {
		// changer la class du logo auteur
		$img = inserer_attribut($img, 'class', 'spip_logo spip_logos photo avatar');
	}

	// si pas de config, retourner ce qu'on a
	if (!$config)
		return $img;
	
	// ensuite le mettre en forme si les options ont ete activees
	if (!$img
		AND $image_default
		AND $img = find_in_path($image_default))
		$img = gravatar_balise_img($img, "", "spip_logo spip_logos photo avatar");

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
 * @param     bool        $force    forcer la recuperation synchrone
 * @return    null|string           le chemin du fichier gravatar, s'il existe
 */
function gravatar($email, $default='404', $force=false) {
	static $nb=5; // ne pas en charger plus de 5 anciens par tour
	static $max=10; // et en tout etat de cause pas plus de 10 nouveaux

	// eviter une requete quand l'email est invalide
	if (!$email=trim($email)
		OR !strlen($email)
		OR !email_valide($email))
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

	$gravatar_default = '';
	if (in_array($default,array('404','mm','identicon','monsterid','wavatar','retro'))){
		$gravatar_default = $default;
		$default = '';
	}
	elseif(strpos($default,".")!==false AND file_exists($default)){
		$gravatar_default = '404';
	}
	else{
		$default = '';
	}

	$gravatar_id .= ($gravatar_default=='404'?"":"-$gravatar_default");
	$gravatar_cache = $tmp.$gravatar_id.'.jpg';
	$gravatar_vide = $tmp.$gravatar_id.'.vide';

	$gravatar = "";
	// On verifie si le gravatar existe en controlant la taille du fichier
	if (@filesize($gravatar_cache)) {
		$gravatar = $gravatar_cache;
	}
	// sinon si default est un chemin d'image, le prendre en fallback
	elseif($default){
		$gravatar = $default;
	}


	// si on est locke, on utilise ce qu'on a
	if (file_exists($lock_file)
		AND $_SERVER['REQUEST_TIME']-filemtime($lock_file)<_GRAVATAR_CACHE_DELAY_LOCK){
		return $gravatar;
	}

	// si on a un cache valide, on l'utilise
	if ($gravatar==$gravatar_cache){
		$duree = $_SERVER['REQUEST_TIME']-filemtime($gravatar_cache);
		if ($duree<_GRAVATAR_CACHE_DELAY_REFRESH OR $nb--<=0){
			return $gravatar;
		}
		spip_log("Actualiser gravatar existant $email anciennete $duree s (cache maxi " . _GRAVATAR_CACHE_DELAY_REFRESH . "s)", "gravatar");
		@touch($gravatar_cache); // un touch pour eviter une autre mise a jour concurrente
	}
	// si c'est un email sans gravatar connu (deja verifie), on ne reverifie pas que passe un delai suffisant
	else {
		// si un fichier vides.txt existe encore, le transformer en touch unitaires
		lire_fichier($tmp . 'vides.txt', $vides);
		if ($vides AND $vides = @unserialize($vides)){
			foreach($vides as $id=>$t){
				@touch($tmp.$id.".vide",$t);
			}
			@unlink($tmp . 'vides.txt');
		}

		if (file_exists($gravatar_vide)){
			$duree_vide = $_SERVER['REQUEST_TIME']-filemtime($gravatar_vide);
			if ($duree_vide<_GRAVATAR_CACHE_DELAY_CHECK_NEW OR $nb--<=0){
				return $gravatar;
			}
			// un actualise un gravatar vide que si c'est celui du visiteur identifie
			if ($force
			  OR (isset($GLOBALS['visiteur_session']['email']) AND $GLOBALS['visiteur_session']['email']===$email)
			  OR (isset($GLOBALS['visiteur_session']['session_email']) AND $GLOBALS['visiteur_session']['session_email']===$email) ){
				spip_log("Actualiser gravatar vide $email $duree_vide s (cache maxi " . _GRAVATAR_CACHE_DELAY_CHECK_NEW . "s)", "gravatar");
				@touch($gravatar_vide); // un touch pour eviter une autre mise a jour concurrente
			}
			else {
				return $gravatar;
			}
		}
		else {
			spip_log("Recherche nouveau gravatar $email", "gravatar");
		}
	}

	// pas trop de requetes sur un seul tour
	if ($max--<=0){
		return $gravatar;
	}

	include_spip("inc/distant");
	spip_timer('gravatar');
	$url_gravatar = _GRAVATAR_HOST
		. '/avatar/'
		. $md5_email
		. ".jpg"
		. ($gravatar_default ? "?d=$gravatar_default" : "")
		. "&s=" . _TAILLE_MAX_GRAVATAR;

	// recuperation OK ?
	$GLOBALS['inc_distant_allow_fopen'] = false; // pas de fallback sur fopen si on est bloque par gravatar.com
	$gravatar_bin = recuperer_page($url_gravatar);
	unset($GLOBALS['inc_distant_allow_fopen']);
	$dt = spip_timer('gravatar', true);
	if ($gravatar_bin){
		spip_log('recuperer gravatar OK pour ' . $email,"gravatar");
		ecrire_fichier($gravatar_cache, $gravatar_bin);
		// si c'est un png, le convertir en jpg
		$a = @getimagesize($gravatar_cache);
		// png ?
		if ($a[2]==3) {
			// pour eviter un warning sous windows si le fichier existe deja
			if (file_exists($gravatar_cache . '.png')){
				@unlink($gravatar_cache . '.png');
			}
			rename($gravatar_cache, $gravatar_cache . '.png');
			include_spip('inc/filtres_images');
			$img = imagecreatefrompng($gravatar_cache . '.png');
			// Compatibilite avec la 2.1
			if (function_exists('_image_imagejpg')){
				_image_imagejpg($img, $gravatar_cache);
			}
			else {
				image_imagejpg($img, $gravatar_cache);
			}
		}
		else {
			if (file_exists($gravatar_cache . '.png')){
				@unlink($gravatar_cache . '.png');
			}
		}
		if (file_exists($gravatar_vide)){
			@unlink($gravatar_vide);
		}

		if ($gravatar!==$gravatar_cache){
			gravatar_verifier_index($tmp);
			$gravatar = $gravatar_cache;
		}
	}
	else {
		// si ca a ete trop long, ne pas ressayer (IP serveur ban par gravatar ?)
		if ($dt>10000){
			$nb = 0;
			@touch($lock_file);
			spip_log("gravatar.com trop long a repondre pour $email ($dt), on lock $lock_file", "gravatar");
		}
		else {
			spip_log('gravatar vide pour ' . $email,"gravatar");
		}
		// si on a pas eu de reponse mais qu'un cache existe le prolonger pour eviter de rechecker tout le temps
		if ($gravatar===$gravatar_cache){
			@touch($gravatar_cache);
		}
		else {
			@touch($gravatar_vide);
		}
	}

	return $gravatar;
}
