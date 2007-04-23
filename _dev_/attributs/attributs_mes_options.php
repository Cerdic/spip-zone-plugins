<?php
include_spip('base/attributs');

function autoriser_attribut_associer_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut'] == '0minirezo')
		return true;
	if ($qui['statut'] == '1comite') {
		$s = spip_query("SELECT redacteurs FROM spip_attributs WHERE id_attribut="._q($id));
		$r = spip_fetch_array($s);
		return $r['redacteurs']=='oui';
	}
	return false;
}
?>