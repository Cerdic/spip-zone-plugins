<?php

// transforme la notation FEN en notation classique
function FEN2classic($fen) {
  global $diag_echecs_globales;
  $w = "b:";
  $b = "n:";

  $fen = explode(" ", $fen);
  /* FEN complete = 6, abregee = 1 */
  if ( count($fen) != 6  && count($fen) != 1 ) {
    die("Erreur dans la syntaxe dans la position FEN (nb de champs = count($fen)) !");
  }
  $side = $fen[1];
  $fen = explode("/", $fen[0]);
  if ( count($fen) != 8  ) {
    die("Erreur dans la syntaxe dans la position FEN (nb de ligne) !");
  }

  for ($i = 0; $i < 8; $i++) {
    $fen[$i]=trim($fen[$i]);
    for ($j = 0, $col = 0; $j < strlen($fen[$i]), $col < 8; $j++ ) {
      if ( ereg("[KQBNRP]", $fen[$i][$j] ) ) {
        if (strlen($w)>2) {
          $w.=",";
        }
        $w .= ($diag_echecs_globales['english2french'][strtolower($fen[$i][$j])] . ($diag_echecs_globales['colonnes'][$col++]) . (8 - $i));
      }
      elseif ( ereg("[kqbnrp]", $fen[$i][$j] ) ) {
        if (strlen($b)>2) {
          $b.=",";
        }
        $b .= ($diag_echecs_globales['english2french'][$fen[$i][$j]] . ($diag_echecs_globales['colonnes'][$col++]) . (8 - $i));
      }
      elseif ( ereg("[1-8]", $fen[$i][$j] ) ) {
        $col += $fen[$i][$j] ;
     } else {
      die("Erreur dans la syntaxe dans la position FEN - caractere : $w/$b - ".$fen[$i][$j]);
      } 
    }
  }
  if ($side == "w") {
    return ($w . "/" . $b);
  } else {
    /* 'est aux noirs de jouer on retourne l'echiquier ! */
    return ($w . "/" . $b . "/r" );
  }
}

