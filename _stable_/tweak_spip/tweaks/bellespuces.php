<?php

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function bellespuces_rempl($texte) {
	return preg_replace('/^-\s+/m','-* ',$texte);
}

function bellespuces_pre_typo($texte) {
	return tweak_exclure_balises('', 'bellespuces_rempl', $texte);
}


?>