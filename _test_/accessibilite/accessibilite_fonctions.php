<?php
function accessibilite_enlien($texte) {
	return extraire_attribut(propre('[->'.$texte.']'),'href');
}
?>