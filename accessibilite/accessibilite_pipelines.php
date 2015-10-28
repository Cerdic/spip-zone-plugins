<?php

function accessibilite_pre_liens($texte){
	if (!defined('_ACCESSIBILITE_CONSERVER_BULLE')) define('_ACCESSIBILITE_CONSERVER_BULLE', false);
	$regs = $match = array();
	// pour chaque lien
	if (preg_match_all(_RACCOURCI_LIEN, $texte, $regs, PREG_SET_ORDER)) {	
		foreach ($regs as $reg) {
			// Attributs du lien (texte, bulle, lang)
			$intitule = traiter_raccourci_lien_atts($reg[1]);
			// si le lien est de type raccourcis "doc40"			
			$type = typer_raccourci($reg[4]);
			if (count($type) AND $type[0] == 'document') {
				// Rechercher la taille du Doc dont l'id est dans $type[2]
				$row = sql_fetsel(
					array('TT1.titre as T1', 'taille', 'TT2.titre as T2'), 
					array('spip_documents AS TT1', 'spip_types_documents AS TT2'), 
					array('id_document='.$type[2], 'TT1.extension=TT2.extension')
					);
				$textelien = ($intitule[0]) ? $intitule[0]:supprimer_numero(typo($row['T1']));
				$langue = ($intitule[2]) ? '{'.$intitule[2].'}':'';
				// Si intitulé du lien, le reprendre,
				// Sinon, si titre pour le doc, le reprendre,
				// Sinon remplacer par "Document"
				$titredoc = ($intitule[0]) ? $intitule[0]:
					(($row['T1']) ? $row['T1']:_T('info_document'));
				// Quand un title est spécifie il doit etre plus plus long que l'intitule
				// car les lecteurs d'ecran lisent le plus long des deux
				$title = ((($intitule[1]) && _ACCESSIBILITE_CONSERVER_BULLE) ? 
						textebrut(supprimer_numero(typo($intitule[1]))) . ' (' . textebrut(supprimer_numero(typo($titredoc))) . ')':textebrut(supprimer_numero(typo($titredoc)))) // Le texte du lien + Nom du doc
					. ' &ndash; ' . $row['T2'] // Le type du doc
					. ' (' . taille_en_octets($row['taille']) . ')' // sa taille
					. (($intitule[2]) ? ' ('.traduire_nom_langue($intitule[2]).')':''); // La langue presente dans le lien (malheureusement, info non disponible dans la table spip_documents)
				
				// Rebatir le raccourcis typo du lien avec les informations modifiees
				$lien = '['. $textelien . '|'. $title .$langue .'->'. $reg[4] .']';
				$texte = str_replace($reg[0], $lien, $texte);
			}
		}	
	}
	return $texte;
}

?>