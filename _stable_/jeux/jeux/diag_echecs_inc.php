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

  $board = imagecreate($board_size,$board_size);
  $light_color = imagecolorallocate($board,$light[0],$light[1],$light[2]);
  imagefill($board,0,0,$light_color);
  $square = imagecreate($taille,$taille);
  $dark_color = imagecolorallocate($square,$dark[0],$dark[1],$dark[2]);
  imagefill($square,0,0,$dark_color);
  for ($i=0 ; $i<8 ; $i++) 
    for ($j=0 ; $j<8 ; $j++) 
      if (($i+$j) & 1) imagecopy($board,$square,$i*$taille,$j*$taille,0,0,$taille,$taille);
  $chessboard = imagecreate($board_size+2*$bordure,$board_size+2*$bordure);
  $black_color = imagecolorallocate($chessboard,0,0,0);
  imagefill($chessboard,0,0,$black_color);
  imagecopy($chessboard,$board,$bordure,$bordure,0,0,$board_size,$board_size);
  return $chessboard;
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

  $url = jeux_config('base_url').$side.$diag_echecs_globales['english'][$name].jeux_config('img_suffix');
  $img_create = jeux_config('img_create');
  $file = $img_create($url);
  if (!$flip) {
    imagecopy($chessboard,$file,($diag_echecs_globales['letter2number'][$letter]-1)*$taille+$bordure,(8-$number)*$taille+$bordure,0,0,$taille,$taille);
  } else {
    imagecopy($chessboard,$file,(8-$diag_echecs_globales['letter2number'][$letter])*$taille+$bordure,($number-1)*$taille+$bordure,0,0,$taille,$taille);
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

  if ((!(ereg("[a-h]",$letter,$match1))) or (!(ereg("[1-8]",$number,$match2))))
	die("Erreur dans la syntaxe (diag_echecs_hilite_square)!");

	$color = $diag_echecs_globales[$hilite];
	$square = imagecreate($taille,$taille);
	$hilite_color = imagecolorallocate($square,$color[0],$color[1],$color[2]);
	imagefill($square,0,0,$hilite_color);
  if (!$flip) {
	imagecopy($chessboard,$square,($diag_echecs_globales['letter2number'][$letter]-1)*$taille+$bordure,(8-$number)*$taille+$bordure,0,0,$taille,$taille);
  } else {
	imagecopy($chessboard,$square,(8-$diag_echecs_globales['letter2number'][$letter])*$taille+$bordure,($number-1)*$taille+$bordure,0,0,$taille,$taille);
  }

}

?>
