<?php
include_spip('base/mots_techniques');

function autoriser_groupemots_modifier($faire, $type, $id, $qui, $opt) {
	$r = sql_fetsel("technique", "spip_groupes_mots", "id_groupe="._q($id));
	if ($r['technique']=='' OR $r['technique']=='oui')
		return
			$qui['statut'] == '0minirezo'
			AND !$qui['restreint'];
	else return false;
}

?>
