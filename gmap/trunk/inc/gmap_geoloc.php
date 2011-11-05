<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Fonctions d'acc�s aux donn�es g�ographiques pour la partie publique
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');
include_spip('inc/gmap_db_utils');

// Rechercher en base comment doit �tre affich�e la carte d'une base
function gmap_get_object_viewport($objet, $id_objet)
{
	// Initialisation
	$vp = array();
	$vp['latitude'] = 0.0;
	$vp['longitude'] = 0.0;
	$vp['zoom'] = 3;
	
	// Rechercher les marqueurs de l'objet
	$points = gmap_get_points($objet, $id_objet);
	$centre = null;
	$count = count($points);
	if ($count == 1)
		$centre = array_shift($points);
	else if ($count > 0)
	{
		// Parcourir le tableau pour r�cup�rer le centre et le d�faut
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
			// Si les points sont assez proches, on fait une moyenne, s'il sont s�par�s de plus de 10 degr�, on prend
			// le d�faut ou le premier, ce qui �vite le probl�me entre 180 et -180 (en consid�rent que, vu que c'est
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
	
	// Renvoyer ce qu'on a trouv�
	if ($centre != null)
	{
		$vp['latitude'] = $centre['latitude'];
		$vp['longitude'] = $centre['longitude'];
		$vp['zoom'] = $centre['zoom'];
		return $vp;
	}
	
	// Si on n'a rien trouv�, remonter sur le parent
	$parents = gmap_parents($objet, $id_objet);
	if (count($parents) == 1)
		return gmap_get_object_viewport($parents[0]['objet'], $parents[0]['id_objet']);
	
	// Sinon prendre le d�faut du site
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	$interface = 'gmap_'.$api.'_interface';
	$vp['latitude'] = gmap_lire_config($interface, 'default_latitude', "0.0");
	$vp['longitude'] = gmap_lire_config($interface, 'default_longitude', "0.0");
	$vp['zoom'] = gmap_lire_config($interface, 'default_zoom', "1");
	return $vp;
}

// D�finition des param�tres de la carte au format d�fini dans gmap_<impl>_public.js
function gmap_definir_parametre_carte($objet, $id_objet, $varName = NULL, $params = NULL)
{
	// R�cup�rer le centre
	$viewport = gmap_get_object_viewport($objet, $id_objet);
	
	// Sp�cificit�s de l'API
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	$parametre_carte = charger_fonction("parametre_carte", "mapimpl/".$api."/public");

	// Retour
	$out = "";
	if ($varName)
	{
		if (strstr($varName, '.') !== FALSE)
			$out .= $varName.' = ';	
		else
			$out .= 'var '.$varName.' = ';	
	}
	$out .= $parametre_carte($viewport, $params);
	if ($varName)
		$out .= ';' . "\n";
	
	return $out;
}

// D�finition des param�tres d'une icone
// Usage :
// gmap_definir_parametre_icon(array('file'=>, 'width'=>, 'height'=>, 'xAnchor'=>, 'yAnchor'=>, 'shadowFile'=>, 'widthShadow'=>, 'heightShadow'=>, 'xShadowAnchor'=>, 'yShadowAnchor'=>, 'xOffset'=>, 'yOffset'=>),
//	array('file'=>, 'width'=>, 'height'=>, 'xAnchor'=>, 'yAnchor'=>), $varName = null)
function gmap_definir_parametre_icon($icon, $complete = null, $varName = null)
{
	// R�cup�rer la taille de l'image
	if ((!isset($icon['width']) || ($icon['width'] <= 0)) ||
		(!isset($icon['height']) || ($icon['height'] <= 0)))
	{
		$imageInfo = @getimagesize($icon['file']);
		$icon['width'] = $imageInfo[0] ? $imageInfo[0] : 32;
		$icon['height'] = $imageInfo[1] ? $imageInfo[1] : 32;
	}
	
	// Si l'ombre n'est pas pr�cis�e, on suppose que c'est l'image par d�faut
	if (!$icon['shadowFile'])
		$icon['shadowFile'] = _DIR_PLUGIN_GMAP . 'images/shadow.png';
	// R�cup�rer la taille de l'ombre
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
		heightIcon: '.$icon['height'].',';
	if ($icon['xAnchor'] != null)
		$out .= '
		anchorX: '.$icon['xAnchor'].',';
	if ($icon['yAnchor'] != null)
		$out .= '
		anchorY: '.$icon['yAnchor'].',';

	// Ombre
	$out .= '
		urlShadowFile: "'.$icon['shadowFile'].'",
		widthShadow: '.$icon['widthShadow'].',
		heightShadow: '.$icon['heightShadow'].',';
	if ($icon['xShadowAnchor'] != null)
		$out .= '
		anchorShadowX: '.$icon['xShadowAnchor'].',';
	if ($icon['yShadowAnchor'] != null)
		$out .= '
		anchorShadowY: '.$icon['yShadowAnchor'].',';
		
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
		$out .= '
		urlCompleteFile: "'.$complete['file'].'",
		widthComplete: '.$complete['width'].',
		heightComplete: '.$complete['height'].',';
		if ($complete['xAnchor'] != null)
			$out .= '
		anchorCompleteX: '.$complete['xAnchor'].',';
		if ($complete['yAnchor'] != null)
			$out .= '
		anchorCompleteY: '.$complete['yAnchor'].',';
	}
	
	// Offset pour l'info bulle (un seul, sur l'icone normale)
	if ($icon['xOffset'] != null)
		$out .= '
		popupOffsetX: '.$icon['xOffset'].',';
	if ($icon['yOffset'] != null)
		$out .= '
		popupOffsetY: '.$icon['yOffset'].',';
		
	$out .= '
	}';
	if ($varName)
		$out .= ';' . "\n";
	return $out;
}

