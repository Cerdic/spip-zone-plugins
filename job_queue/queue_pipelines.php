<?php
/*
 * Plugin Job Queue
 * (c) 2009 Cedric&Fil
 * Distribue sous licence GPL
 *
 */

function queue_affiche_milieu($flux){
	$args = $flux['args'];
	$res = "";
	foreach($args as $key=>$arg){
		if (preg_match(",^id_,",$key)){
			$objet = preg_replace(',^id_,', '', $key);
			$res .= recuperer_fond('modeles/object_jobs_list',array('id_objet'=>$arg,'objet'=>$objet),array('ajax'=>true));
		}
	}
	if ($res)
		$flux['data'] = $res . $flux['data'];

	return $flux;
}

?>