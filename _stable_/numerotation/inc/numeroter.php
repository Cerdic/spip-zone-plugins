<?php

function numero_denumerote_titre($titre){
	return preg_replace(',^([0-9]+[.]\s+),','',$titre);
}

function numero_numeroter_rubrique($id_rubrique,$type='rubrique',$numerote=true){
	$table = table_objet($type);
	$key = id_table_objet($type);
	$parent = ($type=='rubrique')?'id_parent':'id_rubrique';
	
	$res = spip_query("SELECT $key,titre FROM spip_$table WHERE $parent="._q($id_rubrique)." ORDER BY 0+titre, maj DESC");
	$cpt = 1;
	while($row = spip_fetch_array($res)) {
		$titre = ($numerote?($cpt*10) . ". ":"") . numero_denumerote_titre($row['titre']);
		spip_query("UPDATE spip_$table SET titre="._q($titre)." WHERE $key=".$row[$key]);
		$cpt++;
	}
	return;
}

?>