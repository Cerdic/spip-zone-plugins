<?php

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function pucesli_rempl($texte) {
	return preg_replace('/^-\s+/m','-* ',$texte);
}

function pucesli_pre_typo($texte) {
	return cs_echappe_balises('', 'pucesli_rempl', $texte);
}


?>