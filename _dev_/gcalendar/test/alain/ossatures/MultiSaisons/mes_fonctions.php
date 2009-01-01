<?php


/*
 *   +----------------------------------+
 *    Nom du Filtre :    pour plus de stats                                        
 *   +----------------------------------+
 *  . SCOTY .. 
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     affiche le texte à citer    
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.koakidi.com/spip.php?article279
*/

// balise #TOTAL_VISITES

function aff_total_visites() {
	$query = "SELECT SUM(visites) AS total_absolu FROM spip_visites";
	$result = spip_query($query);
	if ($row = spip_fetch_array($result))
		{ return $row['total_absolu']; }
	else { return "0";}
}

function balise_TOTAL_VISITES($p) {
	$p->code = "aff_total_visites()";
	$p->statut = 'php';
	return $p;
}

function visites_du_jour() {
	$q = spip_query("SELECT visites FROM spip_visites WHERE date=NOW()");
	if ($r = @spip_fetch_array($q))
		$g = $r['visites'];
	else
		$g = 0;
			
	return $g;
}
function balise_VISITES_JOUR($p) {
	$p->code = "visites_du_jour()";
	$p->interdire_scripts = false;
	return $p;
}

function generer_jour_val_max_visites($arg) {
	$qv = spip_query("SELECT MAX(visites) as maxvi FROM spip_visites");
	$rv = spip_fetch_array($qv);
	$valmaxi = $rv['maxvi'];

	if($arg=="date") {
		$qd = spip_query("SELECT date FROM spip_visites WHERE visites = $valmaxi");
		$rd = spip_fetch_array($qd);
		$jourmaxi = $rd['date'];
	}
	if($arg=="date") { $a = $jourmaxi; }
	if($arg=="val") { $a = $valmaxi; }
	return $a;
}
function balise_JOUR_MAX_VISITES($p) {
	$arg="date";
	$p->code = "generer_jour_val_max_visites($arg)";
	$p->interdire_scripts = false;
	return $p;
}
function balise_VAL_MAX_VISITES($p) {
	$arg="val";
	$p->code = "generer_jour_val_max_visites($arg)";
	$p->interdire_scripts = false;
	return $p;
}

function aff_moyenne_visites() {
	$query="SELECT UNIX_TIMESTAMP(date) AS date_unix, visites FROM spip_visites ".
			"WHERE 1 AND date > DATE_SUB(NOW(),INTERVAL 420 DAY) ORDER BY date";
	$result=spip_query($query);

	while ($row = spip_fetch_array($result)) {
		$date = $row['date_unix'];
		$visites = $row['visites'];
 		$log[$date] = $visites;
	}

    if (count($log)>0){
		while (list($key, $value) = each($log)) {
			$n++;
			if ($decal == 30) $decal = 0;
			$decal ++;
			$tab_moyenne[$decal] = $value;
	
			$total_loc = $total_loc + $value;
			reset($tab_moyenne);
	
			$moyenne = 0;
			while (list(,$val_tab) = each($tab_moyenne))
				$moyenne += $val_tab;
				$moyenne = $moyenne / count($tab_moyenne);
	    }
    }
    else {
		$moyenne =0;
	}
    
	return round($moyenne);
}

function balise_MOYENNE_VISITES($p) {
	$p->code = "aff_moyenne_visites()";
	$p->interdire_scripts = false;
	return $p;
}


/*
+----------------------------------+
    Filtre : insere_texte_alerter
    Scoty 11/08/07 - gaf 0.5
    Insere texte alerte-abus dans corps message pour webmaster
+----------------------------------+
*/
function insere_texte_alerter($texte,$insere) {
    if (!$premiere_passe = _request('valide')) {
        if(_request('alerter')=='oui') {
            $origine=explode('-',_request('orig'));
            $lien_forum = generer_url_public('discussion',"id_forum=".$origine[0]."#forum".$origine[1],true);
            $texte = $insere."\n".$lien_forum."\n\n";
        }
    }
    return $texte;
}
/*
+----------------------------------+
    Filtre : insere_sujet_alerter
    Scoty 11/08/07 - gaf 0.5
    Insere texte alerte-abus dans sujet message pour webmaster
+----------------------------------+
*/
function insere_sujet_alerter($sujet,$insere) {
    if (!$premiere_passe = _request('valide')) {
        if(_request('alerter')=='oui') {
            $sujet = $insere;
        }
    }
    return $sujet;
}


