<?php

/*
 * transforme un raccourci de ressource en un joli html a embed
 * 
 *
 */

define('_EXTRAIRE_RESSOURCES', ',' . '<"?(https?://|[\w -]+\.[\w -]+).*>'.',UimsS');
define('_RESSOURCE_VIGNETTE_LARGEUR_DEFAUT','small');
define('_RESSOURCE_IMAGE_LARGEUR_DEFAUT', 'large');
include_spip('inc/filtres_images_mini');

/* pipeline pour typo */
function ressource_post_typo($t) {
	if (strpos($t, '<') !== false) {
		$t = preg_replace_callback(_EXTRAIRE_RESSOURCES, 'traiter_ressources', $t);
	}
	return $t;
}

/* pipeline pour propre */
function ressource_pre_liens($t) {
	if (strpos($t, '<') !== false) {
		$t = preg_replace_callback(_EXTRAIRE_RESSOURCES, 'traiter_ressources', $t);

		// echapper les autoliens eventuellement inseres (en une seule fois)
		if (strpos($t,"<html>")!==false)
			$t = echappe_html($t);
	}
	return $t;
}

function traiter_ressources($r) {
	if ($ressource = charger_fonction('ressource', 'inc', true))
		$html = $ressource($r[0]);
	else
		$html = htmlspecialchars($r[0]);

	return '<html>'.$html.'</html>';
}

function inc_ressource_dist($r) {
	// $r contient tout le texte définissant la ressource :
	// <fichier.rtf option1 option2...>

	// 1. phraser le raccourci
	$attrs = phraser_tag('<res src='.substr($r,1));

	# debug :)
	$attrs['debug'] = $r;

	// 2. keywords : right => align=right, etc
	foreach(array(
		'right' => 'align',
		'left' => 'align',
		'center' => 'align',
	) as $k => $v) {
		if ($attrs[$k] == $k) {
			$attrs[$v] = $k;
			unset($attrs[$k]);
		}
	}

	// 2. title => titre, description=>descriptif.... (TODO)

	// 3. constituer les meta-donnees associees a $res[src]
	$meta = ressource_meta($attrs);

	// 4. traiter les parametres d'image / logo / vignette / resize
	// supprimera le href si necessaire
	$GLOBALS['debugsrc'] = true;
	$image = ressource_image($attrs, $meta);
	$GLOBALS['debugsrc'] = false;

	$final = array_merge($meta, $attrs);

	// renvoyer le html final
	$final = array_merge($final, $image);

	$html = embed_ressource($final);
	return $html;
}

