<?php
function inc_barre_langues_dist($id_article){
		$row = sql_fetsel("*", "spip_articles", "id_article=$id_article");
		
		$id_rubrique = $row['id_rubrique'];
		$id_trad = $row["id_trad"];
		
		$virtuel = (strncmp($row["chapo"],'=',1)!==0) ? '' :
		chapo_redirige(substr($row["chapo"], 1));
		

	
	
	/*cr&eacute;ation des onglets traduction*/
	
	if (lire_config('langues_multilingue')) $langues_dispos=explode(',',lire_config('langues_multilingue'));
	else $langues_dispos=explode(',',lire_config('langues_utilisees'));

	$traductions	= array();
	
	if($langues_dispos){		
	if($id_trad>0){
		$sql=sql_select('lang,id_article','spip_articles','id_trad='.$id_trad);
	
	
		while($row=sql_fetch($sql)){
			$traductions[$row['lang']]=$row['id_article'];
			}
	
		$clic = _T('trad_delier');	
		$options = '<div class="options delier">'.icone_inline($clic, ajax_action_auteur("referencer_traduction","$id_article,-$id_trad",'articles', "id_article=$id_article",array($clic)), "traductions-24.gif", "supprimer.gif",'right', false).'</div>';		
		}
	else{
		$id_trad=$id_article;
		$row=sql_fetsel('lang,id_article','spip_articles','id_article='.$id_article);
		$traductions[$row['lang']]=$row['id_article'];
		
		$options =  '<div class="options form_lier"><h2>'._T('taa:lier_traduction').'</h2>'.redirige_action_auteur("referencer_traduction",
			$id_article,
			"articles&id_article=$id_article",
			"id_article=$id_article",
			("<label for='lier_trad'>" . _T('trad_lier') . "</label>" .
			"\n<input type='text' class='fondl' name='lier_trad' id='lier_trad' size='5' />\n"),
			_T('bouton_valider'),
			" class='fondl'").'</div>';
		}
	
	$span_content='<div class="ref">*<span>'._T('spip:trad_reference').'</span></div>';	
	
	foreach($langues_dispos as $key => $value){
	$class='';
	$span='';	
		if($traductions[$value]!=$id_article){
			if(array_key_exists($value,$traductions)){
				if($traductions[$value]==$id_trad){
					$span=$span_content;					
					}
				$onglets_traduction.='<div class="traduit onglet ajax">'.$span.'<a href="?exec=articles&id_article='.$traductions[$value].'">'.traduire_nom_langue($value).'</a></div>';					
			}
			else{
				// Si le plugin traduction rubriques est activé on regarde si on trouve la rubrique traduite
				if (test_plugin_actif('tradrub')) {
					$id_rubrique_traduite=rubrique_traduction($value,$id_rubrique);
					$section='oui';
					}	
				$onglets_traduction.= '<div class="non_traduit onglet"><a href="'.generer_url_ecrire('articles_edit','new=oui&lier_trad='.$id_trad.'&id_rubrique='.$id_rubrique_traduite.'&lang_dest='.$value).'" title="'._T('ecrire:info_tout_site2').'">'.traduire_nom_langue($value).'</a></div>';
			
				$action=redirige_action_auteur ('changer_langue',$id_article,'articles',"id_article=$id_article");
				// Si le plugin traduction rubriques est activé on affiche pas les onglets changement de langue car la langue se change en modifiant la rubrique
				if(!$section){
					$changer_traduction.='<div class="lang onglet"><a href="'.parametre_url($action,'changer_lang',$value).'">'.traduire_nom_langue($value).'</a></div>';					
					}

				}
			}
		else{
			if($traductions[$value]==$id_trad){
					$span=$span_content;					
					}
			$onglets_traduction.='<div class="onglet_off onglet">'.$span.traduire_nom_langue($value).'</div>';
			}
		}
	}

	$contexte=array(
		'onglets_traduction'=>$onglets_traduction,
		'options'=>$options,
		'langue_article'=>$langue_article,
		'changer_traduction'=>$changer_traduction,
		'edition_seule'=>$edition_seule,					
		);
		
		$retour=recuperer_fond('prive/editer/barre_traductions_article',$contexte,array('ajax'=>true));
	return $retour;
	
}
	
?>
