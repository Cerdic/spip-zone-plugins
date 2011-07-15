<?php
/**
* Plugin SPIP-Mashup
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2011
* Logiciel distribue sous licence GNU/GPL.
*
* Fonctions du mashup
*
**/

/** function de filtrage des textes pour inclusion dans un json 
	pas de retour chariot et addslashes pour inclusion dans une string.
*/
function texte_mashup ($s)
{	$s = str_replace (array("\n","\r"),' ',$s);
	$s = addslashes($s);
	return $s;
}

/** Repasser en url page pour un decodage des urls par le client
*/
function mashup_url_page ($s)
{	$GLOBALS['type_urls'] = 'page';
	if ($GLOBALS['meta']['type_urls']) $GLOBALS['meta']['type_urls'] = 'page';
	return '';
}

/** Affichage de la config du mashup
*/
function mashup_getConfig()
{	$options = $GLOBALS['meta']['spip_mashup'];
	
	if ($options) $options = unserialize($options);
	else $options = array( "backLayer"=>1, "zoom_pere"=>1, "zoom_img"=>1, "no_popup"=>1, 
					"largeur"=>25, "largeur_mot"=>10, "bord"=>10, "bord_couleur"=>'#FFFFFF');
	
	return $options;
}

/** Affichage de la config du mashup
*/
function mashup_config($s)
{	$options = mashup_getConfig();
	return 
		"mashup.options= {"
		." largeur : ".($options['largeur']?$options['largeur']:25) .', '
		." largeur_mot : ".($options['largeur_mot']?$options['largeur_mot']:10) .', '
		." backLayer : ".($options['backLayer']?'true':'false') .', '
		." zoom_pere : ".($options['zoom_pere']?'true':'false') .', '
		." zoom_img : ".($options['zoom_img']?'true':'false') .', '
		." no_popup : ".($options['no_popup']?'true':'false') .'} ';
}

// Affichage d'une image avec un cadre et une icone sur le bord
function image_mashup($im, $masque, $bord, $background_color)
{	if (!function_exists(image_valeurs_trans)) include_spip ("inc/filtres_images_compat"); 
	
	// Valeurs de l'image par SPIP
	$image = image_valeurs_trans($im, "mashup-$masque-$bord-$background_color");
	
	if (!$image) return("");
	
	$dest = $image["fichier_dest"];
	
	//if ($image["creer"])
	{	$l = $image["largeur"]+ 2*$bord;
		$h = $image["hauteur"]+ 2*$bord;
		$im = $image["fichier"];

		// images sources et destination
		$im = $image["fonction_imagecreatefrom"]($im);
		$im_ = @imagecreatetruecolor($l, $h);

		// options de transparence
		imagealphablending($im_, false);
		imagesavealpha($im_, true);
		
		// Remplir avec la couleur 
		if ($background_color=='transparent')
			$color_t = imagecolorallocatealpha( $im_, 255, 255, 255 , 127 );
		else 
		{	$bg = _couleur_hex_to_dec($background_color);
			$color_t = imagecolorallocate( $im_, $bg['red'], $bg['green'], $bg['blue']);
		}
		imagefill ($im_, 0, 0, $color_t);
		
		// Copier l'image source et le masque
		imagealphablending($im_, true);
		imagecopy($im_, $im, $bord, $bord, 0, 0, $image["largeur"], $image["hauteur"]);
		
		// Gestion du masque
		$im_masque = @imagecreatefrompng($masque);
		if ($im_masque)
		{	// Position du masque
			$x_masque = imagesx($im_) - imagesx($im_masque);	// left : $x_masque = 0;
			$y_masque = 0;		// bottom : $y_masque = imagesy($im_) - imagesy($im_masque);
			imagecopy($im_, $im_masque, $x_masque, $y_masque, 0, 0, imagesx($im_masque), imagesy($im_masque));
			// Liberer la memoire
			imagedestroy($im_masque);
		}
		
		// SPIP
		$image["fonction_image"]($im_, "$dest");	
	}

	// SPIP
	return image_ecrire_tag($image,array('src'=>$dest));
}

