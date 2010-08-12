<?php

function accessibilite_pre_liens($texte){
	define(_ACCESSIBILITE_CONSERVER_BULLE, false);
	$regs = $match = array();
	// pour chaque lien
	if (preg_match_all(_RACCOURCI_LIEN, $texte, $regs, PREG_SET_ORDER)) {	
		foreach ($regs as $reg) {
			// Attributs du lien (texte, bulle, lang)
			$intitule = traiter_raccourci_lien_atts($reg[1]);
			// si le lien est de type raccourcis "doc40"			
			$type = typer_raccourci($reg[4]);
			if ($type[0] == 'document') {
				// Rechercher la taille du Doc dont l'id est dans $type[2]
				$row = sql_fetsel(
					array('spip_documents.titre as T1', 'taille', 'spip_types_documents.titre as T2'), 
					array('spip_documents', 'spip_types_documents'), 
					array('id_document='.$type[2], 'spip_documents.extension=spip_types_documents.extension')
					);
				$textelien = ($intitule[0]) ? $intitule[0]:$row['T1'];
				$langue = ($intitule[2]) ? '{'.$intitule[2].'}':'';
				// Si pas de titre pour le doc, remplacer par "Document"
				$titredoc = ($row['T1']) ? $row['T1']:_T('info_document');
				// Quand un title est sp�cifie il doit etre plus plus long que l'intitule
				// car les lecteurs d'ecran lisent le plus long des deux
				$title = ((($intitule[1]) && _ACCESSIBILITE_CONSERVER_BULLE) ? $titredoc. ' &ndash; ' .$intitule[1]:$titredoc) // Le texte du lien + Nom du doc
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