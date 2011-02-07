<?php
function csv2spip_insert($csv)
{
	$Tchamps = array();
	$Tref_champs = array('nom' => 'login',
			     'prenom' => 'prenom', 
			     'mdp' => 'pass', 
			     'groupe' => 'groupe',
			     'ss_groupe' => 'ss_groupe',
			     'pseudo_spip' => 	'pseudo_spip',
			     'mel' => 'email');
        $head = array_shift($csv);
	// traiter la première ligne pour récupérer les noms des champs
	// avec leur position dans les colonnes
	foreach (preg_split('/\s*;\s*/', $head) as $champ_ec) {
		$champ_ec = strtolower(trim(str_replace('"', '', $champ_ec)));
		if ($k = array_search($champ_ec, $Tref_champs)) {
		    $Tchamps[$champ_ec]=$k;
		} elseif ($champ_ec) {echo "champ inconnu '$champ_ec'";exit;}
	}
	$n = count($Tchamps)+1;
	$l = 1;
	$err = array();
	foreach ($csv as $k => $ligne) {
		$cols = preg_split('/\s*;\s*/', $ligne);
		if (($m=count($cols)) != $n)  {
		  if (trim($ligne)) $err[] ="ligne $l: $m champs sur $n ";
		  continue;}
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
	  // Fichier correct, on y va
	  foreach($csv as $insert) sql_insertq('spip_tmp_csv2spip', $insert);
	return $err;
}

?>
