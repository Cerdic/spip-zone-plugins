<?php

/**
 * Page temporaire de redirection
 *
 * CP-20100614
 * Il semble que CFG ajoute un lien en exec=admin_plugin
 * dans la boite SPIP-Listes, sur le droite,
 * qui part en erreur 404
 * En attendant de comprendre pourquoi,
 * une petite redirection sur la vraie page
 * de configuration.
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/utils');

$url = generer_url_ecrire('spiplistes_config');

header('Location: '.$url);

?>
<p class="text">La page de configuration via CFG n&#39;est pas disponible.</p>
<p class="text">Vous allez &#234;tre redirig&#233; sur
	<a
	href="<?php echo($url); ?>">
	la page de configuration de SPIP-Listes.</p>

