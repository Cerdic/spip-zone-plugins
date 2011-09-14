<?php


/**
 * @see http://www.quesaco.org/plugin-spip-articles-lister
 */

// autorisation des boutons
function autoriser_articles_lister_bouton_dist ($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre');
}