/*
 *   +----------------------------------+
 *    Nom du Filtre :    citation                                            
 *   +----------------------------------+
 *    BASE : ... Date : vendredi 11 novembre 2006 - Auteur :  BoOz
 *    
 *    MODIF .. SCOTY .. 29/10/06 .. -> spip 1.9.1/2 
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     affiche le texte à citer    
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.spip-contrib.net/Pagination,663
*/

function barre_forum_citer($texte, $lan, $rows, $cols, $lang='') {
	if (!$premiere_passe = rawurldecode(_request('retour_forum'))) {
		if(_request('citer')=='oui'){
			$id_citation = _request('id_forum') ;
			$query = "SELECT auteur, texte FROM spip_forum WHERE id_forum=$id_citation";
		    $result = spip_query($query);
		    $row = spip_fetch_array($result);
		    $aut_cite=$row['auteur'];
		    $text_cite=$row['texte'];
		    
			//ajout de la citation
			$texte="{{ $aut_cite $lan }}\n<quote>\n$text_cite</quote>\n";
		}
	}
	return barre_textarea($texte, $rows, $cols, $lang);
}

 /*
 *   +----------------------------------+
 *    Nom du Filtre :    transformer en coordonnées °,m,s!
 *   +----------------------------------+
 *    Date : lundi 5 juin 2007
 *    Auteur : adapté du net
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *   transformer les coordonnées décimales en sexagésimals
 *   +-------------------------------------+ 
 *  
*/

function coordonnee($texte, $dir) {
	if($texte===null || $texte==='') return '';
	if($texte{0}=='-') {
		$signe=$dir{1};
		$texte= -$texte;
	} else {
		$signe=$dir{0};
	}
	$d= floor($texte);
	$s= ($texte - $d) * 3600;
	$m= floor($s/60);
	$s= $s-$m*60;
	return sprintf("%d&deg;%02d'%02d%s", $d, $m, $s, $signe);
}


 /*
 *   +----------------------------------+
 *    Nom du Filtre :    histograme photo
 *   +----------------------------------+
 *    Date : lundi 5 juin 2007
 *    Auteur : www.paris-beyrouth.org/SPIP
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *   ajoute un histogramme aux photos
 *   +-------------------------------------+ 
 *  
*/

function image_histo($im) {
	
	$image = valeurs_image_trans($im, "courbes", "gif");
	if (!$image) return("");

	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	if ($creer) {
		$im = $image["fonction_imagecreatefrom"]($im);
		$im_ = imagecreatetruecolor(258, 130);
		@imagealphablending($im_, false);
		@imagesavealpha($im_,true);
		$color_t = ImageColorAllocateAlpha( $im_, 255, 255, 255 , 0 );
		imagefill ($im_, 0, 0, $color_t);
		$col_poly = imagecolorallocate($im_,0,0,0);
		imagepolygon($im_, array ( 0, 0, 257, 0, 257, 129, 0,129 ), 4, $col_poly);

		for ($x = 0; $x < $x_i; $x++) {
			for ($y=0; $y < $y_i; $y++) {

				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				$a = (127-$a) / 127;
				$a=1;
				
				$gris = round($a*($r+$g+$b) / 3);
				$r = round($a*$r);
				$g = round($a*$g);
				$b = round($a*$b);
				
				$val_gris[$gris] ++;
				$val_r[$r] ++;
				$val_g[$g] ++;
				$val_b[$b] ++;
			} 
		}
		$max = max( max($val_gris), max($val_r), max($val_g), max($val_b));
		$rapport = (127/$max);

		$gris = imagecolorallocate($im_, 160, 160, 160);
		for ($i = 0; $i < 256; $i++) {
			$val = 127 - round(max(0,$val_gris[$i]) * $rapport);			imageline ($im_, $i+1, 128, $i+1, $val+1, $gris);
		}
		$bleu = imagecolorallocate($im_, 0, 0, 255);
		for ($i = 0; $i < 256; $i++) {
			$val = 127 - round(max(0,$val_b[$i]) * $rapport);
			if ($i==0) imagesetpixel ($im_, $i+1, $val+1, $bleu);
			else imageline($im_, $i, $val_old+1, $i+1, $val+1, $bleu);
			$val_old = $val;
		}
		$green = imagecolorallocate($im_, 0, 255, 0);
		for ($i = 0; $i < 256; $i++) {
			$val = 127 - round(max(0,$val_g[$i]) * $rapport);
			if ($i==0) imagesetpixel ($im_, $i+1, $val+1, $green);
			else imageline($im_, $i, $val_old+1, $i+1, $val+1, $green);
			$val_old = $val;
		}
		$rouge = imagecolorallocate($im_, 255, 0, 0);
		for ($i = 0; $i < 256; $i++) {
			$val = 127 - round(max(0,$val_r[$i]) * $rapport);
			if ($i==0) imagesetpixel ($im_, $i+1, $val+1, $rouge);
			else imageline($im_, $i, $val_old+1, $i+1, $val+1, $rouge);
			$val_old = $val;
		}

		$image["fonction_image"]($im_, "$dest");
		imagedestroy($im_);
		imagedestroy($im);
	}

	return "<img src='$dest'/>";
}


 /*
 *   +----------------------------------+
 *    Nom du Filtre :    niveaux contraste auto photo
 *   +----------------------------------+
 *    Date : lundi 27/08/2007
 *    Auteur : www.paris-beyrouth.org/SPIP
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *   met à niveau les photos
 *   +-------------------------------------+ 
 *  
*/


