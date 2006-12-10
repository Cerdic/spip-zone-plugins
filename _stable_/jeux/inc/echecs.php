<?php

# A lire :  http://www.spip-contrib.net/Afficher-des-diagrammes-d-echecs
#			http://www.iechecs.com/notation.htm

#---------------------------------------------------#
#  Plugin  : jeux , diagrammes d'echecs             #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#


/*************************************************/
/*   Auteur initial : Olivier Bouverot           */
/*          <webmaster@ajec-echecs.org>          */
/*                                               */
/*  Patch (prise en compte de la notation FEN) : */
/*                Patrice Pillot                 */
/*              <p.pillot@free.fr>               */
/*************************************************/

/*

Insere un diagramme de positions d’échecs dans vos articles !
-------------------------------------------------------------

balises du plugin : <jeux></jeux>
separateurs obligatoires : [diag_echecs]
separateurs optionnels   : [titre], [texte], [config]

Attention :
- La librairie GD doit être installée sur le serveur.

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[diag_echecs]
	B:Rg1,Dd1,Ta1,e1,Pa2,f6/N:Rb8,Pa6,f5
</jeux>
<jeux>
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
*/
require("diag_echecs_init.php");
require("diag_echecs_inc.php");

// creation du diagramme d'echecs
function calcul_diagramme_echecs($position, $indexJeux) {
	// qq initialisations
	global $diag_echecs_globales;
	$flip = jeux_config('flip');
	$fond = jeux_config('fond');
	$taille = intval(jeux_config('taille'));
	$bordure = intval(jeux_config('bordure'));
	$board_size = intval(jeux_config('board_size'));
	$font = intval(jeux_config('police'));
	$img = jeux_config('img_img');
	
	// dechiffre le code source du diagramme place dans $position
	// $position = "B:Rf1/N:Pb2,c3,d4,e5/SV:e4/SR:a2";
	// $position = "B:Rg1,Dd1,Ta1,e1,Pa2,f6/N:Rb8,Pa6,f5";
	// $position = "r1bqkbnr/pp2pppp/2n5/2p1P3/3p4/2P2N2/PP1P1PPP/RNBQKB1R";
	$c = preg_replace("/\s*[\r\n]+\s*/", '/', trim($position));
	//$tableau = split("/\t/", trim($position));
	//foreach ($tableau as $i=>$valeur) $tableau[$i] = preg_split('//', trim($valeur), -1, PREG_SPLIT_NO_EMPTY);

	//$position = ereg_replace("[\n\r]","",$position);
	
	/* l'heuristique est faible mais bon... */
	//if ( substr_count($position, "/") == 7 ) {
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
//echo '<br>table : '; print_r($table); echo '<br>';
	$fichier_dest = sous_repertoire(_DIR_VAR, "cache-jeux") . 'echiq_'.md5($position) . jeux_config('img_suffix');
	$image = "<img src=\"$fichier_dest\" alt=\"$position\" border=\"0\"/><br>\n";
	//if (file_exists($fichier_dest)) return $image;

	$chessboard = image_echiquier();

// *********************	
	for ($i=0 ; $i<count($table) ; $i++)  if ( $table[$i] == "r" ) $flip = true;
	
	for ($i=0 ; $i<count($table) ; $i++) {
	  $sub_table = split("[:,]",$table[$i]);
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

	/* Faut-il envoyer un en-tête (cas d'une image "nue"), ou l'image
est-elle destinée à être incluse dans une page Web ? Laisser la
variable à "true" dans le premier cas, la mettre à "false" dans le
second */
	/*	if ($hdr) header(jeux_config('img_header'); */
	
	if (jeux_config('coords')) {
	  $big_chessboard = imagecreate($board_size+2*$bordure+$taille,$board_size+2*$bordure+$taille);
	  $bg_color = imagecolorallocate($big_chessboard,$fond[0],$fond[1],$fond[2]);
	  imagecolortransparent($big_chessboard,$bg_color);
	  imagecopy($big_chessboard,$chessboard,$taille,0,0,0,$board_size+2*$bordure,$board_size+2*$bordure);
	  $width = imagefontwidth($font);
	  $height = imagefontheight($font);
	  $center = intval($taille/2);
	  for ($i=1 ; $i<=8 ; $i++) {
		$empty_coord = imagecreate($taille,$taille);
		$bg_color = imagecolorallocate($empty_coord,$fond[0],$fond[1],$fond[2]);
		imagecolortransparent($empty_coord,$bg_color);
		$font_color = imagecolorallocate($empty_coord,0,0,0);
		if (!$flip) {
		  imagechar($empty_coord,$font,($taille-$width)/2,($taille-$height)/2+$bordure,9-$i,$font_color);
		} else {
		  imagechar($empty_coord,$font,($taille-$width)/2,($taille-$height)/2+$bordure,$i,$font_color);
		}
		imagecopy($big_chessboard,$empty_coord,0,($i-1)*$taille,0,0,$taille,$taille);
	  }
	  for ($i=1 ; $i<=8 ; $i++) {
		$empty_coord = imagecreate($taille,$taille);
		$bg_color = imagecolorallocate($empty_coord,$fond[0],$fond[1],$fond[2]);
		imagecolortransparent($empty_coord,$bg_color);
		$font_color = imagecolorallocate($empty_coord,0,0,0);
		if (!$flip) {
		  imagechar($empty_coord,$font,($taille-$width)/2+$bordure,($taille-$height)/2,$diag_echecs_globales['number2letter'][$i],$font_color);
		} else {
		  imagechar($empty_coord,$font,($taille-$width)/2+$bordure,($taille-$height)/2,$diag_echecs_globales['number2letter'][9-$i],$font_color);
		}
		imagecopy($big_chessboard,$empty_coord,$i*$taille,8*$taille+2*$bordure,0,0,$taille,$taille);
	  }
	 $chessboard = $big_chessboard;
	}
	
	$img($chessboard, $fichier_dest);
	return $image;
}

// decode un diagramme d'echecs 
function jeux_diag_echecs($texte, $indexJeux) { 
	// qq initialisations
	$html = false;
	
	// decodage du texte
	$tableau = jeux_split_texte('diag_echecs', $texte);
	diag_echecs_config_default();
	diag_echecs_config_supplementaire();
	foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $html .= "<p class=\"jeux_titre diag_echecs_titre\">{$tableau[$i+1]}</p>";
	  elseif ($valeur==_JEUX_diag_echecs) $html .= calcul_diagramme_echecs($tableau[$i+1], $indexJeux);
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	}
	
	return $html;
}
?>