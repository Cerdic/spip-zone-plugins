<?php 
function formulaires_memberOf_traiter_dist() {
	$imax = _request('i_max');
	$array_groupes = array();
	for($i=0;$i<$imax;$i++) {
		if( (count(_request('grp_'.$i))!=0) && (_request('memberOf_'.$i)!="")) {
			$array_groupes[_request('memberOf_'.$i)] = _request('grp_'.$i);
		} 
	}
	effacer_meta('ldaplus_memberof');
	ecrire_meta('ldaplus_memberof', serialize($array_groupes));
} 
?>