function image_niveaux_gris_auto($im, $limite=1000) {

	// $limite=1000: les nuances min et max representent 0,1% du total
	
	$image = valeurs_image_trans($im, "niveaux_gris_auto-$limite");
	if (!$image) return("");

	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	if ($creer) {
		$im = $image["fonction_imagecreatefrom"]($im);

		// Calculer les poids des differentes nuances
		for ($x = 0; $x < $x_i; $x++) {
			for ($y=0; $y < $y_i; $y++) {

				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				$a = (127-$a) / 127;
				$a=1;
				
				$gris = round($a*($r+$g+$b) / 3);
				$r = round($a*$r);
				$g = round($a*$g);
				$b = round($a*$b);
								
				$val_gris[$gris] ++;
			} 
		}

		$total = $x_i * $y_i;

		for ($bas = 0; $somme_bas < $total/$limite; $bas++) {
			$somme_bas += $val_gris[$bas];
		}	
		
		for ($haut = 255; $somme_haut < $total/$limite ; $haut--) {
			$somme_haut += $val_gris[$haut];
		}
	
		$courbe[0] = 0;
		$courbe[255] = 255;
		$courbe[$bas] = 0;
		$courbe[$haut] = 255;
	
		// Calculer le tableau des correspondances
		ksort($courbe);
		while (list($key, $val) = each($courbe)) {
			if ($key > 0) {
				$key1 = $key_old;
				$val1 = $val_old;
				$prop = ($val - $val1) / ($key-$key1);
				for ($i = $key1; $i < $key; $i++) {
					$valeur = round($prop * ($i - $key1) + $val1);
					$courbe[$i] = $valeur;
				}
				$key_old = $key;
				$val_old = $val;
			} else {
				$key_old = $key;
				$val_old = $val;
			}
		}

		// Appliquer les correspondances
		$im2 = imagecreatetruecolor($x_i, $y_i);
		@imagealphablending($im2, false);
		@imagesavealpha($im2,true);
		$color_t = ImageColorAllocateAlpha( $im2, 255, 255, 255 , 0 );
		imagefill ($im2, 0, 0, $color_t);

		for ($x = 0; $x < $x_i; $x++) {
			for ($y=0; $y < $y_i; $y++) {
				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;
				$r = ($rgb >> 16) & 0xFF;
				$v = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				
				$r = $courbe[$r];
				$v = $courbe[$v];
				$b = $courbe[$b];
				
				$color = ImageColorAllocateAlpha( $im2, $r, $v, $b , $a );
				imagesetpixel ($im2, $x, $y, $color);			
			}
		}

		$image["fonction_image"]($im2, "$dest");
		imagedestroy($im2);
		imagedestroy($im);
	}

	$class = $image["class"];
	if (strlen($class) > 1) $tags=" class='$class'";
	$tags = "$tags alt='".$image["alt"]."'";
	$style = $image["style"];
	if (strlen($style) > 1) $tags="$tags style='$style'";
	
	return "<img src='$dest'$tags />";
}


 /*
 *   +----------------------------------+
 *    Nom du Filtre :    niveaux couleurs auto photo
 *   +----------------------------------+
 *    Auteur : www.paris-beyrouth.org/SPIP
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *   met à niveau les couleurs photos
 *   +-------------------------------------+ 
 *  
*/


