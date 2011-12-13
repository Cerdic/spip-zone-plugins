<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Fonctions d'accès aux données géographiques pour la partie publique
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');
include_spip('inc/gmap_db_utils');
include_spip('gmap_filtres');

// Rechercher en base comment doit être affichée la carte d'une base
function gmap_get_object_viewport($objet, $id_objet, $profile='interface')
{
	// La clef à laquelle se trouvent les configurations dépend du profile et de l'api
	if (!isset($profile))
		$profile = 'interface';
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	$apiConfigKey = 'gmap_'.$api.'_'.$profile;
	
	// Initialisation sur le défaut du site
	$vp = array();
	$vp['latitude'] = gmap_lire_config($apiConfigKey, 'default_latitude', "0.0");
	$vp['longitude'] = gmap_lire_config($apiConfigKey, 'default_longitude', "0.0");
	$vp['zoom'] = gmap_lire_config($apiConfigKey, 'default_zoom', "1");
	
	// Rechercher les marqueurs de l'objet
	$points = gmap_get_points($objet, $id_objet);
	$centre = null;
	$count = count($points);
	if ($count == 1)
		$centre = array_shift($points);
	else if ($count > 0)
	{
		// Parcourir le tableau pour récupérer le centre et le défaut
		$defaut = null;
		foreach ($points as $point)
		{
			if ($point['type'] == "centre")
			{
				$centre = $point;
				break;
			}
			else if ($point['type'] == "defaut")
				$defaut = $point;
		}
		
		// S'il y a un centre l'utiliser, sinon il faut calculer
		if ($centre === null)
		{
			// Si les points sont assez proches, on fait une moyenne, s'il sont séparés de plus de 10 degré, on prend
			// le défaut ou le premier, ce qui évite le problème entre 180 et -180 (en considérent que, vu que c'est
			// en pleine mer, personne ne saisira deux point proches sur la ligne 180/-180...)
			$minZoom = 50;
			$meanLat = 0.0;
			$meanLng = 0.0;
			foreach ($points as $point)
			{
				if ($point['zoom'] < $minZoom)
					$minZoom = $point['zoom'];
				$meanLat += $point['latitude'];
				$meanLng += $point['longitude'];
			}
			$meanLat /= $count;
			$meanLng /= $count;
			$valid = true;
			foreach ($points as $point)
			{
				if ((abs($point['latitude'] - $meanLat) > 10.0) ||
					(abs($point['longitude'] - $meanLng) > 10.0))
				{
					$valid = false;
					break;
				}
			}
			if ($valid)
			{
				$centre = array();
				$centre['latitude'] = $meanLat;
				$centre['longitude'] = $meanLng;
				$centre['zoom'] = $minZoom;
				$centre['type'] = "moyenne";
			}
			else if ($defaut != null)
				$centre = $defaut;
			else
				$centre = array_shift($points);
		}
	}
	
	// Renvoyer ce qu'on a trouvé
	if ($centre != null)
	{
		$vp['latitude'] = $centre['latitude'];
		$vp['longitude'] = $centre['longitude'];
		$vp['zoom'] = $centre['zoom'];
		return $vp;
	}
	
	// Si on n'a rien trouvé, remonter sur le parent
	$parents = gmap_parents($objet, $id_objet);
	if (count($parents) == 1)
		return gmap_get_object_viewport($parents[0]['objet'], $parents[0]['id_objet'], $profile);
	
	// Sinon renvoyer le défaut du site
	return $vp;
}

// Définition des paramètres de la carte au format défini dans gmap_<impl>_public.js
function gmap_definir_parametre_carte($objet, $id_objet, $varName = null, $params = null, $profile='interface')
{
	// Fonction spécifique à l'API
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	$parametre_carte = charger_fonction("parametre_carte", "mapimpl/".$api."/public");
	if (!$parametre_carte)
		return '';

	// La clef à laquelle se trouvent les configurations dépend du profile et de l'api
	if (!isset($profile))
		$profile = 'interface';
	$apiConfigKey = 'gmap_'.$api.'_'.$profile;
	
	// Récupérer le centre
	if ($params && isset($params['viewport']))
	{
		if ($params['viewport'] === 'site')
		{
			$viewport = array(
				'latitude' => gmap_lire_config($apiConfigKey, 'default_latitude', "0.0"),
				'longitude' => gmap_lire_config($apiConfigKey, 'default_longitude', "0.0"),
				'zoom' => gmap_lire_config($apiConfigKey, 'default_zoom', "1"));
		}
		else if (preg_match("/^([\w]+)([0-9]+)$/i", $params['viewport'], $matches) == 1)
			$viewport = gmap_get_object_viewport($matches[1], intval($matches[2]), $profile);
	}
	else if ($objet && strlen($objet) && $id_objet)
		$viewport = gmap_get_object_viewport($objet, $id_objet, $profile);
	else
	{
		$viewport = array(
			'latitude' => gmap_lire_config($apiConfigKey, 'default_latitude', "0.0"),
			'longitude' => gmap_lire_config($apiConfigKey, 'default_longitude', "0.0"),
			'zoom' => gmap_lire_config($apiConfigKey, 'default_zoom', "1"));
	}
	
	// Retour
	$out = "";
	if ($varName)
	{
		if (strstr($varName, '.') !== false)
			$out .= $varName.' = ';	
		else
			$out .= 'var '.$varName.' = ';	
	}
	$out .= $parametre_carte($viewport, $params, $profile);
	if ($varName)
		$out .= ';' . "\n";
	
	return $out;
}

