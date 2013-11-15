<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@inc_instituer_article_dist
function inc_instituer_projet_dist($id_projet, $statut, $id_parent)
{
	if (!autoriser('modifier', 'projet', $id_projet)) return '';

	$res = '';

	$etats = $GLOBALS['liste_des_etats'];

	if (!autoriser('creer', 'projet', $id_parent)) {
		unset($etats[array_search('publie', $etats)]);
		unset($etats[array_search('refuse', $etats)]);
		if ($statut == 'prepa')
			$res = supprimer_tags(_T('texte_proposer_publication'));
	}

	$res .=
	  "<ul id='instituer_projet-$id_projet' class='instituer_projet instituer'>"
	  . "<li>" . _T('projets:texte_projets_statut')
	  ."<ul>";

	$href = redirige_action_auteur('instituer_projet',$id_projet,'projets', "id_projet=$id_projet");

	foreach($etats as $affiche => $s){
		$puce = puce_statut($s) . _T($affiche);
		if ($s==$statut)
			$class=' selected';
		else {
			$class='';
			$puce = "<a href='"
			. parametre_url($href,'statut_nouv',$s)
			. "' onclick='return confirm(confirm_changer_statut);'>$puce</a>";
		}
		$res .= "<li class='$s $class'>$puce</li>";
	}

	$res .= "</ul></li></ul>";

	return $res;
}
?>