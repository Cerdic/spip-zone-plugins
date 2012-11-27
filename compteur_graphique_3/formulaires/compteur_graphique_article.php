<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_compteur_graphique_article_charger_dist(){	
	$valeurs=array();
	return $valeurs;
}

function formulaires_compteur_graphique_article_traiter_dist(){
	$CG_nom_table = "spip_compteurgraphique";
	$id_article=_request('id_article');
	$res = array('editable'=>true);
	$res['message_ok'] = 'Aucune modification n\'a &eacute;t&eacute; enregistr&eacute;e';
	if ((_request('nouveau_habillage_creation')) 
		AND is_numeric(_request('nouveau_decompte'))
		AND is_numeric(_request('nouveau_chiffres'))
		AND is_numeric(_request('nouveau_habillage_creation'))
		) {
			if (_request('nouveau_decompte')==1) {$CG_dec="NULL";}
			else {$CG_dec=_request('choix_decompte');}
			$resultat_nouveau_compteur = sql_insertq($CG_nom_table,
			array("decompte" => $CG_dec,"id_article" => $id_article,"statut" => _request('nouveau_decompte'),
			"longueur" => _request('nouveau_chiffres'),"habillage" => _request('nouveau_habillage_creation')));
			$res['message_ok'] = 'Cr&eacute;ation du compteur enregistr&eacute;e';
	}
	elseif (_request('compteur_article_supprime')) {
		$resultat_suppr_compteur=sql_delete($CG_nom_table,"id_article=$id_article");
	}
	elseif ((_request('modification_article_validee'))
		AND is_numeric(_request('nouveau_habillage'))
		AND is_numeric(_request('nouveau_chiffres'))
		AND is_numeric(_request('nouveau_decompte'))
		AND is_numeric(_request('choix_decompte'))
		) {
		
		$maj_compteur=sql_updateq($CG_nom_table,array(
			"habillage" => _request('nouveau_habillage'),
			"longueur" => _request('nouveau_chiffres'),
			"statut" => _request('nouveau_decompte'),
			"decompte" => _request('choix_decompte')
			),"id_article = $id_article");
	}
	elseif (_request('reactiver_compteur_specifique_article')) {
		$resultat_suppr_compteur=sql_delete($CG_nom_table,"id_article=$id_article");
	}
	elseif (_request('interdire_compteur_specifique_article')) {
		$resultat_suppr_compteur=sql_delete($CG_nom_table,"id_article=$id_article");
		$resultat_interdiction_compteur=sql_insertq($CG_nom_table,array("id_article" => $id_article,"statut" => 3));
	}
	return $res;
}