<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// Tests pour la verification de type definition
function langonet_tester_definition() {
	// Commenter ou decommenter les lignes suivantes pour tester les differents cas de verification
	// -- Les items suivants ne sont pas definis dans langonet_fr.php
	$essai = _T('langonet:test_item_non_defini_1');
	$essai = _T('langonet:test_item_non_defini_2');

	// -- Les items suivants n'appartiennent pas au module langonet
	$essai = _T('info_langues');
	$essai = _T('articles');
	$essai = array(0 => _T('date_jour_1'));
	$essai = array_push(_T('date_jour_2'));

	// -- Les items suivants sont utilises dans un contexte complexe
	$essai = _T('langonet:test_item_1_'.$variable);
	$essai = _T('test_item_2_'.$variable);
	$essai = _T("langonet:$fond1");
	$essai = _T('langonet:'.$fond2);
	$essai = _T("langonet:".$fond3);
}
?>
