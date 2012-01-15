<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function sjcycle_affiche_gauche(&$flux){
	include_spip('inc/documents');
	if ($flux['args']['exec'] == 'articles_edit') {
		$document='';
		$document = sql_countsel('spip_documents as docs JOIN spip_documents_liens AS lien ON docs.id_document=lien.id_document', '(lien.id_objet='.$flux["args"]["id_article"].') AND (lien.objet="article") AND (docs.extension REGEXP "jpg|png|gif")');
		if ($document<2){
			return $flux;
		}
		$flux['data'] .= debut_cadre_enfonce(url_absolue(find_in_path('images/sjcycle.png')), true, '', 'Diaporama <a href="../plugins/sjcycle/doc.php?art='.$flux["args"]["id_article"].'" target="_blank" onclick="javascript:window.open(\'../plugins/sjcycle/doc.php?art='.$flux["args"]["id_article"].'\', \'aide\', \'scrollbars=yes,resizable=yes,width=740,height=580\');; return false;" rel="#sjcycle_infobulle" id="sjcyle_aide" title="Cliquer pour ouvrir l\'aide dans une nouvelle fen&ecirc;tre"><img src="../prive/images/aide.gif" alt="Cliquer pour ouvrir l\'aide dans une nouvelle fen&ecirc;tre" class="aide" title="Cliquer pour ouvrir l\'aide dans une nouvelle fen&ecirc;tre" /></a>', "", "");
		$flux['data'] .= '<div style="padding:2px;margin:10px 0px;" class="arial1 spip_xx-small">'
				. affiche_raccourci_doc('sjcycle', $flux["args"]["id_article"], 'left')
				. affiche_raccourci_doc('sjcycle', $flux["args"]["id_article"], 'center')
				. affiche_raccourci_doc('sjcycle', $flux["args"]["id_article"], 'right')
				. "</div>\n";
		$flux['data'] .= debut_boite_info(true).'Recopiez l\'un de ces raccourcis et recopiez-le &agrave; l’int&eacute;rieur de la case « Texte », l&agrave; vous d&eacute;sirez le situer le diaporama dans votre article.<br /><br />
		Cliquer sur <a href="../plugins/sjcycle/doc.php?art='.$flux["args"]["id_article"].'" target="_blank" rel="#sjcycle_infobulle" id="sjcyle_aide" title="Cliquer pour consulter l\'aide dans une nouvelle fen&ecirc;tre" onclick="javascript:window.open(\'../plugins/sjcycle/doc.php?art='.$flux["args"]["id_article"].'\', \'aide\', \'scrollbars=yes,resizable=yes,width=740,height=580\');; return false;"><img src="../prive/images/aide.gif" alt="Cliquer pour ouvrir l\'aide dans une nouvelle fen&ecirc;tre" class="aide" title="Cliquer pour consulter l\'aide dans une nouvelle fen&ecirc;tre" /></a> pour consulter l\'aide en ligne.';
		$flux['data'] .= fin_boite_info(true);
		$document='';
	 	$document = sql_countsel('spip_documents as docs JOIN spip_documents_liens AS lien ON docs.id_document=lien.id_document', '(lien.id_objet='.$flux["args"]["id_article"].') AND (lien.objet="article") AND (docs.extension REGEXP "jpg|png|gif")');
		$flux['data'] .= fin_cadre_enfonce(true);
	 }
    return $flux;
}
?>