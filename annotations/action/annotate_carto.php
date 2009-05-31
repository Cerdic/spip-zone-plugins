<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@action_editer_mot_dist
function action_annotate_carto() {
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(',^\d+$,', $arg)) { 
		spip_log("action annotate_carto: $arg pas compris");
		echo "{result:'error'}";
	}
	else {
		if(_request("export")) {
			header("Content-Type: text/csv; charset=".$GLOBALS['meta']['charset']);
			header("Content-Disposition: attachment; filename=\"csv_annotation_id_document_$arg.csv\"");
			$res = sql_select("*","spip_annotations","id_document=$arg");

			ob_start();
			$out = fopen('php://output', 'w');
			$row = sql_fetch($res);
			if($row) {
				fputcsv($out, array_keys($row),";");
				do {
					fputcsv($out, $row,";");
				} while($row = sql_fetch($res));
			};
			fclose($out);
	
			$csv = ob_get_contents();			
			ob_end_clean();

			header("Content-Length: " . strlen($csv));
			
			echo $csv; 
			
			return;
		}
		
		include_spip('inc/filtres');
		$i = 0;
		$annotations = _request("id_annotation");
		$x = _request("x");
		$y = _request("y");
		$titre = preg_replace(",\r?\n,"," ",_request("title"));
		$texte = _request("text");

		$mode = "";
		$new_id = "0";
		foreach($annotations as $annotation) {
			if($annotation>0) {
				//update
				$mode = "update";
				spip_log("update $annotation","annotation");
				$champs = array();
				$champs['titre'] = corriger_caracteres($titre[$i]);
				$champs['texte'] = corriger_caracteres($texte[$i]);
				$champs['x'] = intval($x[$i]);
				$champs['y'] = intval($y[$i]);
				sql_updateq('spip_annotations',$champs,'id_annotation='.$annotation);
			}	elseif($annotation<0) {
				$mode = "delete";
				spip_log("delete $annotation","annotation");
				sql_delete('spip_annotations','id_annotation='.-$annotation);
			} else {
				//insert
				//if(function_exists("spip_abstract_insert"))
				//	spip_abstract_insert('spip_annotations','(id_document,titre,texte,x,y)','('.$arg.',\''._q($titre[$i]).'\',\''._q($texte[$i]).'\','.$x[$i].','.$y[$i].')');
				//else
				$mode = "insert";
				$new_id = sql_insert('spip_annotations','(id_document,titre,texte,x,y)','('.$arg.','._q(corriger_caracteres($titre[$i])).','._q(corriger_caracteres($texte[$i])).','.intval($x[$i]).','.intval($y[$i]).')');
			}
			$i++;
		}
		
		echo "{result:'success',mode:'$mode',new_id:$new_id}";
	}
}

?>
