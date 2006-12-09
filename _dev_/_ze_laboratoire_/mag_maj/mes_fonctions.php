<?php

	// retourne l'extraction des url <telechargement_url> et <documentation_url> à partir de l'extraction sql 
	// chaine en entrée de la forme : doc|url_doc::zip|url_zip::svn|url_svn (les 3 types sont facultatifs)
		 function mag_maj_url_plugin($texte, $type_url='doc') {
							$Ttypes = array('doc', 'zip', 'svn');
							$Turl = array();
		 					$Texp = explode('::', $texte);
//$ret = '';
							foreach ($Texp as $exp) {
//$ret .= '<br>$exp = '.$exp;	
											$Telem = explode('|', $exp);
//$ret .= '<br>$Turl intègre :$Turl['.$Telem[0].'] = '.$Telem[1];							 
    									if (in_array($Telem[0], $Ttypes)) {
												 $Turl[$Telem[0]] = $Telem[1];
											}
							}
//return '<br>$ret = '.$ret.'<br>$Texp = '.print_r($Texp);
							return $Turl[$type_url];
		 }
		 
	// retourne le #TITRE du secteur à partir de l'id_rubrique
		 function nom_secteur($id_secteur) {
		 					$sql = spip_query("SELECT titre FROM spip_rubriques WHERE id_rubrique = $id_secteur LIMIT 1");
							$row = spip_fetch_array($sql);
							return $row['titre'];
		 }

?>