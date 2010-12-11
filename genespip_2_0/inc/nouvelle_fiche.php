<?
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

//Formulaire Nouvelle fiche
$ret .= "<a name='images'></a>\n";
$ret .= debut_cadre_relief("petition-24.gif", true, "creer.gif", _T('genespip:nouvelle_fiche'));
$ret .= "<form action='".$url_action_accueil."' method='post'>";
$ret .= "<table>";
$ret .= "<tr><td>"._T('genespip:nom').":</td><td><input type='text' name='nom' size='12' /></td></tr>";
$ret .= "<tr><td>"._T('genespip:prenom').":</td><td><input type='text' name='prenom' size='12' /></td></tr>";
$ret .= "<tr><td colspan='2'>M&nbsp;<input type='radio' name='sexe' value='0' id='1' checked />";
$ret .= "&nbsp;F&nbsp;<input type='radio' name='sexe' value='1' id='2' /></td></tr>";
$ret .= "<tr><td colspan='2'><input type='submit' name='submit' value='"._T('genespip:valider')."' size='8' /></td></tr></table>";
$ret .= "<input type='hidden' name='action' value='nouvellefiche' size='8' />";
$ret .= "</form>";
$ret .= fin_cadre_relief(true);
echo $ret;

?>