// R�cup�rer un fichier de d�finition pour un triplet (objet, id_objet, type_point)
function _gmap_find_file($prefix, &$name, $ext, &$filename, $ids_rubrique = NULL)
{
	$file = FALSE;
	if (is_array($ids_rubrique) && count($ids_rubrique))
	{
		$rubs = count($ids_rubrique);
		$base = $name;
		$name = $base.'='.$ids_rubrique[0];
		if ($file = _gmap_find_file($prefix, $name, $ext, &$filename))
			return $file;
		for ($index = 0; $index < $rubs; $index++)
		{
			$name = $base.'-'.$ids_rubrique[$index];
			if ($file = _gmap_find_file($prefix, $name, $ext, &$filename))
				return $file;
		}
		$name = $base;
	}
	$filename = $prefix.$name;
	if ($file = find_in_path('modeles/'.$filename.'.'.$ext))
		return $file;
	if ($file = find_in_path($filename.'.'.$ext))
		return $file;
	return FALSE;
}
function gmap_trouve_def_file($objet, $id_objet, $type_point, $prefix, $ext, $buffer = null)
{
	$result = NULL;
	$file = FALSE;
	$fileSpip = NULL;

	// Rechercher d'abord s'il y a un fichier sp�cifique
	$name = 'spe-'.$objet.'-'.$id_objet;
	$file = _gmap_find_file($prefix, $name, $ext, $fileSpip);
	if ($file !== FALSE)
	{
		// Ne pas ajouter dans le buffer : �a n'a pas de sens pour une icone sp�cifique
		$result = array(
			'name'=>'spe-'.$objet.'-'.$id_objet,
			'spip-path'=>$fileSpip,
			'file'=>$file
		);
		return $result;
	}

	// On a aussi besoin de la rubrique
	if ($objet === "rubrique")
		$id_rubrique = $id_objet;
	else
		$id_rubrique = gmap_get_rubrique($objet, $id_objet);
	
	// Rechercher dans le buffer
	if (($file === FALSE) && ($buffer != NULL) &&
		($resultInBuffer = $buffer[$objet.'-'.$type_point.'-'.$id_rubrique]))
	{
		// On renvoie seulement le nom trouv�, l'icone est d�j� cr��e
		$result = array('name'=>$resultInBuffer);
		return $result;
	}

	// R�cup�rer les rubriques situ�es au dessus
	$ids_rubrique = NULL;
	if ($id_rubrique)
	{
		$ids_rubrique = array();
		$ids_rubrique[] = $id_rubrique;
		$rub = $id_rubrique;
		while ($rub = gmap_get_rubrique('rubrique', $rub))
			$ids_rubrique[] = $rub;
	}

	// Recherche le marqueur selon le type
	$name = $objet.'-'.$type_point;
	if (!$type_point || (strlen($type_point) == 0) ||
		($file = _gmap_find_file($prefix, $name, $ext, $fileSpip, $ids_rubrique)) === FALSE)
	{
		// sinon, rechercher seulement avec l'objet
		$name = $objet;
		if (($file = _gmap_find_file($prefix, $name, $ext, $fileSpip, $ids_rubrique)) === FALSE)
		{
			// et avec le type
			$name = $type_point;
			if (!$type_point || (strlen($type_point) == 0) ||
				(($file = _gmap_find_file($prefix, $name, $ext, $fileSpip, $ids_rubrique)) === FALSE))
			{
				// fichier par defaut de la partie publique
				$name = 'default';
				$file = _gmap_find_file($prefix, $name, $ext, $fileSpip, $ids_rubrique);
			}
		}
	}
	
	// Ajouter l'icone dans le buffer pour �viter de la chercher � nouveau
	if ($buffer)
		$buffer[$objet.'-'.$type_point.'-'.$id_rubrique] = $name;
	
	$result = array();
	$result['name'] = $name;
	$result['spip-path'] = $fileSpip;
	if ($file && (strlen($file) > 0))
		$result['file'] = $file;
	return $result;
}

