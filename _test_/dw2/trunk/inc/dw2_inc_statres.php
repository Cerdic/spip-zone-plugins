<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| functions  : statistiques abonnes
+--------------------------------------------+
*/


//
//
//
function totaux_restreint_stats($prdd,$prdf) {
	
	if($prdf=='--') { $wheredate="date='$prdd'"; }
	else { $wheredate = "date BETWEEN '$prdd' AND '$prdf'"; }
	
	$qtd=sql_select("COUNT(id_doc) as nb_telech, id_auteur",
					"spip_dw2_stats_auteurs",
					$wheredate,
					"id_auteur");
	$tot_auteur=sql_count($qtd);

	while($ra=sql_fetch($qtd)) {
		$tbl_tel[]=$ra['nb_telech'];
	}
	if($tot_auteur>0) { // <-- eviter erreur array_sum !
		$tot_telech=array_sum($tbl_tel);
		
		$qtf=sql_select("COUNT(DISTINCT id_doc) as f",
						"spip_dw2_stats_auteurs",
						$wheredate);
		while($rf=sql_fetch($qtf)) {
			$tot_fichier=$rf['f'];
		}
	}

	return $a=array($tot_auteur,$tot_telech,$tot_fichier);
}



function alpha_restreint_item($prdd,$prdf,$table) {
	
	if($prdf=='--') { $wheredate="ds.date='$prdd'"; }
	else { $wheredate = "ds.date BETWEEN '$prdd' AND '$prdf'"; }
	
	if($table=='auteurs') {
	$rq=sql_select("sa.nom ",
					"spip_dw2_stats_auteurs AS ds LEFT JOIN spip_auteurs AS sa ON ds.id_auteur = sa.id_auteur ",
					$wheredate ,
					"ds.id_auteur",
					"sa.nom");
	}
	if($table=='documents') {
	$rq=sql_select("sd.nom ",
					"spip_dw2_stats_auteurs AS ds LEFT JOIN spip_dw2_doc AS sd ON ds.id_doc = sd.id_document ",
					$wheredate,
					"ds.id_doc",
					"sd.nom");
	}
	
	// prepa table des prem lettres + compteur
	$gen_ltt=array();
	while ($row=sql_fetch($rq)) {
		$gen_ltt[]=strtoupper(substr($row['nom'],0,1));
	}

	// calcul tableau du tri-alphabet
	reset($gen_ltt);
	$tbl_ltt=array();
	foreach($gen_ltt as $ltt) {
			if($ltt != $ltt_prec) {
				$tbl_ltt[$ltt] = 1;
			} else {
				$tbl_ltt[$ltt]++;
			}
			$ltt_prec = $ltt;
	}
	return $tbl_ltt;
}


function select_premiere_date() {
	$row=sql_fetsel("DATE_FORMAT(MIN(date),'%d/%m/%Y') as prem",
					"spip_dw2_stats_auteurs",
					"date");
	return $row['prem'];	
}

?>