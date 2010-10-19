<?php


	/**
	 * SPIP-Lettres
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	/**
	 * balise_URL_FORMULAIRE_LETTRES
	 *
	 * @param p est un objet SPIP
	 * @return string url du formulaire d'inscription aux lettres
	 * @author Pierre Basson
	 **/
	function balise_URL_FORMULAIRE_LETTRES($p) {
		$_lang = champ_sql('lang', $p);
		$p->code = "generer_url_public(\$GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], ($_lang ? 'lang='.$_lang : ''), true)";
		$p->statut = 'php';
		return $p;
	}


	/**
	 * balise_URL_LETTRE
	 *
	 * @param p est un objet SPIP
	 * @return string url d'une lettre
	 * @author Pierre Basson
	 **/
	function balise_URL_LETTRE($p) {
		$_id_lettre = champ_sql('id_lettre', $p);
		$p->code = "generer_url_lettre($_id_lettre)";
		$p->statut = 'php';
		return $p;
	}


?>