// D�coder le fichier d'ic�ne pour en recup�rer toute l'information
function gmap_init_icon_def()
{
	$icon = array();
	// Ne pas mettre de valeurs par d�faut : si ce n'est pas fix� il n'y a rien...
}
class gmap_icon_def_file_parser
{					   
	var $_file;			// Cha�ne de description, au format XML
	var $_errorString;	// Cha�ne d'erreur
	var $_bIsValid;		// Indique que le tag "markers" a �t� trouv�
	var $_icons;		// Tableau des icones trouv�es "normal" et "selected"
	var $_currentIcon;	// Indice de l'icone en cours de traitement
	var $_currentTag;	// Tag en cours de lecture
	var $_insideData;	// Indique que des donn�es ont d�j� �t� trouv�es
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
		
		// Si la requ�te existe, la d�coder
		$isError = FALSE;
		if (strlen($this->_file) > 0)
		{
			// Cr�er un parseur XML pour d�coder la cha�ne de requ�te
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

			// Lib�rer le parseur XML
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
		
		// D�finition d'une icone
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
	
	// Fonctions d'acc�s
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

// Fonctions permettant de r�cup�rer l'info d'une liste d'icones
function gmap_get_icon($icons, $bSelected = FALSE, $bComplete = FALSE)
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
			return array('file'=>find_in_path($iconComplete['image']),
							'width'=>$iconComplete['cxSize'], 'height'=>$iconComplete['cySize'],
							'xAnchor'=>$iconComplete['xAnchor'], 'yAnchor'=>$iconComplete['yAnchor']);
		else if ($iconSimple)
			return array('file'=>find_in_path($iconSimple['image']),
							'width'=>$iconSimple['cxSize'], 'height'=>$iconSimple['cySize'],
							'xAnchor'=>$iconSimple['xAnchor'], 'yAnchor'=>$iconSimple['yAnchor']);
		else
			return null;
	}
	else
	{
		if ($iconSimple && $iconShadow)
			return array('file'=>find_in_path($iconSimple['image']), 'width'=>$iconSimple['cxSize'], 'height'=>$iconSimple['cySize'], 'xAnchor'=>$iconSimple['xAnchor'], 'yAnchor'=>$iconSimple['yAnchor'],
						'shadowFile'=>($iconShadow ? find_in_path($iconShadow['image']) : ""), 'widthShadow'=>$iconShadow['cxSize'], 'heightShadow'=>$iconShadow['cySize'], 'xShadowAnchor'=>$iconShadow['xAnchor'], 'yShadowAnchor'=>$iconShadow['yAnchor'],
						'xOffset'=>$iconSimple['xOffset'], 'yOffset'=>$iconSimple['yOffset']);
		else if ($iconComplete)
			return array('file'=>find_in_path($iconComplete['image']), 'xAnchor'=>$iconComplete['xAnchor'], 'yAnchor'=>$iconComplete['yAnchor'],
						'xOffset'=>$iconComplete['xOffset'], 'yOffset'=>$iconComplete['yOffset']);
		else if ($iconSimple)
			return array('file'=>find_in_path($iconSimple['image']), 'width'=>$iconSimple['cxSize'], 'height'=>$iconSimple['cySize'], 'xAnchor'=>$iconSimple['xAnchor'], 'yAnchor'=>$iconSimple['yAnchor'],
						'xOffset'=>$iconSimple['xOffset'], 'yOffset'=>$iconSimple['yOffset']);
		else
			return null;
	}
}

