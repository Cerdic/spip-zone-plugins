<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/shoutbox');

// fonction rapide inspiree de couteau-suisse, a revoir calmement
function liens_orphelins_actifs($texte) {
	return preg_replace('!(^|\s|>)(https?://[^\s<]+)!s', '$1<a href="$2" class="spip_out">$2</a>', $texte);
}

?>
