<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
// transforme la notation FEN en notation classique
function FEN2classic($fen) {
  global $diag_echecs_globales;
  $w = "b:";
  $b = "n:";

  $fen = explode(" ", $fen);
  /* FEN complete = 6, abregee = 1 */
  if (count($fen) != 6  && count($fen) != 1)
    die("Erreur dans la syntaxe dans la position FEN (nb de champs = count($fen)) !");

  $side = $fen[1];
  $fen = explode("/", $fen[0]);
  if (count($fen) != 8)
    die("Erreur dans la syntaxe dans la position FEN (nb de ligne) !");

  for ($i = 0; $i < 8; $i++) {
    $fen[$i]=trim($fen[$i]);
    for ($j = 0, $col = 0; $j < strlen($fen[$i]), $col < 8; $j++ ) {
      if (preg_match(',[KQBNRP],', $fen[$i][$j])) {
        if (strlen($w)>2) $w .= ",";
        $w .= ($diag_echecs_globales['english2french'][strtolower($fen[$i][$j])] . ($diag_echecs_globales['colonnes'][$col++]) . (8 - $i));
      }
      elseif (preg_match(',[kqbnrp],', $fen[$i][$j])) {
        if (strlen($b)>2) $b .= ",";
        $b .= ($diag_echecs_globales['english2french'][$fen[$i][$j]] . ($diag_echecs_globales['colonnes'][$col++]) . (8 - $i));
      }
      elseif (preg_match(',[1-8],', $fen[$i][$j]))
        $col += $fen[$i][$j] ;
      else
        die("Erreur dans la syntaxe dans la position FEN - caractere : $w/$b - ".$fen[$i][$j]);
    }
  }
  if ($side == "w")
    return $w . '/' . $b;
  // ici c'est aux noirs de jouer, on retourne l'echiquier !
  return $w . '/' . $b . '/r' ;
}

// cree un echiquier vierge
function image_echiquier($flip) {
	// qq initialisations 
	global $jeux_couleurs;
	$light = $jeux_couleurs[jeux_config('blancs')];
	$dark = $jeux_couleurs[jeux_config('noirs')];
	$taille = intval(jeux_config('taille'));
	$bordure = intval(jeux_config('bordure'));
	$board_size = jeux_config('board_size');
	$nomfichier = jeux_config('plateau');
	
	if ($nomfichier && ($url = find_in_path('img/echiquiers/' . $nomfichier))){
		// ici une image d'echiquier est demandee
		if (!$flip) {
			$board = imagecreatefrompng($url);
		}
		else {			
			$url=str_replace('.png','2.png',$url);
			$board = imagecreatefrompng($url);
		}
		if(!$board)
			die("Erreur lors de la creation de l'image : img/echiquiers/$nomfichier");
		// recupere la largeur (on suppose l'image carree)
		list($board_size, $height) = getimagesize($url);
		jeux_config_set('board_size', $board_size);			

		// le nom du fichier peut contenir les coordonnees de l'origine entre parentheses
		// format a utiliser : monplato(12-12).png
		if(preg_match(',\((\d+)\-(\d+)\),', $nomfichier, $regs)) {
			jeux_config_set('xori', $regs[1]);
			jeux_config_set('yori', $regs[2]);		
			jeux_config_set('coords', 'non');	//évite a l'utilisateur de la faire	(plante si à oui)
		} else {
			jeux_config_set('xori', 0);
			jeux_config_set('yori', 0);		
		}		
	} else {
	  // pas d'image pour l'echiquier
	  $board = imagecreatetruecolor($board_size,$board_size);
	  $light_color = imagecolorallocate($board,$light[0],$light[1],$light[2]);
	  imagefill($board,0,0,$light_color);
	  $square = imagecreatetruecolor($taille,$taille);
	  $dark_color = imagecolorallocate($square,$dark[0],$dark[1],$dark[2]);
	  imagefill($square,0,0,$dark_color);
	  for ($i=0 ; $i<8 ; $i++) 
		for ($j=0 ; $j<8 ; $j++) 
		  if (($i+$j) & 1) imagecopy($board,$square,$i*$taille,$j*$taille,0,0,$taille,$taille);
	}
	// if ($bordure)	{ 
		// inutile autrement
		//permet de transformer l'image en truecolor si le plateau est en 256
		$chessboard = imagecreatetruecolor($board_size+2*$bordure,$board_size+2*$bordure);
		$black_color = imagecolorallocate($chessboard,0,0,0);
		imagefill($chessboard,0,0,$black_color);
		imagecopy($chessboard,$board,$bordure,$bordure,0,0,$board_size,$board_size);
		return $chessboard;
	//}
	//return $board;
}