// R�cup�rer tous les param�tres d'une icone et la cr�er
function gmap_ajoute_icone($name, $defFile, $map)
{
	// R�cup�rer la d�finition de l'icone
	$icons = gmap_parse_icone_def_file($defFile);
	$icon = gmap_get_icon($icons, FALSE, FALSE);
	$selected = gmap_get_icon($icons, TRUE, FALSE);
	if (!$icon)
		return "";

	// Ajout des icones compl�tes
	$complete = gmap_get_icon($icons, FALSE, TRUE);
	$completeSelected = gmap_get_icon($icons, TRUE, TRUE);
	if (!$complete)
		$complete = array(0=>NULL, NULL, NULL);
	if (!$completeSelected)
		$completeSelected = $complete;
		
	// Cr�er les icones
	$cmd = "";
	$cmd .= '	'.$map.'.setIcon("'.$name.'", '.gmap_definir_parametre_icon($icon, $complete).');' . "\n";
	if ($selected)
		$cmd .= '	'.$map.'.setIcon("'.$name.'_sel", '.gmap_definir_parametre_icon($selected, $completeSelected).');' . "\n";
	else
		$cmd .= '	'.$map.'.setIcon("'.$name.'_sel", '.gmap_definir_parametre_icon($icon, $completeSelected).');' . "\n";
	
	return $cmd;
}

// Prot�ger le texte de la bulle pour pouvoir le mettre dans un script 
function _gmap_protege_html($html)
{
	$html = str_replace(array("\r\n", "\r", "\n"), array("", "", ""), $html);
	return addslashes($html);
}

// R�cup�ration de l'int�rieur d'un bulle
function gmap_get_object_info_contents($objet, $id_objet, $marker_type)
{
	$fond = gmap_trouve_def_file($objet, $id_objet, $marker_type, 'gmap-info-', 'html');
	if (!$fond)
		return "";
	$fullhtml = recuperer_fond($fond['spip-path'], array('objet'=>$objet, 'id_objet'=>$id_objet, 'type_point'=>$marker_type, 'id_'.$objet=>$id_objet));
	if (!$fullhtml || !strlen($fullhtml))
		return "";
	$fullhtml = str_replace(array("\r\n", "\r", "\n"), array("", "", ""), $fullhtml);
	if (preg_match('/<body(.*)>(.*)<\/body>/Ui', $fullhtml, $matches) === 1)
		return addslashes($matches[2]);
	else
		return addslashes($fullhtml);
}

// Utilitaire pour ajouter un marqueur
function gmap_ajoute_marqueur($marker, &$iconsBuffer, $map)
{
	$precmd = '';
	
	// Param�tres standards du marqueur (position et meta-info)
	$markerParams = '';
	$markerParams .= '			latitude: '.$marker['latitude'];
	$markerParams .= ','."\n" . '			longitude: '.$marker['longitude'];
	$markerParams .= ','."\n" . '			owner_objet: "'.$marker['objet'].'"';
	$markerParams .= ','."\n" . '			owner_id_objet: '.$marker['id_objet'];
	$markerParams .= ','."\n" . '			marker_zoom: '.$marker['zoom'];
	$markerParams .= ','."\n" . '			marker_type: "'.$marker['type'].'"';
	
	// D�terminer le titre
	$titre = gmap_marqueur_titre($marker['objet'], $marker['id_objet']);
	if (strlen($titre))
		$markerParams .= ','."\n" . '			title: "'.$titre.'"';
	
	// D�terminer l'icone
	if ($icon = gmap_trouve_def_file($marker['objet'], $marker['id_objet'], $marker['type'], 'gmap-marker-', 'gmd', $iconsBuffer))
	{
		if (isset($icon['name']))
		{
			if (isset($icon['file']) && ($icon['name'] != "default"))
				$precmd .= gmap_ajoute_icone($icon['name'], $icon['file'], $map);
			$markerParams .= ','."\n" . '			icon: "'.$icon['name'].'"';
			$markerParams .= ','."\n" . '			icon_sel: "'.$icon['name'].'_sel"';
		}
	}

	// Ajouter l'info-bulle
	if (($bulle = gmap_get_object_info_contents($marker['objet'], $marker['id_objet'], $marker['type'])) && strlen($bulle))
	{
		$markerParams .= ','."\n" . '			click: "showInfoWindow"';
		$markerParams .= ','."\n" . '			html: "'.$bulle.'"';
	}
	
	$cmd = '	'.$map.'.setMarker('.$marker['id'].', {'."\n".$markerParams."\n".'		});' . "\n";
	return $precmd.$cmd;
}

