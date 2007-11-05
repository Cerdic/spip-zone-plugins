<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function echoppe_echec_autorisation(){
	echo debut_boite_alerte();
	echo _T('echoppe:acces_non_autorise');
	echo fin_boite_alerte();
}

?>
