<?php
/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions');

// http://doc.spip.org/@exec_mots_tous_dist
function exec_grappes_dist()
{
	global $spip_lang, $spip_lang_left, $spip_lang_right;

	pipeline('exec_init',array('args'=>array('exec'=>'grappes'),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('grappes:titre_page_grappes'), "naviguer", "mots");
	echo debut_gauche('', true);


	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'grappes'),'data'=>''));


	$out = "";
	$grappes = sql_allfetsel("id_grappe,titre, ".sql_multi ("titre", "$spip_lang"), "spip_grappes", "", "", "multi");
	foreach ($grappes as $g) {
		$id_grappe = $g['id_grappe'];
		$titre_grappe = typo($g['titre']);		
		$out .= "<li><a href='#grappe-$id_grappe' onclick='$(\"div.grappe\").hide().filter(\"#grappe-$id_grappe\").show();return false;'>$titre_grappe</a></li>";
	}
	if (strlen($out))
		$out = "
		<ul class='raccourcis_rapides'>".$out."</ul>
		<a href='#' onclick='$(\"div.grappe\").show();return false;'>"._T('grappes:icone_voir_toutes_grappes')."</a>";

	if (autoriser('creer','grappe'))
		$out = icone_horizontale(_T('grappes:icone_creation_grappe'), generer_url_ecrire("grappes_edit","new=oui"), find_in_path('images/grappe-24.gif'), "creer.gif",false)
			.$out;
	echo bloc_des_raccourcis($out);
	


	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'grappes'),'data'=>''));
	echo debut_droite('', true);

	echo gros_titre(_T('grappes:titre_grappe'),'', false);
	if (autoriser('creer','grappe')) {
	  echo typo(_T('grappes:info_creation_grappes'));
	}
	echo "<br /><br />";

//
// On boucle d'abord sur les grappes
//

	$grappes = sql_allfetsel("*, ".sql_multi ("titre", "$spip_lang"), "spip_grappes", "", "", "multi");

	foreach ($grappes as $g) {
		$id_grappe = $g['id_grappe'];
		$titre_grappe = typo($g['titre']);
		$descriptif = $g['descriptif'];
		$options = ($o = unserialize($g['options'])) ? $o : array();
		$liaisons = $g['liaisons'];
		
		$editable = autoriser('modifier','grappe',$id_grappe);

		// Afficher le titre de la grappe
		echo "<div id='grappe-$id_grappe' class='grappe fiche_objet'>";
		//echo debut_cadre_enfonce("grappe-24.gif", true, '', $titre_grappe);
		echo "<div class='bandeau_actions'>"
			."<h1>$titre_grappe</h1>"
			."</div>";
		
		// Affichage des options  de la grappe (types d'elements affectables...)
		$res = '';
		
		$tables_liees = $liaisons ? explode(',',$liaisons) : array();
		
		$libelles = array(
			'articles'=>'grappes:info_lier_articles',
			'auteurs'=>'grappes:info_lier_auteurs',
			'mots'=>'grappes:info_lier_mots',
			'rubriques'=>'grappes:info_lier_rubriques',
			'syndic'=>'grappes:info_lier_sites',
		);
		$libelles = pipeline('libelle_association_grappes',$libelles);
		foreach($tables_liees as $table)
			if (strlen($table))
				$res .= "> " . _T(isset($libelles[$table])?$libelles[$table]:"$table:info_$table") . " &nbsp;&nbsp;";

		// afficher les restrictions d'associations (qui peut asocier)
		$res .= "<br />";
		if (is_array($options['acces'])){
			foreach ($options['acces'] as $type)
				$res .= "> "._T('grappes:info_acces_' . $type)." &nbsp;&nbsp;";
		}
		
 		echo "<span class='verdana1 spip_x-small'>", $res, "</span>";
 		
		if (strlen($descriptif)) {
			echo "<div class='verdana1 spip_small '>", propre($descriptif), "</div>";
		}

		//
		// Retrouver les elements lies/liables de la grappe
		//
		$tables_liees = array_map('objet_type',$tables_liees);

		$elements = sql_allfetsel("objet","spip_grappes_liens", "id_grappe=$id_grappe","objet");
		if (is_array($elements)) {
			foreach ($elements as $e) 
				if (!in_array($o = objet_type($e['objet']),$tables_liees))
					$tables_liees[]=$o;
		}

		// afficher les objets lies
		$grappes_lister_objets = charger_fonction('grappes_lister_objets','inc');
		foreach($tables_liees as $objet){
			echo $grappes_lister_objets($objet,'grappe',$id_grappe);
		}
	
		if ($editable){
			echo "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
			echo "<tr>";
			echo "<td>";
			echo icone_inline(_T('grappes:icone_modif_grappe'), generer_url_ecrire("grappes_edit","id_grappe=$id_grappe"), find_in_path("images/grappe-24.gif"), "edit.gif", $spip_lang_left);
			echo "</td>";
			echo "\n<td id='editer_grappe-$id_grappe-supprimer'",
			  (!$elements ? '' : " style='visibility: hidden'"),
			  ">";
			echo icone_inline(_T('grappes:icone_supprimer_grappe'), redirige_action_auteur('supprimer_grappe', "-$id_grappe", "grappes"), find_in_path("images/grappe-24.gif"), "supprimer.gif", $spip_lang_left);
			echo "</td></tr></table>";
		}	

		//echo fin_cadre(true);
		echo "</div>";
	}

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'grappes'),'data'=>''));


	echo fin_gauche(), fin_page();
}

?>
