<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// Tests pour la verification de type definition
function langonet_tester_definition() {
	// Commenter ou decommenter les lignes suivantes pour tester les differents cas de verification

	// VERIFICATION UTILISATION : Erreurs
	// -- Les items suivants, utilises comme des items du module Langonet, ne sont pas definis dans langonet_fr.php
	$essai = _T('langonet:test_item_non_defini_1');
	$essai = _T('langonet:test_item_non_defini_2');

	// VERIFICATION UTILISATION : Erreurs
	// -- Les items suivants, utilises comme des items de modules differents de Langonet, ne sont pas definis dans leur fichier
	// de langue respectif (spip, ecrire ou public.php pour le premier, cfg_fr pour le second)
	$essai = _T('test_item_non_defini_3');
	$essai = _T('cfg:test_item_non_defini_4');

	// VERIFICATION UTILISATION : Avertissements
	// -- Les items suivants n'appartiennent pas au module Langonet, sont utilises dans Langonet mais bien definis dans leur 
	// fichier de langue respectif
	$essai = array(0 => _T('date_jour_1'));
	$essai = array_push(_T('cfg:bouton_effacer'));

	// VERIFICATION UTILISATION : Avertissements
	// -- Les items suivants sont utilises dans un contexte complexe. Ce sont des items de Langonet ou pas
	$essai = _T('langonet:test_item_1_'.$variable);
	$essai = _T("langonet:$fond1");
	$essai = _T('langonet:'.$fond2);
	$essai = _T("langonet:".$fond3);
	$essai = _T('test_item_2_'.$variable);

}
?>