// Définition des paramètres d'une icone
// Usage :
// gmap_definir_parametre_icon(array('file'=>, 'width'=>, 'height'=>, 'xAnchor'=>, 'yAnchor'=>, 'shadowFile'=>, 'widthShadow'=>, 'heightShadow'=>, 'xShadowAnchor'=>, 'yShadowAnchor'=>, 'xOffset'=>, 'yOffset'=>),
//	array('file'=>, 'width'=>, 'height'=>, 'xAnchor'=>, 'yAnchor'=>), $varName = null)
function gmap_definir_parametre_icon($icon, $complete = null, $varName = null)
{
	// Récupérer la taille de l'image
	if ((!isset($icon['width']) || ($icon['width'] <= 0)) ||
		(!isset($icon['height']) || ($icon['height'] <= 0)))
	{
		$imageInfo = @getimagesize($icon['file']);
		$icon['width'] = $imageInfo[0] ? $imageInfo[0] : 32;
		$icon['height'] = $imageInfo[1] ? $imageInfo[1] : 32;
	}
	
	// Si l'ombre n'est pas précisée, on suppose que c'est l'image par défaut
	if (!$icon['shadowFile'])
		$icon['shadowFile'] = _DIR_PLUGIN_GMAP . 'images/shadow.png';
	// Récupérer la taille de l'ombre
	if ((!isset($icon['widthShadow']) || ($icon['widthShadow'] <= 0)) ||
		(!isset($icon['heightShadow']) || ($icon['heightShadow'] <= 0)))
	{
		$imageInfo = @getimagesize($icon['shadowFile']);
		$icon['widthShadow'] = $imageInfo[0] ? $imageInfo[0] : 32;
		$icon['heightShadow'] = $imageInfo[1] ? $imageInfo[1] : 32;
	}

	// Sortie
	$out = "";
	if ($varName)
		$out .= 'var '.$varName.' = ';	
	$out .= '{';
	
	// Icone normale
	$out .= '
		urlIconFile: "'.$icon['file'].'",
		widthIcon: '.$icon['width'].',
		heightIcon: '.$icon['height'];
	if ($icon['xAnchor'] != null)
		$out .= ',
		anchorX: '.$icon['xAnchor'];
	if ($icon['yAnchor'] != null)
		$out .= ',
		anchorY: '.$icon['yAnchor'];

	// Ombre
	$out .= ',
		urlShadowFile: "'.$icon['shadowFile'].'",
		widthShadow: '.$icon['widthShadow'].',
		heightShadow: '.$icon['heightShadow'];
	if ($icon['xShadowAnchor'] != null)
		$out .= ',
		anchorShadowX: '.$icon['xShadowAnchor'];
	if ($icon['yShadowAnchor'] != null)
		$out .= ',
		anchorShadowY: '.$icon['yShadowAnchor'];
		
	// Image complete, avec l'ombre	
	if ($complete != null)
	{
		if ((!isset($complete['width']) || ($complete['width'] <= 0)) ||
			(!isset($complete['height']) || ($complete['height'] <= 0)))
		{
			$imageInfo = @getimagesize($complete['file']);
			$complete['width'] = $imageInfo[0] ? $imageInfo[0] : 32;
			$complete['height'] = $imageInfo[1] ? $imageInfo[1] : 32;
		}
		$out .= ',
		urlCompleteFile: "'.$complete['file'].'",
		widthComplete: '.$complete['width'].',
		heightComplete: '.$complete['height'];
		if ($complete['xAnchor'] != null)
			$out .= ',
		anchorCompleteX: '.$complete['xAnchor'];
		if ($complete['yAnchor'] != null)
			$out .= ',
		anchorCompleteY: '.$complete['yAnchor'];
	}
	
	// Offset pour l'info bulle (un seul, sur l'icone normale)
	if ($icon['xOffset'] != null)
		$out .= ',
		popupOffsetX: '.$icon['xOffset'];
	if ($icon['yOffset'] != null)
		$out .= ',
		popupOffsetY: '.$icon['yOffset'];
		
	$out .= '
	}';
	if ($varName)
		$out .= ';' . "\n";
	return $out;
}

// Calculer le path du thème utilisé
function gmap_theme_folder()
{
	$api = gmap_lire_config('gmap_api', 'api', "gma3");
	$trouve_theme = charger_fonction("trouve_theme", "mapimpl/".$api."/public", true);
	if ($trouve_theme)
		$folder = $trouve_theme();
	return 'themes/'.($folder ? $folder : 'gmap');
}