function ressource_meta($res) {
	$meta = $res;

	// on va beaucoup travailler avec l'attribut src
	$src = $res['src'];

	// identifier la ressource
	// s'agit-il d'un fichier decrit dans la mediathèque,
	// d'un fichier local, d'un oembed, d'un doc distant connu, etc ?

	// ressource fichier.rtf => rtf/fichier.rtf
	if (preg_match(',^[^/]+\.([^.]+)$,', $src, $r))
		$fichier = $r[1].'/'.$r[0];
	else
		$fichier = $src;

	// determiner temporairement l'extension de la ressource (ca pourra changer
	// si on en fait une copie locale et qu'elle indique un autre type mime)
	if (preg_match(',\.(\w+)([?#].*)?$,S', $src, $r)) {
		$meta['extension'] = strtolower($r[1]);

		if ($meta['extension'] == 'jpeg')
			$meta['extension'] = 'jpg';
	}

	# d'abord fouiller la mediatheque
	include_spip('base/abstract_sql');
	if ($s = sql_fetsel('*', 'spip_documents', 'fichier='.sql_quote($fichier))) {
		$meta = $s;
		$meta['href'] = get_spip_doc($s['fichier']);
		$meta['local'] = copie_locale($meta['href'], 'test');
	}

	/* un lien github */
	if (preg_match(",^https://(github\.com)/([^/]+/[^/]+)/blob/([^/]+)/(.*\.([^.?]+))([?].*)?$,",
	$src, $r)) {

		# 1. recuperer le repo, la branche et le chemin du fichier
		$server = $r[1];
		$repo = $r[2];
		$branch = $r[3];
		$file = $r[4];
		$ext = str_replace('jpeg', 'jpg', $r[5]);
		$qs = $r[6];

		# 2. calculer l'url avec ?raw=true pour un access https
		#    et l'url de la copie locale
		$src = parametre_url($src, 'raw', 'true');
		$local = _DIR_RACINE . nom_fichier_copie_locale($src, $ext);

		# 3. regarder si on a un clone en local
		if (defined('_DIR_RESSOURCE_GIT') && (_DIR_RESSOURCE_GIT !== false)) {

			$dir = _DIR_RESSOURCE_GIT . $server . '/' . $repo;
			if (is_dir($dir) && is_dir($dir.'/.git')) {
				$_dir = escapeshellarg($dir);
				$_branch = escapeshellarg($branch);

				try {
					# 4. se mettre dans la branche et recuperer le fichier
					$cmd = "cd $_dir && git checkout $_branch";
					$b = trim(`$cmd`);
					spip_log("$cmd: $b", 'distant');

					# 5. si pas de fichier, ou demande de ?var_mode=images, 
					#    mettre à jour le repo
					if (!file_exists($dir.'/'.$file)
					|| (_request('var_mode') == 'images' && autoriser('reload', 'images') && (filemtime($dir.'/.git/FETCH_HEAD') < time() - 60))
					) {
						$cmd = "cd $_dir && git pull && git checkout $_branch";
						$b = trim(`$cmd`);
						spip_log("$cmd: $b", 'distant');

						# 6. Si ca ne marche toujours pas on est déçu
						#   on regarde si on a déjà une copie locale,
						#   sinon on va quand même tenter un accès https
						if (!file_exists($local)
						and !file_exists($dir.'/'.$file)) {
							throw new Exception('github: pas de version locale, on essaiera par https');
						}
					}

					if (file_exists($dir.'/'.$file)
					and ( !file_exists($local)
						OR filemtime($local) < filemtime($dir.'/'.$file) )
					) {
						@copy($dir.'/'.$file, $local);
					}
					$fichier = $src = $local;
					$meta['local'] = $fichier;
					$meta['href'] = $fichier;
				}
				catch (Exception $e) {
					spip_log($e, 'distant');
				}
			}
		}
	}
	/* fin github */

	if (preg_match(',^https?://,', $src)) {

		/* un document dropbox : remplacer www par dl */
		$src = preg_replace(",^(https://)(www)(\.dropbox\.com/.*/.*/.*)$,",
			'\1dl\3', $src);

		$meta['href'] = $src;

		/* pipeline ! */
		/* exemple : traitement par autoembed */
		include_spip('autoembed/autoembed');
		if (function_exists('embed_url')
		AND $u = embed_url($src)) {
			$meta['embed'] = $u;
		}

		/* autre exemple de traitement avec oembed */
		include_spip('oembed_fonctions');
		if (function_exists('oembed')
			AND $u = oembed($src)
			AND $u != $src) 
		{
			$meta['embed'] = $u;
		}

		/* recuperer un album flickr */
		if (preg_match(',^https?://(www\.)?flickr\.com/.*/sets/(\d+),', $src, $r)) {
			$meta['album'] = $r[2];
			if (strlen($class = array_shift(explode(' ', $meta['class'])))
			AND $f = 'modeles/album_flickr'.'.'.$class
			AND find_in_path($f.'.html')
			AND $html = recuperer_fond($f, $meta)) {
				$meta['embed'] = $html;
			} else
			
			if ($html = recuperer_fond('modeles/album_flickr', $meta)) {
				$meta['embed'] = $html;
				unset($meta['title']); // title utilise par l'embed
			}
		}
		
		// recuperer un blocks.org/blockbuilder *en iframe*
		// (le cas normal est géré par autoembed)
		else if (preg_match("/^https?\:\/\/(bl\.ocks|blockbuilder)\.org\/(\w+\/\w+)(\/\w+)?/i", $src, $regs)
		AND $meta['iframe'] == 'iframe') {
			$meta['embed'] = "<iframe>";
			$meta['embed'] = inserer_attribut($meta['embed'], 'src', "https://cdn.rawgit.com/" . $regs[2] . "/raw" . $regs[3] . "/");
			$meta['embed'] = inserer_attribut($meta['embed'], 'style', "width:100%;height:70vw;border:0;" . $meta['style']);
			if ($meta['class']) $meta['embed'] = inserer_attribut($meta['embed'], 'class', $meta['class']);
			$meta['embed'] .=  sinon($meta['embed'], $src) . "</iframe>";
		}


		$meta = pipeline('ressource_meta',
			array(
				'args' => $res,
				'data' => $meta
			)
		);
		
		/* chargement distant */
		if (!isset($meta['html'])) {
			include_spip('inc/distant');
			if (!$local = copie_locale($src, 'test')
			AND !in_array($meta['extension'], array('mp3'))
			) {
				include_spip('inc/queue');
				queue_add_job('copie_locale', 'copier', array($src), $file = 'inc/distant', $no_duplicate = true, $time=0, $priority=0);
			}
			if ($local = copie_locale($src, 'test')) {
				$meta['local'] = $local;
			}
		}

	}
	// fichier dans IMG/ ?
	else if (preg_match(',^[^/]+\.([^.]+)$,', $src, $r)
	AND $h = _DIR_IMG.$r[1].'/'.$r[0]
	AND @file_exists($h)
	) {
		$meta['local'] = $h;
		$meta['href'] = $h;
	}

	##### tests pour le plugin OPUS :
	##### <image.jpg> correspond à opus/[article]/image.jpg
	else if ($r
	AND isset($GLOBALS['contexte'])
	AND isset($GLOBALS['contexte']['id_article'])
	AND $q = sql_fetsel('url_site', 'spip_articles', 'id_article='.sql_quote($GLOBALS['contexte']['id_article']))
	AND $opus = $q['url_site']
	AND $h = _DIR_RACINE.$opus.'/'.$r[0]
	AND file_exists($h)) {
		$meta['local'] = $h;
		$meta['href'] = $h;
		$meta['logodocument'] = $h;
	}
	####

	// si on l'a, renseigner ce qu'on peut dire du fichier
	if (isset($meta['local'])
	AND @file_exists($meta['local'])) {
		$meta['extension'] = strtolower(preg_replace(',^.*\.,', '', $meta['local']));
		$meta['taille'] = @filesize($meta['local']);
		if ($r = getimagesize($meta['local'])) {
			// donnees brutes du fichier
			$meta['width'] = $r[0];
			$meta['height'] = $r[1];
			$meta['largeur'] = $meta['width'];
			$meta['hauteur'] = $meta['height'];
		}

		if ($meta['extension'] == 'html') {
			// choper ce qu'on peut du html
			ressource_html($meta);
		}

		// extraire ses donnees !
		if (!isset($meta['extract'])
		AND $u = ressource_extract($meta)) {
			$meta['fullextract'] = $u;
			$meta['extract'] = propre(couper($u, 500));
		}
	}

	// recupere le type mime de la ressource
	if (isset($meta['extension']))
		$meta['type_document'] = ressource_mime($meta['extension']);

	// demander le reload du contenu (?var_mode=images)
	if (_request('var_mode') == 'images'
	&& preg_match(',^https?://,', $src)
	&& autoriser('reload', 'distant')) {
		include_spip('inc/queue');
		queue_add_job('copie_locale', 'copier', array($src, 'force'), $file = 'inc/distant', $no_duplicate = true, $time=1, $priority=0);
	}

	return $meta;
}

