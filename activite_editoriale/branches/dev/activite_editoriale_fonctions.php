<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// obtenir l'age de la rubrique en nombre de jour
function age_rubrique($date_str) {
	return intval((time() - strtotime($date_str)) / (60 * 60 * 24));
}

// retrouver ici la fonction du genie, utile dans prive/exec/activite_editoriale_rubrique
include_spip('genie/activite_editoriale_alerte','activite_editoriale_emails');

// retrouver ici la fonction du genie, utile dans prive/exec/activite_editoriale_rubrique
include_spip('genie/activite_editoriale_alerte','activite_editoriale_id_auteurs');


/*
* Le filtre : [(#DATE|decompte)] retourne la valeur de
* l'intervalle de temps entre la date courante et la date passée
* en argument. Si l'écart est négatif (#DATE est passée) la fonction
* préfixe le retour d'un signe 'moins'.
*
* @param string $dat_x
*   une date quelconque : 'aaaa-mm-jj hh:mm:ss' ou 'aaaa-mm-jj'
*  
* @return string
*   l'intervalle de temps entre la date courante et celle passée en argument.
*/
function decompte($dat_x) {
	$aujourd_hui = new DateTime();
	$date_compare = new DateTime($dat_x);
	$ect = $aujourd_hui->diff($date_compare);
	$sens = ($ect->invert == 1) ? '- ' : '';
	$format = $sens . '%1$04d-%2$02d-%3$02d %4$02d:%5$02d:%6$02d';
	return sprintf($format, $ect->y, $ect->m, $ect->d, $ect->h, $ect->i, $ect->s);
}

/* La balise : #DECOMPTE{aaaa-mm-jj hh:mm:ss}
*
* Affiche (sous la forme 'aaaa-mm-jj hh:mm:ss') la valeur de l'intervalle
* de temps entre la date courante et la date passée en paramètre.
* Si 'hh:mm:ss' est omis dans l'argument, 00:00:00 sera utilisé.
* Si l'écart est négatif le retour est préfixé  d'un signe 'moins'.
*
*/
function balise_DECOMPTE($p) {
	$p->code = interprete_argument_balise(1, $p);
	$p->code = 'decompte(' . $p->code . ')';
	$p->interdire_scripts = false;
	return $p;
}