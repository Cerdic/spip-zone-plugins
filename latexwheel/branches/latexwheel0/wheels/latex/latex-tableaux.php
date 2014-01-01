<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

//include_spip('inc/texte');

/**
 * Callback de traitement de chaque tableau
 *
 * @param array $m
 * @return string
 */
function latex_replace_tableaux($m){
	$m[2] = str_replace("|\r","|",$m[2]);
	return $m[1] . latex_traiter_tableau($m[2]) . $m[3];
}

function latex_traiter_tableau($bloc) {
	// id "unique" pour les id du tableau
	$tabid = substr(md5($bloc),0,4);
	// Decouper le tableau en lignes
	preg_match_all(',([|].*)[|]\n,UmsS', $bloc, $regs, PREG_PATTERN_ORDER);
	$lignes = array();
	$debut_table = $summary = '';
	$l = 0;
	$numeric = true;

	// Traiter chaque ligne
	$reg_line1 = ',^(\|(' . _RACCOURCI_TH_SPAN . '))+$,sS';
	$reg_line_all = ',^('  . _RACCOURCI_TH_SPAN . ')$,sS';
	$hc = $hl = array();
	foreach ($regs[1] as $ligne) {
		$l ++;

		// Gestion de la premiere ligne :
		if ($l == 1) {
		// - <caption> et summary dans la premiere ligne :
		//   || caption | summary || (|summary est optionnel)
			if (preg_match(',^\|\|([^|]*)(\|(.*))?$,sS', rtrim($ligne,'|'), $cap)) {
				$l = 0;
				if ($caption = trim($cap[1]))
					$debut_table .= "\caption/debut".$caption."/fin\\\\\n";
				// pas d'équivalent à l'attribut summary
			}
		// - <thead> sous la forme |{{titre}}|{{titre}}|
		//   Attention thead oblige a avoir tbody
			else if (preg_match($reg_line1,	$ligne, $thead)) {
			  	preg_match_all('/\|([^|]*)/S', $ligne, $cols);
				$ligne='';$cols= $cols[1];
				$colspan=1;
				for($c=count($cols)-1; $c>=0; $c--) {
					$attr='';
					if($cols[$c]=='<') {
					  $colspan++;
					} else {
					  if($colspan>1) {
						$attr= " colspan='$colspan'";
						$colspan=1;
					  }
					  $ligne= "$cols[$c]/sepcel$ligne";
						$hc[$c] = "id{$tabid}_c$c"; // pour mettre dans les headers des td
					}
				}

				$debut_table .= "".
					$ligne."\\\\\n\endhead\n";
				$l = 0;
			}
		}

		// Sinon ligne normale
		if ($l) {
			// Gerer les listes a puce dans les cellules
			if (strpos($ligne,"\n-*")!==false OR strpos($ligne,"\n-#")!==false)
				$ligne = appliquer_regles_wheel($ligne,array('latex/latex-listes.yaml'));


			// tout mettre dans un tableau 2d
			preg_match_all('/\|([^|]*)/S', $ligne, $cols);
			$lignes[]= $cols[1];
		}
	}

	// maintenant qu'on a toutes les cellules
	// on prepare une liste de rowspan par defaut, a partir
	// du nombre de colonnes dans la premiere ligne.
	// Reperer egalement les colonnes numeriques pour les cadrer a droite
	$rowspans = $numeric = array();
	$n = count($lignes[0]);
	$k = count($lignes);
	// Si on va veur le HTML, on distingue les colonnes numeriques a point ou a virgule,
	// pour les alignements eventuels sur "," ou "."
	// pour le moment on ne gère pas encore cela, car il faut que je regarde comme faire en latex
	$numeric_class = array('.'=>'point',','=>'virgule');
	for($i=0;$i<$n;$i++) {
	  $align = true;
	  for ($j=0;$j<$k;$j++) {
		  $rowspans[$j][$i] = 1;
			if ($align AND preg_match('/^\d+([.,]?)\d*$/', trim($lignes[$j][$i]), $r)){
				if ($r[1])
					$align = $r[1];
			}
			else
				$align = '';
	  }
	  $numeric[$i] = $align ? (" class='numeric ".$numeric_class[$align]."'") : '';
	}

	for ($j=0;$j<$k;$j++) {
		if (preg_match($reg_line_all, $lignes[$j][0])) {
			$hl[$j] = "id{$tabid}_l$j"; // pour mettre dans les headers des td
		}
		else
			unset($hl[0]);
	}
	if (!isset($hl[0]))
		$hl = array(); // toute la colonne ou rien

	// et on parcourt le tableau a l'envers pour ramasser les
	// colspan et rowspan en passant

	$html = '';

	for($l=count($lignes)-1; $l>=0; $l--) {
		$cols= $lignes[$l];
		$colspan=1;
		$ligne='';
		for($c=count($cols)-1; $c>=0; $c--) {
			$attr= $numeric[$c]; 
			$cell = trim($cols[$c]);
			if($cell=='<') {
			  $colspan++;

			} elseif($cell=='^') {
			  	$rowspans[$l-1][$c]+=$rowspans[$l][$c];
				$ligne= "\n/sepcel".$ligne; 	
			} else {


			  $b = ($c==0 AND isset($hl[$l]))?'th':'td';
				$h = (isset($hc[$c])?$hc[$c]:'').' '.(($b=='td' AND isset($hl[$l]))?$hl[$l]:'');
				//fusion des lignes
				if(($x=$rowspans[$l][$c])>1) {
					$ligne= "\n\multirow/debut$x/fin/debut*/fin/debut".$cols[$c]."/fin/sepcel".$ligne;
			  	}
				// fusion des colonnes
				elseif($colspan>1) {
					$largeur = round($colspan * round(1/$n,2,PHP_ROUND_HALF_DOWN),2,PHP_ROUND_HALF_DOWN);
					$ligne= "\n\multicolumn/debut$colspan/fin/debutp/debut$largeur".'\textwidth'."/fin/fin/debut".$cols[$c]."/fin/sepcel".$ligne;
					$colspan=1;
				}
				else{
			  		$ligne= "\n".$cols[$c]."/sepcel".$ligne;
				}
			}
		}

		// ligne complete
		$html = "$ligne\\\\\n$html";
	}
	
	
	// calcul des alignements de tableaux : par défaut, p{1/cellule*\textwith}
	$largeur_cellule = round(1/$n,2,PHP_ROUND_HALF_DOWN);	// par défaut, taille de colonne constante
	$alignement = '/debut'.str_repeat(p.'/debut'.$largeur_cellule.'\textwidth/fin',$n).'/fin';
	
	// en latex, contrairement au html, on ne marque pas la fin de la dernière cellule d'une ligne
	$debut_table 	= str_replace('/sepcel\\','\\',$debut_table);
	$html 			= str_replace('/sepcel\\','\\',$html);
	
	return "\n\n\begin/debutlongtable/fin$alignement\n"
		. $debut_table
		. $html
		. "\\end/debutlongtable/fin\n\n";
}