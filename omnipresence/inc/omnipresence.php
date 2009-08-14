<?php

// Champs utilisés
define('CHAMP_SERVEUR_OMNIPRESENCE', 'omnipresence_serveur');
define('CHAMP_JID', 'jid');

// Serveur Omniprésence par dépit si rien n'est précisé comme serveur par défaut
// La changer également définie dans fonds/cfg_omnipresence.html
// TODO: Définir la chaîne à un seul endroit.
define('OMNIPRESENCE_SERVEUR_DEFAUT_DEFAUT', 'http://presence.jabberfr.org');

// notre fonction de recherche de logo
function calcule_logo_ou_avatar($jid, $host) {
	$a = func_get_args();
	$jid = array_shift($a);
	$host = array_shift($a);

	// la fonction normale
	$c = call_user_func_array('calcule_logo',$a);

	// si elle repond pas, on va chercher l'avatar
	if (!$c[0]) {
		$c[0] = demander_action('avatar', $jid, $host, $url = True);
	}

	return $c;
}

function omnipresence_verifier_index($tmp) {
	static $done = false;
	if ($done) return;
	$done = true;
	if (!file_exists($tmp.'index.php'))
		ecrire_fichier ($tmp.'index.php', '<?php
	foreach(glob(\'./*.{png,gif,jpg,bmp}\', GLOB_BRACE) as $i)
		echo "<img src=\'$i\' />\n";
?>'
		);
}

// Récupère l'information sur le serveur, écrit le résultat en cache.
// Si c'est une image, $cache est renommé pour refléter le type de l'image.
function omnipresence_recuperer_info(&$cache, $md5_jid, $action, $host) {
	include_spip("inc/distant");
	if ('' == $host) {
		$host = lire_config('omnipresence/omnipresence_serveur_defaut', '');
		if ('' == $host)
			$host = OMNIPRESENCE_SERVEUR_DEFAUT_DEFAUT;
	}
	$data = recuperer_page("$host/$md5_jid/$action");
	ecrire_fichier($cache, $data);
	$info = @getimagesize($cache);
	$img_ext = array(
		IMAGETYPE_PNG => 'png',
		IMAGETYPE_GIF => 'gif',
		IMAGETYPE_JPEG => 'jpg',
		IMAGETYPE_BMP => 'bmp',
		// IMAGETYPE_ICO => 'ico', // Seulement à partir de PHP 5.3.0
	);
	if (array_key_exists($info[2], $img_ext)) {
		$cache_new = $cache . '.' . $img_ext[$info[2]];
		rename($cache, $cache_new);
		$cache = $cache_new;
	}
	return $data;
}

// Fonction principale. Demande une information, stocke le résultat en cache,
// renvoie le résultat sous forme d'URL, de contenu ou de balise IMG.
// Pour les données rarement changées (avatar), on garde une liste des JID
// qui n'ont pas renvoyé de réponse, pour ne pas redemander l'info trop
// souvent. On les garde aussi en cache plus longtemps (10 min au lieu de 24h).
// Pour référence, les actions demandées par les balises de ce plugin sont :
// - avatar, image, text, message
// - pep/mood/value, pep/mood/text.txt, pep/mood/image.png
// - pep/activity/value.txt, pep/activity/text.txt, pep/activity/image.png
// - pep/tune.txt, pep/tune/artist.txt, pep/tune/song.txt
// -> image peut être image-nomdetheme
// -> text peut être text-locale
// -> */value.txt peut être value-locale.txt
function demander_action($action, $jid, $host, $url = False) {
	static $nb;
	static $max;
	$nb[$action] = 8; // ne pas en charger plus de 8 anciens par tour
	$max[$action] = 10; // et en tout etat de cause pas plus de 10 nouveaux
	$rarely_changed = array('avatar');

	if (!strlen($jid))
		return '';
	$tmp = sous_repertoire(_DIR_VAR, "cache-omnipresence");
	$md5_jid = md5(strtolower($jid));
	$cache = $tmp.$md5_jid.'-'.urlencode($action);
	$expiry = (in_array($action, $rarely_changed)) ? 60*60*24 : 60*10;
	$binary_content = preg_match('/image-?.*|avatar/', $action);

	if ((!file_exists("$cache")
	OR (
		(time() - $expiry > filemtime($cache))
		AND $nb[$action] > 0
	  ))
	) {
		// Pas de cache, ou bien : cache trop ancien + il reste des actions
		$liste_vides = "$tmp$action-vides.txt";
		lire_fichier($liste_vides, $vides);
		$vides = @unserialize($vides);
		if ((!isset($vides[$md5_jid])
		OR time() - $vides[$md5_jid] > 3600*8
		) AND $max[$action]-- > 0) {
			// md5 inconnu dans vides, ou bien: md5 trop ancien + on est pas au max
			$nb[$action]--;
			if (omnipresence_recuperer_info($cache, $md5_jid, $action, $host)) {
				spip_log("Jabber: $action ok pour $jid");
			} else {
				if (in_array($action, $rarely_changed)) {
					$vides[$md5_jid] = time();
					ecrire_fichier($liste_vides, serialize($vides));
				}
			}
			omnipresence_verifier_index($tmp);
		}
	}
	// On verifie si l'info existe en controlant la taille du fichier
	if (@filesize($cache)) {
		if ($url) {
			return $cache;
		} else {
			if ($binary_content) {
				return "<img src=\"$cache\" />";
			} else {
				lire_fichier($cache, $contenu);
				return $contenu;
			}
		}
	} else
		return '';
}

?>