function image_niveaux_auto($im, $limite=1000) {

	// $limite=1000: les nuances min et max representent 0,1% du total
	
	$image = valeurs_image_trans($im, "niveaux_auto-$limite");
	if (!$image) return("");

	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	if ($creer) {
		$im = $image["fonction_imagecreatefrom"]($im);

		// Calculer les poids des differentes nuances
		for ($x = 0; $x < $x_i; $x++) {
			for ($y=0; $y < $y_i; $y++) {

				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				$a = (127-$a) / 127;
				$a=1;
				
				$gris = round($a*($r+$g+$b) / 3);
				$r = round($a*$r);
				$g = round($a*$g);
				$b = round($a*$b);
								
				$val_r[$r] ++;
				$val_v[$g] ++;
				$val_b[$b] ++;
			} 
		}

		$total = $x_i * $y_i;

		// Calculer le tableau des correspondances
		// Rouge
		for ($bas_r = 0; $somme_bas_r < $total/$limite; $bas_r++) {
			$somme_bas_r += $val_r[$bas_r];
		}	
		
		for ($haut_r = 255; $somme_haut_r < $total/$limite ; $haut_r--) {
			$somme_haut_r += $val_r[$haut_r];
		}
	
		$courbe_r[0] = 0;
		$courbe_r[255] = 255;
		$courbe_r[$bas_r] = 0;
		$courbe_r[$haut_r] = 255;
	
		ksort($courbe_r);
		while (list($key, $val) = each($courbe_r)) {
			if ($key > 0) {
				$key1 = $key_old;
				$val1 = $val_old;
				$prop = ($val - $val1) / ($key-$key1);
				for ($i = $key1; $i < $key; $i++) {
					$valeur = round($prop * ($i - $key1) + $val1);
					$courbe_r[$i] = $valeur;
				}
				$key_old = $key;
				$val_old = $val;
			} else {
				$key_old = $key;
				$val_old = $val;
			}
		}

		// Bleu
		for ($bas_b = 0; $somme_bas_b < $total/$limite; $bas_b++) {
			$somme_bas_b += $val_b[$bas_b];
		}	
		
		for ($haut_b = 255; $somme_haut_b < $total/$limite ; $haut_b--) {
			$somme_haut_b += $val_b[$haut_b];
		}
	
		$courbe_b[0] = 0;
		$courbe_b[255] = 255;
		$courbe_b[$bas_b] = 0;
		$courbe_b[$haut_b] = 255;
	
		ksort($courbe_b);
		while (list($key, $val) = each($courbe_b)) {
			if ($key > 0) {
				$key1 = $key_old;
				$val1 = $val_old;
				$prop = ($val - $val1) / ($key-$key1);
				for ($i = $key1; $i < $key; $i++) {
					$valeur = round($prop * ($i - $key1) + $val1);
					$courbe_b[$i] = $valeur;
				}
				$key_old = $key;
				$val_old = $val;
			} else {
				$key_old = $key;
				$val_old = $val;
			}
		}

		// Vert
		for ($bas_v = 0; $somme_bas_v < $total/$limite; $bas_v++) {
			$somme_bas_v += $val_v[$bas_v];
		}	
		
		for ($haut_v = 255; $somme_haut_v < $total/$limite ; $haut_v--) {
			$somme_haut_v += $val_v[$haut_v];
		}
	
		$courbe_v[0] = 0;
		$courbe_v[255] = 255;
		$courbe_v[$bas_v] = 0;
		$courbe_v[$haut_v] = 255;
	
		ksort($courbe_v);
		while (list($key, $val) = each($courbe_v)) {
			if ($key > 0) {
				$key1 = $key_old;
				$val1 = $val_old;
				$prop = ($val - $val1) / ($key-$key1);
				for ($i = $key1; $i < $key; $i++) {
					$valeur = round($prop * ($i - $key1) + $val1);
					$courbe_v[$i] = $valeur;
				}
				$key_old = $key;
				$val_old = $val;
			} else {
				$key_old = $key;
				$val_old = $val;
			}
		}

		// Appliquer les correspondances
		$im2 = imagecreatetruecolor($x_i, $y_i);
		@imagealphablending($im2, false);
		@imagesavealpha($im2,true);
		$color_t = ImageColorAllocateAlpha( $im2, 255, 255, 255 , 0 );
		imagefill ($im2, 0, 0, $color_t);

		for ($x = 0; $x < $x_i; $x++) {
			for ($y=0; $y < $y_i; $y++) {
				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;
				$r = ($rgb >> 16) & 0xFF;
				$v = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				
				$r = $courbe_r[$r];
				$v = $courbe_v[$v];
				$b = $courbe_b[$b];
				
				$color = ImageColorAllocateAlpha( $im2, $r, $v, $b , $a );
				imagesetpixel ($im2, $x, $y, $color);			
			}
		}

		$image["fonction_image"]($im2, "$dest");
		imagedestroy($im2);
		imagedestroy($im);
	}

	$class = $image["class"];
	if (strlen($class) > 1) $tags=" class='$class'";
	$tags = "$tags alt='".$image["alt"]."'";
	$style = $image["style"];
	if (strlen($style) > 1) $tags="$tags style='$style'";
	
	return "<img src='$dest'$tags />";
}





 /*
 *   +----------------------------------+
 *    Nom du Filtre :    tuer les lettres!
 *   +----------------------------------+
 *    Date : lundi 11 mai 2005
 *    Auteur :  Posted by cerdic
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *   remplacement des caractères accentués
 *    exemple trouvé là: 
 *    http://be.php.net/manual/fr/function.strtr.php#52098
 *   +-------------------------------------+ 
 *  
*/


