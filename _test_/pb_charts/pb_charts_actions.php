<?php

if ($GLOBALS["spip_version"] >= 1.93) {
	$couleurs = charger_fonction('couleurs', 'inc');
	$var = $couleurs($GLOBALS['auteur_session']['prefs']['couleur']);
}

function pb_charts_image_rgb2hsv ($R,$G,$B) {
	$var_R = ( $R / 255 ) ;                    //Where RGB values = 0 Ö 255
	$var_G = ( $G / 255 );
	$var_B = ( $B / 255 );

	$var_Min = min( $var_R, $var_G, $var_B ) ;   //Min. value of RGB
	$var_Max = max( $var_R, $var_G, $var_B ) ;   //Max. value of RGB
	$del_Max = $var_Max - $var_Min  ;           //Delta RGB value

	$V = $var_Max;
	$L = ( $var_Max + $var_Min ) / 2;
	
	if ( $del_Max == 0 )                     //This is a gray, no chroma...
	{
	   $H = 0 ;                            //HSL results = 0 Ö 1
	   $S = 0 ;
	}
	else                                    //Chromatic data...
	{
	   $S = $del_Max / $var_Max;
	
	   $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
	   $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
	   $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
	
	   if      ( $var_R == $var_Max ) $H = $del_B - $del_G;
	   else if ( $var_G == $var_Max ) $H = ( 1 / 3 ) + $del_R - $del_B;
	   else if ( $var_B == $var_Max ) $H = ( 2 / 3 ) + $del_G - $del_R;
	
	   if ( $H < 0 )  $H =  $H + 1;
	   if ( $H > 1 )  $H = $H - 1;
	}
				
	$ret["h"] = $H;
	$ret["s"] = $S;
	$ret["v"] = $V;
	
	return $ret;
}

function pb_charts_image_hsv2rgb ($H,$S,$V) {
	if ( $S == 0 )                       //HSV values = 0 Ö 1
	{
	   $R = $V * 255;
	   $G = $V * 255;
	   $B = $V * 255;
	}
	else
	{
	   $var_h = $H * 6;
	   if ( $var_h == 6 ) $var_h = 0 ;     //H must be < 1
	   $var_i = floor( $var_h )  ;           //Or ... var_i = floor( var_h )
	   $var_1 = $V * ( 1 - $S );
	   $var_2 = $V * ( 1 - $S * ( $var_h - $var_i ) );
	   $var_3 = $V * ( 1 - $S * ( 1 - ( $var_h - $var_i ) ) );
	
	   if      ( $var_i == 0 ) { $var_r = $V     ; $var_g = $var_3 ; $var_b = $var_1 ; }
	   else if ( $var_i == 1 ) { $var_r = $var_2 ; $var_g = $V     ; $var_b = $var_1 ; }
	   else if ( $var_i == 2 ) { $var_r = $var_1 ; $var_g = $V     ; $var_b = $var_3 ; }
	   else if ( $var_i == 3 ) { $var_r = $var_1 ; $var_g = $var_2 ; $var_b = $V ;     }
	   else if ( $var_i == 4 ) { $var_r = $var_3 ; $var_g = $var_1 ; $var_b = $V ; }
	   else                   { $var_r = $V     ; $var_g = $var_1 ; $var_b = $var_2; }
	
	   $R = $var_r * 255;                  //RGB results = 0 Ö 255
	   $G = $var_g * 255;
	   $B = $var_b * 255;
	}
	$ret["r"] = floor($R);
	$ret["g"] = floor($G);
	$ret["b"] = floor($B);
	
	return $ret;
}


function pb_charts_couleur_chroma ($coul, $num) {

	$pos = substr($num, 0, strpos($num, "/")) -  1;
	$tot = substr($num, strpos($num, "/")+1, strlen($num));
	
	include_spip("inc/filtres_images");
	$couleurs = couleur_hex_to_dec($coul);
	$r= $couleurs["red"];
	$g= $couleurs["green"];
	$b= $couleurs["blue"];

	$hsv = pb_charts_image_rgb2hsv($r,$g,$b);
	$h = $hsv["h"];
	$s = $hsv["s"];
	$v = $hsv["v"];
	
	$h = $h + (1/$tot)*$pos;
	if ($h > 1) $h = $h - 1;
					
	$rgb = pb_charts_image_hsv2rgb($h,$s,$v);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];
	
	$couleurs = couleur_dec_to_hex($r, $g, $b);
	
	return $couleurs;
}


// En variables globales:
// - couleur_claire : couleur de la couleur de la premiere courbe
//                    (les couleurs des autres courbes sont calculees automatiquement 
//                    en parcourant la roue chromatique)
// - couleur_texte :  la couleur du texte des legendes
//                    (la couleur du fond est automatiquement l'extreme inverse
//                     - noir ou blanc - de la couleur du texte)
// - largeur_charts et hauteur_charts : dimensions de l'animation

