<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'refresher_description' => 'Ce plugin intercepte l\'activit&eacute; &eacute;ditoriale sur les objets du site au travers de pipelines, cr&eacute;e ensuite une liste d\'URLs qui vont s\'accumuler dans le job_queue, 
	afin d\'&ecirc;tre rafraichies au plus vite ou &agrave; une date pr&eacute;cise.
	Il est possible d\'invalider les pages sur un CDN. Les fonctions qui cr&eacute;ent les listes d\'URLs &agrave; rafraichir pour chaque action &eacute;ditoriale doivent &ecirc;tre d&eacute;finies dans inc/refresher_functions.php ou mes_fonctions.php et sont &agrave; personnaliser.
	',
	'refresher_nom' => 'Rafraichissement',
	'refresher_slogan' => 'Recalculer les pages publiques quand on ajoute/supprime/&eacute;dite des objets'
);

?>