// cree un echiquier vierge
function image_echiquier() {
	// qq initialisations 
	global $jeux_couleurs;
	$light = $jeux_couleurs[jeux_config('blancs')];
	$dark = $jeux_couleurs[jeux_config('noirs')];
	$taille = intval(jeux_config('taille'));
	$bordure = intval(jeux_config('bordure'));
	$board_size = intval(jeux_config('board_size'));

	
	// ***************** Ouvre une image pour l'échiquier *******************
	if (strlen(jeux_config('plateau'))>1 )	{
	// si une image d'échiqier est utilisé
	$nomfichier=jeux_config('plateau');
	$url = _DIR_PLUGIN_JEUX.'img/echiquiers/'.$nomfichier;
	if (file_exists($url)) {
		$board=imagecreatefrompng($url);
		if(!$board) { die("Erreur lors de l'ouverture du fichier : ".jeux_config('plateau')); }
		list($width, $height) = getimagesize($url); //récupère la largeur on suppose l'image carrée
		$board_size = $width;
		jeux_config_set('board_size', $width);			
		if(strpos($nomfichier,'(')<>0) { //si le nom du fichier contient les coordonnées de l'origine
			$xori=substr($nomfichier,strpos($nomfichier,'(')+1,strpos($nomfichier,'-')-strpos($nomfichier,'(')-1);
			$yori=substr($nomfichier,strpos($nomfichier,'-')+1,strpos($nomfichier,')')-strpos($nomfichier,'-')-1);
			jeux_config_set('xori', $xori);
			jeux_config_set('yori', $yori);		
		} else {
			jeux_config_set('xori', '0');
			jeux_config_set('yori', '0');		
		}		
	}
	
	

  }
	else {
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
	if ($bordure<>0)	{ //inutile autrement
		$chessboard = imagecreatetruecolor($board_size+2*$bordure,$board_size+2*$bordure);
		$black_color = imagecolorallocate($chessboard,0,0,0);
		imagefill($chessboard,0,0,$black_color);
		imagecopy($chessboard,$board,$bordure,$bordure,0,0,$board_size,$board_size);
		return $chessboard;
	}
	else return $board;
	
  
}

// mets une piece sur l'echiquier
function diag_echecs_put_piece($chessboard,$side,$name,$square,$flip) {
	// qq initialisations 
	global $diag_echecs_globales;
	$suffix = jeux_config('img_suffix');
	$taille = intval(jeux_config('taille'));
	$bordure = intval(jeux_config('bordure'));
	$letter = $square[0];//substr($square,0,1);
	$number = $square[1];//substr($square,1,1);
	
  if ((!(ereg("[a-h]",$letter,$match1))) or (!(ereg("[1-8]",$number,$match2))))
	die("Erreur dans la syntaxe (diag_echecs_put_piece)!");
	
	$xori=intval(jeux_config('xori'));
	$yori=intval(jeux_config('yori'));

  $url = jeux_config('base_url').$side.$diag_echecs_globales['english'][$name].jeux_config('img_suffix');
  $img_create = jeux_config('img_create');
  $file = $img_create($url);
  if (!$flip) {
    imagecopy($chessboard,$file,($diag_echecs_globales['letter2number'][$letter]-1)*$taille+$bordure+$xori,(8-$number)*$taille+$bordure+$yori,0,0,$taille,$taille);
  } else {
    imagecopy($chessboard,$file,(8-$diag_echecs_globales['letter2number'][$letter])*$taille+$bordure+$xori,($number-1)*$taille+$bordure+$yori,0,0,$taille,$taille);
  }
}

// colore une case de l'echiquier
function diag_echecs_hilite_square($chessboard,$square,$hilite,$flip) {
	// qq initialisations 
	global $diag_echecs_globales;
	$img_create = jeux_config('img_create');
	$suffix = jeux_config('img_suffix');
	$taille = intval(jeux_config('taille'));
	$bordure = intval(jeux_config('bordure'));
	$board_size = intval(jeux_config('board_size'));
	$letter = substr($square,0,1);
	$number = substr($square,1,1);
	
	$xori=intval(jeux_config('xori'));
	$yori=intval(jeux_config('yori'));

  if ((!(ereg("[a-h]",$letter,$match1))) or (!(ereg("[1-8]",$number,$match2))))
	die("Erreur dans la syntaxe (diag_echecs_hilite_square)!");

	$color = $diag_echecs_globales[$hilite];
	$square = imagecreatetruecolor($taille,$taille);
	$hilite_color = imagecolorallocatealpha($square,$color[0],$color[1],$color[2],50);
	imagefill($square,0,0,$hilite_color);
  if (!$flip) {
	imagecopy($chessboard,$square,($diag_echecs_globales['letter2number'][$letter]-1)*$taille+$bordure+$xori,(8-$number)*$taille+$bordure+$yori,0,0,$taille,$taille);
  } else {
	imagecopy($chessboard,$square,(8-$diag_echecs_globales['letter2number'][$letter])*$taille+$bordure+$xori,($number-1)*$taille+$bordure+$yori,0,0,$taille,$taille);
  }

}
// trace une ligne colorée sur l'échiquier
function diag_echecs_hilite_line($image, $squares, $hilite ,$flip)
{    
	global $diag_echecs_globales;
	$taille = intval(jeux_config('taille'));
	$bordure = intval(jeux_config('bordure'));
	
	//épaisseur de la ligne
	$thick=round($taille/7);
	if ($taille==35) {$thick= 5;};
	if ($taille==55) {$thick= 7;};	 
	
	//$squares est de la forme a1-g8
	$xfrom = substr($squares,0,1);
	$yfrom = substr($squares,1,1);	
	$xto = substr($squares,3,1);
	$yto = substr($squares,4,1);
	
	$xori=intval(jeux_config('xori'));
	$yori=intval(jeux_config('yori'));
	
	$color = $diag_echecs_globales[$hilite];
	$hilite_color = imagecolorallocatealpha($image,$color[0],$color[1],$color[2],50);
	
	if (!$flip) {
		$x1=round(($diag_echecs_globales['letter2number'][$xfrom]-1)*$taille+$bordure+$taille/2)-1+$xori;
		$y1=round((8-$yfrom)*$taille+$bordure+$taille/2)-1+$yori;
		$x2=round(($diag_echecs_globales['letter2number'][$xto]-1)*$taille+$bordure+$taille/2)-1+$xori;
		$y2=round((8-$yto)*$taille+$bordure+$taille/2)-1+$yori;
  } else {
		$x1=round((8-$diag_echecs_globales['letter2number'][$xfrom])*$taille+$bordure+$taille/2)-1+$xori;
		$y1=round(($yfrom-1)*$taille+$bordure+$taille/2)-1+$yori;
		$x2=round((8-$diag_echecs_globales['letter2number'][$xto])*$taille+$bordure+$taille/2)-1+$xori;
		$y2=round(($yto-1)*$taille+$bordure+$taille/2)-1+$yori;		
  }
  
	// cercle aux extrémités en attendant une vraie flèche
	imagefilledellipse($image, $x1,$y1,$thick*2,$thick*2,$hilite_color); 	
	imagefilledellipse($image, $x2,$y2,$thick*2,$thick*2,$hilite_color); 	
  
    if ($thick == 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $hilite_color);
    }
    $t = round($thick / 2)-1 ;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $hilite_color);
    }
	// alors là, bonjour les équations de droites !
	$k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
	// dès que je comprends j'ajoute 3 points pour faire une flèche ;-)
    $points = array(
        round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
        round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
        round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),		
        round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a)    		
    );	
    return imagefilledpolygon($image, $points, 4, $hilite_color);   
}
?>
