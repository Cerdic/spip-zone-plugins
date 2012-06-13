<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'apropos_stats_description' => 'Vous souhaitez afficher le nombre de site utilisant un plugin, &Agrave; Propos Statistiques vous le permet mais vous devez avoir install&eacute; et activ&eacute; le plugin SVP Stat.

Pour afficher le nombre de sites utilisant tel ou tel plugin, il suffit d&rsquo;&eacute;crire dans le corps d&rsquo;un article le code suivant : < apropos_stats|prefixe=le prefixe du plugin > .

Pour initialiser les statistiques pour le plugin, allez dans Configuration -> Gestion des plugins, sélectionnez "Ajouter des plugins", puis cliquez sur le bouton "Ajouter". Ensuite, allez dans le menu Maintenance -> Liste des travaux et faites 
"Tache CRON svp_actualiser_depots" et ensuite faites Tache CRON svp_actualiser_stats et tout devrait fonctionner.',
	'apropos_stats_nom' => 'À propos statistiques',
	'apropos_stats_slogan' => 'Affiche le nombre de sites qui utilisent un plugin.',
);

?>