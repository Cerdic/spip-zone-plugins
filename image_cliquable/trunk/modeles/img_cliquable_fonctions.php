<?php
if (!defined('_ECRIRE_INC_VERSION')) return; 

function accessibilite_enlien($texte) {
	return extraire_attribut(propre('[->'.$texte.']'),'href');
}
?>