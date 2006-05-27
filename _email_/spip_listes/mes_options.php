<?php

//$dossier_squelettes = " "; 


//
// Definition de tous les extras possibles
//

$champs_extra = true;

	$GLOBALS['champs_extra'] = Array (
		'auteurs' => Array (
				"abo" => "radio|brut|Format|Html,Texte,D&eacute;sabonnement|html,texte,non"

			),
			
		'articles' => Array (
				'squelette' => 'bloc|propre|Bibliographie'

			)

		);
		
		$GLOBALS['champs_extra_proposes'] = Array (
'auteurs' => Array (
		'tous' => 'abo',
		'inscription' => 'abo'
	        ),
'articles' => Array (
		'0' => 'squelette',
		'tous' => ''
		
                )
				
);


include('inc/options_spip_listes.php3');

?>
