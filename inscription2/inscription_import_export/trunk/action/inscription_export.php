<?php

/**
 * Action d'export CSV des auteurs
 * 
 * Plugin Inscription Import / Export
 * 
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_inscription_export_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	include_spip('inc/autoriser');
	if(autoriser('webmestre')){
		include_spip('inc/inscription_import');
		$delim = $arg ? $arg : (_request('delim') ? _request('delim') : ',');
		
		/**
		 * Récupération des champs à exporter
		 */
		$tablefield=inscription_import_table_fields();
		
		/**
		 * Export des tables mergées
		 */
		$output = inscription_import_csv_ligne($tablefield,$delim);
		spip_log($output,'test');
		$result = sql_select($tablefield,'spip_auteurs','','','','','',false);
		while ($row=sql_fetch($result)){
			$ligne=array();
			foreach($tablefield as $key)
			  if (isset($row[$key]))
			    $ligne[]=$row[$key];
				else
				  $ligne[]="";
			$output .= inscription_import_csv_ligne($ligne,$delim);
		}
	
		$charset = $GLOBALS['meta']['charset'];
	
		include_spip('inc/texte');
		$filename = preg_replace(',[^-_\w]+,', '_', translitteration(textebrut(typo(_T('inscription_import:export_users_sites',array('date' => date('Y-m-d'),'site'=>$GLOBALS['meta']['nom_site']))))));
	
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