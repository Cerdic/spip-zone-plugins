<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
#---------------------------------------------------#
#  Plugin  : jeux , diagrammes d'echecs             #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : https://contrib.spip.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#
/*

Insere un diagramme de positions d’echecs dans vos articles !
-------------------------------------------------------------
 Module base sur les travaux de:
	 François SCHREUER (plugin)
	 Olivier BOUVEROT (DIAGOL)
	 Patrice PILLOT (notation FEN)
	 Andrew TEMPLETON (figures)
-------------------------------------------------------------

separateurs obligatoires : [diag_echecs]
separateurs optionnels   : [titre], [texte], [config]

Attention :
- La librairie GD doit etre installee sur le serveur.

La position doit etre decrite sous forme de notation FEN, ou bien en utilisant la forme "classique".
Exemples :
	classique : B:Rg1,Dd1,Ta1,e1,Pa2,f6/N:Rb8,Pa6,f5
	FEN       : r1bqkbnr/pp2pppp/2n5/2p1P3/3p4/2P2N2/PP1P1PPP/RNBQKB1R

Notation "classique" : la description de la position blanche commence par "B:", celle de la position noire par "N:", et les deux sont separees par un "/".
Il est possible de n'indiquer qu'une couleur (B ou N) pour la description. L'ordre dans cette derniere est indifferent.
Les majuscules ne sont pas non plus obligatoires

Cases en surbrillance : on peut utiliser "/sv" pour mettre du vert, "/sb" pour du bleu, "/sj" pour du jaune et "/sr" pour du rouge.
Attention : pour utiliser cette fonctionnalite, il faut obligatoirement utiliser la notation "classique".

Retournement de l'echiquier : C'est automatique en utilisant la notation FEN (et si bien entendu le trait est aux Noirs), sinon il suffit d'ajouter "/r" a la description "classique".

Exemples de syntaxe dans l'article :
------------------------------------
	<jeux>
		[diag_echecs]
		B:Rg1,Dd1,Ta1,e1,Pa2,f6/N:Rb8,Pa6,f5
		[diag_echecs]
		r1bqkbnr/pp2pppp/2n5/2p1P3/3p4/2P2N2/PP1P1PPP/RNBQKB1R
		[config]
		taille=35
	</jeux>
	<jeux>
		[diag_echecs]
		B:Rb6,pa3,Fh2/N:Rc8/sr:a8/sb:g3,f4,e5,d6,c7,b8
		[config]
		taille=35
		noirs=bleu
	</jeux>

Parametres [config] definis par defaut :
----------------------------------------
	taille=29		// Taille des images en pixels (29 ou 35)
	blancs=blanc	// Couleur des cases 'blanches'
	noirs=brun		// Couleur des cases 'noires'
	fond=blanc		// Couleur de fond de la page web
	bordure=2		// Epaisseur de la bordure de l'echiquier, en pixels
	police=5		// Code de la police utilisee pour les coordonnees (1 a 5)
	flip=non		// Faut-il retourner l'echiquier ?
	coords=oui		// Afficher les coordonnees ?
	truecolor=non	// Image finale en 24bit ?
	cache=30		// Nombre de jour avant le recalcul de l'image (0 : pas de cache)
	plateau=non		// Fichier image de l'echiquier
*/
require("diag_echecs_init.php");
require("diag_echecs_inc.php");