// Ajout des marqueurs sur la carte
function gmap_ajoute_markers($table, $id, $mapId, $params)
{
	$map = "";

	// Initialisation du tableau des icones d�j� cr��es
	$iconsBuffer = array();
	
	// Marqueurs locaux, sur l'objet
	if ($params['markers'] === "local")
	{
		$map .= '
	// Chargement du/des marqueur(s) de l\'objet
';
		$markers = gmap_get_tree_points($table, $id);
		foreach ($markers as $idxMarker => $marker)
			$map .= gmap_ajoute_marqueur($marker, $iconsBuffer, "map");
	}
	
	// Marqueurs sur l'objet et ses descendants imm�diats
	else if ($params['markers'] === "childs")
	{
		$map .= '
	// Chargement des marqueurs des descendants
';
		$markers = gmap_get_tree_points($table, $id, 1);
		foreach ($markers as $idxMarker => $marker)
			$map .= gmap_ajoute_marqueur($marker, $iconsBuffer, "map");
	}
	
	// Marqueurs sur l'objet et ses descendants
	else if ($params['markers'] === "recursive")
	{
		$map .= '
	// Chargement des marqueurs des descendants
';
		$markers = gmap_get_tree_points($table, $id, 99);
		foreach ($markers as $idxMarker => $marker)
			$map .= gmap_ajoute_marqueur($marker, $iconsBuffer, "map");
	}
	
	// Requ�te ajax
	else if ($params['markers'] === "query")
	{
		if (isset($params['query']))
			$queryFile = $params['query'];
		else
		{
			$queryDefault = gmap_trouve_def_file($table, $id, '', 'gmap-query-', 'html');
			if (!$queryDefault)
				$queryFile = "gmap-query-default";
			else
				$queryFile = $queryDefault['spip-path'];
		}
		$queryParams = "id_".$table."=".$id."&prefix=gmm"; // gmm est n�cessaire pour le script qui d�code les icones...
		if ($queryUrl = generer_url_public($queryFile, $queryParams, true))
			$map .= '
	// Chargement des marqueurs par requ�te ajax
	bWaitToBeDeleted = false;
	jQuery.ajax({
		url: "'.$queryUrl.'",
		type: "GET",
		dataType: "xml",
		success: function(xmlDoc, status, request) {
			gmap_handleXMLMarkers(map, xmlDoc);
		},
		error: function() {
			//alert("Query failed");
		},
		complete: function() {
			jQuery("#gmap_attente'.$mapId.'").remove();
		}
	});
';
	}
	
	return $map;
}