function lettre1($texte) {
	$texte = $texte{0}; // première lettre
		$texte = 
strtr($texte, "\xA1\xAA\xBA\xBF\xC0\xC1\xC2\xC3\xC5\xC7\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2\xD3\xD4\xD5\xD8\xD9\xDA\xDB\xDD\xE0\xE1\xE2\xE3\xE5\xE7\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF8\xF9\xFA\xFB\xFD\xFF", "!ao?AAAAACEEEEIIIIDNOOOOOUUUYaaaaaceeeeiiiidnooooouuuyy");
	$texte = strtr($texte, 
array("\xC4"=>"Ae", "\xC6"=>"AE", "\xD6"=>"Oe", "\xDC"=>"Ue", "\xDE"=>"TH", "\xDF"=>"ss", "\xE4"=>"ae", "\xE6"=>"ae", "\xF6"=>"oe", "\xFC"=>"ue", "\xFE"=>"th"));
	$texte = strtoupper($texte); // tout en majuscules
	return $texte;
}


/*
 *   +----------------------------------+
 *    Nom du Filtre :    titre_forum
 *   +----------------------------------+
 *    Date : lundi 11 mai 2005
 *    Auteur :  BoOz (booz@bloog.net)
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *    Nettoie les titres des forums
 *    [(#ID_AUTEUR|messages_prives)]
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au site bloog.net
*/
function titre_forum($titre=''){
$ze_titre = ereg_replace( "> ", "", $titre );
return $ze_titre;
}



 /*  +----------------------------------+
 *    Nom du Filtre :    Filtre NORM_LIENS v3.0 - 
 *   +----------------------------------+
 *    Fonctions de ce filtre :
 *    Cette fonction calme les tentatives des spammeurs
 *    en ajoutant une nofollow dans les urls des forums.
 *   +-------------------------------------+ 
*/

function norm_liens($texte) {

   $texte=str_replace("<a href","<a rel='nofollow' href",$texte);
   

   return $texte;
}
/*
 *   +----------------------------------+
 *    Nom du Filtre :    get_auteur_infos
 *   +----------------------------------+
 *    Date : lundi 23 février 2004
 *    Auteur :  Nikau (luchier@nerim.fr)
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *    Cette fonction permet d'obtenir toutes les infos 
 *    d'un auteur avec son nom ou son id_auteur
 *    ATTENTION !! cette fonction ne s'utilise pas de       
 *    façon classique !! voir explication dans la contrib'
 *    Fonction utilisée également dans la fonction
 *    'afficher_avatar'
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/article.php3?id_article=261
*/
function get_auteur_infos($id='', $nom='') {
if ($id) $query = "SELECT * FROM spip_auteurs WHERE id_auteur=$id";
if ($nom) $query = "SELECT * FROM spip_auteurs WHERE nom='$nom'";
$result = spip_query($query);
if ($row = spip_fetch_array($result)) {
$row=serialize($row);
}
return $row;
}