// Récupérer un fichier de définition selon un prefix, un nom et un contexte
function _gmap_find_in_path($squelette, $folder)
{
	if ($file = find_in_path($squelette))
		return $file;
	else if ($folder && strlen($folder) && ($file = find_in_path($folder.'/'.$squelette)))
		return $file;
	return false;
}
function _gmap_find_file($prefix, &$name, $ext, &$fond, $folder, $args = null)
{
	// J'ai bien essayé de limiter les recherches par find_in_path, mais ça ne
	// marche pas : comme on fait déjà varier les noms de fichiers avec le type
	// d'objet et le type de point, si on fait, comme spip, d'abord une recherche
	// sur un fichier générique avant de faire varier les rubriques, ça force
	// à créer dans le dossier squelettes des fichiers qui n'existent pas dans
	// le plugin (par exemple gmap-json-article.html). À l'inverse, si on recherche
	// d'abord le fichier le plus simple (gmap-json-default), il faudra forcément tout 
	// redéfinir...
	
	// D'abord chercher un fichier sans le suffixe de zone
	$fileBase = false;
	$fond = $prefix;
	if (strlen($name))
		$fond .= '-'.$name;
	if ($file = _gmap_find_in_path($fond.'.'.$ext, $folder))
		$fileBase = $file;
		
	// Si on n'a pas d'indication de rubriques, s'arrêter là aussi (en succès)
	if (!$args || !$args['ids_rubrique'])
		return $fileBase;

	// Rechercher en faisant varier la rubrique
	// On n'utilise pas le pipeline styliser pour deux raisons :
	// - Il y a plusieurs appels successifs donc le buffer des ids des parents
	// est utile.
	// - Il faudrait utiliser _gmap_find_in_path au lieu d'un file_exists
	$base = $name;
	$name = $base.'='.$args['ids_rubrique'][0];
	$fond = $prefix.'-'.$name;
	if ($file = _gmap_find_in_path($fond.'.'.$ext, $folder))
		return $file;
	$rubs = count($args['ids_rubrique']);
	for ($index = 0; $index < $rubs; $index++)
	{
		$name = $base.'-'.$args['ids_rubrique'][$index];
		$fond = $prefix.'-'.$name;
		if ($file = _gmap_find_in_path($fond.'.'.$ext, $folder))
			return $file;
	}
	
	// Pas trouvé, revenir à la version de base...
	$name = $base;
	$fond = $prefix.(strlen($name)?'-':'').$name;
	return $fileBase;
	// Tout de même, si un jour on ne passe pas de cache...
	//{
	//	$squelette = substr($fileBase, 0, - strlen('.'.$ext));
	//	$fond = pipeline('styliser', array(
	//		'args' => array(
	//			'id_rubrique' => $args['id_rubrique'],
	//			'ext' => $ext,
	//			'fond' => $fond,
	//			'lang' => $GLOBALS['spip_lang'],
	//			'contexte' => $args['contexte']
	//		),
	//		'data' => $squelette,
	//	));
	//	$name = filename($fond);
	//	$file = $fond.'.'.$ext;
	//	return $file;
	//}
}
function gmap_trouve_def_file($contexte, $prefix, $ext, $branches = true, $folder = null, $buffer = null, $default = 'default')
{
	$result = NULL;
	$file = FALSE;
	$fond = NULL;
	
	// Arguments pour _gmap_find_file
	$args = $branches ? array('contexte' => $contexte) : null;
	$id_rubrique = 0;
	
	// Si on cherche un fichier lié à un objet
	if ($contexte['objet'] &&  $contexte['id_objet'])
	{
		// Rechercher d'abord s'il y a un fichier spécifique
		$name = '='.$contexte['objet'].$contexte['id_objet'];
		if ($file = _gmap_find_file($prefix, $name, $ext, $fond, $folder, $args))
		{
			// Ne pas ajouter dans le buffer : ça n'a pas de sens pour une icone spécifique
			$result = array(
				'name'=>$name,
				'spip-path'=>$fond,
				'file'=>$file
			);
			return $result;
		}

		// On a aussi besoin de la rubrique
		if ($branches)
		{
			if ($contexte['objet'] === "rubrique")
				$id_rubrique = $contexte['id_objet'];
			else
				$id_rubrique = gmap_get_rubrique($contexte['objet'], $contexte['id_objet']);
		}
	}
	// Sinon, on peut chercher un fichier lié à un point
	if ($contexte['id_point'])
	{
		$name = '=point'.$contexte['id_point'];
		if ($file = _gmap_find_file($prefix, $name, $ext, $fond, $folder, $args))
		{
			$result = array(
				'name'=>$name,
				'spip-path'=>$fond,
				'file'=>$file
			);
			return $result;
		}
	}
	
	// Si on n'a pas d'objet, passer directement au défaut
	$bufferEntry = false;
	if (!$contexte['objet'] || !$contexte['id_objet'])
	{
		$fond = $prefix;
		if ($default && strlen($default))
			$fond = $prefix.'-'.$default;
		if (!($file = find_in_path($folder.'/'.$fond.'.'.$ext)))
			$file = find_in_path($fond.'.'.$ext);
	}
	else
	{
		// Rechercher dans le buffer (seulement pour les icones)
		if (is_array($buffer) && $contexte['type_point'] && $id_rubrique)
		{
			$bufferEntry = $contexte['objet'].'-'.$contexte['type_point'].'-'.$id_rubrique;
			if ($resultInBuffer = $buffer[$bufferEntry])
			{
				$result = array('name'=>$resultInBuffer); // On renvoie seulement le nom trouvé, ça indique que l'icone est déjà créée
				return $result;
			}
		}
		
		// Ajouter la rubrique dans le contexte pour faire tourner la recherche par rubrique
		if ($id_rubrique)
		{
			$args['id_rubrique'] = $id_rubrique;
			$args['ids_rubrique'] = array($id_rubrique);
			while ($id_rubrique = gmap_get_rubrique('rubrique', $id_rubrique))
				$args['ids_rubrique'][] = $id_rubrique;
			// Ca fait beaucoup de requêtes, on pourrait optimiser en cachant cette
			// liste : sur un même carte on va passer ici pour la requête, les définitions
			// de marqueurs et les images des marqueurs...
		}
		
		// Recherche le marqueur selon le type
		$name = $contexte['objet'].'-'.$contexte['type_point'];
		if (!$type_point || (strlen($type_point) == 0) ||
			!($file = _gmap_find_file($prefix, $name, $ext, $fond, $folder, $args)))
		{
			// sinon, rechercher seulement avec l'objet
			$name = $contexte['objet'];
			if (!($file = _gmap_find_file($prefix, $name, $ext, $fond, $folder, $args)))
			{
				// et avec le type
				$name = $type_point;
				if (!$type_point || (strlen($type_point) == 0) ||
					(!($file = _gmap_find_file($prefix, $name, $ext, $fond, $folder, $args))))
				{
					// fichier par defaut de la partie publique
					$name = $default;
					$file = _gmap_find_file($prefix, $name, $ext, $fond, $folder, $args);
				}
			}
		}
	}

	if (!$file)
		return null;
	$result = array();
	$result['name'] = $name;
	$result['spip-path'] = $fond;
	if ($file && (strlen($file) > 0))
		$result['file'] = $file;
	$result['buffer'] = $bufferEntry;
	return $result;
}

// Décoder le fichier d'icône pour en recupérer toute l'information
function gmap_init_icon_def()
{
	$icon = array();
	// Ne pas mettre de valeurs par défaut : si ce n'est pas fixé il n'y a rien...
}
class gmap_icon_def_file_parser
{					   
	var $_file;			// Chaîne de description, au format XML
	var $_errorString;	// Chaîne d'erreur
	var $_bIsValid;		// Indique que le tag "markers" a été trouvé
	var $_icons;		// Tableau des icones trouvées "normal" et "selected"
	var $_currentIcon;	// Indice de l'icone en cours de traitement
	var $_currentTag;	// Tag en cours de lecture
	var $_insideData;	// Indique que des données ont déjà été trouvées
	function gmap_icon_info_file_parser()
	{
		$this->_file = "";
		$this->_errorString = "Erreur inconnue";
		$this->_bIsValid = FALSE;
		$this->_icons = array();
		$this->_currentIcon = -1;
		$this->_currentTag = NULL;
		$this->_insideData = FALSE;
	}
	
