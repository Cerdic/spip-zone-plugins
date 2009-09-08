<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

include_spip('inc/presentation');

function exec_fiche_union(){
global $connect_statut, $connect_toutes_rubriques;


debut_page(_T('Fiche'), "", "");
$url_action_fiche=generer_url_ecrire('fiche_union');
$url_retour = $_SERVER['HTTP_REFERER'];
$id_individu = $_GET['id_individu'].$_POST['id_individu'];
$id_mariage = $_GET['id_mariage'].$_POST['id_mariage'];

debut_gauche();
include_spip('inc/boite_info');

include_spip('inc/raccourcis_fiche');
debut_droite();

debut_cadre_relief(  "", false, "", $titre = _T('genespip:fiche union'));
    debut_boite_info();
//Requ�tes parents

gros_titre(_T(genespip_nom_prenom($id_individu,3)));
echo "<br /><fieldset><legend>"._T('genespip:Liste des unions')."</b></i></legend>";

if ($_POST['action']=='modif'){
genespip_modif_union();
}
if ($_POST['action']=='choixepoux'){
genespip_ajout_union($id_individu, $_POST['newepoux']);
}
if ($_GET['action']=='delete'){
genespip_supp_entree($_GET['id_mariage'],'spip_genespip_mariage');
}
 echo "<table style='border:1px;border-color:black'>";
echo "<tr>",
      "<td>Union</td>",
      "<td>Date</td>",
      "<td>Lieu</td>",
      "<td>Dep.</td>",
      "<td>Pays</td>",
      "<td colspan='2'></td>",
     "</tr>";
$result = spip_query("SELECT * FROM spip_genespip_mariage where individu = ".$id_individu);
while ($union = spip_fetch_array($result)) {
echo '<form action="'.$url_action_fiche.'" method="post">';
echo "<tr>",
      "<td style='text-align:middle'><b>".genespip_nom_prenom($union['epoux'],1)."</b></td>",
      "<td><input size='8' type='text' name='mar' value='".genespip_datefr($union['mar'])."' /></td>",
      "<td><input size='8' type='text' name='marlieu' value='".stripslashes($union['marlieu'])."' /></td>",
      "<td><input size='2' type='text' name='mardep' value='".$union['mardep']."' /></td>",
      "<td><input size='6' type='text' name='marpays' value='".$union['marpays']."' /></td>",
      "<td><input type='image' src='"._DIR_PLUGIN_GENESPIP."img_pack/update.gif' name='update' /></td>",
      "<td><a href='".$url_action_fiche."&action=delete&id_individu=".$id_individu."&id_mariage=".$union['id_mariage']."'><img border='0' noborder src='"._DIR_PLUGIN_GENESPIP."img_pack/del.gif' alt='Supprimer' /></a></td>",
     "</tr>";
echo "<input name='action' type='hidden' value='modif'>";
echo "<input name='epoux' type='hidden' value='".$union['epoux']."' /></td>";
echo "<input name='id_mariage' type='hidden' value='".$union['id_mariage']."'>";
echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
echo "</form>";
}
echo "</table><br /><br />";
echo "<table style='border:1px;border-color:black'>";
echo '<form action="'.$url_action_fiche.'" method="post">';

//Listing des noms pour nouvelle union
$sql = spip_query("SELECT nom FROM spip_genespip_individu group by nom") or die ("Requ�te invalide");
echo "<tr><td><select name='choix_nom'>";
echo "<option value=''>-- NOM --</option>";
 while ($list = spip_fetch_array($sql)) {
 $nom = strtoupper($list['nom']);
 echo "<option value='$nom'>$nom</option>";
 }
echo "</select></td>";
echo "<td>&mdash;&mdash;&rsaquo;"._T('genespip:nouvelle union')."&mdash;&mdash;&rsaquo;</td>";
echo "<td><INPUT TYPE='submit' VALUE='Valider' class='fondo' /></td></tr>";
echo "<input name='action' type='hidden' value='choixnom' />";
echo "<input name='id_mariage' type='hidden' value='".$union['id_mariage']."' />";
echo "<input name='id_individu' type='hidden' value='".$id_individu."' />";
echo "</form>";
echo "</table>";
echo "</fieldset>";
if ($_POST['action']=='choixnom'){
   echo "<br /><fieldset><legend>"._T("genespip:Liste des personnes n&eacute;es ".$_POST['choix_nom'])."</b></i></legend>";
   echo "<table style='border:1px;border-color:black'>";
   echo "<form action='".$url_action_fiche."' method='post'>";
   $result_epoux = spip_query("SELECT id_individu, nom, prenom, naissance, deces FROM spip_genespip_individu where nom='".$_POST['choix_nom']."' and poubelle <> '1'");
     while ($liste = spip_fetch_array($result_epoux)) {
     $info_individu=$liste['nom']."&nbsp;".$liste['prenom']."&nbsp;(&ordm;".genespip_datefr($liste['naissance'])."-&dagger;".genespip_datefr($liste['deces']).")";
     echo "<tr><td><input type='radio' name='newepoux' value='".$liste['id_individu']."' /></td>";
     echo "<td>".$info_individu."</td></tr>";
     }
   echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
   echo "<input name='action' type='hidden' value='choixepoux'>";
   echo "<tr><td><INPUT TYPE='submit' VALUE='Valider' class='fondo' /></td></tr>";
   echo "</form>";
   echo "</table>";
   echo "</fieldset>";
}

fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>



