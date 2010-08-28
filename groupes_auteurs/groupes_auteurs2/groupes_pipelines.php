<?php 
function groupes_listeauteurs($flux) {
	if($flux['args']['exc']=='false')
		$exc = false;
	else $exc = true;
	$flux=table_liste_auteurs(_request('id_groupe'), _request('debut'), _request('crit'), _request('order'), $flux['args']['id_tableau'],$exc, $flux['args']['callback']);
	return $flux;
}

function groupes_listeliens($flux) {

	$liens = unserialize(lire_meta('groupes_liens'));

	$i=0;
	
	if(!is_array($liens)) {
		$flux .= div_lien(0);
		$i++;
	} else {
		foreach($liens as $lien) {
			$flux.= div_lien(0, $lien);
			$i++;
		}
	}
	$flux .= '<input type="hidden" name="imax" value='.$i.' id="imax"/>';
	return $flux;
}
?>