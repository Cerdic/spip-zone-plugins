<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/
	if ($_GET['id_individu']!=NULL){$id_individu = $_GET['id_individu'];}else{$id_individu=$_POST['id_individu'];}
	echo debut_boite_info(true);
	if ($id_individu){
		echo gros_titre(_T("genespip:fiche_no") .$id_individu, '', false);
		echo icone_horizontale(_T('voir_en_ligne'), generer_url_public('individu')."&id_individu=".$id_individu."&var_mode=calcul", 'racine-24.gif', 'rien.gif',false);
	}else{
		echo propre(_T('genespip:info_doc'));
	}
	echo  fin_boite_info(true);
?>
