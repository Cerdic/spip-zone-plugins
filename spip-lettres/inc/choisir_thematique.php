<?php
	/**
	 * SPIP-Lettres inc/choisir_thematiques.php
	 *
	 * Copyright (c) 2006-2011
	 * 		Artégo - Cédric Morin - JLuc
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/

function choisir_thematique ($id_rubrique=0) {
	if (!lettres_nombre_themes() or $id_rubrique
		or ($GLOBALS['meta']['spip_lettres_admin_abo_toutes_rubriques']=='oui')) {
		$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
		return $selecteur_rubrique($id_rubrique, 'rubrique', false);
	} else
		return recuperer_fond("formulaires/selecteur/thematiques", "");
};

?>