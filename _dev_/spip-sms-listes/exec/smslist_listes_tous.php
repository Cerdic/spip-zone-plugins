<?php
/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

include_spip('exec/template/tables_affichage');

function exec_smslist_listes_tous(){
	echo afficher_tables_tous('smslist_liste',_T("smslistliste:toutes_listes_abonnes"),_T("smslistliste:listes_abonnes"),_T("smslistliste:icone_creer_liste"));
}

?>