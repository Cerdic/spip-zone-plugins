<?php

/**
 * Action d'export CSV des auteurs
 * 
 * Plugin Inscription 2 Import / Export
 * 
 */
function action_i2_export_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	include_spip('inc/autoriser');
	if(autoriser('webmestre')){
		include_spip('inc/i2_import');
		$delim = $arg ? $arg : (_request('delim') ? _request('delim') : ',');
		
		/**
		 * Récupération des champs à exporter
		 */
		$tables = array('spip_auteurs','spip_auteurs_elargis');
		$tablefield=i2_import_table_fields($tables);
		
		/**
		 * Export des tables mergées
		 */
		$output = i2_import_csv_ligne($tablefield,$delim);
	
		$result = sql_select($tablefield,'spip_auteurs AS A LEFT JOIN spip_auteurs_elargis AS B USING(id_auteur)','','','','','',false);
		while ($row=sql_fetch($result)){
			$ligne=array();
			foreach($tablefield as $key)
			  if (isset($row[$key]))
			    $ligne[]=$row[$key];
				else
				  $ligne[]="";
			$output .= i2_import_csv_ligne($ligne,$delim);
		}
	
		$charset = $GLOBALS['meta']['charset'];
	
		include_spip('inc/texte');
		$filename = preg_replace(',[^-_\w]+,', '_', translitteration(textebrut(typo(_T('i2_import:export_users_sites',array('date' => date('Y-m-d'),'site'=>$GLOBALS['meta']['nom_site']))))));
	
		// Excel ?
		if ($delim == ',')
			$extension = 'csv';
		else {
			$extension = 'xls';
			# Excel n'accepte pas l'utf-8 ni les entites html... on fait quoi?
			include_spip('inc/charsets');
			$output = unicode2charset(charset2unicode($output), 'iso-8859-1');
			$charset = 'iso-8859-1';
		}
	
		Header("Content-Type: text/comma-separated-values; charset=$charset");
		Header("Content-Disposition: attachment; filename=$filename.$extension");
		Header("Content-Length: ".strlen($output));
		echo $output;
		exit;
	}
	return;
}
?>