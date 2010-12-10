<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_tous(){
	$par = _request('par');
	($par =='') ? $par='date' : $par = $par;
    $commencer_page = charger_fonction('commencer_page', 'inc');
	include_spip('public/assembler');
	echo $commencer_page(_T("jeux:liste_jeux")),
		recuperer_fond('fonds/jeux_tous', array('par'=>$par));
}

function table_jeux_caracteristiques() {
	global $jeux_caracteristiques;
	$res = '|{{'._T('jeux:jeux').'}}|{{'._T('public:signatures_petition').'}}|{{'._T('jeux:options').'}}|{{'._T('spip:icone_configuration_site').'}}|';
	foreach($jeux_caracteristiques['TYPES'] as $j=>$t) {
		include_spip('jeux/'.$j);
		$config = function_exists($f='jeux_'.$j.'_init')?trim($f()):'';
		$res .= "\n|$t|&#91;" 
			. join("]\n_ &#91;",$jeux_caracteristiques['SIGNATURES'][$j]) . ']|['
			. join("]\n_ &#91;",array_diff($jeux_caracteristiques['SEPARATEURS'][$j],$jeux_caracteristiques['SIGNATURES'][$j])) . ']|'
			. preg_replace(array(',//.*,', ',[\n\r]+,'), array('', "\n_ "), $config) . '|';
	}
	return propre($res);
}

?>