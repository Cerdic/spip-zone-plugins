<?php
function csv2spip_analyse($contenu)
{
	$Tchamps = $err = array();
	$Tref_champs = array('nom' => 'login',
			     'prenom' => 'prenom', 
			     'pass' => 'pass', 
			     'bio' => 'bio', 
			     'groupe' => 'groupe',
			     'ss_groupe' => 'ss_groupe',
			     'pseudo_spip' => 	'pseudo_spip',
			     'email' => 'email');
        list($head, $csv) = $contenu;
	// traiter la premiere ligne pour avoir les noms des champs
	// avec leur position dans les colonnes
	foreach ($head as $champ_ec) {
		$champ_ec = strtolower(trim(str_replace('"', '', $champ_ec)));
		if ($k = array_search($champ_ec, $Tref_champs)) {
		    $Tchamps[$champ_ec]=$k;
		} elseif ($champ_ec) 
		    $err[]= _L("Champ inconnu @champ@", 
			       array('champ' => $champ_ec));
	}
	$n = count($Tchamps);
	$l = 1;

	foreach ($csv as $k => $cols) {
		if (($m=count($cols)) != $n)  {
			$err[]= _L("ligne @ligne@: @faute@ champs sur @bon@: @source@",
				   array('ligne' => $k, 'faute' => $m, 'bon' =>$n, 'source' => '"' . join('" "', $cols) . '"'));
			continue;
		}
		$i = 0;
		$insert = array();
		foreach($Tchamps as $v) 
		  $insert[$v] = trim(str_replace('"', '', $cols[$i++]));
		if (isset($insert['pass']) AND !$insert['pass'])
		  $insert['pass'] = $insert['nom'];
		$csv[$k] = $insert;
		$l++;
	}
	return $err ? join("<br />", $err) : $csv;
}

// fonction analyse_csv du noyau de SPIP2.3 legerement modifiee.

function csv2spip_normalise($file)
{
	$t = file_get_contents($file);
	$virg = substr_count($t, ',');
	$pvirg = substr_count($t, ';');
	$tab = substr_count($t, "\t");
	if ($virg > $pvirg)
		{ $sep = ','; $hs = '&#44;';}
	else	{ $sep = ';'; $hs = '&#59;'; $virg = $pvirg;}
	if ($tab > $virg) {$sep = "\t"; $hs = "\t";}

	$t = preg_replace('/\r?\n/', "\n", $t);
	// un separateur suivi de 3 guillemets attention !
	// attention au ; suceptible d'etre confondu avec un separateur
	// on substitue un # et on remplacera a la fin
	$t = preg_replace("/([\n$sep])\"\"\"/",'\\1"&#34#',$t);
	$t = str_replace('""','&#34#',$t);
	preg_match_all('/"[^"]*"/', $t, $r);
	foreach($r[0] as $cell)
		$t = str_replace($cell,
			str_replace($sep, $hs,
				str_replace("\n", "<br />",
					    substr($cell,1,-1))),
			$t);
	list($entete, $corps) = explode("\n",$t,2);
	$caption = '';
	// sauter la ligne de tete formee seulement de separateurs
	if (substr_count($entete, $sep) == strlen($entete)) {
		list($entete, $corps) = explode("\n",$corps,2);
	}
	// si une seule colonne, en faire le titre
	if (preg_match("/^([^$sep]+)$sep+\$/", $entete, $l)) {
			$caption = "\n||" .  $l[1] . "|";
			list($entete, $corps) = explode("\n",$corps,2);
	}
	// si premiere colonne vide, le raccourci doit quand meme produire <th...
	if ($entete[0] == $sep) $entete = ' ' . $entete;

	$lignes = explode("\n", $corps);

	// retrait des lignes vides finales
	while(count($lignes) > 0
	AND preg_match("/^$sep*$/", $lignes[count($lignes)-1]))
	  unset($lignes[count($lignes)-1]);
	//  calcul du  nombre de colonne a chaque ligne
	$nbcols = array();
	$max = $mil = substr_count($entete, $sep);
	foreach($lignes as $k=>$v) {
	  if ($max <> ($nbcols[$k]= substr_count($v, $sep))) {
	    if ($max > $nbcols[$k])
	      $mil = $nbcols[$k];
	    else { $mil = $max; $max = $nbcols[$k];}
	  }
	}
	// Si pas le meme nombre, cadrer au nombre max
	if ($mil <> $max)
	  foreach($nbcols as $k=>$v) {
	    if ($v < $max) $lignes[$k].= str_repeat($sep, $max-$v);
	    }
	// et retirer les colonnes integralement vides
	while(true) {
	  $nbcols =  ($entete[strlen($entete)-1]===$sep);
	  foreach($lignes as $v) $nbcols &= ($v[strlen($v)-1]===$sep);
	  if (!$nbcols) break;
	  $entete = substr($entete,0,-1);
	  foreach($lignes as $k=>$v) $lignes[$k] = substr($v,0,-1);
	}

	foreach($lignes as &$l) {
	  $l = explode($sep, str_replace('<br />', "\n",$l));
	}
	return array(explode($sep, $entete), $lignes);
}
?>
