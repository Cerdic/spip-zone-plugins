<?php

// Prepare les messages d'aide de la page de configuration du plugin

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation'); // pour compat cfg 1.0.1

// Qui sont les webmestres ?
$webmestres = array();
include_spip('inc/texte');
$s = spip_query("SELECT * FROM spip_auteurs WHERE id_auteur IN (". join (',', array_filter(explode(':', _ID_WEBMESTRES), is_numeric)).")");
while ($qui = spip_fetch_array($s)) {
	if (autoriser('webmestre','','',$qui))
		$webmestres[$qui['id_auteur']] = typo($qui['nom']);
}

$message = 'Cette page de configuration est r&#233;serv&#233;e aux webmestres du site&nbsp;: &nbsp; <b>';
$message .= join(', ', $webmestres);
$message .= "</b>\n"
	."<hr />\n"
	."<p><small>Si vous souhaitez modifier cette liste, veuillez éditer le fichier <tt>config/mes_options.php</tt> (le cr&#233;er le cas &#233;ch&#233;ant) et y indiquer la liste des identifiants des auteurs webmestres, sous la forme suivante&nbsp;:</small></p>
<pre style='text-align:left;'>&lt;?php
define ('_ID_WEBMESTRES',
  '1:5:8');
?&gt;</pre>";

$message .= "<p><small>A noter&nbsp;: les webmestres d&#233;finis de cette mani&#232;re n'ont plus besoin de proc&#233;der &#224; l'authentification par FTP pour les op&#233;rations d&#233;licates (mise &#224; niveau de la base de donn&#233;es, par exemple).</small></p>\n";


define('MESSAGE_CONFIG_AUTORISER', $message); // pour le squelette cfg_autorite


?>