	// Fonctions de parsing
	function parse($fileName)
	{
		// Lire le fichier
		if (!@file_exists($fileName))
		{
			$this->_errorString = "Fichier ".$fileName." inaccessible";
			return FALSE;
		}
		$this->_file = @file_get_contents($fileName);
		$this->_file = preg_replace('/<\?xml(.*)>/imU', '', $this->_file);
		
		// Si la requête existe, la décoder
		$isError = FALSE;
		if (strlen($this->_file) > 0)
		{
			// Créer un parseur XML pour décoder la chaîne de requête
			$parseurXML = xml_parser_create();
			if ($parseurXML == NULL)
				return FALSE;
			xml_set_object($parseurXML, $this);
			xml_parser_set_option($parseurXML, XML_OPTION_CASE_FOLDING, 0);
			xml_set_element_handler($parseurXML, "parseTagOpen", "parseTagClose");
			xml_set_character_data_handler($parseurXML, "parseContents");

			// Parser
			if (xml_parse($parseurXML, $this->_file, TRUE) != 1)
			{
				$errorCode = xml_get_error_code($parseurXML);
				$this->_errorString = 'Erreur "'.xml_error_string($errorCode).'" ('.$errorCode.') at line '.xml_get_current_line_number($parseurXML);
				$isError = TRUE;
			}

			// Libérer le parseur XML
			xml_parser_free($parseurXML);
		}
		else
		{
			$this->_errorString = "Fichier vide";
			$isError = TRUE;
		}

		return $isError ? FALSE : TRUE;
	}
	function parseTagOpen($parser, $tag, $attributes) 
	{
		// Ouverture du fichier
		if (strcasecmp($tag, "markers") == 0)
			$this->_bValid = TRUE;
		else if (!$this->_bValid)
		{
			$this->_errorString = 'Fichier non valide';
			return;
		}
		
		// Définition d'une icone
		if (strcasecmp($tag, "icon") == 0)
		{
			$this->_currentIcon = count($this->_icons);
			$this->_icons[$this->_currentIcon] = gmap_init_icon_def();
			if (isset($attributes['type']))
				$this->_icons[$this->_currentIcon]['type'] = $attributes['type'];
			if (isset($attributes['state']))
				$this->_icons[$this->_currentIcon]['state'] = $attributes['state'];
		}
		else if (strcasecmp($tag, "iconShort") == 0)
		{
			$this->_currentIcon = count($this->_icons);
			$this->_icons[$this->_currentIcon] = gmap_init_icon_def();
			if (isset($attributes['type']))
				$this->_icons[$this->_currentIcon]['type'] = $attributes['type'];
			if (isset($attributes['state']))
				$this->_icons[$this->_currentIcon]['state'] = $attributes['state'];
			if (isset($attributes['url']))
				$this->_icons[$this->_currentIcon]['image'] = $attributes['url'];
			if (isset($attributes['cxSize']))
				$this->_icons[$this->_currentIcon]['cxSize'] = $attributes['cxSize'];
			if (isset($attributes['cySize']))
				$this->_icons[$this->_currentIcon]['cySize'] = $attributes['cySize'];
			if (isset($attributes['xAnchor']))
				$this->_icons[$this->_currentIcon]['xAnchor'] = $attributes['xAnchor'];
			if (isset($attributes['yAnchor']))
				$this->_icons[$this->_currentIcon]['yAnchor'] = $attributes['yAnchor'];
			if (isset($attributes['xOffset']))
				$this->_icons[$this->_currentIcon]['xOffset'] = $attributes['xOffset'];
			if (isset($attributes['yOffset']))
				$this->_icons[$this->_currentIcon]['yOffset'] = $attributes['yOffset'];
		}
		else if (($this->_currentIcon < 0) || ($this->_currentIcon >= count($this->_icons)))
		{
			$this->_errorString = 'Fichier non valide';
			return;
		}
		
		// Attributs de l'icone 
		if (strcasecmp($tag, "image") == 0)
		{
			$this->_currentTag = $tag;
			$this->_insideData = FALSE;
		}
		else if (strcasecmp($tag, "size") == 0)
		{
			if (isset($attributes['x']))
				$this->_icons[$this->_currentIcon]['cxSize'] = $attributes['x'];
			if (isset($attributes['y']))
				$this->_icons[$this->_currentIcon]['cySize'] = $attributes['y'];
		}
		else if (strcasecmp($tag, "anchor") == 0)
		{
			if (isset($attributes['x']))
				$this->_icons[$this->_currentIcon]['xAnchor'] = $attributes['x'];
			if (isset($attributes['y']))
				$this->_icons[$this->_currentIcon]['yAnchor'] = $attributes['y'];
		}
		else if (strcasecmp($tag, "offset") == 0)
		{
			if (isset($attributes['x']))
				$this->_icons[$this->_currentIcon]['xOffset'] = $attributes['x'];
			if (isset($attributes['y']))
				$this->_icons[$this->_currentIcon]['yOffset'] = $attributes['y'];
		}
	}
	function parseTagClose($parser, $tag) 
	{
		// Fin du fichier
		if (!$this->_bValid)
		{
			$this->_errorString = 'Fichier non valide';
			return;
		}
		if (strcasecmp($tag, "markers") == 0)
			$this->_bValid = FALSE;
			
		// Fin d'une icone
		if (strcasecmp($tag, "icon") == 0)
			$this->_currentIcon = -1;
		
		// Si il y avait du contenu, on en sort
		$this->_currentTag = NULL;
	}
	function parseContents($parser, $text)
	{
		if (($this->_currentIcon < 0) || ($this->_currentIcon >= count($this->_icons)))
			return;
		if ($this->_currentTag && (strcasecmp($this->_currentTag, "image") == 0))
		{
			if (!$this->_insideData)
				$this->_icons[$this->_currentIcon]['image'] = $text;
			else
				$this->_icons[$this->_currentIcon]['image'] .= $text;
			$this->_insideData = TRUE;
		}
	}
	
	// Fonctions d'accès
	function getIcons()
	{
		return $this->_icons;
	}
	function getError()
	{
		return $this->_errorString;
	}
}
function gmap_parse_icone_def_file($file)
{
	if (@file_exists($file))
	{
		$parser = new gmap_icon_def_file_parser;
		if ($parser->parse($file))
			return $parser->getIcons();
		else
			spip_log("Erreur dans la lecture du fichier ".$file.", ".$parser->getError(), "gmap");
	}
	return NULL;
}

