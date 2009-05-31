<?php

	function affdate_long($date) {
		return affdate_base($date, 'nom_jour').' '.affdate_base($date, 'entier');
	}

	/***
	* |me compare un id_auteur avec les auteurs d'un article
	* et renvoie la valeur booleenne true (vrai) si on trouve une correspondance
    * utilisation: <div id="forum#ID_FORUM"[(#ID_ARTICLE|me{#ID_AUTEUR}|?{' ', ''})class="me"]>
	***/
	function me($id_article, $id_auteur = 0) {
		static $deja = false;
		static $auteurs = array();
		if(!$deja) {
			$r = spip_query("SELECT id_auteur FROM spip_auteurs_articles WHERE id_article=$id_article");
			while($row = spip_fetch_array($r))
				$auteurs[] = intval($row['id_auteur']);
			$deja = true;
		}
		return in_array($id_auteur, $auteurs);
	}

?>
