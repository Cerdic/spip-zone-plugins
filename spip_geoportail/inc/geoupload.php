<?php
include_spip('base/db_mysql');
/**
* Plugin SPIP Geoportail
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2010
* Logiciel distribue sous licence GNU/GPL.
*
**/
/** API pour la lecture des coordonnees dans un fichier.
	Il faut definir une fonction geoportail_get_coord_xxx 
	pour le type de fichier xxx qui renvoit lon et lat lue dans le fichier
*/
function geoportail_get_coord ($fichier, $type, &$lon, &$lat)
{	$f = 'geoportail_get_coord_'.$type;
	$lon = $lat = null;
	if (function_exists($f)) return $f($fichier, $lon, $lat);
	return false;
}

/** Lecture des coordonnees GPX
*/
function geoportail_get_coord_gpx ($dest, &$lon, &$lat) 
{	// Lire une lon et lat dans le fichier
	if ($TabFich = file($dest)) 
	{	for($i = 0; $i < count($TabFich); $i++) 
		{	// Ne pas prendre la boite...
			$p = explode ("minlat", $TabFich[$i]);
			if (!$p[1])
			{	// Latitude
				$p = explode ("lat=\"", $TabFich[$i]);
				if ($p[1]) 
				{	$lat = explode ("\"", $p[1]);
					$lat = $lat[0];
				}
				// Longitude
				$p = explode ("lon=\"", $TabFich[$i]);
				if ($p[1]) 
				{	$lon = explode ("\"", $p[1]);
					$lon = $lon[0];
				}
				// OK ?
				if ($lon && $lat) return true;
			}
		}
	}
	return false;
}

/** Lecture des coordonnees KML
*/
function geoportail_get_coord_kml($dest, &$lon, &$lat) 
{	// Lire une lon et lat dans le fichier
	if ($TabFich = file($dest)) 
	{	for($i = 0; $i < count($TabFich); $i++) 
		{	$p = explode ("<coordinates>", $TabFich[$i]);
			if ($p[1]) 
			{	$p = $p[1];
				// Si on est sur plusieurs lignes
				for ($j=$i+1; $j < count($TabFich) && $j < $i+4; $j++)
				{	$p .= $TabFich[$j];
				}
				$p = str_replace ("\p","",$p);
				$p = str_replace ("\r","",$p);
				$p = explode (",", $p);
				if ($p[1])
				{	$lon = $p[0];
					$lat = $p[1];
					return true;
				}
			} 
		}
	}
	return false;
}

/** Lecture des informations GPS dans les images
*/
function geoportail_lire_exif($img, $type) //, &$lon, &$lat) 
{	// Bibliotheque pas installee
  if (!function_exists("exif_read_data")) return false;
  // Rechercher dans le fichier...
  if ($type=='JPG' || $type=='TIFF')
	{	$lon = $lat = null;
		// Lecture des informations EXIF
		$date = 0;
		$exif = @exif_read_data($img, 'FILE', true);
		if($exif) 
		{	// BUG readexif : decalage bug/EXIF.UndefinedTag:0xA500
			if ($exif['EXIF']['UndefinedTag:0xA500'] && !is_array($exif['EXIF']['UndefinedTag:0xA500'])) 
			{	$tag = implode('/',$exif['GPS']['GPSLatitude'])
						.'/'.implode('/',$exif['GPS']['GPSLongitude'])
						.'/'.$exif['GPS']['GPSAltitude']
						.'/'.implode('/',$exif['GPS']['GPSTimeStamp']);
				$tag = explode('/',$tag);
				$exif['GPS']['GPSLatitude'] = array($tag[3].'/'.$tag[4],$tag[5].'/'.$tag[6],$tag[7].'/'.$tag[8]);
				$exif['GPS']['GPSLongitude'] = array($tag[9].'/'.$tag[10],$tag[11].'/'.$tag[12],$tag[13].'/'.$tag[14]);
			}
			/* Test exif data
			foreach ($exif as $key => $section) 
			{	foreach ($section as $name => $val) 
				{	if (is_array($val)) echo "$key.$name: ".implode(' ',$val)."<br />\n";
					else echo "$key.$name: $val<br />\n";
				}
			}
			*/

			// Coordonnees GPS
			$value = $exif['GPS']['GPSLongitude'];
			if ($value) eval("\$lon = ".$value[0]." + ".$value[1]."/60 + ".$value[2]."/3600;"); 
			else return false;
			$value = $exif['GPS']['GPSLatitude'];
			if ($value) eval("\$lat = ".$value[0]." + ".$value[1]."/60 + ".$value[2]."/3600;"); 
			else return false;
			$flon = ($exif['GPS']['GPSLongitudeRef']=='E')? 1 : -1; 
			$flat = ($exif['GPS']['GPSLatitudeRef']=='N')? 1 : -1;
			// Nord - Sud - Est - Ouest
			$lon *= $flon;
			$lat *= $flat;
			// Renvoyer
			return array ( 'lon'=>$lon, 'lat'=>$lat, 'date'=>$exif['EXIF']['DateTimeDigitized'] );
		} 
	}
	return false;
}

