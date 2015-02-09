<?php
function genie_linkcheck_test_postedition($id, $objet){
	
	include_spip('inc/linkcheck_fcts');
	
	$sel = sql_select( 'sl.url, sl.distant, sl.id_linkcheck, sl.essais','spip_linkchecks AS sl, spip_linkchecks_liens AS sll', 'sll.id_objet='.$id.' AND sll.objet='.sql_quote($objet).' AND sll.id_linkcheck=sl.id_linkcheck');

	while($res=sql_fetch($sel))
		linkcheck_maj_etat($res);
		
	return 1;
}
?>