function pb_charts_traiter_charts ($texte) {
	global $couleur_claire, $couleur_texte;
	if (strlen($couleur_claire) > 3) $couleur_courbe = ereg_replace("^#", "", $couleur_claire);
	else $couleur_courbe = "ff00ff";

	if (strlen($couleur_texte))  $couleur_texte = ereg_replace("^#", "", $couleur_texte);
	else $couleur_texte = "333333";
	
	include_spip("inc/filtres_images");
	$couleur_fond = couleur_extreme(couleur_inverser($couleur_texte));


	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_PB_CHARTS',(_DIR_PLUGINS.end($p)));
	$charts = _DIR_PLUGIN_PB_CHARTS."/charts/charts.swf";
	
	if (!file_exists($charts)) {
		return "<ul style='font-size: 10px; text-align: left;'><li>T&eacute;l&eacute;chargez <a href='http://www.maani.us/xml_charts/index.php?menu=Download'>XML/SWF Charts</a>;</li><li>d&eacute;compactez le fichier;</li><li>Installez le contenu du dossier &laquo;charts&raquo;, par FTP, dans le dossier &laquo;/pb_charts/charts&raquo;.</li></ul>";
		
		
	}
	

	$largeur = 400;
	$hauteur = 340;
	if ($GLOBALS["largeur_charts"]) $largeur = $GLOBALS["largeur_charts"];
	if ($GLOBALS["hauteur_charts"]) $hauteur = $GLOBALS["hauteur_charts"];


	$preg = ",<chart type=\"([^\"]*)\">(.*)</chart>,Uimss";
	if (preg_match_all( $preg, $texte, $matches, PREG_SET_ORDER)) {

		foreach ($matches as $regs) {
			
			
			// Analyser le fichier XML
			$cache = sous_repertoire(_DIR_VAR, "cache-charts");
			$nom_fichier = $cache.md5($regs[0].$couleur_courbe.$largeur.$hauteur).".xml";
		
			if( !file_exists($nom_fichier)) {
				$valeurs_xml = "";
				$xml = "";
				$couleurs_xml = "";
				$total_lignes = 0;
				$titre_xml = "";
				
				$type = strtolower($regs[1]);
				$vals = trim($regs[2]);
				
				$lignes = explode("\n", $vals);
					for ($i = 0; $i < count($lignes); $i++) {
					$ligne = trim($lignes[$i]);
					$ligne_xml = "";
					
					if (ereg("^\|\|([^\|]*)\|\|$", $ligne, $titre_xml)) {
						$titre_xml = $titre_xml[1];
						$titre_xml = "<draw><text x='0' h_align='center' y='25' v_align='bottom' color='$couleur_texte' size='12'>$titre_xml</text></draw>";

					} else {
						$total_lignes++;
						$entrees = explode("|", $ligne);
						$total_col = count($entrees);
						for ($j = 1; $j < count($entrees) - 1; $j++) {
							$entree = trim($entrees[$j]);
		
							if ($entree == "") $ligne_xml .= "<string></string>";
							else if (ereg("^[0-9\-\.\,]*$", $entree)) {
								$ligne_xml .= "<number>".str_replace(",",".",$entree)."</number>" ;
							}
							else $ligne_xml .= "<string>".$entree."</string>" ;
		
						}
						$valeurs_xml .= "<row>".$ligne_xml."</row>\n";
					}
				}
				$valeurs_xml = "<chart_data>".$valeurs_xml."</chart_data>\n";
				$type_xml = "<chart_type>$type</chart_type>\n";

				if ($type == "pie" OR $type == "3d pie") {
					$total_lignes = $total_col - 3;
				}
				for ($i = 0; $i < $total_lignes; $i++) {
					$couleurs_xml .= "<color>".pb_charts_couleur_chroma($couleur_courbe,($i+1)."/".$total_lignes)."</color>";
				}
				$couleurs_xml = "<series_color>".$couleurs_xml."</series_color>";
				
				if ($type == "polar") $pref_xml = "<chart_pref grid='circular' />";

				
				$axis_xml = "<axis_category color='$couleur_texte' size='10'/><axis_ticks major_color='$couleur_texte' minor_color='$couleur_texte' /><axis_value size='10' color='$couleur_texte' /><chart_border color='$couleur_texte' /><legend_label color='$couleur_texte' size='11' /><legend_rect size='11' fill_color='$couleur_fond' fill_alpha='40' />";
				
				if ($titre_xml) $hauteur_chart = $hauteur - 100;
				else $hauteur_chart = $hauteur - 75;
				
				$largeur_chart = $largeur - 50;
				
				if (!ereg("pie", $type)) $axis_xml .= "<chart_rect positive_alpha='35' x='45' y='45' width='$largeur_chart' height='$hauteur_chart' positive_color='$couleur_fond' negative_alpha='60' negative_color='$couleur_fond' />";
				
				if (!ereg("pie", $type)) $axis_xml .= "<chart_value position='cursor' size='11' color='$couleur_texte' alpha='100' />";
				else  $axis_xml .= "<chart_value size='11' color='$couleur_texte' alpha='100' />";
				
				$transition_xml = "<chart_transition type='scale' delay='1' duration='0.5' order='series' />";


				$xml = "<chart>".$type_xml.$valeurs_xml.$couleurs_xml.$transition_xml.$axis_xml.$pref_xml.$titre_xml."</chart>";
				
				//echo "<pre>$xml</pre>";
				
				ecrire_fichier($nom_fichier, $xml);
			}
			
			
			include_spip("inc/pb_charts");
			
			$flash = pb_charts_afficher_charts ($nom_fichier,$largeur,$hauteur);
			
			$compteur_charts ++;
			
			
			$insert = "\n\nSPIP_INSERT_CHARTS_$compteur_charts-\n\n";

			$GLOBALS["charts_cache"][$compteur_charts] = $flash;
			
			
			$texte = str_replace($regs[0], $insert, $texte);
			
			
			
		}
	}

	return $texte;
}



function pb_charts_retablir_script($texte) {
	for ($i=1; $i<=count($GLOBALS["charts_cache"]); $i++) {
		$texte = ereg_replace("(<p class=\"spip\">)?SPIP_INSERT_CHARTS_$i-(</p>)?",$GLOBALS["charts_cache"][$i],$texte);
	}

	return $texte;

}



?>