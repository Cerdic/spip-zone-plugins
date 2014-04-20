<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// Tests pour la verification de type definition
function langonet_tester_1() {
	// Commenter ou decommenter les lignes suivantes pour tester les differents cas de verification
	// VERIFICATION DEFINITION : Erreurs
	// -- Les items suivants, utilises comme des items du module Langonet, ne sont pas definis dans verification_fr.php
	$essai = _T('verification:test_item_non_defini_1');
	$essai = _T("verification:test_item_non_defini_2");
	$essai = _T("verification:test_item_non_defini_2", array('param1'=>$param1));

	// VERIFICATION DEFINITION : Erreurs
	// -- Les items suivants, utilises comme des items de modules differents de Langonet, ne sont pas definis dans leur fichier
	// de langue respectif (spip, ecrire ou public.php pour le premier, cfg_fr pour le second)
	$essai = _T('test_item_non_defini_3');
	$essai = _T('cfg:test_item_non_defini_4');
	$essai = _T('cfg:bouton_valider');

	// VERIFICATION DEFINITION : Avertissements
	// -- Les items suivants n'appartiennent pas au module Langonet, sont utilises dans Langonet mais bien definis dans leur 
	// fichier de langue respectif
	$essai = array(0 => _T('date_jour_1'));
	$essai = array_push(_T('cfg:bouton_effacer'));

	// VERIFICATION DEFINITION : Avertissements
	// -- Les items suivants sont utilises dans un contexte complexe. Ce sont des items de Langonet ou pas
	$essai = _T('verification:test_item_1_'.$suite1);
	$essai = _T('test_item_2_'.$suite2);
	$essai = _T('test_item_2_'.$suite2, array('param1'=>$param1));
	$essai = _T('verification:' . $item1);
	$essai = _T("verification:$item2");
	$essai = _T("verification:". $item3, array('param1'=>$param1));
	$essai = _T("verification:defini_html_partiel_".$suite3);
	$essai = _T("verification:defini_html_partiel_$suite4");
	$essai = _T("verification:defini_html_partiel_${suite5}");
	$message = _T("verification:defini_html_partiel_{$suite6['numero']}", array('param1' => $param1, 'param2' => $param2));

	// VERIFICATION _L() : Avertissements
	$essai = _L('Test 1 _L() de langonet');
	$essai = _L("Test 2 _L() de langonet");
	$essai = _L("Test 3 _L()", $arg4);
	$essai = _L('Test{} 4 _L{}' . $arg5);
	$essai = _L("Test 5 _L()$arg6");
	$essai = _L("Test 6 _L()${arg7}");
	$essai = _L("Test 7 _L()");
	$essai = _L  ("TEST 7 _L()");
	$essai = _L		("Test 7 _L()");
	$essai = _L("T1234567890123456789012345678901abcdef");
}
?>
