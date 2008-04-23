<?php

function env_verifier_base() {			
	ecrire_config('env/environnement','NON');
	ecrire_config('env/redirection','http://www.nouveauxterritoires.fr');
}




function env_vider_tables() {
	effacer_config('env/environnement');
	effacer_config('env/addressip');
	effacer_config('env/redirection');
	effacer_config('env/banniesip');
}
	
function env_install($action){
	switch ($action){
		case 'test':
			return (isset($GLOBALS['meta']['env']['environnement']));
			break;
		case 'install':
			env_verifier_base();
			break;
		case 'uninstall':
			env_vider_tables();
			break;
	}
}


?>