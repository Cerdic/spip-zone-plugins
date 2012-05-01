<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 01/2012                                      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('exec/compte_resultat'); // c'est pour la definition de classe ExportCompteResultats

// Export du Compte de Resultat au format TSV
// http://fr.wikipedia.org/wiki/Format_TSV
function exec_export_compteresultats_tsv() {
	if (!autoriser('associer', 'export_comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$tsv = new ExportCompteResultats(_request('var'));
		$tsv->exportLignesUniques("\t", "\n", array("\t"=>'\t',"\n"=>'\n'), '"', '"');
		$tsv->leFichier('tab');
	}
}

/**
 *  Utilisation d'une classe tres tres tres simple !!!
 */
class TSV extends ExportCompteResultats {

	function EnTete() {
		$this->out .= str_replace(array("\t","\n"), array('\t','\n'), utf8_decode(html_entity_decode(_T('asso:entete_code')))) ."\t";
		$this->out .= str_replace(array("\t","\n"), array('\t','\n'), utf8_decode(html_entity_decode(_T('asso:entete_intitule')))) ."\t";
		$this->out .= str_replace(array("\t","\n"), array('\t','\n'), utf8_decode(html_entity_decode(_T('asso:entete_montant')))) ."\n";
	}

	function LesEcritures($key) {
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
			"imputation, $quoi, DATE_FORMAT(date, '%Y') AS annee".$this->sel, // select
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
				$this->out .= str_replace(array("\t","\n"), array('\t','\n'), $new_chapitre) .'",';
				$this->out .= str_replace(array("\t","\n"), array('\t','\n'), ($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'"))) ."\t\n";
				$chapitre = $new_chapitre;
			}
			$this->out .= str_replace(array("\t","\n"), array('\t','\n'), $data['code']) ."\t";
			$this->out .= str_replace(array("\t","\n"), array('\t','\n'), $data['intitule']) ."\t";
			$this->out .= $valeurs."\n";
		}
	}

}

?>