// Fonctions permettant de récupérer l'info d'une liste d'icones
function gmap_get_icon($icons, $bSelected = FALSE, $bComplete = FALSE, $folder = '')
{
	if (!$icons)
		return null;
	$bSimple = FALSE;
	$iconSimple = null;
	$iconShadow = null;
	$iconComplete = null;
	foreach ($icons as $icon)
	{
		if (isset($icon['state']) && ((($icon['state'] === "selected") ? TRUE : FALSE) !== $bSelected))
			continue;
		if (!isset($icon['type']) || ($icon['type'] === "simple"))
			$iconSimple = $icon;
		else
		{
			if ($icon['type'] === "shadow")
				$iconShadow = $icon;
			else if ($icon['type'] === "complete")
				$iconComplete = $icon;
		}
	}
	if ($bComplete === TRUE)
	{
		if ($iconComplete)
			return array('file'=>_gmap_find_in_path($iconComplete['image'], $folder),
							'width'=>$iconComplete['cxSize'], 'height'=>$iconComplete['cySize'],
							'xAnchor'=>$iconComplete['xAnchor'], 'yAnchor'=>$iconComplete['yAnchor']);
		else if ($iconSimple)
			return array('file'=>_gmap_find_in_path($iconSimple['image'], $folder),
							'width'=>$iconSimple['cxSize'], 'height'=>$iconSimple['cySize'],
							'xAnchor'=>$iconSimple['xAnchor'], 'yAnchor'=>$iconSimple['yAnchor']);
		else
			return null;
	}
	else
	{
		if ($iconSimple && $iconShadow)
			return array('file'=>_gmap_find_in_path($iconSimple['image'], $folder), 'width'=>$iconSimple['cxSize'], 'height'=>$iconSimple['cySize'], 'xAnchor'=>$iconSimple['xAnchor'], 'yAnchor'=>$iconSimple['yAnchor'],
						'shadowFile'=>($iconShadow ? _gmap_find_in_path($iconShadow['image'], $folder) : ""), 'widthShadow'=>$iconShadow['cxSize'], 'heightShadow'=>$iconShadow['cySize'], 'xShadowAnchor'=>$iconShadow['xAnchor'], 'yShadowAnchor'=>$iconShadow['yAnchor'],
						'xOffset'=>$iconSimple['xOffset'], 'yOffset'=>$iconSimple['yOffset']);
		else if ($iconComplete)
			return array('file'=>_gmap_find_in_path($iconComplete['image'], $folder), 'xAnchor'=>$iconComplete['xAnchor'], 'yAnchor'=>$iconComplete['yAnchor'],
						'xOffset'=>$iconComplete['xOffset'], 'yOffset'=>$iconComplete['yOffset']);
		else if ($iconSimple)
			return array('file'=>_gmap_find_in_path($iconSimple['image'], $folder), 'width'=>$iconSimple['cxSize'], 'height'=>$iconSimple['cySize'], 'xAnchor'=>$iconSimple['xAnchor'], 'yAnchor'=>$iconSimple['yAnchor'],
						'xOffset'=>$iconSimple['xOffset'], 'yOffset'=>$iconSimple['yOffset']);
		else
			return null;
	}
}

// Récupérer tous les paramètres d'une icone et la créer
function gmap_definition_icone($name, $bSelected = FALSE)
{
	$folder = gmap_theme_folder();
	$branches = false; // fonction utilisée depuis la partie privée uniquement, il n'y a pas de variations
	if (!($icon = gmap_trouve_def_file(null, $name, 'gmd', $branches, $folder, null, '')) || !isset($icon['file']))
		return 'null';
	
	// Récupérer la définition de l'icone
	$icons = gmap_parse_icone_def_file($icon['file']);
	$icon = gmap_get_icon($icons, $bSelected, false, $folder);
	if (!$icon)
		return "";

	// Ajout des icones complètes
	$complete = gmap_get_icon($icons, $bSelected, true, $folder);
	if (!$complete)
		$complete = array(0=>null, null, null);
		
	// Créer les icones
	return gmap_definir_parametre_icon($icon, $complete);
}
function gmap_ajoute_icone($name, $defFile, $map)
{
	$gerer_selection = (gmap_lire_config('gmap_optimisations', 'gerer_selection', 'oui') === 'oui') ? true : false;
	
	// Récupérer la définition de l'icone
	$icons = gmap_parse_icone_def_file($defFile);
	$folder = gmap_theme_folder();
	$icon = gmap_get_icon($icons, FALSE, FALSE, $folder);
	if ($gerer_selection)
		$selected = gmap_get_icon($icons, TRUE, FALSE, $folder);
	if (!$icon)
		return "";

	// Ajout des icones complètes
	$complete = gmap_get_icon($icons, FALSE, TRUE, $folder);
	if (!$complete)
		$complete = array(0=>NULL, NULL, NULL);
	if ($gerer_selection)
	{
		$completeSelected = gmap_get_icon($icons, TRUE, TRUE, $folder);
		if (!$completeSelected)
			$completeSelected = $complete;
	}
		
	// Créer les icones
	$cmd = "";
	$cmd .= '	'.$map.'.setIcon("'.$name.'", '.gmap_definir_parametre_icon($icon, $complete).');' . "\n";
	if ($gerer_selection)
	{
		if ($selected)
			$cmd .= '	'.$map.'.setIcon("'.$name.'_sel", '.gmap_definir_parametre_icon($selected, $completeSelected).');' . "\n";
		else
			$cmd .= '	'.$map.'.setIcon("'.$name.'_sel", '.gmap_definir_parametre_icon($icon, $completeSelected).');' . "\n";
	}
	
	return $cmd;
}

// Récupération de l'intérieur d'une bulle
function gmap_get_object_info_contents($contexte)
{
	$branches = (gmap_lire_config('gmap_optimisations', 'gerer_branches', 'oui') === 'oui') ? true : false;
	$fond = gmap_trouve_def_file($contexte, 'gmap-info', 'html', $branches, 'modeles');
	if (!$fond)
		return "";
	$page = recuperer_fond($fond['spip-path'], $contexte);
	return $page;
}