/*
 *   +---------------------------------------------+
 *    Nom du Filtre : membres nouveaux
 *   +---------------------------------------------+
 *        par : [(#URL_SITE|new_connect)]
 *   +---------------------------------------------+
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/article.php3?id_article=94
 *
 */
function new_connect($resultat){
global $table_prefix;
$query = "SELECT nom, en_ligne FROM ".$table_prefix."_auteurs WHERE statut = 'nouveau' ORDER BY en_ligne DESC";
$resultat = "";
$result_auteurs = spip_query($query);
$new_connectes = spip_num_rows($result_auteurs);
$flag_cadre = ($new_connectes > 0);
if ($flag_cadre) {
	$resultat.="<p>";
	if ($new_connectes > 1) $resultat.="Vous etes inscrits mais vous ne vous etes jamais connectés au site:<br><br>";
	else $resultat.="Tu es inscrit mais tu ne t'es jamais connecté au site:<br><br>";
	while ($row = spip_fetch_array($result_auteurs)) {
		$nom_auteur = $row["nom"];
		$en_ligne = $row["en_ligne"];
		$resultat.="<div class=h5>$nom_auteur</div> ";
	}
}
return $resultat;
}
// FIN du Filtre new_connect

/*
 *   +----------------------------------+
 *    Nom du Filtre :    smileys II
 *   +----------------------------------+
 *    Date : mercredi 14 octobre 2003
 *    Auteur :  BoOz (booz.bloog@laposte.net)
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *    Dans un texte, génère automatiquement le smiley 
 *    approprié à la place d'une chaine :nom.
 *    Ce filtre utilise les smileys disponibles dans       
 *    le répertoire smileys/
 *    Exemple d'application :
 *    [(#TEXTE|smileys)]
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/article.php3?id_article=261
*/
function smileys($chaine) {
$listimag=array();
$rep1="smileys/";
$listfich=opendir($rep1);
while ($fich=readdir($listfich))
{ 	if(($fich !='..') and ($fich !='.') and ($fich !='.test'))
	{ 
$nomfich=substr($fich,0,strrpos($fich, "."));
$listimag[$nomfich]="<img alt=\"smiley\" src=\"smileys/".$fich."\"/>";
	}
}
ksort($listimag);
reset($listimag);
while (list($nom,$chem) = each($listimag))
{ 
  $chaine = str_replace(":".$nom, $chem , $chaine);
}
        return $chaine;
}
/*
 *   +----------------------------------+
 *    Nom du Filtre : Glossaire interne                                               
 *   +----------------------------------+
 *    Date : jeudi 11 septembre 2003
 *    Auteur :  François Schreuer <francois (sur) schreuer (point) org>
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *    Gestion des liens vers un glossaire interne à un site
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.spip-contrib.net/article241.html
*/

# Remplace seulement la première occurence. Mêmes arguments que str_replace
# Cette fonction est inspirée d'une fonction trouvée à l'adresse http://www.phpapps.org/index.php?action=sources&go=voir_source&id=174 (qui toutefois contenait un bug lorsque la chaîne recherchée ne se trouvait pas dans le texte
function first_replace($c,$r,$t)
{
	if(strstr($t,$c))
	{
		$d = str_replace(strstr($t,$c),"",$t);
		$f = strstr($t,$c);
		$f = substr($f,strlen($c));
		return $d . $r . $f;
	}
	else
		return $t;
}

# Crée des liens vers le glossaire
function lier_au_glossaire($texte)
{
	# Config
	# L'identifiant (id_rubrique) de la rubrique glossaire
	$id_rubrique = 39;
	# Limiter l'effet du filtre à la première occurence
	$eviter_doublons = 0; // 0 : afficher toutes les occurences

	# On checke si l'entrée est déjà présente dans la table
	# Mettre l'identifiant de la rubrique contenant le glos
	$r = spip_query("SELECT id_breve,titre FROM spip_breves WHERE statut='publie' AND id_rubrique='$id_rubrique'");

	while($o = spip_fetch_array($r))
	{
		if($eviter_doublons == 1)
		{
			$texte = first_replace("$o[titre]","<a href=\"spip.php?breve".$o[id_breve]."\" class=\"glossaire\">$o[titre]</a>",$texte);
		}
		else
		{
			$texte = str_replace("$o[titre]","<a href=\"spip.php?breve".$o[id_breve]."\" class=\"glossaire\">$o[titre]</a>",$texte);
		}
	}
	return $texte;
}
?>