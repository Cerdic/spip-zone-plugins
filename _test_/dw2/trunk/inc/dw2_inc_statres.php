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
	
	$qtd=spip_query("SELECT COUNT(id_doc) as nb_telech, id_auteur 
					FROM spip_dw2_stats_auteurs 
					WHERE $wheredate 
					GROUP BY id_auteur");
	$tot_auteur=spip_num_rows($qtd);

	while($ra=spip_fetch_array($qtd)) {
		$tbl_tel[]=$ra['nb_telech'];
	}
	if($tot_auteur>0) { // <-- eviter erreur array_sum !
		$tot_telech=array_sum($tbl_tel);
		
		$qtf=spip_query("SELECT COUNT(DISTINCT id_doc) as f 
					FROM spip_dw2_stats_auteurs 
					WHERE $wheredate");
		while($rf=spip_fetch_array($qtf)) {
			$tot_fichier=$rf['f'];
		}
	}

	return $a=array($tot_auteur,$tot_telech,$tot_fichier);
}



function alpha_restreint_item($prdd,$prdf,$table) {
	
	if($prdf=='--') { $wheredate="ds.date='$prdd'"; }
	else { $wheredate = "ds.date BETWEEN '$prdd' AND '$prdf'"; }
	
	if($table=='auteurs') {
	$rq=spip_query("SELECT sa.nom 
					FROM spip_dw2_stats_auteurs AS ds 
					LEFT JOIN spip_auteurs AS sa ON ds.id_auteur = sa.id_auteur 
					WHERE $wheredate  
					GROUP BY ds.id_auteur 
					ORDER BY sa.nom 
					");
	}
	if($table=='documents') {
	$rq=spip_query("SELECT sd.nom 
					FROM spip_dw2_stats_auteurs AS ds 
					LEFT JOIN spip_dw2_doc AS sd ON ds.id_doc = sd.id_document 
					WHERE $wheredate  
					GROUP BY ds.id_doc 
					ORDER BY sd.nom 
					");
	}
	
	// prepa table des prem lettres + compteur
	$gen_ltt=array();
	while ($row=spip_fetch_array($rq)) {
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
	$prd=spip_query("SELECT DATE_FORMAT(MIN(date),'%d/%m/%Y') as prem 
					FROM spip_dw2_stats_auteurs 
					GROUP BY date 
					");
	$row=spip_fetch_array($prd);
	return $row['prem'];	
}

?>
