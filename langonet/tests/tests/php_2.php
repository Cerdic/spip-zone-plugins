<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// Tests pour la verification de type definition
function langonet_tester_2() {
	// Commenter ou decommenter les lignes suivantes pour tester les differents cas de verification
	// VERIFICATION DEFINITION : Erreurs
	// -- Les items suivants, utilises comme des items du module Langonet, ne sont pas definis dans langonet-tests_fr.php

	// VERIFICATION DEFINITION : Erreurs
	// -- Les items suivants, utilises comme des items de modules differents de Langonet, ne sont pas definis dans leur fichier
	// de langue respectif (spip, ecrire ou public.php pour le premier, cfg_fr pour le second)

	// VERIFICATION DEFINITION : Avertissements
	// -- Les items suivants n'appartiennent pas au module Langonet, sont utilises dans Langonet mais bien definis dans leur 
	// fichier de langue respectif

	// VERIFICATION DEFINITION : Avertissements
	// -- Les items suivants sont utilises dans un contexte complexe. Ce sont des items de Langonet ou pas
	$message = _T("verification:defini_html_partiel_{$suite6['numero']}", array('param1' => $param1, 'param2' => $param2));
	$message = _T('langotests:obsolete_4');

	// VERIFICATION _L() : Erreurs
	$essai = _L("TEST 7 _L()");
	$essai = _L("T1234567890123456789012345678901xyz");
	$essai = _L("Test multiple 1") . '_' . _L("Test multiple 2");
	$essai = _L("Test multiple 3"); _L("Test multiple 4");
	$essai = _L("Test multiple 5") . ' et la suite ' . _L("Test multiple 5");

	// DETECTION IMPOSSIBLE POUR L'INSTANT !!!
	$message = singulier_ou_pluriel(
				$nb,
				'verification:non_detecte_1_1',
				"verification:non_detecte_1_n",
				$options);
}
?>
