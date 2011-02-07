<?php
function csv2spip_insert($csv)
{
	$Tchamps = $err = array();
	$Tref_champs = array('nom' => 'login',
			     'prenom' => 'prenom', 
			     'mdp' => 'pass', 
			     'groupe' => 'groupe',
			     'ss_groupe' => 'ss_groupe',
			     'pseudo_spip' => 	'pseudo_spip',
			     'mel' => 'email');
        $head = array_shift($csv);
	// traiter la premiere ligne pour avoir les noms des champs
	// avec leur position dans les colonnes
	foreach (preg_split('/\s*;\s*/', $head) as $champ_ec) {
		$champ_ec = strtolower(trim(str_replace('"', '', $champ_ec)));
		if ($k = array_search($champ_ec, $Tref_champs)) {
		    $Tchamps[$champ_ec]=$k;
		} elseif ($champ_ec) 
		    $err[]= _L("Champ inconnu @champ@", 
			       array('champ' => $champ_ec));
	}
	$n = count($Tchamps)+1;
	$l = 1;

	foreach ($csv as $k => $ligne) {
		$cols = preg_split('/\s*;\s*/', $ligne);
		if (($m=count($cols)) != $n)  {
			if (trim($ligne))
				$err[]= _L("ligne @ligne@: @faute@ champs sur @bon@.",
					   array('ligne' => $l, 'faute' => $m, 'bon' =>$n));
			continue;
		}
		$i = 0;
		$insert = array();
		foreach($Tchamps as $k => $v) 
		  $insert[$v] = trim(str_replace('"', '', $cols[$i++]));
		if (isset($insert['mdp']) AND !$insert['mdp']) 
		  $insert['mdp'] = $insert['nom'];
		$csv[$k] = $insert;
		$l++;
	}
	if (!$err)
	  // Fichier correct, on y va, en ignorant les lignes vides rencontrees
	  foreach($csv as $insert) 
	    if (is_array($insert)) sql_insertq('spip_tmp_csv2spip', $insert);
	return $err;
}

function csv2spip_normalise($file)
{
  $lignes = file($file);
  $prev = '';
  for($k=0;;) {
    if (!(substr_count($lignes[$k], '"') %2))
      $k++;
    else {
      $lignes[$k] .= $lignes[$k+1];
      unset($lignes[$k+1]);
    }
    if ($k > count($lignes)) break;
  }
  return $lignes;
}
?>