// mets une piece sur l'echiquier
function diag_echecs_put_piece($chessboard,$side,$name,$square,$flip) {
	// qq initialisations 
	global $diag_echecs_globales;
	$suffix = jeux_config('img_suffix');
	$taille = intval(jeux_config('taille'));
	$bordure = intval(jeux_config('bordure'));
	$xori = jeux_config('xori');
	$yori = jeux_config('yori');

	$letter = $square[0]; // substr($square,0,1);
	$number = $square[1]; // substr($square,1,1);
	
	if (!preg_match(',[a-h],',$letter,$match1) || !preg_match(',[1-8],',$number,$match2))
		die("Erreur dans la syntaxe (diag_echecs_put_piece)!");

	$url = jeux_config('base_url').$side.$diag_echecs_globales['english'][$name].jeux_config('img_suffix');
	$img_create = jeux_config('img_create');
	$file = $img_create($url);
	if (!$flip)
		imagecopy($chessboard,$file,($diag_echecs_globales['letter2number'][$letter]-1)*$taille+$bordure+$xori,
			(8-$number)*$taille+$bordure+$yori,0,0,$taille,$taille);
	else
		imagecopy($chessboard,$file,(8-$diag_echecs_globales['letter2number'][$letter])*$taille+$bordure+$xori,
			($number-1)*$taille+$bordure+$yori,0,0,$taille,$taille);
}

// colore une case de l'echiquier
function diag_echecs_hilite_square($chessboard,$square,$hilite,$flip) {
	// qq initialisations 
	global $diag_echecs_globales;
	$img_create = jeux_config('img_create');
	$suffix = jeux_config('img_suffix');
	$taille = intval(jeux_config('taille'));
	$bordure = intval(jeux_config('bordure'));
	$board_size = jeux_config('board_size');
	$letter = substr($square,0,1);
	$number = substr($square,1,1);
	$xori = intval(jeux_config('xori'));
	$yori = intval(jeux_config('yori'));

	if (!preg_match(',[a-h],',$letter,$match1) || !preg_match(',[1-8],',$number,$match2))
		die("Erreur dans la syntaxe (diag_echecs_hilite_square)!");

	$color = $diag_echecs_globales[$hilite];
	$square = imagecreatetruecolor($taille,$taille);
	$hilite_color = imagecolorallocatealpha($square,$color[0],$color[1],$color[2],50);
	imagefill($square,0,0,$hilite_color);
	if (!$flip)
		imagecopy($chessboard,$square,($diag_echecs_globales['letter2number'][$letter]-1)*$taille+$bordure+$xori,
			(8-$number)*$taille+$bordure+$yori,0,0,$taille,$taille);
	else
		imagecopy($chessboard,$square,(8-$diag_echecs_globales['letter2number'][$letter])*$taille+$bordure+$xori,
			($number-1)*$taille+$bordure+$yori,0,0,$taille,$taille);
}

