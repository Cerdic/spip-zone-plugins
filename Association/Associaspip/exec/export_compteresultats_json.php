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

// Export du Compte de Resultat au format JSON
// http://fr.wikipedia.org/wiki/Json
function exec_export_compteresultats_json() {
	if (!autoriser('associer', 'export_compteresultats')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('inc/charsets');
		include_spip('inc/association_plan_comptable');
		$var = _request('var');
		$json = new JSON($var);
		$json->EnTete();
		foreach (array('charges', 'produits', 'contributions_volontaires') as $key) {
			$json->LesEcritures($key);
		}
		$json->Pied();
		$json->leFichier('json');
	}
}

/**
 *  Utilisation d'une classe tres tres tres simple !!!
 */
class JSON extends ExportCompteResultats {

	function EnTete() {
		$this->out .= "{\n\t'CompteDeResultat': \n";
		$this->out .= "\t\t{\n\t\t\t'Entete': \n\t\t\t{\n\t\t\t\n";
		$this->out .= "\t\t\t\t{'Titre': '". utf8_decode(html_entity_decode(_T('asso:cpte_resultat_titre_general'))) ."' }\n";
		$this->out .= "\t\t\t\t{'Nom': '". $GLOBALS['association_metas']['nom'] ."' }\n";
		$this->out .= "\t\t\t\t{'Exercice': '". sql_asso1champ('exercice', $this->exercice, 'intitule') ."' }\n";
		$this->out .= "\t\t\t}\n\t\t}\n"; // /Entete
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
		$this->out .= $this->out .= "\t\t{\n\t\t\t". ucfirst($key) ."': \n\t\t\t{\n\t\t\t\n";
		$query = sql_select(
			"imputation, $quoi, DATE_FORMAT(date, '%Y') AS annee ".$this->sel, // select
			'spip_asso_comptes'.$this->join, // from
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
				if ($chapitre!='') {
					$this->out .= "\t\t\t}\n"; // /Chapitre
				}
				$this->out .= "\t\t\t'Chapitre': \n\t\t\t{\n";
				$this->out .= "\t\t\t\t{'Code': '". str_replace(array("'",'<','>'), array('&quot;','&lt;','&gt;'), $new_chapitre) ."' }\n";
				$this->out .= "\t\t\t\t{'Libelle': '". str_replace(array("'",'<','>'), array('&quot;','&lt;','&gt;'), ($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'"))) ."' }\n";
				$chapitre = $new_chapitre;
			}
			$this->out .= "\t\t\t\t'Categorie': \n\t\t\t\t\t{\n";
			$this->out .= "\t\t\t\t\t\t{'Code': '". str_replace(array("'",'<','>'), array('&quot;','&lt;','&gt;'), $data['code']) ."' }\n";
			$this->out .= "\t\t\t\t\t\t{'Intitule': '". str_replace(array("'",'<','>'), array('&quot;','&lt;','&gt;'), $data['intitule']) ."' }\n";
			$this->out .= "\t\t\t\t\t\t{'Montant': '$valeurs' }\n";
			$this->out .= "\t\t\t\t\t}\n"; // /Categorie
		}
		if ($chapitre!='') {
			$this->out .= "\t\t\t}\n"; // /Chapitre
		}
		$this->out .= "\n\t\t\t}\n"; // /Classe
	}

	function Pied() {
		$this->out .= "\n}\n";
	}

}

?>