<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/presentation");

function requeteur_csv_champ($champ) {
    $champ = preg_replace(',[\s]+,', ' ', $champ);
    $champ = str_replace(',",', '""', $champ);
    return '"'.$champ.'"';
}


function requeteur_csv_ligne($ligne, $delim = ',') {
    return join($delim, array_map('requeteur_csv_champ', $ligne))."\r\n";
}

function exec_requeteursql_export_csv_dist(){
	
	// si pas autorise : message d'erreur
	if (!autoriser('voir', 'sqlrequete')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	$id_sql_requete = _request('id_sql_requete');
	$tDelim = array(1=>',',2=>';',3=>"\t");
	$delim = $tDelim[_request('delim')];
	
	$result = sql_select(array('titre','requetesql'),'spip_sql_requetes',"id_sql_requete = $id_sql_requete");
	if($res = sql_fetch($result)) {
		$sql = $res['requetesql'];
		$titre = $res['titre'];
		
		$output = '';
		//$output = amengees_csv_ligne($tablefield,$delim);
		//$tablefield = array_flip($tablefield);
		
		$result = sql_query($sql);
		$bFirstLine = true;
		while ($row=sql_fetch($result)){
			if($bFirstLine) {
				$output .= requeteur_csv_ligne(array_keys($row),$delim);
				$bFirstLine = false;
			}
			$output .= requeteur_csv_ligne($row,$delim);
		}

		$charset = $GLOBALS['meta']['charset'];

		$filename = preg_replace(',[^-_\w]+,', '_', translitteration(textebrut(typo($titre))));

		// Excel ?
		if ($delim == ',') 
		{
			$extension = 'csv';
		} 
		else
		{
			// Extension 'csv' si delim = ';' (et pas forcément 'xls' !)
			if ($delim == ';') 
			{ 
				$extension = 'csv'; 
			}
			else 
			{ 
				$extension = 'xls';
			}
			# Excel n'accepte pas l'utf-8 ni les entites html... on fait quoi?
			include_spip('inc/charsets');
			$output = unicode2charset(charset2unicode($output), 'iso-8859-1');
			$charset = 'iso-8859-1';
		}

		Header("Content-Type: text/comma-separated-values; charset=$charset");
		Header("Content-Disposition: attachment; filename=$filename.$extension");
		Header("Content-Length: ".strlen($output));
		echo $output;
		exit;
	}
}
?>