// trace une flèche colorée sur l'echiquier
function diag_echecs_hilite_line($image, $squares, $hilite ,$flip) {
	global $diag_echecs_globales;
	$taille = intval(jeux_config('taille'));
	$bordure = intval(jeux_config('bordure'));
	$xori = intval(jeux_config('xori'));
	$yori = intval(jeux_config('yori'));
	
	// epaisseur de la flèche
	switch($taille) {
		case 35 : $thick = 5; break;
		case 55 : $thick = 7; break;
		default : $thick = round($taille / 7);
	}
	
	// $squares est de la forme a1-g8
	list(,$xfrom,$yfrom,,$xto,$yto ) = preg_split('//', $squares); // str_split() mieux, mais on reste compatible PHP 4
	
	$color = $diag_echecs_globales[$hilite];
	$hilite_color = imagecolorallocatealpha($image,$color[0],$color[1],$color[2],50);
	
	if (!$flip) {
		$x1 = (($diag_echecs_globales['letter2number'][$xfrom]-1)*$taille+$bordure+$taille/2)-1+$xori;
		$y1 = ((8-$yfrom)*$taille+$bordure+$taille/2)-1+$yori;
		$x2 = (($diag_echecs_globales['letter2number'][$xto]-1)*$taille+$bordure+$taille/2)-1+$xori;
		$y2 = ((8-$yto)*$taille+$bordure+$taille/2)-1+$yori;
	} else {
		$x1 = ((8-$diag_echecs_globales['letter2number'][$xfrom])*$taille+$bordure+$taille/2)-1+$xori;
		$y1 = (($yfrom-1)*$taille+$bordure+$taille/2)-1+$yori;
		$x2 = ((8-$diag_echecs_globales['letter2number'][$xto])*$taille+$bordure+$taille/2)-1+$xori;
		$y2 = (($yto-1)*$taille+$bordure+$taille/2)-1+$yori;		
	}
  
	// cercle au début
	imagefilledellipse($image, $x1,$y1,$thick*2,$thick*2,$hilite_color); 	

	if ($thick == 1)
        return imageline($image, $x1, $y1, $x2, $y2, $hilite_color);

	$ep=round(2.5*$thick/2); //épaisseur totale de la pointe
	$lg=round(5*$thick/2);  //longeur de la pointe
    $t=round($thick / 2)-1;	 //demi épaisseur de la flèche
	
    if ($y1 == $y2) {// flèches horizontales
		if ($x1 < $x2) {//vers la droite
			$points = array(
				round($x1),round($y1+$t),
				round($x2-$lg),round($y2+$t),
				round($x2-$lg),round($y2+$ep),
				round($x2),round($y2),
				round($x2-$lg),round($y2-$ep),
				round($x2-$lg),round($y2-$t),	
				round($x1),round($y1-$t)
			);			
			return imagefilledpolygon($image, $points, 7, $hilite_color);   
		} else {//vers la gauche
			$points = array(
				round($x1),round($y1+$t),
				round($x2+$lg),round($y2+$t),
				round($x2+$lg),round($y2+$ep),
				round($x2),round($y2),
				round($x2+$lg),round($y2-$ep),
				round($x2+$lg),round($y2-$t),	
				round($x1),round($y1-$t)
			);			
			return imagefilledpolygon($image, $points, 7, $hilite_color);   	
		}
	}
	
	if ($x1 == $x2) {// flèches verticales
		if ($y1 < $y2) {//vers le bas
			$points = array(
				round($x1-$t),round($y1),
				round($x2-$t),round($y2-$lg),
				round($x2-$ep),round($y2-$lg),
				round($x2),round($y2),
				round($x2+$ep),round($y2-$lg),
				round($x2+$t),round($y2-$lg),
				round($x1+$t),round($y1)
			);			
			return imagefilledpolygon($image, $points, 7, $hilite_color);   
		} else {//vers le haut
			$points = array(
				round($x1-$t),round($y1),
				round($x2-$t),round($y2+$lg),
				round($x2-$ep),round($y2+$lg),
				round($x2),round($y2),
				round($x2+$ep),round($y2+$lg),
				round($x2+$t),round($y2+$lg),
				round($x1+$t),round($y1)
			);			
			return imagefilledpolygon($image, $points, 7, $hilite_color);   
		}
	}
	//autres flèches
	// utilisation des coordonnées polaires
	$pi=pi();
	$teta=atan(($y2-$y1)/($x2-$x1));
	// (x3,y3) est le point intermédiare pour contruire la pointe
	if ($x2>$x1) {
		$x3=$x2+$lg*cos($teta+$pi);
		$y3=$y2+$lg*sin($teta+$pi);
	} else {
		$x3=$x2-$lg*cos($teta+$pi);
		$y3=$y2-$lg*sin($teta+$pi);
	}
	
	$points = array(
		round($x1+$t*cos($teta+$pi/2)),round($y1+$t*sin($teta+$pi/2)),
		round($x3+$t*cos($teta+$pi/2)),round($y3+$t*sin($teta+$pi/2)),
		round($x3+$ep*cos($teta+$pi/2)),round($y3+$ep*sin($teta+$pi/2)),
		round($x2),round($y2),
		round($x3+$ep*cos($teta-$pi/2)),round($y3+$ep*sin($teta-$pi/2)),
		round($x3+$t*cos($teta-$pi/2)),round($y3+$t*sin($teta-$pi/2)),
		round($x1+$t*cos($teta-$pi/2)),round($y1+$t*sin($teta-$pi/2))
	);				
	return imagefilledpolygon($image, $points, 7, $hilite_color);   
	
}
?>
