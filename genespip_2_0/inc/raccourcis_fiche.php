<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

if ($_GET['id_individu']!=NULL){$id_individu = $_GET['id_individu'];}else{$id_individu=$_POST['id_individu'];}

    $rac=icone_horizontale(_T('genespip:liste_patronyme'), generer_url_ecrire("genespip"), '../'._DIR_PLUGIN_GENESPIP.'/img_pack/globe.gif', '',false);
	$rac.=icone_horizontale(_T('genespip:retour_fiche'), generer_url_ecrire("fiche_detail&id_individu=".$id_individu), '../'._DIR_PLUGIN_GENESPIP.'/img_pack/globe.gif', '',false);
    $rac.=icone_horizontale(_T('genespip:parente'), generer_url_ecrire("fiche_parent&id_individu=".$id_individu), '../'._DIR_PLUGIN_GENESPIP.'/img_pack/globe.gif', '',false);
    $rac.=icone_horizontale(_T('genespip:lieux'), generer_url_ecrire("fiche_lieux&id_individu=".$id_individu), '../'._DIR_PLUGIN_GENESPIP.'/img_pack/globe.gif', '',false);
    $rac.=icone_horizontale(_T('genespip:document'), generer_url_ecrire("fiche_document&id_individu=".$id_individu), '../'._DIR_PLUGIN_GENESPIP.'/img_pack/globe.gif', '',false);
    $rac.=icone_horizontale(_T('genespip:enfants'), generer_url_ecrire("fiche_enfant&id_individu=".$id_individu), '../'._DIR_PLUGIN_GENESPIP.'/img_pack/globe.gif', '',false);
	echo bloc_des_raccourcis($rac);
?>