// choper les trucs du genre meta opengraph ; meta description etc
function ressource_html(&$meta) {

	include_spip('fonctionsale');
	if (function_exists('sale')) {
		$u = sale(spip_file_get_contents($meta['local']));

		$meta['fullextract'] = $u;
		$meta['extract'] = propre(couper($u, 500));
	}
}

function ressource_mime($e) {
	global $tables_images, $tables_sequences, $tables_documents, $tables_mime, $mime_alias;
	include_spip('base/typedoc');


	$mime = $tables_mime[$e];
	if (!$t = $tables_documents[$e]
	AND !$t = $tables_images[$e]
	AND !$t = $tables_sequences[$e])
		$t = $e;

	return $t;

}

/*
 * recoit une chaine
 * renvoie un array
 * les valeurs par defaut sont mappees
 * inspire de http://w-shadow.com/blog/2009/10/20/how-to-extract-html-tags-and-their-attributes-with-php/
 */
function phraser_tag($rr) {
	$attribute_pattern =
	'@
	(
	(?P<name>[-\w]+)			 # attribute name
	\s*=\s*
	(
	    (?P<quote>[\"\'])(?P<value_quoted>.*?)(?P=quote)    # a quoted value
	    |			   # or
	    (?P<value_unquoted>[^\s"\']+?)(?:\s+)	   # an unquoted value
	)
	|(?P<auto>[-\w]+)
	)
	@xsiS';

	// d'abord eliminer le type du tag et l'eventuelle fermeture auto
	$res = array();
	$rr = preg_replace(',^<[:\w]+\s+,S', '', $rr);
	$rr = preg_replace(',\s*/?'.'>$,S', ' ', $rr);

	// ensuite parser le reste des attributs
	preg_match_all($attribute_pattern, $rr, $z, PREG_SET_ORDER);

	foreach($z as $t) {
		if (isset($t['auto'])) {
			if (is_numeric($t['auto'])) # 200
				$res['width'] = $t['auto'];
			elseif (preg_match(',^\d+x\d+$,', $t['auto'])) # 200x300
				$res['geometry'] = $t['auto'];
			else
				$res[$t['auto']] = $t['auto'];
		}
		elseif (isset($t['value_unquoted'])) {
			$res[$t['name']] = $t['value_unquoted'];
		}
		elseif (isset($t['value_quoted'])) {
			$res[$t['name']] = $t['value_quoted'];
		}
	}

	return $res;
}

function embed_ressource($res) {
	// si la ressource a un embed, charger le modele ressource_embed
	// qui l'encapsule dans un <figure>, ajoute eventuellement une légende etc.
	if (isset($res['embed'])) {
		if (!isset($res['width'])
		AND $w = extraire_attribut($res['embed'], 'width'))
			$res['width'] = $w;
		if (!isset($res['height'])
		AND $h = extraire_attribut($res['embed'], 'height'))
			$res['height'] = $h;
		return recuperer_fond('modeles/ressource_embed', $res);
	}

	// si la ressource est un document, renvoyer <doc1>
	if (isset($res['id_document'])) {
#		return recuperer_fond('modeles/doc', $res);
	}

	if ($res['type_document'] == 'PDF') {
		return
			recuperer_fond('modeles/application', $res);
	}

	return
#		"<pre>".var_export($res,true)."</pre>" .
		recuperer_fond('modeles/ressource', $res);
}

/* ici c'est flou… */
function ressource_image($attrs, $meta) {
	$image = array();

	// creer une vignette pour le doc ; si une largeur est exigee,
	// adapter la taille.
	if ($attrs['largeur'] OR $attrs['hauteur']) {
		$resize = true;
	}
	// size
	else {
		if (!$attrs['size']) {
			if ($attrs['image']) # ???? c'est quoi ? le mode ?
				$attrs['size'] = _RESSOURCE_VIGNETTE_LARGEUR_DEFAUT;
			else
				$attrs['size'] = _RESSOURCE_IMAGE_LARGEUR_DEFAUT;
		}

		if (in_array($meta['extension'], array('gif', 'png', 'jpg'))
		AND strlen($b = image_stdsize($meta, $attrs))) {
			$a = $b;
			$resize = true;
		}
	}

	// Verifier d'abord si le parametre 'icon' force l'icon
# todo : icone => icon
	if ($attrs['icon']) {
		$f = charger_fonction('vignette','inc');
		$img = $f($meta['extension'], false);
		if ($resize)
			$a = filtrer('image_graver', filtrer('image_reduire', $img, $attrs['largeur'] ? $attrs['largeur'] : -1, $attrs['hauteur'] ? $attrs['hauteur'] : -1));
		else
			$a = '<img src="'.$img.'" />';

	}
	// methode normale : reduire l'image si possible, sinon icon
	else {
		if (!$a) {
			$w = sinon($attrs['largeur'],500);
			$h = sinon($attrs['hauteur'],700);
			$a = vignette_automatique($meta['id_vignette'], $meta,
				'' /*url*/, $w, $h, null /* align */);
		}
	}

	if ($a) $image['logodocument'] = $a;

	// experimental : DEST
	// TODO: parametre à mieux nommer ?
	// parametre |dest=800 pour reduire l'image LIEE a 800px max
	if ($attrs['dest']) {
		$tmp = filtrer('image_graver', filtrer('image_reduire', $meta['local'], $attrs['dest']));
		if ($tmp = extraire_attribut($tmp, 'src'))
			$image['href'] = $tmp;
	}

	return $image;
}




# s t m d z b o
function image_stdsize($meta, $attrs) {

	$s = $attrs['size'];
	if (isset($meta['local']))
		$img = $meta['local'];
	else
		$img = $attrs['src'];

	# intercepter les URLs flickr pour choper les jolies reductions
	if (preg_match(',^(http://farm.*.staticflickr.com/(\d+/[0-9a-z_]+?))(_[zbo])?\.jpg$,', $img, $r)) {
		if (in_array($s, array('s', 't', 'm', 'z', 'b') )){
			$img = $r[1].'_'.$s.'.jpg';
			return '<img src="'.$img.'" />';
		}
		if (in_array($s, array('d'))) {
			$img = $r[1].'.jpg';
			return '<img src="'.$img.'" />';
		}
	}
	elseif (preg_match(',^(http://farm.*.staticflickr.com/(\d+/[0-9a-z_]+?))(_[k])?\.jpg$,', $img, $r)) {
		if (in_array($s, array('k') )){
			$img = $r[1].'_'.$s.'.jpg';
			return '<img src="'.$img.'" />';
		}
		if (in_array($s, array('d'))) {
			$img = $r[1].'.jpg';
			return '<img src="'.$img.'" />';
		}
	}
	elseif (preg_match(',^(http://farm.*.staticflickr.com/(\d+/[0-9a-z_]+?))(_[h])?\.jpg$,', $img, $r)) {
		if (in_array($s, array('h') )){
			$img = $r[1].'_'.$s.'.jpg';
			return '<img src="'.$img.'" />';
		}
		if (in_array($s, array('d'))) {
			$img = $r[1].'.jpg';
			return '<img src="'.$img.'" />';
		}
	}

	// IMG/jpg/truc.jpg depuis l'espace prive
	if (_DIR_RACINE
	AND substr(_DIR_RACINE.$img, 0, strlen(_DIR_IMG)) == _DIR_IMG)
		$img = _DIR_RACINE.$img;

	if (!is_numeric($s)) {
	switch($s) {
		case 'sq':
		case 'square':
			# la c'est dur
			$d = 75;
			$img = filtrer('image_graver', filtrer('image_passe_partout', $img, $d, $d));
			$img = filtrer('image_graver', filtrer('image_recadre', $img, $d, $d));
			break;
		case 't':
		case 'thumb':
		case 'thumbnail':
			$a = 100;
			break;
		case 'm':
		case 's':
		case 'small':
			$a = 240;
			break;
		case 'z':
		case 'medium640':
			$a = 640;
			break;
		case 'b':
		case 'large':
			$a = 1024;
			break;
		// xl = 'k' chez flickr (2048px)
		case 'k':
		case 'xl':
		case 'extra':
		case 'extralarge':
			$a = 2048;
			break;
		case 'o':
		case 'original':
			$a = 10000000;
			break;
		case '-':
		case '':
		case 'd':
		case 'default':
		default:
			$a = 500;
			break;
	}
	}

	if ($a)
		$img = filtrer('image_graver', filtrer('image_reduire', $img, $a));
	else if (is_numeric($s))
		$img = filtrer('image_graver', filtrer('image_reduire', $img, $s));
	return $img;
}



function ressource_extract($meta) {
	/*

	global $extracteur;

	$extension = $meta['extension'];

	include_spip('extract/'.$extension);
	if (function_exists($lire = $extracteur[$extension])) {
		$charset = 'iso-8859-1';
		$contenu = $lire($meta['local'], $charset);
		var_dump($lire, $contenu);
	}
	*/

	switch($meta['extension']) {
		case 'html':
		case 'doc':
		case 'docx':
		case 'rtf':
		case 'odt':
			$conv = converthtml($meta['local'], $err);
			include_spip('fonctionsale');
			if (function_exists('sale')) {
				$conv = sale($conv);
			}
			break;
		default:
			break;
	}

	return $conv;
}

/**
* Multiple Curl Handlers
* @author Jorge Hebrard ( jorge.hebrard@gmail.com )
**/
class curlNode{
    static private $listenerList;
    private $callback;
    public function __construct($url){
        $new =& self::$listenerList[];
        $new['url'] = $url;
        $this->callback =& $new;
    }
    /**
    * Callbacks needs 3 parameters: $url, $html (data of the url), and $lag (execution time)
    **/
    public function addListener($callback){
        $this->callback['callback'] = $callback;
    }
    /**
    * curl_setopt() wrapper. Enjoy!
    **/
    public function setOpt($key,$value){
        $this->callback['opt'][$key] = $value;
    }
    /**
    * Request all the created curlNode objects, and invoke associated callbacks.
    **/
    static public function request(){
    
        //create the multiple cURL handle
        $mh = curl_multi_init();
        
        $running=null;
        
        # Setup all curl handles
        # Loop through each created curlNode object.
        foreach(self::$listenerList as &$listener){
            $url = $listener['url'];
            $current =& $ch[];
            
            # Init curl and set default options.
            # This can be improved by creating
            $current = curl_init();

            curl_setopt($current, CURLOPT_URL, $url);
            # Since we don't want to display multiple pages in a single php file, do we?
            curl_setopt($current, CURLOPT_HEADER, 0);
            curl_setopt($current, CURLOPT_RETURNTRANSFER, 1);
            
            # Set defined options, set through curlNode->setOpt();
            if (isset($listener['opt'])){
                foreach($listener['opt'] as $key => $value){
                    curl_setopt($current, $key, $value);
                }
            }
            
            curl_multi_add_handle($mh,$current);
            
            $listener['handle'] = $current;
            $listener['start'] = microtime(1);
        } unset($listener);

        # Main loop execution
        do {
            # Exec until there's no more data in this iteration.
            # This function has a bug, it
            while(($execrun = curl_multi_exec($mh, $running)) == CURLM_CALL_MULTI_PERFORM);
            if($execrun != CURLM_OK) break; # This should never happen. Optional line.
            
            # Get information about the handle that just finished the work.
            while($done = curl_multi_info_read($mh)) {
                # Call the associated listener
                foreach(self::$listenerList as $listener){
                    # Strict compare handles.
                    if ($listener['handle'] === $done['handle']) {
                        # Get content
                        $html = curl_multi_getcontent($done['handle']);
                        # Call the callback.
                        call_user_func($listener['callback'],
                        $listener['url'],
                        $html,(microtime(1)-$listener['start']));
                        # Remove unnecesary handle (optional, script works without it).
                        curl_multi_remove_handle($mh, $done['handle']);
                    }
                }
                
            }
            # Required, or else we would end up with a endless loop.
            # Without it, even when the connections are over, this script keeps running.
            if (!$running) break;
            
            # I don't know what these lines do, but they are required for the script to work.
            while (($res = curl_multi_select($mh)) === 0);
            if ($res === false) break; # Select error, should never happen.
        } while (true);

        # Finish out our script ;)
        curl_multi_close($mh);
    
    }
}

function converthtml($f, $err) {
	define('_CONVERT_URL', 'http://office.rezo.net/office/v1/?email=fil@rezo.net&key=1223649b375bb98e1b57141f96643cd47a3029c3');

	$signature = md5_file($f);

	// 1. a-t-on le fichier en local
	$html = sous_repertoire(_DIR_TMP,'converthtml').$signature.'.html';
	if (file_exists($html))
		return spip_file_get_contents($html);

	// 2. sinon le chercher sur le serveur office.rezo
	if (!defined('_CONVERT_URL'))
		return false;

	$url = parametre_url(_CONVERT_URL, 'signature', $signature, '&');

	include_spip('inc/queue');
	queue_add_job('convert_html_fetch', 'convert_html_fetch('.$f.')', array($url, $f, $html), $file = 'inc/ressource', $no_duplicate = true, $time=0, $priority=0);
	#convert_html_fetch($url, $f);

	return '';
}

function convert_html_fetch($url, $f, $html=null) {
	if (!$html) return;
	include_spip('inc/distant');
	if ($rep = recuperer_page($url)
	AND $rep = json_decode($rep)
	AND isset($rep->content)) {
		ecrire_fichier($html, $rep->content);
		return;
	}
	

	// 3. si 404, l'envoyer au serveur
	#include_spip('inc/queue');
	#queue_add_job('convert_html_send', 'convert_html_send('.$f.')', array($url, $f), $file = 'inc/ressource', $no_duplicate = true, $time=0, $priority=0);
	convert_html_send($url, $f);

}

function convert_html_send($url, $f) {
	include_spip('inc/filtres');
	spip_log('curl @'.$f.' '.$url.' ('.taille_en_octets(filesize($f)).')');
	$data = array('file' => '@'.$f);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$n = curl_exec($ch);
	spip_log($n);
	curl_close($ch);
}


function xconverthtml($f, &$err) {


	$k = escapeshellarg($f);

	exec("/usr/bin/textutil -convert html -stdout -noload -nostore $k", $ret, $err);

	if ($err) {
		spip_log($err);
	} else {
		$ret = join($ret, "\n");
		// les notes de bas de page word sont parfois transformees en truc chelou
		$ret = str_replace('<span class="Apple-converted-space"> </span>', '~', $ret);
		return nettoyer_utf8($ret);
	}
}

function nettoyer_utf8($t) {
	if (!preg_match('!\S!u', $t))
		$t = preg_replace_callback(',&#x([0-9a-f]+);,i', 'utf8_do', utf8_encode(utf8_decode($t)));
	return $t;
}

