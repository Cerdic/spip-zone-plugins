<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  ajouté en 08/2011 par Marcel BOLLA ... à partir de bilan.php           *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/navigation_modules');

function exec_compte_resultat()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
// initialisations
		$plan = sql_countsel('spip_asso_plan','active=1');
		$id_exercice = intval(_request('exercice'));
		if(!$id_exercice){
			/* on recupere l'id_exercice dont la date "fin" est "la plus grande" */
			$id_exercice = sql_getfetsel('id_exercice', 'spip_asso_exercices', '', '', 'fin DESC');
		}
		$id_destination = intval(_request('destination'));
		$exercice_data = sql_asso1ligne('exercice', $id_exercice);
		include_spip('inc/association_comptabilite');
// traitements
		onglets_association('titre_onglet_comptes');
		// INTRO : rappel de l'exercicee affichee
		$infos['exercice_entete_debut'] = association_datefr($exercice_data['debut'], 'dtstart');
		$infos['exercice_entete_fin'] = association_datefr($exercice_data['fin'], 'dtend');
		echo totauxinfos_intro($exercice_data['intitule'], 'exercice', $id_exercice, $infos);
		// pas de sommes de synthes puisque tous les totaux sont dans la zone centrale ;-
		// datation et raccourcis
		icones_association(array('comptes', "exercice=$id_exercice"), array(
			'encaisse_titre_general' => array('finances-24.png', 'encaisse', "exercice=$id_exercice".($destination?"&destination=$id_destination":'')),
			'cpte_bilan_titre_general' => array('finances-24.png', 'compte_bilan', "exercice=$id_exercice".($destination?"&destination=$id_destination":'')),
#			'annexe_titre_general' => array('finances-24.png', 'annexe', "exercice=$id_exercice".($destination?"&destination=$id_destination":'')),
		));
		// elements communs aux requetes
		if ($plan) {
			$join = ' RIGHT JOIN spip_asso_plan ON imputation=code';
			$sel = ', code, intitule, classe';
			$where = " date>='$exercice_data[debut]' AND date<='$exercice_data[fin]' ";
			$having = 'classe = ';
			$order = 'code';
		} else {
			$join = $sel = $where = $having = $order = '';
		}
		$var = serialize(array($id_exercice, $join, $sel, $where, $having, $order)); //!\ les cles numeriques peuvent poser probleme... <http://www.mail-archive.com/php-bugs@lists.php.net/msg100262.html> mais il semble qu'ici le souci vient de l'absence d'encodage lorsqu'on passe $var par URL...
//		$var = serialize(array('id'=>$id_exercice, '1'=>$join, '2'=>$sel, '3'=>$where, '4'=>$having, '5'=>$order));
		if(autoriser('associer', 'export_comptes')){ // on peut exporter : pdf, csv, xml, ...
			echo debut_cadre_enfonce('',true);
			echo '<h3>'. _T('asso:cpte_resultat_mode_exportation') .'</h3>';
			if (test_plugin_actif('FPDF')) { // impression en PDF : _T('asso:bouton_impression')
				echo icone1_association('PDF', generer_url_ecrire('export_compteresultats_pdf').'&var='.rawurlencode($var), 'print-24.png'); //!\ generer_url_ecrire() utilise url_enconde() or il est preferable avec les grosses variables serialisees d'utiliser rawurlencode()
			}
			foreach(array('csv','ctx','dbk','json','tex','tsv','xml','yaml') as $type) { // autres exports (donnees brutes) possibles
				echo icone1_association(strtoupper($type), generer_url_ecrire("export_compteresultats_$type").'&var='.rawurlencode($var), 'export-24.png'); //!\ generer_url_ecrire($exec, $param) equivaut a generer_url_ecrire($exec).'&'.urlencode($param) or il faut utiliser rawurlencode($param) ici...
			}
			echo fin_cadre_enfonce(true);
		}
		debut_cadre_association('finances-24.png', 'cpte_resultat_titre_general', $exercice_data['intitule']);
		// Filtres
		filtres_association(array(
			'exercice'=>$id_exercice,
			'destination'=>$id_destination,
		), 'compte_resultat');
		// liste des charges (depenses d'exploitation) cumulees par comptes
		$charges = association_liste_totaux_comptes_classes($GLOBALS['association_metas']['classe_charges'], 'cpte_resultat', '-1', $id_exercice, $id_destination);
		// liste des produits (recettes d'exploitation) cumules par comptes
		$produits = association_liste_totaux_comptes_classes($GLOBALS['association_metas']['classe_produits'], 'cpte_resultat', '+1', $id_exercice, $id_destination);
		// resultat comptable courant : c'est la difference entre les recettes et les depenses d'exploitation
		association_liste_resultat_net($produits, $charges);
		// liste des contributions volontaires (emplois et ressources) par comptes
		$contributions = association_liste_totaux_comptes_classes($GLOBALS['association_metas']['classe_contributions_volontaires'], 'cpte_benevolat', 0, $id_exercice, $id_destination);
		fin_page_association();
	}
}


	include_spip('inc/charsets');
	include_spip('inc/association_plan_comptable');

