<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'grigri_nom' => 'Grigri',
	'grigri_slogan' => 'Ajouter un grigri aux objets',
	'grigri_description' => 'Ce plugin permet d\'attribuer un grigri texte aux objets : auteurs, articles, mots, groupes_mots, rubriques, documents.
	Ainsi, au lieu de faire <code><BOUCLE_rubrique(RUBRIQUES){id_rubrique=N}></code>, vous pourrez faire par exemple <code><BOUCLE_rubrique(RUBRIQUES){id_rubrique IN #TGRIGRI{rubrique, mon_grigri}}></code> ou <code><BOUCLE_rubrique(RUBRIQUES){grigri = mon_grigri}></code>.
	Seuls les webmestres peuvent voir et manipuler les grigri',
);
