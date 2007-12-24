<?php
/*
|
*/
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

//include_spip('inc/spipbb_util');


function action_spipbb_editer_infos() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();
	
	$redirect = urldecode(_request('redirect'));
	$id_auteur = _request('arg');
	$table_support=lire_config("spipbb/table_support");
	$traiter_chps=array();
	
	$nouv_inscrit=_request('spipbb_nouveau');
	
	# lister champs, recup et filtrage
	foreach($GLOBALS['champs_sap_spipbb'] as $chp => $def) {
		$filtres_recup=$def['filtres_recup'];
		if($filtres_recup!='' && function_exists($filtres_recup)) {
			$traiter_chps[$chp] = $filtres_recup(_request('spipbb_'.$chp));
		}
		else {
			$traiter_chps[$chp] = _request('spipbb_'.$chp);
		}
	}
	
	$set='';
	foreach($traiter_chps as $k => $v) {
		if($k=="date_crea_spipbb" && $v=='') {
			$set.= ",".$k."=NOW()";
		}
		# h.10/11 .. tempo : ne pas traiter !
		# ulterieurement : prepa tableau idem modele/form_profil .. .html
		/*
		elseif($k=="refus_suivi_thread" && is_array($v)) {
			$set.= ",".$k."="._q(join(',',$v));
		}
		*/
		else {
			$set.= ",".$k."="._q($v);
		}
	}
	$set=substr($set,1);
	if(strlen($set)>0) { $sep = ","; }
	
	if($nouv_inscrit) {
		spip_query("INSERT INTO spip_".$table_support." SET id_auteur=".$id_auteur." ".$sep.$set);
	}
	else {
		spip_query("UPDATE spip_".$table_support." SET $set WHERE id_auteur=".$id_auteur);	
	}

	
	redirige_par_entete($redirect);
}


?>
