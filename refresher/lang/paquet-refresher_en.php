<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'refresher_description' => 'We intercept editorial activity on object through pipelines, then create a list of URLs to refresh depending on the object and the editing action.
	Then we add URLs to the job queue, to be refreshed as soon as possible or at a specific time.
	We can also activate CDN invalidation. The functions that create the list of URLs for each action need to be defined in inc/refresher_functions.php or mes_fonctions.php and need to be custom made.
	',
	'refresher_nom' => 'Refresher',
	'refresher_slogan' => 'Refresh public pages when we add/remove/edit objects'
);

?>
