<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/
	if ($_GET['id_individu']!=NULL){$id_individu = $_GET['id_individu'];}else{$id_individu=$_POST['id_individu'];}
	$result = spip_query("SELECT id_individu FROM spip_genespip_individu where poubelle = '1'");
	$compte = mysql_num_rows($result);
	
	if ($id_individu){
		$rac.=icone_horizontale(_T('genespip:retour fiche'), generer_url_ecrire("fiche_detail&id_individu=".$id_individu), '../'._DIR_PLUGIN_GENESPIP.'/img_pack/globe.gif', '',false);
	}
	$rac.=icone_horizontale(_T('genespip:lieux'), generer_url_ecrire("fiche_lieux&id_individu=".$id_individu), '../'._DIR_PLUGIN_GENESPIP.'/img_pack/globe.gif', '',false);
	$rac.=icone_horizontale(_T('genespip:gedcom'), generer_url_ecrire("genespip_database"), '../'._DIR_PLUGIN_GENESPIP.'/img_pack/globe.gif', '',false);
	if ($compte > 0){
		$rac.=icone_horizontale(_T('genespip:poubelle'), generer_url_ecrire("genespip&poubelle=1"), '../'._DIR_PLUGIN_GENESPIP.'/img_pack/poubelle.gif', '',false);
	}
	echo bloc_des_raccourcis($rac);
?>