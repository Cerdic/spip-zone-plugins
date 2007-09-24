<?php
include_spip('base/mots_techniques');

function autoriser_groupemots_modifier($faire, $type, $id, $qui, $opt) {
	$s = spip_query("SELECT technique FROM spip_groupes_mots WHERE id_groupe="._q($id));
	$r = sql_fetch($s);
	if ($r['technique']=='' OR $r['technique']=='oui')
		return
			$qui['statut'] == '0minirezo'
			AND !$qui['restreint'];
	else return false;
}

?>
