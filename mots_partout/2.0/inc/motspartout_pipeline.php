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

		  //on ajoute les différentes tables

	    $tables_installees = unserialize(lire_meta('MotsPartout:tables_installees'));
      foreach ($tables_installees as $table) {
  			$checked = in_array($table, $flux['args']['contexte']['tables_liees']);
  			$checked = $checked ? ' checked="checked"' : '';
  			$input .= '<div class="choix"><input type="checkbox" class="checkbox" name="tables_liees&#91;&#93;" value="'.$table.'" id="motpartout_'.$table.'" '.$checked.' /><label for="motpartout_'.$table.'">'._T('motspartout:item_mots_cles_association_'.$table).'</label></div>';
      }
      $flux['data'] = str_replace('<!--choix_tables-->',"$input\n<!--choix_tables-->", $flux['data']);

			//on ajoute le groupe de mots parent
			$id_parent=$flux['args']['contexte']['id_parent'];
			$contexte=array("id_parent"=>$id_parent,
			                "name"=>"id_parent",
			                "id"=>"id_parent",
			                "id_groupe"=>$flux['args']['contexte']['id_groupe']
			               );
			$contenu=recuperer_fond("prive/editer/selecteur_groupe_mot_partout",$contexte);
      $flux['data'] = str_replace('<!--extra-->',"$contenu\n<!--extra-->", $flux['data']);

		}
		return $flux;
	}


	/**
 *
 * Insertion dans le pipeline post_edition
 * ajouter le champ id_parent lors de l'edition d'un groupe de mots
 * @return
 * @param object $flux
 */
function MotsPartout_post_edition($flux){
	if ($flux['args']['type']=='groupe_mot') {

		$id_groupe = $flux['args']['id_objet'];
		$id_parent=intval(_request('id_parent'));
		$var_update=array('id_parent'=>$id_parent);

		sql_updateq("spip_groupe_mots",$var_update,"id_groupe=$id_groupe");
	}
	return $flux;
}



/**
 *
 * Insertion dans le pipeline pre_edition
 * ajouter le champ id_parent lors de l'edition d'un groupe de mots
 * @return
 * @param object $flux
 */
function MotsPartout_pre_edition($flux){

  //on ajoute le champ en pre_edition du groupe de mot
  if ($flux['args']['type']=='groupe_mot') {
	  $flux['args']['champs']['id_parent']=intval(_request('id_parent'));
	}
	return $flux;
}

?>