// Ajout de la carte clicable
function gmap_ajoute_carte_public($table, $id, $mapId, $params)
{
	$map = "";

	// Ajouter un DIV qui va recevoir la carte
	$map .= '<div id="gmap_cont'.$mapId.'" class="carte_gmap"></div>' . "\n";
	
	// Script associ�
	$map .= '<script type="text/javascript">'."\n".'//<![CDATA['."\n";
	$map .= '
// Chargement de la carte et mise en place des gestionnaire d\'�v�nement
function loadCartePublic'.$mapId.'()
{
	// D\'abord afficher le masque d\'attente
	var parent = jQuery("#gmap_cont'.$mapId.'").parent();
	parent.addClass("carte_gmap_container");
	var height = parent.innerHeight();
	var waitBlock = \'<div id="gmap_attente'.$mapId.'" class="map_wait_mask" style="width: 100%; height: \'+height+\'px;"><span class="map_wait_logo" style="width: 100%; height: 100%;"></span></div>\';
	jQuery("#gmap_cont'.$mapId.'").after(waitBlock); 
	var bWaitToBeDeleted = true;
	
	// R�cup�rer la carte
	var map = MapWrapper.getMap("gmap_map'.$mapId.'", true);
	if (!isObject(map))
		return false;

	// Avant de cr�er la carte, redimensionner le conteneur � la taille du parent
	jQuery("#gmap_cont'.$mapId.'").css("width", "100%");
	jQuery("#gmap_cont'.$mapId.'").css("height", height+"px");
	
	// Chargement de la carte
	';
	// Lecture des param�tres de la carte
	$map .= gmap_definir_parametre_carte($table, $id, 'mapParams'.$mapId, $params);
	$map .= '
	mapParams'.$mapId.'.handleResize = true;
	if (!map.load("gmap_cont'.$mapId.'", mapParams'.$mapId.'))
	{
		if (bWaitToBeDeleted)
			jQuery("#gmap_attente'.$mapId.'").remove();
		return false;
	}
';

	// Ajout du chargement des marqueurs
	if (isset($params['markers']))
		$map .= gmap_ajoute_markers($table, $id, $mapId, $params);
	
	$map .= '
	if (bWaitToBeDeleted)
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

	return $map;
}

// Ajout d'un fichier KML
function gmap_ajoute_kml_url($id, $url, $mapId, $show = true)
{
	// Tests de validit�
	if (!$id || !strlen($url) || !$mapId)
		return "";
		
	// D�but du code
	$code = '<script type="text/javascript">'."\n".'//<![CDATA[';
	
	// G�rer des id chaine ou num�rique
	if (is_string($id))
		$id = '"'.$id.'"';
	
	// Ajouter un handler d'�v�nement sur le chargement de la carte
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

	// Code renvoy�
	$code .= '//]]>'."\n".'</script>'."\n";
	return $code;
}
function gmap_ajoute_kml($id_document, $mapId, $show = true)
{
	// Tests de validit�
	if (!$id_document || !$mapId)
		return "";
		
	// R�cup�rer l'url du document
	include_spip('inc/documents'); // pour 'get_spip_doc'
	include_spip('inc/filtres'); // pour 'url_absolue'
	if (!($doc = sql_fetsel('fichier', 'spip_documents', 'id_document='.$id_document)))
		return "";
	if (!($url = url_absolue(get_spip_doc($doc['fichier']))))
		return "";
	
	// Passer par la fonction d'ajout par url
	return gmap_ajoute_kml_url($id_document, $url, $mapId, $show);
}

// Ajout manuel d'un marqueur provenant d'un objet SPIP g�olocalis�
// Cette fonction est faite pour �tre appel�e depuis la balise GEOMARKER pour ajouter un
// marqueur qui n'est pas normalement renvoy� par la balise GEOMAP.
function gmap_ajoute_marqueur_site($objet, $id_objet, $mapId, $type)
{
	// Tests de validit�
	if (!$mapId)
		return "";
	
	// R�cup�rer un point visible et le mieux plac� s'il y en a plusieurs (selon la colonne priorite des types)
	$point = gmap_get_point($objet, $id_objet, $type);
	if (!$point)
		return "";
		
	// Construire le code du marqueur
	$point['objet'] = $objet;
	$point['id_objet'] = $id_objet;
	$buffer = array();
	$codeMarker = gmap_ajoute_marqueur($point, $buffer, "map");
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

// Ajout d'un marqueur special, c'est-�-dire qui ne provient pas d'un objet SPIP g�olocalis�
// (on passe donc en param�tre toutes les informations n�cessaires)
function gmap_ajoute_marqueur_special($id, $latitude, $longitude, $mapId, $titre = null, $texte = null, $iconFile = null)
{
	// Tests de validit�
	if (!$id || !$latitude || !$longitude || !$mapId)
		return "";
		
	// G�rer des id chaine ou num�rique
	$iconId = 'icon_'.$id;
	if (is_string($id))
		$id = '"'.$id.'"';
	
	// Param�tres standards du marqueur (position et meta-info)
	$markerParams = '';
	$markerParams .= '			latitude: '.$latitude;
	$markerParams .= ','."\n" . '			longitude: '.$longitude;
	
	// Ajouter le titre
	if (strlen($titre))
		$markerParams .= ','."\n" . '			title: "'.$titre.'"';
	
	// D�terminer l'icone
	$precmd = '';
	if ($iconFile)
	{
		$file = find_in_path($iconFile.'.gmd');
		$precmd .= gmap_ajoute_icone($iconId, $file, "map");
		$markerParams .= ','."\n" . '			icon: "'.$iconId.'"';
	}

	// Ajouter l'info-bulle
	if (strlen($titre) || strlen($texte))
	{
		$html = '<div class="gmap-balloon">' . "\n";
		if (strlen($titre))
			$html .= '	<h1>'.$titre.'</h1>' . "\n";
		if (strlen($texte))
		{
			$html .= '	<div class="contents">' . "\n";
			$html .= '		<div class="texte"><p>'.$texte.'</p></div>' . "\n";
			$html .= '	</div>' . "\n";
			$html .= '</div>' . "\n";
		}
		$markerParams .= ','."\n" . '			click: "showInfoWindow"';
		$markerParams .= ','."\n" . '			html: "'._gmap_protege_html($html).'"';
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
			});
		}
	});
});
//]]>
</script>';
	return $code;
}

// Test des capacit�s d'une impl�mentation de carte
function gmap_teste_capability($capability)
{
	return gmap_capability($capability);
}

?>