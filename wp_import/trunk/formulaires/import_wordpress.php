<?php


function formulaires_import_wordpress_charger(){

$valeurs['mes_saisies'] = mes_saisies_import_wordpress();

return $valeurs;
}


function formulaires_import_wordpress_verifier_dist(){

	$erreurs=array();
	return $erreurs;
}




// http://doc.spip.org/@inc_editer_mot_dist
function formulaires_import_wordpress_traiter_dist(){


	list($message,$erreurs)=wp_import_import_wordpress();

$retour['editable']=true;
if(count($erreurs)==0){
	$retour['message_ok'] = $message;
} else {
	$retour['message_erreur'] = implode('<br />',$erreurs);
}

return $retour;
}

function wp_import_trim(&$value,$key,$char=null){
    $value = trim($value,$char);   
}


function wp_import_import_wordpress(){



	$chemin_temp = sous_repertoire(_DIR_TMP,'wordpress');
	$doc = &$_FILES['document_xml'];
	$annee=_request('id_rubrique');
	$nom_fichier=$doc['name'];
	$chemin_fichier=$chemin_temp.$nom_fichier;
	include_spip('inc/getdocument');

	if (!deplacer_fichier_upload($doc['tmp_name'],$chemin_fichier))
		$erreurs['document_csv'] = _T('copie_document_impossible');
	else {

	}

if (file_exists($chemin_fichier)) {

	$tab_fichier = array();
	$nb = 0;
	include_spip('inc/xml');
	$arbre=spip_xml_load($chemin_fichier);
	$arbre=array_shift($arbre);
	$arbre=$arbre[0]['channel'][0];

	//var_dump(array_keys($arbre));
	sql_query('TRUNCATE TABLE spip_documents ');
	sql_query('TRUNCATE TABLE spip_documents_liens');
	sql_query('TRUNCATE TABLE spip_articles');
	sql_query('TRUNCATE TABLE spip_rubriques');
	sql_query('TRUNCATE TABLE spip_rubriques_liens');
	sql_query('DELETE FROM spip_auteurs where id_auteur>1 ');
	sql_query('TRUNCATE TABLE spip_auteurs_liens ');
	sql_query('ALTER TABLE `spip_auteurs` AUTO_INCREMENT =1');
	$tab_document=array();
	include_spip("action/ajouter_documents");
	
	foreach($arbre as $type=>$a)
	{ 
		
		switch($type)
		{
			
		// Importation des auteurs	
			
		case "wp:author":
			include_spip('action/editer_auteur');
			foreach($a as $auteur){
			$nom=twp($auteur['wp:author_display_name'][0]);
			$nom=empty($nom)?$auteur['wp:author_login'][0]:$nom;
			$data_auteur=array(
								'login'=>$auteur['wp:author_login'][0],
								'email'=>$auteur['wp:author_email'][0],
								'statut'=>'1comite',
								'nom'=>$nom);
			$id_auteur=auteur_inserer();
			auteur_modifier($id_auteur,$data_auteur);
			$tab_auteur[$auteur['wp:author_login'][0]]=$id_auteur;
			}
		break;
		case "wp:term":
			//var_dump($a);print_r('<br/>');
		break;
		
		// Importation des rubriques	
		
		case "wp:category":
			include_spip('action/editer_rubrique');
			
			foreach($a as &$cat){
				$data_rub=array('titre'=>twp($cat['wp:cat_name'][0]));
				$id_rub=rubrique_inserer(0);
				$cat["id"]=$id_rub;
				$tab_cat[$cat['wp:category_nicename'][0]]=$id_rub;
				rubrique_modifier($id_rub,$data_rub);
				
			}
			foreach($a as $cat){
				$id_parent=$tab_cat[twp($cat['wp:category_parent'][0])]+0;
				$data_rub=array('id_parent'=>$id_parent);
				rubrique_modifier($cat["id"],$data_rub);
			}
		break;
		
		// Importation des articles et documents
		
		case "item":
			include_spip('action/editer_article');
			foreach($a as $item){
				switch($item['wp:post_type'][0]){
					case 'attachment':
						
				
						$data_document=array(
								'titre'=>$item['title'][0],
								'descriptif'=>twp($item['description'][0]),
								'date'=>$item['post_date'][0]);
								
						$fichier=$item['wp:attachment_url'][0];
						$result=array();
						$path_parts=pathinfo($item['wp:attachment_url'][0]);
						$e = $path_parts['extension'];
						$mode=strpos($GLOBALS['meta']['formats_graphiques'], $e)===false?'document':'image';
						
						$tmp_name = basename($item['wp:attachment_url'][0]);
						$nom_fichier = basename($item['wp:attachment_url'][0]);
						$chemin_temp_document = sous_repertoire($chemin_temp,'uploads');
						if(file_exists($chemin_temp_document.$nom_fichier))
							$tmp_name=$chemin_temp_document.$nom_fichier;
						else
							$tmp_name=$item['wp:attachment_url'][0];
							
						$file=array('tmp_name'=>$tmp_name,
									'name'=>$nom_fichier,
									'titrer'=>true,
									'distant'=>false,
									'mode'=>'document');
					
						$ajouter_un_document = charger_fonction('ajouter_un_document','action');
						$id_document=$ajouter_un_document(0,$file,'',0,'document');
						document_modifier($id_document,$data_document);

						$tab_document[basename($item['wp:attachment_url'][0])]=$id_document;			
					break;	
				
				}
			}
			
		break;
		
		}
	}
	
	
	
	
	foreach($arbre as $type=>$a){
		switch($type){

		case "item":
		include_spip('action/editer_article');
		foreach($a as $item){
			switch($item['wp:post_type'][0]){
				case "page":
				case 'post':
					$statut='publie';
					if($item['wp:status'][0]=='publish') $statut='publie';
					
					$data_article=array(
							'titre'=>$item['title'][0],
							'descriptif'=>twp($item['description'][0]),
							'statut'=>'publie',
							'texte'=>html_to_spip(twp($item['content:encoded'][0]),$tab_document),
							'date_modif'=>$item['post_date'][0]);
					$categorys="";
					$categorys = preg_array_key_exists('/^category/', $item);
					if(is_array($categorys)){
						$tab_id_rub=array();
						foreach($categorys as $cats){
							$cat_name=donne_nom_cat($cats);
							$tab_id_rub[]=$tab_cat[$cat_name];
							}
						}
					else {
						$tab_id_rub=array(-1);
						$data_article['page']=$item['wp:post_name'][0];
					}
					$id_article=article_inserer(array_shift($tab_id_rub));
					article_modifier($id_article,$data_article);
					if(!empty($tab_id_rub)){
						include_spip('inc/polyhier');
						polyhier_set_parents($id_article,'article',$tab_id_rub);
					}
					$data_article=array(
							'statut'=>'publie',
							'date'=>$item['post_date'][0]);
					sql_updateq('spip_articles',$data_article,$id_article);
					
					//lier l'article à un auteur
					auteur_associer($tab_auteur[$item['dc:creator'][0].''],array("article"=>$id_article));
				break;
				
			}
		}
		
	break;
	}
	}
	if(empty($erreurs))
		$message="Le contenu de votre site Wordpress a bien été importé";
}

	return array($message,$erreurs);
}



