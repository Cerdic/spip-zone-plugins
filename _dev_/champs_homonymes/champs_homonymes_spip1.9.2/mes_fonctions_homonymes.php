<?php

function extra_homonyme_maj($id, $type, $action='') {

		$type = strtolower(substr(trim($type), 0, 2));
        switch ($type) {
                case 'ar':
                        $id_table = 'id_article';
						$type = 'articles';
                        //$id=$GLOBALS['id_article'];
                        break;
                case 'br':
                        $id_table = 'id_breve';
						$type = 'breves';
                        //$id=$GLOBALS['id_breve'];
                        break;
                case 'ru':
                        $id_table = 'id_rubrique';
						$type = 'rubriques';
                        //$id=$GLOBALS['id_rubrique'];
                        break;
                case 'au':
                        $id_table = 'id_auteur';
						$type = 'auteurs';
                        //$id=$GLOBALS['id_auteur'];
                        break;
                case 'si':
                        $id_table = 'id_syndic';
						$type='syndic';
                        //$id=$GLOBALS['id_syndic'];
                        break;
                case 'mo':
                        $id_table = 'id_mot';
						$type = 'mots';
                        //$id=$GLOBALS['id_mot'];
                        break;
                        
                default:
                        $id_table ='';
           break;
       }
        
        $table = spip_fetch_array(spip_query("SELECT * FROM spip_$type WHERE $id_table=$id"));
		if (!$table["extra"]) return;
		$extra = unserialize ($table["extra"]);
       
		if ($action=='chverse'){
                while (list($champ,$contenu) = each($extra)) {
                        // Pour chaque nom de champs extra 
                        // vérifier si la table comporte un champs du même nom (homonyme)
                        if (isset($table[$champ])){
								if ($extra[$champ]!=$table[$champ]){
                                	//Si oui, changer la valeur dans le champs extra par celle du champs de la table
                                	$extra[$champ]=$table[$champ];
									$modification=1;
								}
                        }
                }
				/******************************************************/
				if ($modification==1){// si la valeur d'un champ homonyme dans la table diffère de celui dans le champs extra, mettre à jour le champ extra
								$extra = serialize($extra);
                               $query = "UPDATE spip_$type SET 
                                extra ='".$extra."'
                                WHERE $id_table=".$id;
								//echo $query;
								$result = spip_query($query);
								debug($result);
                               //$trace .= spip_query($query) OR die($query);
				}
				/******************************************************/
		}else if($action=='eversch'){
                while (list($champ,$contenu) = each($extra)) {
                        // Pour chaque nom de champs extra 
                        // vérifier si la table comporte un champs du même nom (homonyme)
                        if (isset($table[$champ])){
                                //Si oui, mettre à jour la valeur des champs de la table par la valeur du champs extra du même nom
                                $query = "UPDATE spip_$type SET 
                                $champ='".addslashes($extra[$champ])."'
                                WHERE $id_table=".$id;
                                $trace .= spip_query($query) OR die($query);
                        }
                 }
        }  
}
?>