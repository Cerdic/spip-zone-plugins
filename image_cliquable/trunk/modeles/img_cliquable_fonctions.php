<?php
if (!defined('_ECRIRE_INC_VERSION')) return; 

function accessibilite_enlien($texte) {
	return str_replace('&','&amp;',str_replace('&amp;','&',extraire_attribut(propre('[->'.$texte.']'),'href')));
}
?>