// Utilitaire pour ajouter un marqueur
function gmap_ajoute_marqueur($marker, $map, $mapId)
{
	$precmd = '';
	
	// Paramètres standards du marqueur (position et meta-info)
	$markerParams = '';
	$markerParams .= 			'			latitude: '.$marker['latitude'];
	$markerParams .= ','."\n" . '			longitude: '.$marker['longitude'];
	$markerParams .= ','."\n" . '			objectName: "'.$marker['objet'].'"';
	$markerParams .= ','."\n" . '			objectId: '.$marker['id_objet'];
	$markerParams .= ','."\n" . '			visible: "'.$marker['visible'].'"';
	$markerParams .= ','."\n" . '			priority: '.$marker['priorite'];
	$markerParams .= ','."\n" . '			zoom: '.$marker['zoom'];
	$markerParams .= ','."\n" . '			type: "'.$marker['type'].'"';
	
	// Déterminer le titre
	$titre = gmap_marqueur_titre($marker['objet'], $marker['id_objet']);
	if (strlen($titre))
		$markerParams .= ','."\n" . '			title: "'.protege_titre($titre).'"';

	// Contexte de la recherche des fichiers
	$contexte = array();
	if ($marker['objet'] && strlen($marker['objet']) && $marker['id_objet'])
	{
		$contexte['objet'] = $marker['objet'];
		$contexte['id_objet'] = $marker['id_objet'];
		$contexte['id_'.$marker['objet']] = $marker['id_objet'];
	}
	if ($marker['type'] && strlen($marker['type']))
		$contexte['type_point'] = $marker['type'];
	if ($marker['id'])
		$contexte['id_point'] = $marker['id'];
	
	// Déterminer l'icone
	if (!$GLOBALS['iconsAliases'.$mapId])
		$GLOBALS['iconsAliases'.$mapId] = array();
	if (!$GLOBALS['iconsDefs'.$mapId])
		$GLOBALS['iconsDefs'.$mapId] = array();
	$branches = (gmap_lire_config('gmap_optimisations', 'gerer_branches', 'oui') === 'oui') ? true : false;
	if (($icon = gmap_trouve_def_file($contexte, 'gmap-marker', 'gmd', $branches, gmap_theme_folder(), $GLOBALS['iconsAliases'.$mapId])) &&
		isset($icon['name']))
	{
		// Gérer le buffer
		if ($icon['file'] && $icon['buffer'])
			$GLOBALS['iconsAliases'.$mapId][$icon['buffer']] = $icon['name'];
			
		// Éviter de mettre plusieurs fois la même icone dans le fichier
		// Le buffer des icones ne suffit pas puisqu'il est index sur un nom complet :
		// dans le cas des rubriques, on les créé toujours !
		if ($GLOBALS['iconsDefs'.$mapId][$icon['name']]) // Déjà défini dans cette session
			unset($icon['file']);
		else
			$GLOBALS['iconsDefs'.$mapId][$icon['name']] = true;
	
		// Ajouter le code pour créer l'icone et l'ajouter à la définition du marqueur
		if ($icon['file'] && ($icon['name'] != "default"))
			$precmd .= gmap_ajoute_icone($icon['name'], $icon['file'], $map);
		$markerParams .= ','."\n" . '			icon: "'.$icon['name'].'"';
		$markerParams .= ','."\n" . '			icon_sel: "'.$icon['name'].'_sel"';
	}

	// Ajouter l'info-bulle
	if (($bulle = gmap_get_object_info_contents($contexte)) && strlen($bulle))
	{
		$markerParams .= ','."\n" . '			click: "showInfoWindow"';
		$markerParams .= ','."\n" . '			html: "'.protege_html_body($bulle).'"';
	}
	
	$cmd = '	'.$map.'.setMarker('.$marker['id'].', {'."\n".$markerParams."\n".'		});' . "\n";
	return $precmd.$cmd;
}

// Ajout des marqueurs sur la carte
//$GLOBALS['raccourcis_markers'] = array(
//	'rubriques', 'articles', 'documents', 'breves', 'auteurs', 'mots',
//	'recherche', 'racine'); // inutilisé, gardé au cas ou...
function _gmap_ajoute_markers_kml($queryUrl, $table, $id, $mapId, $params, $mapInit)
{
		
	$map = '';
	$map .= '
	// Chargement des marqueurs par requête ajax/xml';
	if ($mapInit)
		$map .= '
	bCompleted = false;';
	$map .= '
	jQuery.ajax({
		url: "'.$queryUrl.'",
		type: "GET",
		dataType: "xml",
		data: {';
	foreach ($params as $key => $value)
		$map .= '
			'.$key.': "'.$value.'",';
	if ($table && $id)
		$map .= '
			id_'.$table.': '.$id.',';
	$map .= '
			prefix: "gmm"
		},
		success: function(xmlDoc, status, request) {
			if (xmlDoc)
				gmap_handleXMLMarkers(map, xmlDoc);
		},
		complete: function() {';
	if ($mapInit)
		$map .= '
			jQuery("#gmap_attente'.$mapId.'").remove();';
	if ($params['focus'])
		$map .= '
			gmap_setViewportOnMarkers("'.$mapId.'");';
	$map .= '
		}
	});
';
	return $map;
}
function _gmap_ajoute_markers_json($queryUrl, $table, $id, $mapId, $params, $mapInit)
{
	$map = '';
	$map .= '
	// Chargement des marqueurs par requête ajax/json';
	if ($mapInit)
		$map .= '
	bCompleted = false;';
	$map .= '
	jQuery.ajax({
		url: "'.$queryUrl.'",
		type: "GET",
		dataType: "json",
		data: {';
	foreach ($params as $key => $value)
		$map .= '
			'.$key.': "'.$value.'",';
	if ($table && $id)
		$map .= '
			id_'.$table.': '.$id;
	$map .= '
		},
		success: function(content, status, request) {
			if (content)
				gmap_handleJSONMarkers(map, content);
		},
		complete: function() {';
	if ($mapInit)
		$map .= '
			jQuery("#gmap_attente'.$mapId.'").remove();';
	if ($params['focus'])
		$map .= '
			gmap_setViewportOnMarkers("'.$mapId.'");';
	$map .= '
		}
	});
';
	return $map;
}
function gmap_ajoute_markers($table, $id, $mapId, $params, $mapInit)
{
	$map = "";

	$bFocusOnExit = $params['focus'] ? true : false;
	
	// Marqueurs locaux, sur l'objet
	if ($params['markers'] === "local")
	{
		if ($table && $id)
		{
			$markers = gmap_get_tree_points($table, $id);
			foreach ($markers as $idxMarker => $marker)
				$map .= gmap_ajoute_marqueur($marker, "map", $mapId);
		}
	}
	
	// Marqueurs sur l'objet et ses descendants immédiats
	else if ($params['markers'] === "childs")
	{
		if ($table && $id)
		{
			$markers = gmap_get_tree_points($table, $id, 1);
			foreach ($markers as $idxMarker => $marker)
				$map .= gmap_ajoute_marqueur($marker, "map", $mapId);
		}
	}
	
	// Marqueurs sur l'objet et ses descendants
	else if ($params['markers'] === "recursive")
	{
		if ($table && $id)
		{
			$markers = gmap_get_tree_points($table, $id, 99);
			foreach ($markers as $idxMarker => $marker)
				$map .= gmap_ajoute_marqueur($marker, "map", $mapId);
		}
	}
	
	// Sinon requête ajax
	else
	{
		$format = isset($params['format']) ? $params['format'] : 'kml';
		$bFocusOnExit = false; // au retour de la requête ajax
		
		// Raccourcis
		if ($params['markers'] !== 'query')
		{
			$params['query'] = $params['markers'];
			$params['markers'] = 'query';
		}
		
		// Créer un contexte de recherche de fichier
		$contexte = array();
		if ($table && strlen($table) && $id)
		{
			$contexte['objet'] = $table;
			$contexte['id_objet'] = $id;
			$contexte['id_'.$table] = $id;
		}
		
		// Il y a une requête ajax, directe ou par raccourci
		$branches = (gmap_lire_config('gmap_optimisations', 'gerer_branches', 'oui') === 'oui') ? true : false;
		if ($params['markers'] === 'query')
		{
			if (isset($params['query']))
			{
				if ($queryMatch = gmap_trouve_def_file($contexte, 'gmap-'.$format.'-'.$params['query'], 'html', $branches, 'modeles', null, ''))
					$queryFile = $queryMatch['spip-path'];
				else if ($queryMatch = gmap_trouve_def_file($contexte, $params['query'], 'html', $branches, 'modeles', null, ''))
					$queryFile = $queryMatch['spip-path'];
				else
					spip_log("Requete ".$params['query']." introuvable", "gmap");
			}
			else if ($queryMatch = gmap_trouve_def_file($contexte, 'gmap-'.$format, 'html', $branches, 'modeles'))
				$queryFile = $queryMatch['spip-path'];
			else
				$queryFile = find_in_path('gmap-'.$format.'-default');
				
			if ($queryUrl = generer_url_public($queryFile, "", true, false))
			{
				if ($format == 'json')
					$map .= _gmap_ajoute_markers_json($queryUrl, $table, $id, $mapId, $params, $mapInit);
				else if ($format == 'kml')
					$map .= _gmap_ajoute_markers_kml($queryUrl, $table, $id, $mapId, $params, $mapInit);
			}
		}
	}
	
	// Afficher tous les marqueurs
	if ($bFocusOnExit)
		$map .= '
	gmap_setViewportOnMarkers("'.$mapId.'");';
		
	
	return $map;
}