function twp($texte){
$texte=str_replace('<![CDATA[','',$texte);
$texte=str_replace(']]>','',$texte);
return	$texte;
}

function html_to_spip($texte,$tab_document=array()){
$texte=str_replace('<strong></strong>','',$texte);
$texte=str_replace(array('<strong>','</strong>'),array(' {{','}} '),$texte);
$texte=str_replace(array('<em>','</em>'),array(' {','} '),$texte);
$texte=str_replace(array('<p>','</p>'),"\n",$texte);
$texte=str_replace(array('</span>'),"",$texte);
$texte=preg_replace('/<p[^>]*>/i',"\n",$texte);
$texte=preg_replace('/<span[^>]*>/i',"\n",$texte);
$texte=preg_replace('`<style type="text/css"></style>`i',"",$texte);
$texte=preg_replace('`{{(<img[0-9]{1,8}>)}}`i',"\\1",$texte);
$texte=inserer_balise_image($texte,$tab_document);

return	$texte;
}



function inserer_balise_image($texte,$tab_document){


	$patterns = 	'`<a[^>]*><img .* src="(.*)/wp-content/uploads/([^"]*)(-[0-9]{1,4}x?[0-9]{1,4})(\.[a-z]{3})"[^>]*></a>`i';
	$texte=preg_replace_callback($patterns,function($matches) use ($tab_document) { if(isset($tab_document[urldecode($matches[2]).$matches[4]])){return "<img".intval($tab_document[urldecode($matches[2]).$matches[4]]).">";} return $matches[0]; }, $texte);
	
	$patterns = 	'`<a[^>]*><img .* src="(.*)/wp-content/uploads/([^"]*)(\.[a-z]{3})"[^>]*></a>`i';
	$texte=preg_replace_callback($patterns,function($matches) use ($tab_document) { if(isset($tab_document[urldecode($matches[2])])){return "<img".intval($tab_document[urldecode($matches[2])]).">";} return $matches[0]; }, $texte);
	
	$patterns = 	'`<img .* src="(.*)/wp-content/uploads/([^"]*)(-[0-9]{1,4}x?[0-9]{1,4})(\.[a-z]{3})"[^>]*>`i';
	$texte=preg_replace_callback($patterns,function($matches) use ($tab_document) { if(isset($tab_document[urldecode($matches[2]).$matches[4]])){return "<img".intval($tab_document[urldecode($matches[2]).$matches[4]]).">";} return $matches[0]; }, $texte);
	$patterns = 	'`<img .* src="(.*)/wp-content/uploads/([^"]*)(\.[a-z]{3})"[^>]*>`i';
	$texte=preg_replace_callback($patterns,function($matches) use ($tab_document) { if(isset($tab_document[urldecode($matches[2])])){return "<img".intval($tab_document[urldecode($matches[2])]).">";} return $matches[0]; }, $texte);

	

	return $texte;	
}






function preg_array_key_exists($pattern, $array) {
    $keys = array_keys($array);    
    return preg_grep($pattern,$keys);
}

function donne_nom_cat($cat) {
	
    preg_match('/nicename=\"([^\"]*)\"/' , $cat , $matches );
    return $matches[1];
}



function mes_saisies_import_wordpress() {

$options_fichier=array(
			'nom'=>'document_xml',
			'label' => 'Fichier',
			'explication'=>'Fichier xml exporté depuis Wordpress',
			);


$mes_saisies = array(
          //Champ titre
        array(
        'saisie' => 'input',
        'options' => array(
                'nom' => 'id_rubrique',
                'label' => 'Rubrique',
                'obligatoire' => 'oui',
                'defaut'=>0
                )),

              //Champ document
		array(
		'saisie' => 'fichier_wp',
		'options' =>$options_fichier
		),


  );

  return $mes_saisies;
}

?>
