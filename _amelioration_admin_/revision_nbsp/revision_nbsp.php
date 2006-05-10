<?php

/*
 * revision_nbsp
 *
 * Dans l'espace prive, souligne en grise les espaces insecables
 *
 * Auteur : fil@rezo.net
 * © 2005-2006 - Distribue sous licence GNU/GPL
 *
 */


	function RevisionNbsp_revision_nbsp($letexte) {
		if (!_DIR_RESTREINT) {
			$letexte = echappe_html($letexte, '', true, ',(<img[^<]*>),Ums');
			return str_replace('&nbsp;',
				'<span class="spip-nbsp">&nbsp;</span>', $letexte);
		} else
			return $letexte;
	}


?>