<?


/* utilisation du pipeline qui permet de rajouter des libéllés sur les choses sur lesquelles on peut associer les mots */
function MotsPartout_libelle_association_mots($libelles){
    $tables_installees = unserialize(lire_meta('MotsPartout:tables_installees'));
    foreach ($tables_installees as $table) {
    	if(!isset($libelles[$table]))
      $libelles[$table] = 'motspartout:libelle_'.strtolower($table);
    }
		return $libelles;
	}



function MotsPartout_editer_contenu_objet($flux){
		if ($flux['args']['type'] == 'groupe_mot'){

	    $tables_installees = unserialize(lire_meta('MotsPartout:tables_installees'));
      foreach ($tables_installees as $table) {
  			$checked = in_array($table, $flux['args']['contexte']['tables_liees']);
  			$checked = $checked ? ' checked="checked"' : '';
  			$input .= '<div class="choix"><input type="checkbox" class="checkbox" name="tables_liees&#91;&#93;" value="'.$table.'" id="motpartout_'.$table.'" '.$checked.' /><label for="motpartout_'.$table.'">'._T('motspartout:item_mots_cles_association_'.$table).'</label></div>';
      }
			$flux['data'] = str_replace('<!--choix_tables-->',"$input\n<!--choix_tables-->", $flux['data']);
		}
		return $flux;
	}

?>