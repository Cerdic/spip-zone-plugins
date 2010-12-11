<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

include_spip('inc/presentation');

function exec_fiche_document(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('genealogie:fiche_document'), "", "");
$id_rubrique=genespip_creer_rubrique();
$url_action_document=generer_url_ecrire('fiche_document');
$url_nouveau_document=generer_url_ecrire('articles_edit');
$id_individu = $_GET['id_individu'].$_POST['id_individu'];
$id_article = $_GET['id_article'].$_POST['id_article'];
$action=$_GET['action'].$_POST['action'];

debut_gauche();
include_spip('inc/boite_info');
include_spip('inc/raccourcis_fiche');

debut_droite();

debut_cadre_relief(  "", false, "", $titre = _T('genespip:fiche_document'));
    debut_boite_info();

gros_titre(_T(genespip_nom_prenom($id_individu,3)));
   echo "<br /><fieldset><legend>"._T('genespip:liste_des_articles')."</b></i></legend>";
if ($action=="delete"){
genespip_supp_document($id_individu,$id_article);
}
if ($action=="Valider"){
genespip_ajout_document($id_individu,$id_article);
}
echo '<form action="'.$url_action_document.'" method="post">';

echo genespip_liste_document($id_individu);
echo "</form>";
echo "<hr />";
echo "<form action='".$url_action_document."' method='post'>";
echo "<table width='100%'><tr><td colspan='2'>";
echo _T('genespip:ajouter_nouveau_lien_article_spip_existant')"</td></tr>";
echo "<tr><td>"._T('genespip:article_spip_lie')."</td><td colspan='2'>";
echo genespip_choix_article();
echo "</td></tr>";
echo "<tr><td colspan='2'><input type='submit' value='"._T('genespip:valider')."' name='action' class='fondo' /></td></tr></table>";
echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
echo "</form>";
echo "<hr />";
echo icone_horizontale(_T('genespip:creer_document'), $url_nouveau_document."&id_rubrique=".$id_rubrique."&id_individu=".$id_individu, 'rien.gif', 'creer.gif');
   echo "</fieldset>";


fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>
