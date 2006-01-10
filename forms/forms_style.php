<?php
function forms_style($letexte){
	/*
	 * Sondages et formulaires
	 */

	$letexte .= ".spip_forms {
		margin: 10px;
		padding: 10px;
		border: 1px dashed " . $couleur_foncee ."	}";
		
	$letexte .= ".spip_sondage .ligne_barre {
		height: 8px;
		background: " . $couleur_foncee . "
		border: 1px solid black;
	}";

	$letexte .= ".spip_sondage .sondage_table {
		display: table;
	}";
	$letexte .= ".spip_sondage .sondage_ligne {
		display: table-row;
	}";
	
	$letexte .= ".spip_sondage .sondage_ligne > div {
		display: table-cell;
		vertical-align: middle;
		padding: 2px;
		white-space: nowrap;
	}";
	
	return $letexte;
}
?>
