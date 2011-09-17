<?php
function inc_barre_langues_dist($id_article){
	$row = sql_fetsel("*", "spip_articles", "id_article=$id_article");
		
	$id_rubrique = $row['id_rubrique'];
	$id_trad = $row["id_trad"];
		
	$virtuel = (strncmp($row["chapo"],'=',1)!==0) ? '' :
	chapo_redirige(substr($row["chapo"], 1));
		
	if($version = $GLOBALS['spip_version_branche']<3) $objet='articles';
	else $objet='article';	
	
	/*création des onglets traduction*/
	
	// Les langues du site
	
	if (lire_config('langues_multilingue')) $langues_dispos=explode(',',lire_config('langues_multilingue'));
	else $langues_dispos=explode(',',lire_config('langues_utilisees'));
	
	
	
	// On établit les traductions de l'article
	$traductions	= array();
	
	if($langues_dispos){	
		
	// L'article est traduit	
	if($id_trad>0){
	
		// les traductions de l'article
		$sql=sql_select('lang,id_article','spip_articles','id_trad='.$id_trad);
	
		while($row=sql_fetch($sql)){
			$traductions[$row['lang']]=$row['id_article'];
			}
			
		// Le menu des options
		$clic = _T('trad_delier');	
		
		// possibilité de délier l'article
		$options = '<div class="options delier">'.icone_inline($clic, ajax_action_auteur("referencer_traduction","$id_article",$objet, "id_article=$id_article",array($clic)), "traductions-24.gif", "supprimer.gif",'right', false).'</div>';		
		
		}
	// L'article n'est pas traduit	
	else{
		$id_trad=$id_article;
		$row=sql_fetsel('lang,id_article','spip_articles','id_article='.$id_article);
		
		// Seul l'aticle présent est pris dans l'array traductions
		$traductions[$row['lang']]=$row['id_article'];
		
		// Le menu des options
		$options =  '<div class="options form_lier"><h2>'._T('taa:lier_traduction').'</h2>'.redirige_action_auteur("referencer_traduction",
			$id_article,
			"articles&id_article=$id_article",
			"id_article=$id_article",
			("<label for='lier_trad'>" . _T('trad_lier') . "</label>" .
			"\n<input type='text' class='fondl' name='lier_trad' id='lier_trad' size='5' />\n"),
			_T('bouton_valider'),
			" class='fondl'").'</div>';
		}
	
	// Pour chaque langue présente on crée un bouton
	
	// indique l'article par défaut
	$span_content='<div class="ref">*<span>'._T('spip:trad_reference').'</span></div>';	
	
	foreach($langues_dispos as $key => $value){
		$class='';
		$span='';	

		// les boutons hors article présent
		if($traductions[$value]!=$id_article){
					
			//Si il existe une traduction dans une langue du site on crée le bouton avec le lien de l'article
			if(array_key_exists($value,$traductions)){
				// Article de référence?	
				if($traductions[$value]==$id_trad){
					$span=$span_content;					
					}
				$onglets_traduction.='<div class="traduit onglet ajax">'.$span.'<a href="'.generer_url_ecrire($objet,'id_article='.$traductions[$value]).'">'.traduire_nom_langue($value).'</a></div>';					
			}
			// Sinon on crée un nouvel article dans la langue souhaitée
			else{
				// Si le plugin traduction rubriques est activé on regarde si on trouve la rubrique traduite
				if ($trad_rub=test_plugin_actif('tradrub')) {
					$id_rubrique_traduite=rubrique_traduction($value,$id_rubrique);
					
					$onglets_traduction.= '<div class="non_traduit onglet"><a href="'.generer_url_ecrire($objet.'_edit','new=oui&lier_trad='.$id_trad.'&id_rubrique='.$id_rubrique_traduite.'&lang_dest='.$value).'" title="'._T('ecrire:info_tout_site2').'">'.traduire_nom_langue($value).'</a></div>';					
					
					$section='oui';

					}
				else{
					$onglets_traduction.= '<div class="non_traduit onglet"><a href="'.generer_url_ecrire($objet.'_edit','new=oui&lier_trad='.$id_trad.'&lang_dest='.$value).'" title="'._T('ecrire:info_tout_site2').'">'.traduire_nom_langue($value).'</a></div>';				
				}

				
				
			
				$action=redirige_action_auteur ('changer_langue',$id_article,$objet,"id_article=$id_article");
				// Si le plugin traduction rubriques est activé on affiche pas les onglets changement de langue car la langue se change en modifiant la rubrique
				if(!$section){
					$changer_traduction.='<div class="lang onglet"><a href="'.parametre_url($action,'changer_lang',$value).'">'.traduire_nom_langue($value).'</a></div>';					
					}

				}
			}
		// le bouton de l'article présent	
		else{
			if($traductions[$value]==$id_trad){
					$span=$span_content;					
					}
			$onglets_traduction.='<div class="onglet_off on onglet">'.$span.traduire_nom_langue($value).'</div>';
			}
		}
	}

	$contexte=array(
		'onglets_traduction'=>$onglets_traduction,
		'options'=>$options,
		'langue_article'=>$langue_article,
		'changer_traduction'=>$changer_traduction,
		'edition_seule'=>$edition_seule,
		'id_article'=>$id_article,	
		'voir'=>_request('voir'),									
		);
		
		$retour=recuperer_fond('prive/editer/barre_traductions_article',$contexte,array('ajax'=>true));
	return $retour;
	
}
	
?>