// Brique commune aux classes d'exportation des donnees du compte de resultat
class ExportCompteResultats {

	var $exercice;
	var $join;
	var $sel;
	var $where;
	var $having;
	var $order;
	var $out;

	function  __construct($var) {
		$tableau = unserialize(rawurldecode($var));
		$this->exercice = $tableau[0];
		$this->join = $tableau[1];
		$this->sel = $tableau[2];
		$this->where = $tableau[3];
		$this->having = $tableau[4];
		$this->order = $tableau[5];
		$this->out = '';
	}

	// de type CSV,INI,TSV, etc.
	function LignesSimplesEntete($champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='') {
		$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_code')))) .$champFin.$champsSeparateur;
		$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_intitule')))) .$champFin.$champsSeparateur;
		$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_montant')))) .$champFin.$lignesSeparateur;
	}

	// de type CSV,INI,TSV, etc.
	function LignesSimplesCorps($key, $champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='') {
		switch ($key) {
			case 'charges' :
				$quoi = "SUM(depense) AS valeurs";
				break;
			case 'produits' :
				$quoi = "SUM(recette) AS valeurs";
				break;
			case 'contributions_volontaires' :
				$quoi = "SUM(depense) AS charge_evaluee, SUM(recette) AS produit_evalue";
				break;
		}
		$query = sql_select(
			"imputation, $quoi ".$this->sel, // select
			'spip_asso_comptes '.$this->join, // from
			$this->where, // where
			$this->order, // group by
			$this->order, // order by
			'', // limit
			$this->having .$GLOBALS['association_metas']['classe_'.$key] // having
		);
		$chapitre = '';
		$i = 0;
		while ($data = sql_fetch($query)) {
			if ($key==='contributions_volontaires') {
				if ($data['charge_evaluee']>0) {
					$valeurs = $data['charge_evaluee'];
				} else {
					$valeurs = $data['produit_evalue'];
				}
			} else {
				$valeurs = $data['valeurs'];
			}
			$new_chapitre = substr($data['code'], 0, 2);
			if ($chapitre!=$new_chapitre) {
				$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), $new_chapitre) .$champFin.$champsSeparateur;
				$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), ($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'"))) .$champFin.$champsSeparateur;
				$this->out .= $champsSeparateur.' '.$champsSeparateur;
				$this->out .= $lignesSeparateur;
				$chapitre = $new_chapitre;
			}
			$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), $data['code']) .$champFin.$champsSeparateur;
			$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), $data['intitule']) .$champFin.$champsSeparateur;
			$this->out .= $champDebut.$valeurs.$champFin.$lignesSeparateur;
		}
	}

	// export texte de type tableau (lignes*colonnes) simple : CSV,CTX,HTML*SPIP,INI*,TSV,etc.
	// de par la simplicite recherchee il n'y a pas de types ou autres : CSV et CTX dans une certaine mesure pouvant distinguer "nombres", "chaines alphanumeriques" et "chaine binaires encodees"
	function exportLignesUniques($champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='', $entete=true) {
		if ($entete) {
			LignesSimplesEntete($champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='');
		}
		foreach (array('charges', 'produits', 'contributions_volontaires') as $nomClasse) {
			LignesSimplesCorps($nomClasse, $champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='');
		}
	}

	// export texte de type s-expression / properties-list / balisage (conteneurs*conteneurs*donnees) simple : JSON, XML (utilisable avec ASN.1), YAML, etc.
	// de par la simplicite recherchee il n'y a pas de types ou d'attributs : BSON, Bencode, JSON, pList, XML, etc.
	function exportLignesMultiples($balises, $echappements=array(), $champDebut='', $champFin='', $indent="\t", $entetesPerso='') {
		$this->out .= "$balises[compteresultat1]\n";
		if (!$entetesPerso) {
			$this->out .= "$indent$balises[entete1]\n";
			$this->out .= "$indent$indent$balises[titre1] $champDebut". utf8_decode(html_entity_decode(_T('asso:cpte_resultat_titre_general'))) ."$champFin $balises[titre0]\n";
			$this->out .= "$indent$indent$balises[nom1] $champDebut". $GLOBALS['association_metas']['nom'] ."$champFin $balises[nom0]\n";
			$this->out .= "$indent$indent$balises[exercice1] $champDebut". sql_asso1champ('exercice', $this->exercice, 'intitule') ."$champFin $balises[exercice0]\n";
			$this->out .= "$indent$balises[entete0]\n";
		}
		foreach (array('charges', 'produits', 'contributions_volontaires') as $nomClasse) {
			switch ($nomClasse) {
				case 'charges' :
					$quoi = "SUM(depense) AS valeurs";
					break;
				case 'produits' :
					$quoi = "SUM(recette) AS valeurs";
					break;
				case 'contributions_volontaires' :
					$quoi = "SUM(depense) AS charge_evaluee, SUM(recette) AS produit_evalue";
					break;
			}
			$baliseClasse = $nomClasse.'1';
			$this->out .= "$indent$balises[$baliseClasse]\n";
			$query = sql_select(
				"imputation, $quoi ".$this->sel, // select
			'spip_asso_comptes'.$this->join, // from
				$this->where, // where
				$this->order, // group by
				$this->order, // order by
			'', // limit
				$this->having .$GLOBALS['association_metas']['classe_'.$nomClasse] // having
			);
			$chapitre = '';
			$i = 0;
			while ($data = sql_fetch($query)) {
				if ($key==='contributions_volontaires') {
					if ($data['charge_evaluee']>0) {
						$valeurs = $data['charge_evaluee'];
					} else {
						$valeurs = $data['produit_evalue'];
					}
				} else {
					$valeurs = $data['valeurs'];
				}
				$new_chapitre = substr($data['code'], 0, 2);
				if ($chapitre!=$new_chapitre) {
					if ($chapitre!='') {
						$this->out .= "$indent$indent$balises[chapitre0]\n";
					}
					$this->out .= "$indent$indent$balises[chapitre1]\n";
					$this->out .= "$indent$indent$indent$balises[code1] $champDebut". str_replace(array_keys($echappements), array_values($echappements), $new_chapitre) ."$champFin $balises[code0]\n";;
					$this->out .= "$indent$indent$indent$balises[libelle1] $champDebut". str_replace(array_keys($echappements), array_values($echappements), ($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'"))) ."$champFin $balises[libelle0]\n";
					$chapitre = $new_chapitre;
				}
				$this->out .= "$indent$indent$indent$balises[categorie1]\n";
				$this->out .= "$indent$indent$indent$indent$balises[code1] $champDebut". str_replace(array_keys($echappements), array_values($echappements), $data['code']) ."$champFin $balises[code0]\n";
				$this->out .= "$indent$indent$indent$indent$balises[intitule1] $champDebut". str_replace(array_keys($echappements), array_values($echappements), $data['intitule']) ."$champFin $balises[intitule0]\n";
				$this->out .= "$indent$indent$indent$indent$balises[montant1] $champDebut".$valeurs."$champFin $balises[montant0]\n";
				$this->out .= "$indent$indent$indent$balises[categorie0]\n";
			}
			if ($chapitre!='') {
				$this->out .= "$indent$indent$balises[chapitre0]\n";
			}
			$baliseClasse = $nomClasse.'0';
			$this->out .= "$indent$balises[$baliseClasse]\n";
		}
		$this->out .= "$balises[compteresultat0]\n";
	}

	// fichier texte final a afficher/telecharger
	function leFichier($ext) {
		$fichier = _DIR_RACINE.'/'._NOM_TEMPORAIRES_ACCESSIBLES.'compte_resultats_'.$this->exercice.".$ext"; // on essaye de creer le fichier dans le cache local/ http://www.spip.net/fr_article4637.html
		$f = fopen($fichier, 'w');
		fputs($f, $this->out);
		fclose($f);
		header('Content-type: application/'.$ext);
		header('Content-Disposition: attachment; filename="'.$fichier.'"');
		readfile($fichier);
	}

}

?>