// API pour la lecture des coordonnees dans un fichier
function geoportail_get_coord_jpg($dest, &$lon, &$lat) 
{	$exif = geoportail_lire_exif ($dest, 'JPG');
	if ($exif)
	{	$lon = $exif['lon']; 
		$lat = $exif['lat']; 
		return true;
	}
	else return false;
}
function geoportail_get_coord_tif($dest, &$lon, &$lat) 
{	$exif = geoportail_lire_exif ($dest, 'TIFF');
	if ($exif)
	{	$lon = $exif['lon']; 
		$lat = $exif['lat']; 
		return true;
	}
	else return false;
}


/** Recherche de la commune associee a des coord
*/
function geoportail_chercher_adm($lon, $lat, &$adm) 
{
	if ($lon && $lat)
	{	// Chercher la commune correspondante
		$delta = 0.1;
		$dmin = 100;
		while(true)
		{	$lon1 = $lon-$delta;
			$lon2 = $lon+$delta;
			$lat1 = $lat-$delta;
			$lat2 = $lat+$delta;
			$res = spip_query("SELECT * FROM spip_georgc WHERE feature_class>'0' AND lon>".$lon1." AND lon<".$lon2." AND lat>".$lat1." AND lat<".$lat2);
			// La plus proche...
			while ($row=spip_fetch_array($res))
			{	// Coord en radian
				$lon1 = $row['lon']*pi()/180;
				$lat1 = $row['lat']*pi()/180;
				$lon2 = $lon *pi()/180;
				$lat2 = $lat *pi()/180;
				// Longueur sur le grand cercle (en km)
				$d = 2 * 6367 * asin( sqrt ( 
									pow( sin(($lat1-$lat2)/2),2 )
									+ ( cos($lat1) * cos($lat2) * pow(sin(($lon1-$lon2)/2),2) )
							));
				/* Risques d'arrondis ?
				$d = 6367 * acos( sin($lat1)*sin($lat2) + cos($lat1)*cos($lat2)*cos($lon1-$lon2) );
				*/
				if ($d<$dmin)
				{	$dmin = $d;
					$com = $row;
				}
			}
			// On a trouve !
			if ($dmin < 100) break;
			// Elargir la recherche
			$delta *= 2;
			// Trop loins !
			if ($delta > 0.6) break;
		}
		// Trouve
		if ($dmin < 100)
		{	$adm = $com;
			// No INSEE
			$adm['insee'] = $com['id_dep'].$com['id_com'];
			// Nom du departement
			include_spip ('public/geoportail_boucles');
			$adm['departement'] = geoportail_departement($com['id_dep']);
			// Distance (en km)
			$adm['dist'] = $dmin;
			return true;
		}
		else return false;
	}
	return false;
}


?>
