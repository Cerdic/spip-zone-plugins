<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/autorite/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// D
	'description_page' => 'Vous pouvez trouver ci-dessous la liste des plugins actifs du site proposant une page de configuration du type <code>?exec=configurer_prefixe-plugin</code>.',
	'description_lister_exec' => '<strong>Mise en garde !</strong> Faites attention à l\'utilisation de ces pages. <br/>On ne liste pas ici les pages de vue et d\'édition d\'un objet éditorial comme les pages d\'un article, de brèves, de mots clés, etc. <br /><em>Cette page est un proof of concept.</em>',
	'description_lister_plugins' => 'G&eacute;n&eacute;ration du fichier d\'appel des plugins n&eacute;cessaires au site ',
	'description_utiliser_plugins' => 'Vous pourriez utiliser cette trame pour batir un fichier <em>paquet.xml</em>,<br />
	Utilisable comme pseudo-plugin pour reconfigurer votre site (cf. mes_fichiers) !<br />
	(vous pouvez aussi passer vos squelettes dans ce plugin, qui pourra faciliter vos migrations).',

	// I
	'intertitre_exec' => 'Selon exec/*.php',
	'intertitre_exec_contenu' => 'Selon squelettes/contenu',

	// T
	'titre_lister_config' => 'Les pages de configuration',
	'titre_lister_exec' => 'La liste des pages ?exec=xxx',
	'titre_lister_plugins' => 'Les plugins nécessaires au site',
	'titre_page' => 'Pages de configuration des plugins actifs du site'

);

?>