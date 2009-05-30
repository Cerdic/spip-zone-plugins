<?php


// fonction adaptant la base de donnee
function webRadio_modifierDB() {
	sql_query("ALTER TABLE spip_documents ADD playlist ENUM('oui', 'non') NOT NULL DEFAULT 'non'");
	return _T('webradio:base_modifie');
}


// fonction transformant les liens dans les articles en document distant
// verifie si le document distant n'est pas deja present dans la base
// afin d'éviter les doublons
function webRadio_ajouterLien() {

 	//[TITRE->XXX.mp3]
	$pattern_raccourci_lien = ",\[([^][]*)->(>?)([^]]*\.mp3)\],msS";



	// afaire : tester si les liens sont fonctionnels //

	/* exemple de pattern a rechercher :
	 <object type="application/x-shockwave-flash" data="http://www.passerellesud.org/IMG/mp3/playerwpress.swf?soundFile=http://www.passerellesud.org/IMG/mp3/sans_papiers_04.05.mp3" height="50" width="400"><font face="verdana,arial,helvetica" size="2"> </font><param name="movie" value="http://www.passerellesud.org/IMG/
mp3/playerwpress.swf?soundFile=http://www.passerellesud.org/IMG/mp3/sans_papiers_04.05.mp3"><font face="verdana,arial,helvetica" size="2"> </font></object>
	*/
	
	$res = sql_select(
		Array('texte','id_article'),
		Array('spip_articles'),
		Array('statut='.sql_quote('publie'))
	);

	$list = '<ul>';
	while ($row = sql_fetch($res)) {
		if (preg_match_all($pattern_raccourci_lien, $row['texte'], $matches, PREG_SET_ORDER)) {
			foreach ($matches as $regs) {
				// on ajoute dans spip_documents en tant que document distant
				$titre = $regs[1];
				$fichier = $regs[3];
				$distant = 'oui';
				$idx = '1';
				$id_article = $row['id_article'];
				$id_type = 17; // mp3
				$descriptif = '';
				$taille = 10;
				$hauteur = 0;
				$largeur = 0;
				$mode= 'document';

				// si fichier est déjà la, ne pas le rajouter

				$result = sql_fetch(sql_select(
					Array('fichier'),
					Array('spip_documents'),
					Array('fichier = '.sql_quote($fichier))
				));

				if (!( $result['fichier'] == $fichier)) {
					$list .= '<li>'.$fichier.'</li>';

					sql_insertq (
						'spip_documents',
						Array(
						'titre' => $titre,
						'fichier' => $fichier,
						'distant' => $distant,
						'idx' => $idx,
						'id_type' => $id_type,
						'taille' => $taille,
						'hauteur' => $hauteur,
						'largeur' => $largeur,
						'descriptif' => $descriptif,
						'mode' => $mode,
						'date' => 'NOW()',
						'maj' => 'NOW()'
						)
					);
	
	
					$ret = sql_fetch(sql_select(
						array('MAX(id_document) as id_document'),
						array('spip_documents')
					));
	
					$id_document = $ret['id_document'];
					$id_article = $row['id_article'];
	
					sql_insertq (
						'spip_documents_articles',
						Array(
						'id_document' => $id_document,
						'id_article' => $id_article
						)
					);
				}
			}
		}
	}
	$list .= '</ul>';
	return '<b>'._T('webradio:liste_modifie').'</b><br />'.$list.'<br />';
}

?>