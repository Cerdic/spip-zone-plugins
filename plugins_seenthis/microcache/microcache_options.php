<?

function supprimer_microcache($id, $fond) {
	include_spip("inc/microcache");
	_supprimer_microcache($id, $fond);
}

function microcache($id, $fond, $calcul=false) {
	include_spip("inc/microcache");
	return _microcache($id, $fond, $calcul);
}

?>