// Ajout de la carte clicable
function gmap_ajoute_carte_public($table, $id, $mapId, $params)
{
	// Mécanisme anti-récursion : si on affiche une carte sur laquelle on affiche un 
	// marqueur dont la bulle d'information contient une carte, on entre dans une
	// boucle infinie.
	// Donc on bloque.
	if ($GLOBALS["in_geomap"])
		return "";
	$GLOBALS["in_geomap"] = true;
	
	$map = "";

	// Ajouter un DIV qui va recevoir la carte
	$map .= '<div id="gmap_cont'.$mapId.'" class="carte_gmap"></div>' . "\n";
	
	// Script associé
	$map .= '<script type="text/javascript">'."\n".'//<![CDATA['."\n";
	$map .= '
// Chargement de la carte et mise en place des gestionnaire d\'évènement
function loadCartePublic'.$mapId.'()
{
	// D\'abord afficher le masque d\'attente
	var parent = jQuery("#gmap_cont'.$mapId.'").parent();
	parent.addClass("carte_gmap_container");
	var height = parent.height();
	var waitBlock = \'<div id="gmap_attente'.$mapId.'" class="map_wait_mask" style="width: 100%; height: \'+height+\'px;"><span class="map_wait_logo" style="width: 100%; height: 100%;"></span></div>\';
	jQuery("#gmap_cont'.$mapId.'").after(waitBlock); 
	var bCompleted = true;
	
	// Récupérer la carte
	var map = MapWrapper.getMap("gmap_map'.$mapId.'", true);
	if (!isObject(map))
		return false;

	// Avant de créer la carte, redimensionner le conteneur à la taille du parent
	jQuery("#gmap_cont'.$mapId.'").css("width", "100%");
	jQuery("#gmap_cont'.$mapId.'").css("height", height+"px");
	
	// Chargement de la carte
	';
	// Lecture des paramètres de la carte
	$map .= gmap_definir_parametre_carte($table, $id, 'mapParams'.$mapId, $params);
	$map .= '
	mapParams'.$mapId.'.handleResize = true;
	if (!map.load("gmap_cont'.$mapId.'", mapParams'.$mapId.'))
	{
		if (bCompleted)
			jQuery("#gmap_attente'.$mapId.'").remove();
		return false;
	}
';

	// Ajout du chargement des marqueurs
	if (isset($params['markers']))
		$map .= gmap_ajoute_markers($table, $id, $mapId, $params, true);

	$map .= '
	if (bCompleted)
		jQuery("#gmap_attente'.$mapId.'").remove();

	return true;
}
	
// Chargement du document
jQuery(document).ready(function()
{
	if (!isObject(gMap("gmap_map'.$mapId.'")) || !gMap("gmap_map'.$mapId.'").isLoaded())
		loadCartePublic'.$mapId.'();
});

// Fermeture de la page
jQuery(document).unload(function()
{
	if (isObject(gMap("gmap_map'.$mapId.'")))
		MapWrapper.freeMap("gmap_map'.$mapId.'");
});
	
';
	$map .= '//]]>'."\n".'</script>'."\n";

	$GLOBALS["in_geomap"] = false;
	return $map;
}

// Ajout d'un fichier KML
function gmap_ajoute_kml_url($id, $url, $mapId, $show = true)
{
	// Tests de validité
	if (!$id || !strlen($url) || !$mapId)
		return "";
		
	// Début du code
	$code = '<script type="text/javascript">'."\n".'//<![CDATA[';
	
	// Gérer des id chaine ou numérique
	if (is_string($id))
		$id = '"'.$id.'"';
	
	// Ajouter un handler d'évènement sur le chargement de la carte
	$code .= '
jQuery(document).ready(function()
{
	jQuery("#gmap_cont'.$mapId.'").gmapReady(function()
	{
		var map = gMap("gmap_map'.$mapId.'");
		if (isObject(map))
			map.addLayerKML('.$id.', "'.$url.'", '.($show?'true':'false').');
	});
});
';

	// Code renvoyé
	$code .= '//]]>'."\n".'</script>'."\n";
	return $code;
}
function gmap_ajoute_kml($id_document, $mapId, $show = true)
{
	// Tests de validité
	if (!$id_document || !$mapId)
		return "";
		
	// Récupérer l'url du document
	include_spip('inc/documents'); // pour 'get_spip_doc'
	include_spip('inc/filtres'); // pour 'url_absolue'
	if (!($doc = sql_fetsel('fichier', 'spip_documents', 'id_document='.$id_document)))
		return "";
	if (!($url = url_absolue(get_spip_doc($doc['fichier']))))
		return "";
	
	// Passer par la fonction d'ajout par url
	return gmap_ajoute_kml_url($id_document, $url, $mapId, $show);
}

