<?php

/*
 * revision_nbsp
 *
 * Dans l'espace prive, souligne en grise les espaces insecables
 *
 * Auteur : fil@rezo.net
 * © 2005 - Distribue sous licence GNU/GPL
 *
 */


class RevisionNbsp{

	function revision_nbsp($letexte) {
		return str_replace('&nbsp;',
			'<span class="spip-nbsp">&nbsp;</span>', $letexte);
	}

}

?>