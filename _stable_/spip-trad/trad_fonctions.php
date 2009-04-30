<?php


	/**
	 * SPIP-Trad
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function trad_autoriser() {}
	
	
	function autoriser_trad_dist($faire, $type, $id, $qui, $opt) {
		switch ($faire) {
			case 'voir':
			case 'editer':
				return ($qui['statut'] == '0minirezo');
				break;
			default:
				return false;
				break;
		}
	}


	function trad_exec_init($flux) {
		$exec			= $flux['args']['exec'];
		$id_rubrique	= $flux['args']['id_rubrique'];
		switch($exec) {
			case 'naviguer':
				if (autoriser('editer', 'trad')) {
					if (!empty($_POST['lier_traduction'])) {
						$rubrique_reference = sql_select('id_trad', 'spip_rubriques', 'id_rubrique='.intval($_POST['id_parent']));
						if (sql_count($rubrique_reference) == 1) {
							$t = sql_fetch($rubrique_reference);
							sql_updateq('spip_rubriques', array('id_trad' => intval($t['id_trad'])), 'id_rubrique='.intval($id_rubrique));
						}
					}
					if ($_GET['supprimer_traductions'] == 'oui') {
						$traductions = sql_select('id_rubrique', 'spip_rubriques', 'id_trad='.intval($id_rubrique).' AND id_rubrique!='.intval($id_rubrique), '', '', '1');
						if (sql_count($traductions) == 1) {
							$t = sql_fetch($traductions);
							sql_updateq('spip_rubriques', array('id_trad' => intval($t['id_rubrique'])), 'id_trad='.intval($id_rubrique));
						}
						sql_updateq('spip_rubriques', array('id_trad' => 'id_rubrique'), 'id_rubrique='.intval($id_rubrique));
					}
				}
				break;
		}
		return $flux;
	}
	
	
	function trad_affiche_milieu($flux) {
		$exec			= $flux['args']['exec'];
		$id_rubrique	= $flux['args']['id_rubrique'];
		switch($exec) {
			case 'naviguer':
				if (autoriser('voir', 'trad')) {
					$flux['data'].= trad_afficher_traductions($id_rubrique);
				}
				break;
		}
		return $flux;
	}
	
	
	function trad_calculer_rubriques($flux) {
		sql_update('spip_rubriques', array('id_trad' => 'id_rubrique'), 'id_trad=0');
		return $flux;
	}


	function trad_afficher_traductions($id_rubrique) {
		$traductions = '<form method="post" action="'.generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique).'">';
		$bouton = bouton_block_depliable(_L('TRADUCTIONS DE CETTE RUBRIQUE'), false, 'traductions_rubrique');
		$traductions.= debut_cadre_enfonce(_DIR_PLUGIN_TRAD.'/prive/images/traductions-24.png', true, "", $bouton);
		$id_trad = sql_getfetsel('id_trad', 'spip_rubriques', 'id_rubrique='.intval($id_rubrique));
		$trads = sql_select('*', 'spip_rubriques', 'id_trad='.intval($id_trad));
		if (sql_count($trads) > 1) {
			$existe_trads = true;
			$traductions.= "<div class='liste'>\n";
			$traductions.= "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>\n";
			while ($arr = sql_fetch($trads)) {
				$traductions.= "<tr class='tr_liste'>\n";
				$traductions.= "<td width='12' class='arial11'>\n";
				$traductions.= "</td>\n";
				$traductions.= "<td class='arial2'>\n";
				$traductions.= "<a href='".generer_url_ecrire("naviguer","id_rubrique=".$arr['id_rubrique'])."'>";
				$traductions.= typo($arr['titre']);
				$traductions.= "</a>";
				$traductions.= "</td>\n";
				$traductions.= "<td class='arial2'>\n";
				if ($arr['id_rubrique'] == $id_trad)
					$traductions.= _L('Rubrique de référence');
				else
					$traductions.= '';
				$traductions.= "</td>\n";
				$traductions.= "<td class='arial1'>\n";
				$traductions.= "<b>"._T('info_numero_abbreviation').$arr['id_rubrique']."</b>";
				$traductions.= "</a>\n";
				$traductions.= "</td>\n";
				$traductions.= "</tr>\n";
			}
			$traductions.= "</table>";
			$traductions.= "</div>";
		}

		$traductions.= debut_block_depliable(false, 'traductions_rubrique');
		if (!$existe_trads) {
			$traductions.= "<form action='" . generer_url_ecrire("naviguer","id_rubrique=$id_rubrique") . "' method='post' style='margin:0px; padding:0px;'>";
			$traductions.= _L('Cette rubrique est une traduction de la rubrique :');
			$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
			$traductions.= $selecteur_rubrique(0, 'rubrique', false);
			$traductions.= "<div align='right'><input type='submit' name='lier_traduction' value='"._T('bouton_valider')."' class='fondl'></div>";
			$traductions.= "</form>";
		}
		if ($existe_trads AND $id_trad == $id_rubrique) {
			$traductions.= "<table width='100%'><tr>";
			$traductions.= "<td>";
			$traductions.= icone_horizontale(_L('Ne plus lier ces rubriques à celle-ci'), generer_url_ecrire("naviguer","id_rubrique=$id_rubrique&supprimer_traductions=oui"), '', "supprimer.gif", false);
			$traductions.= "</td>\n";
			$traductions.= "</tr></table>";
		}
		$traductions.= fin_block();

		$traductions.= fin_cadre_enfonce(true);
		
		return $traductions;
	}


	/**
	 * balise_URL_LANG
	 *
	 * @param p est un objet SPIP
	 * @return string url d'un secteur
	 * @author Pierre Basson
	 **/
	function balise_URL_LANG($p) {
		$lang = champ_sql('lang', $p);
		$p->code = "generer_url_lang($lang)";
		$p->statut = 'php';
		return $p;
	}


	/**
	 * generer_url_lang
	 *
	 * @param p est un objet SPIP
	 * @return string url d'un secteur
	 * @author Pierre Basson
	 **/
	function generer_url_lang($lang) {
		$id_rubrique = sql_getfetsel('id_rubrique', 'spip_rubriques', 'id_parent=0 AND lang="'.addslashes($lang).'"');
		$url = generer_url_entite($id_rubrique, 'rubrique');
		return $url;
	}


/** BOUCLE LANGUES **/

$langues = array(
	"lang" => "varchar(10)"
);
$langues_key = array(
	"PRIMARY KEY"	=> "lang"
);
$GLOBALS['tables_principales']['spip_langues'] =
	array('field' => &$langues, 'key' => &$langues_key);
$GLOBALS['table_des_tables']['langues'] = 'langues';

function boucle_LANGUES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];

	if (count($boucle->separateur))
	  $code_sep= ("'". ereg_replace("'","\'",join('',$boucle->separateur)) ."'");
	else
	  $code_sep="''";

  	$liste= "calcule_langues()";

	$code=<<<CODE
	\$SP++;
	\$code=array();
	\$l= $liste;
	foreach(\$l as \$k) {
		\$Pile[\$SP]['lang'] = \$k;
		\$code[]=$boucle->return;
	}
	\$t0= join($code_sep, \$code);
	return \$t0;
CODE;

	return $code;
}

function calcule_langues() {
	$res = sql_select('DISTINCT(lang)', 'spip_rubriques', 'id_parent=0 AND statut="publie"');
	$tab = array();
	while ($arr = sql_fetch($res)) {
		$tab[] = $arr['lang'];
	}
	return $tab;
}

?>