// Ajout manuel d'un marqueur provenant d'un objet SPIP géolocalisé
// Cette fonction est faite pour être appelée depuis la balise GEOMARKER pour ajouter un
// marqueur qui n'est pas normalement renvoyé par la balise GEOMAP.
function gmap_ajoute_marqueur_site($objet, $id_objet, $mapId, $type, $params)
{
	// Tests de validité
	if (!$mapId)
		return "";
	
	// Récupérer un point visible et le mieux placé s'il y en a plusieurs (selon la colonne priorite des types)
	$point = gmap_get_point($objet, $id_objet, $type);
	if (!$point)
		return "";
		
	// Construire le code du marqueur
	$point['objet'] = $objet;
	$point['id_objet'] = $id_objet;
	$codeMarker = gmap_ajoute_marqueur($point, "map", $mapId);
	if (!strlen($codeMarker))
		return "";
	
	// Construction du code
	$code = '
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function()
{
	jQuery("#gmap_cont'.$mapId.'").gmapReady(function()
	{
		var map = gMap("gmap_map'.$mapId.'");
		if (isObject(map))
		{
	'.$codeMarker;
	if ($params && $params['focus'])
		$code .= '
			gmap_setViewportOnMarkers("'.$mapId.'");';
	$code .= '
		}
	});
});
//]]>
</script>';
	return $code;
}

// Ajout manuel des maruqeurs d'une requête, selon les même arguments que GEOMAP
function gmap_ajoute_marqueur_query($objet, $id_objet, $mapId, $params)
{
	// Tests de validité
	if (!$mapId)
		return "";
	
	// Construire le code du marqueur
	$buffer = array();
	$codeMarker = gmap_ajoute_markers($params['objet'], intval($params['id_objet']), $mapId, $params, false);
	if (!strlen($codeMarker))
		return "";
	
	// Construction du code
	$code = '
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function()
{
	jQuery("#gmap_cont'.$mapId.'").gmapReady(function()
	{
		var map = gMap("gmap_map'.$mapId.'");
		if (isObject(map))
		{
	'.$codeMarker.'
		}
	});
});
//]]>
</script>';
	return $code;
}

// Ajout d'un marqueur special, c'est-à-dire qui ne provient pas d'un objet SPIP géolocalisé
// (on passe donc en paramètre toutes les informations nécessaires)
function gmap_ajoute_marqueur_special($id, $latitude, $longitude, $mapId, $params = null)
{
	// Tests de validité
	if (!$id || !$latitude || !$longitude || !$mapId)
		return "";
		
	// Gérer des id chaine ou numérique
	$iconId = 'icon_'.$id;
	if (is_string($id))
		$id = '"'.$id.'"';
	
	// Paramètres standards du marqueur (position et meta-info)
	$markerParams = '';
	$markerParams .= '			latitude: '.$latitude;
	$markerParams .= ','."\n" . '			longitude: '.$longitude;
	
	// Ajouter le titre
	if ($params && strlen($params['titre']))
		$markerParams .= ','."\n" . '			title: "'.$params['titre'].'"';
	
	// Déterminer l'icone
	$precmd = '';
	if ($params && $params['icon'])
	{
		$file = find_in_path($params['icon'].'.gmd');
		$precmd .= gmap_ajoute_icone($iconId, $file, "map");
		$markerParams .= ','."\n" . '			icon: "'.$iconId.'"';
	}

	// Ajouter l'info-bulle
	if ($params && (strlen($params['titre']) || strlen($params['texte'])))
	{
		$html = '<div class="gmap-balloon">' . "\n";
		if (strlen($params['titre']))
			$html .= '	<h1>'.$params['titre'].'</h1>' . "\n";
		if (strlen($params['texte']))
		{
			$html .= '	<div class="contents">' . "\n";
			$html .= '		<div class="texte"><p>'.$params['texte'].'</p></div>' . "\n";
			$html .= '	</div>' . "\n";
		}
		$html .= '</div>' . "\n";
		$markerParams .= ','."\n" . '			click: "showInfoWindow"';
		$markerParams .= ','."\n" . '			html: "'.protege_html($html).'"';
	}
	
	// Construction du code
	$code = '
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function()
{
	jQuery("#gmap_cont'.$mapId.'").gmapReady(function()
	{
		var map = gMap("gmap_map'.$mapId.'");
		if (isObject(map))
		{
	'.$precmd.'
			map.setMarker('.$id.', {
	'.$markerParams.'
			});';
	if ($params && $params['focus'])
		$code .= '
			gmap_setViewportOnMarkers("'.$mapId.'");';
	$code .= '
		}
	});
});
//]]>
</script>';
	return $code;
}

// Ajout d'un maruquer à partir d'une adresse
function gmap_ajoute_marqueur_adresse($id, $adresse, $mapId, $params = null)
{
	// Tests de validité
	if (!$id || !$adresse || !$mapId)
		return "";
		
	// Gérer des id chaine ou numérique
	$iconId = 'icon_'.$id;
	if (is_string($id))
		$id = '"'.$id.'"';
	
	// Paramètres standards du marqueur (position et meta-info)
	$markerParams = '';
	
	// Ajouter le titre
	if ($params && strlen($params['titre']))
		$markerParams .= ','."\n" . '			title: "'.$params['titre'].'"';
	
	// Déterminer l'icone
	$precmd = '';
	if ($params && $params['icon'])
	{
		$file = find_in_path($params['icon'].'.gmd');
		$precmd .= gmap_ajoute_icone($iconId, $file, "map");
		$markerParams .= ','."\n" . '			icon: "'.$iconId.'"';
	}

	// Ajouter l'info-bulle
	if ($params && (strlen($params['titre']) || strlen($params['texte'])))
	{
		$html = '<div class="gmap-balloon">' . "\n";
		if (strlen($params['titre']))
			$html .= '	<h1>'.$params['titre'].'</h1>' . "\n";
		if (strlen($params['texte']))
		{
			$html .= '	<div class="contents">' . "\n";
			$html .= '		<div class="texte"><p>'.$params['texte'].'</p></div>' . "\n";
			$html .= '	</div>' . "\n";
		}
		$html .= '</div>' . "\n";
		$markerParams .= ','."\n" . '			click: "showInfoWindow"';
		$markerParams .= ','."\n" . '			html: "'.protege_html($html).'"';
	}
	
	// Construction du code
	$code = '
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function()
{
	jQuery("#gmap_cont'.$mapId.'").gmapReady(function()
	{
		var map = gMap("gmap_map'.$mapId.'");
		if (isObject(map))
		{
			map.searchGeocoder("'.$adresse.'", function(latitude, longitude)
			{
				if (latitude && longitude)
				{
			'.$precmd.'
					map.setMarker('.$id.', {
						latitude: latitude,
						longitude: longitude'.$markerParams.'
					});';
	if ($params['focus'])
		$code .= '
					gmap_setViewportOnMarkers("'.$mapId.'");';
	$code .= '
				}
			});
		}
	});
});
//]]>
</script>';
	return $code;
}

// Test des capacités d'une implémentation de carte
function gmap_teste_capability($capability)
{
	return gmap_capability($capability);
}

?>