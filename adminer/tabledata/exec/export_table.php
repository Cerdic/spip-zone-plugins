<?php
/* 
	Nota : la table ne doit contenir que des champs sans sauts de ligne.
	Si un champ contient des sauts de ligne, le fichier csv risque d'être tronqué ou malformé.
*/

function exec_export_table(){

	if( isset($_POST['table_exporter']) && $_POST['table_exporter'] != '') {
		
		
		$table		=	$_POST['table_exporter'];
		
		$query		=	"SELECT * FROM ".$table;
		$resQuery 	= 	mysql_query($query);
		
		$filename = 'export_table_'.$table.'.csv';
		
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename='.$filename);	
		
		if (mysql_num_rows($resQuery) != 0) {
			
			// titre des colonnes
			$fields = mysql_num_fields($resQuery);
			$i = 0;
			
			// on construit le titre de la colonne en commençant par ["] et en terminant par [";]
			while ($i < $fields) {
				echo "\"".mysql_field_name($resQuery, $i)."\";";
				$i++;
			}
		  
		  	echo "\n";
		
			// on construit la colonne elle même en commençant par ["] et en terminant par [";]
		  	while ($arrSelect = mysql_fetch_array($resQuery, MYSQL_ASSOC)) {
		   		foreach($arrSelect as $elem) {
					echo "\"$elem\";";
		   		}
		   	echo "\n";
		  	}
			
		}
		
	}

}

?>