// creation du diagramme d'echecs
function calcul_diagramme_echecs($position, $coloration, $indexJeux) {
	// qq initialisations
	global $diag_echecs_globales, $jeux_couleurs;
	$flip = jeux_config('flip');
	$taille = intval(jeux_config('taille'));
	$redim = intval(jeux_config('redim'));
	$nbjour = intval(jeux_config('cache')); // nombre de jour avant recalcul
	$bordure = intval(jeux_config('bordure'));
	$board_size = jeux_config('board_size');
	$font = intval(jeux_config('police'));
	$img = jeux_config('img_img');

	// dechiffre la surbrillance eventuelle des cases
	 $surbrillance = array();
	 $coloration = preg_split("/[\r\n]+/", $coloration);
	 foreach ($coloration as $ligne)
		if ($regs = jeux_parse_ligne_config($ligne))
			$surbrillance[] = array($regs[1], explode(',',$regs[2])); // (couleur, valeur)

	// dechiffre le code source du diagramme place dans $position
	$position = preg_replace("/\s*[\r\n]+\s*/", '/', trim($position));
	$position = preg_replace(",\/+,", '/', trim($position));

	// quelle est la notation !?	
	$masque=',(([a-zA-Z1-8]+)\/([a-zA-Z1-8]+)\/([a-zA-Z1-8]+)\/([a-zA-Z1-8]+)\/([a-zA-Z1-8]+)\/([a-zA-Z1-8]+)\/([a-zA-Z1-8]+)\/([a-zA-Z1-8]+)) *(.*),';
	if ( preg_match($masque, $position, $table) ) {
	  $position = FEN2classic($position);			// FEN
	  $table = explode("/", $position);
	} else {
	  $position = preg_replace(",\s,","",$position);		// CLASSIC
	  $position = strtolower($position);
	  $table = explode("/",$position);
	  if (count($table)<2) return "Erreur dans la syntaxe [explode table classic]";
	}
	
	// image en cache
	global $jeux_config;
	$md5 = md5($position.$coloration.serialize($jeux_config));
	$fichier_dest = sous_repertoire(_DIR_VAR, "cache-jeux") . 'echiq_'.$md5 . jeux_config('img_suffix');
	list(,,,$size) = @getimagesize($fichier_dest);
	$image = "<img class=\"no_image_filtrer \" src=\"$fichier_dest\" alt=\"$position\" title=\"$position\" border=\"0\" $size/><br>\n";

	// pas de recalcul de l'image pendant 30 jours si le fichier existe deja
	if (file_exists($fichier_dest) 
			AND (_request('var_mode') != 'recalcul') AND (_request('var_mode') != 'calcul') 
			AND (time()-@filemtime($fichier_dest) < 24*3600*$nbjour))
		 return $image;

	

// *********************	
	//for ($i=0 ; $i<count($table) ; $i++)  if ( $table[$i] == "r" ) $flip = true;
	if (in_array('r', $table)) $flip = true;
	
	$chessboard = image_echiquier($flip);
	

	// *************** CASE A COLORIER & LIGNE A TRACER *************************
	foreach($surbrillance as $surb) 
		if(in_array($surb[0], array('jaune', 'bleu', 'rouge', 'vert')))
			foreach($surb[1] as $square)
				switch(strlen($square)) {
					// case de couleur : forme a1
					case 2: diag_echecs_hilite_square($chessboard, $square, 'h'.$surb[0], $flip); break;
					// ligne de couleur : forme a1-g8
					case 5: diag_echecs_hilite_line($chessboard, $square, 'h'.$surb[0], $flip); break;
				}

	
	for ($i=0 ; $i<count($table) ; $i++) {
	  $sub_table = preg_split("/[:,]/",$table[$i]);
	  switch($sub_table[0]) {
		case "b" : $side = "w";break;
		case "n" : $side = "b";break;
		case "sr" : $side = "-"; $hilite = "hrouge"; break;
		case "sv" : $side = "-"; $hilite = "hvert"; break;
		case "sb" : $side = "-"; $hilite = "hbleu"; break;
		case "sj" : $side = "-"; $hilite = "hjaune"; break;
		case "r" : $side = "-"; $flip = true; break; /* Ne sert qu'a eviter des erreurs ;) */
		default : return "Erreur dans la syntaxe [couleur]";
	  }
	  /* Ici j'ai change car sinon il est impossible de commencer la */
	  /* description de la position par un pion sans 'p' */
	  /* et cela pose probleme pour la partie hilite qui elle ne */
	  /* comporte pas de piece */
	  if (strlen($sub_table[1])==2) $name = "p";
	  elseif (strlen($sub_table[1])==3) $name = substr($sub_table[1],0,1);
	  else {
		if ( (strlen($sub_table[1])==0) && $sub_table[0]!="r" ) {
		  echo "<p>$sub_table[1]</p>";
		  return "Erreur dans la syntaxe [piece]";
		}
	  }
	
	  for ($j=1 ; $j<count($sub_table) ; $j++) {
		switch(strlen($sub_table[$j])) {
		  case 2 :
			$square = substr($sub_table[$j],0,2);
			break;
		  case 3 :
			$name = substr($sub_table[$j],0,1);
			$square = substr($sub_table[$j],1,2);
			break;
		  default :
			die("Erreur dans la syntaxe (pos) !");
		}
		if ($side!="-") diag_echecs_put_piece($chessboard,$side,$name,$square,$flip);
		 else diag_echecs_hilite_square($chessboard,$square,$hilite,$flip);
	  }
	}

	/* Faut-il envoyer un en-tete (cas d'une image "nue"), ou l'image
est-elle destinee a etre incluse dans une page Web ? Laisser la
variable a "true" dans le premier cas, la mettre a "false" dans le
second */
	/*	if ($hdr) header(jeux_config('img_header'); */
	
	if (jeux_config('coords')) {
	  $fond = $jeux_couleurs[jeux_config('fond')];
	  $big_chessboard = imagecreatetruecolor($board_size+2*$bordure+$taille,$board_size+2*$bordure+$taille);
	  $bg_color = imagecolorallocate($big_chessboard,$fond[0],$fond[1],$fond[2]);
	  //imagecolortransparent($big_chessboard,$bg_color);
	  imagefill($big_chessboard,0,0,$bg_color);			
	  imagecopy($big_chessboard,$chessboard,$taille,0,0,0,$board_size+2*$bordure,$board_size+2*$bordure);
	  $width = imagefontwidth($font);
	  $height = imagefontheight($font);
	  $center = intval($taille/2);
	  for ($i=1 ; $i<=8 ; $i++) {
		$empty_coord = imagecreatetruecolor($taille,$taille);
		$bg_color = imagecolorallocate($empty_coord,$fond[0],$fond[1],$fond[2]);
		//imagecolortransparent($empty_coord,$bg_color);
		imagefill($empty_coord,0,0,$bg_color);				
		$font_color = imagecolorallocate($empty_coord,0,0,0);
		if (!$flip) {
		  imagechar($empty_coord,$font,($taille-$width)/2,($taille-$height)/2+$bordure,9-$i,$font_color);
		} else {
		  imagechar($empty_coord,$font,($taille-$width)/2,($taille-$height)/2+$bordure,$i,$font_color);
		}
		imagecopy($big_chessboard,$empty_coord,0,($i-1)*$taille,0,0,$taille,$taille);
	  }
	  for ($i=1 ; $i<=8 ; $i++) {
		$empty_coord = imagecreatetruecolor($taille,$taille);
		$bg_color = imagecolorallocate($empty_coord,$fond[0],$fond[1],$fond[2]);
		//imagecolortransparent($empty_coord,$bg_color);
		imagefill($empty_coord,0,0,$bg_color);
		$font_color = imagecolorallocate($empty_coord,0,0,0);
		if (!$flip) {
		  imagechar($empty_coord,$font,($taille-$width)/2+$bordure,($taille-$height)/2,$diag_echecs_globales['number2letter'][$i],$font_color);
		} else {
		  imagechar($empty_coord,$font,($taille-$width)/2+$bordure,($taille-$height)/2,$diag_echecs_globales['number2letter'][9-$i],$font_color);
		}
		imagecopy($big_chessboard,$empty_coord,$i*$taille,8*$taille+2*$bordure,0,0,$taille,$taille);
	  }
	 $chessboard = $big_chessboard;
	} // if (jeux_config('coords'))
	
	// ************* redimensionement final *************
	if ($redim) {
		if (jeux_config('coords')) { $nbcases=9; } else	{ $nbcases=8; }
		$newsize = $redim / $taille;
		if ($newsize>3) $newsize=3; // taille maximale pour eviter les erreurs
		$img_finale = imagecreatetruecolor(round($newsize*imagesx($chessboard)),round($newsize*imagesy($chessboard)));
		imagecopyresampled($img_finale,$chessboard,0,0,0,0,round($newsize*imagesx($chessboard)),round($newsize*imagesy($chessboard)),imagesx($chessboard),imagesy($chessboard));
		// convertir l'image en 256 couleurs si truecolor=non
		if (!jeux_config('truecolor')) imagetruecolortopalette($img_finale,false,256);	
		$img($img_finale, $fichier_dest);
	}
	else {
		// convertir l'image en 256 couleurs si truecolor=non
		if (!jeux_config('truecolor')) imagetruecolortopalette($chessboard,false,256);	
		$img($chessboard, $fichier_dest);
	}
	
	return $image;
}

