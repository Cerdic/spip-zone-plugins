<?
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

//Formulaire Nouvelle fiche
$ret .= "<a name='images'></a>\n";
$ret .= debut_cadre_relief("petition-24.gif", true, "creer.gif", _T('genespip:nouvelle fiche'));
$ret .= "<form action='".$url_action_accueil."' method='post'>";
$ret .= "<table><tr><td>";
$ret .= _T('genespip:nom').":</td><td><input type='text' name='nom' size='12' /></td></tr><tr><td>";
$ret .= _T('genespip:prenom').":</td><td><input type='text' name='prenom' size='12' /></td></tr>";
$ret .= "<tr><td colspan='2'><input type='submit' name='submit' value='Valider' size='8' /></td></tr></table>";
$ret .= "<input type='hidden' name='action' value='nouvellefiche' size='8' />";
$ret .= "</form>";
$ret .= fin_cadre_relief(true);
echo $ret;

?>