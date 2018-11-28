<?php
/**
 * Fonctions utiles au plugin chatbox2
 *
 * @plugin     chatbox2
 * @copyright  2018
 * @author     Ptroll
 * @licence    GNU/GPL
 * @package    SPIP\Chatbox2\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


include_spip('base/shoutbox');

// fonction rapide inspiree de couteau-suisse, a revoir calmement
function liens_orphelins_actifs($texte) {
	return preg_replace('!(^|\s|>)(https?://[^\s<]+)!s', '$1<a href="$2" class="spip_out">$2</a>', $texte);
}

?>