// decode un diagramme d'echecs, pas de formulaire
function jeux_diag_echecs($texte, $indexJeux, $form=true) { 
	// qq initialisations
	$html = false;
	$coloration = $diagramme = '';
	
	// decodage du texte
	$tableau = jeux_split_texte('diag_echecs', $texte);
	diag_echecs_config_supplementaire();
	foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $html .= "<div class=\"jeux_titre diag_echecs_titre\">{$tableau[$i+1]}</div>";
	  elseif ($valeur==_JEUX_DIAG_ECHECS) {
		  if($diagramme) {
			  $html .= calcul_diagramme_echecs($diagramme, $coloration, $indexJeux);
			  $coloration = $diagramme = '';
		  }
		  $diagramme = $tableau[$i+1];
		  if($coloration) {
			  $html .= calcul_diagramme_echecs($diagramme, $coloration, $indexJeux);
			  $coloration = $diagramme = '';
		  }
	  } elseif ($valeur==_JEUX_COLORATION) {
		  if($coloration && $diagramme) {
			  $html .= calcul_diagramme_echecs($diagramme, $coloration, $indexJeux);
			  $coloration = $diagramme = '';
		  }
		  $coloration .= "\n".$tableau[$i+1];
		  if($diagramme) {
			  $html .= calcul_diagramme_echecs($diagramme, $coloration, $indexJeux);
			  $coloration = $diagramme = '';
		  }
	  } elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	}
    if($diagramme)
	  $html .= calcul_diagramme_echecs($diagramme, $coloration, $indexJeux);

	return $html;
}
?>