/** Calcul d'un logo pour affichage sur la carte (necessite GD2).
	Pour les fichiers JPG : extraction d'un logo carre avec bordure.
	Pour les autres extension, renvoie le fichier reduit.
*/
function icon_mashup ($s, $masque, $l=100, $bord=null, $couleur=null)
{	// Options
	$options = mashup_getConfig();
	if (!$bord) $bord = $options['bord'];
	if (!$couleur) $couleur = $options['bord_couleur'];
	//
	$logo = filtrer ('image_passe_partout', $s, $l, $l);
	$src = extraire_attribut($logo,'src');
	$ext = substr ($src, -3);
	if ($ext == 'jpg') 
	{	$logo = filtrer ('image_recadre', $logo, $l, $l, 'center');
		$logo = image_mashup ($logo, $masque, $bord, $couleur);
	}
	return $logo;
}

/** Extraction des informations du mashup */
function mashup_extraire ($p, $deb, $fin='|')
{	$n = strpos ($p,$deb);
	if ($n === FALSE) return "";
	$p = substr ($p, $n+strlen($deb));
	$n = strpos ($p,$fin);
	if ($n>0) $p = substr ($p, 0, $n);
	return $p;
}

/** Decodage des parametres dans le raccourcis mashup */
function mashup_param($p, $what, $val="")
{	$p = mashup_extraire ($p,'<mashup|', '>');
	$p = mashup_extraire ($p,"$what=");
	if (!$p) return $val;
	return $p;
}

/** Gestion des mashups :
	- renvoie la page mashup si la rubrique a un raccourcis <mashup|carte> + localisation
	- renvoie sur la page mashup_objet pour affichage dans un dialogue (si ajax_mashup==1)
*/
function spip_mashup_styliser($flux)
{	// On est dans une rubrique
	if (($fond = $flux['args']['fond'])
	 AND $flux['args']['ext']="html")
	{	// On est dans un mashup => renvoyer en AJAX
		if ($flux['args']['contexte']['ajax_mashup'])
		{	// Recherche du squelette
			$base = find_in_path("mashup_$fond.html");
			$squelette = substr($base, 0, - 5);
			$flux['data'] = $squelette;
			// $flux['data'] = _DIR_PLUGIN_SPIP_MASHUP."mashup_".$fond;
		}
		// Cas des rubriques georeferencees avec un raccourcis <mashup|carte>
		else if ($fond == 'rubrique')
		{	// Georeferencement ?
			$row = sql_fetsel("id_geoposition", "spip_geopositions", "objet='rubrique' AND id_objet=".$flux['args']['id_rubrique']);
			// Si article ou rubrique avec un tag mashup
			if ($row)
			{	$row = sql_fetsel("descriptif", "spip_rubriques", "id_rubrique=".$flux['args']['id_rubrique']);
				$n = strpos($row['descriptif'],"<mashup|carte");
				if ($n!==FALSE) 
				{	// Recherche du squelette
					$base = find_in_path("mashup.html");
					$squelette = substr($base, 0, - 5);
					$flux['data'] = $squelette;
					// $flux['data'] = _DIR_PLUGIN_SPIP_MASHUP."mashup";
				}
			}
		}
	}
	return $flux;
}

/** Affichage d'un lien sur la page de config du plugin SPIP-geoportail */
function spip_mashup_affiche_droite($flux)
{	// Ajouter une configuration dans le menu droite du plugin SPIP-geoportail
	if ($flux['args']['exec'] == 'geoportail_config' && autoriser('configurer', 'plugins'))
	{	$rac = debut_cadre_enfonce('',true)
			.icone_horizontale(_T('spip_mashup:spip_mashup'), generer_url_ecrire("spip_mashup_config"), _DIR_PLUGIN_SPIP_MASHUP."img/mashup-24.png","rien.gif", false)
			.fin_cadre_enfonce(true);
		$flux['data'] .= $rac;
	}
	return $flux;
}

?>