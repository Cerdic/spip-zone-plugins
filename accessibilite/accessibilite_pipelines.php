<?php

function accessibilite_pre_liens($texte){
	// uniquement dans le public
	//if (test_espace_prive()) return $texte;
	$regs = $match = array();
	// pour chaque lien
	if (preg_match_all(_RACCOURCI_LIEN, $texte, $regs, PREG_SET_ORDER)) {	
		foreach ($regs as $reg) {
			// si le lien est de type raccourcis "doc40"
			$type = typer_raccourci($reg[4]);
			if ($type[0] == 'document') {
				// Rechercher la taille du Doc dont l'id est dans $type[2]
				$row = sql_fetsel(
					array('taille', 'spip_types_documents.titre'), 
					array('spip_documents', 'spip_types_documents'), 
					array('id_document='.$type[2], 'spip_documents.extension=spip_types_documents.extension')
					);
				$title = (strpos($reg[0], '|') ? ' - ':'|') 
					. $row['titre'] 
					. ' (' . taille_en_octets($row['taille']) . ')';
				$lien = substr_replace($reg[0], $title, strpos($reg[0], '->'), 0);
				$texte = str_replace($reg[0], $lien, $texte);
			}
		}	
	}
	return $texte;
}

?>