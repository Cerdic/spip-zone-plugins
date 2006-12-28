<?php

// cette fonction n'est pas appelee dans les balises html : cadre|code
function bellespuces_rempl($texte) {
	return preg_replace('/^-\s+/m','-* ',$texte);
}

function bellespuces_pre_typo($texte) {
	return tweak_exclure_balises('cadre|code', 'bellespuces_rempl', $texte);
}


?>