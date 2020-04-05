<?php

function imports_arrondissements_dist(){
	include_spip('inc/charset');

	$fichier = preg_files(_DIR_PLUGIN_GEOGRAPHIE . "/base/",'arrondissements[.]txt$');
	//echo count( $fichier),' trouvé, ',$fichier[0];
	lire_fichier( $fichier[0], $arronds);	//echo $arronds;
	if (($arronds = unserialize($arronds))) {
	  $liCpt=0;
		foreach( $arronds as $lsAbbr => $laDptArronds ) {
		  foreach( $laDptArronds as $liIndex => $laDptArrond) {
				if ($liIdDepartement = sql_getfetsel( 'id_departement', 'spip_geo_departements', '(abbr='. "'$lsAbbr')")) {//sql_quote( $lsAbbr).')')) {
					if ($liIdCommune = sql_getfetsel( 'id_commune', 'spip_geo_communes', "(id_departement=$liIdDepartement) AND (nom=".sql_quote( $laDptArrond['commune']).')')) {
						if (sql_insertq('spip_geo_arrondissements', array(
								'id_departement'=>$liIdDepartement,
								'nom'=>$laDptArrond['nom'],
								'id_commune'=>$liIdCommune,
								'population'=>$laDptArrond['population'],
								'superficie'=>$laDptArrond['superficie'],
              	'densite'   =>$laDptArrond['densite'],
              	'nb_communes'=>$laDptArrond['nb_communes'],
							))) {
							$liCpt++;
						}	else { echo 'Echec à la création de ', $laDptArrond['abbr'],':', $laDptArrond['commune'],'<br>';	};
					} else { echo 'Commune ', $laDptArrond['abbr'],': ', $laDptArrond['commune'],' non trouvée !<br>'; }
				} else { echo "Département $lsAbbr non trouvé !<br>";	}
			}
		}
	}
	echo "$liCpt arrondissements installés.<br/>";
}