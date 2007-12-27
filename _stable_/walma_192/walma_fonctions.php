<?php

/*uniquement pour objet ayant table adequate! */	 
function titre_depuis_id($id_objet,$objet) {
    /* par prEcaution, on vErifiE que le parametre est
    une valeur numErique entiere, */
    if(!($id_objet = intval($id_objet))) return '';
    /* on rEdige puis on exEcute la requete pour la base de donnEes */
    $q = 'SELECT titre FROM spip_'.$objet.'s WHERE id_'.$objet.'='.$id_objet;
    if($r = spip_query($q))
        /* si cette requete renvoie un rEsultat pour le champ demandE,
        on le retourne */
        if($row = spip_fetch_array($r))//SVN=sql_fetch
            return $row['titre'];
    /* sinon, on renvoie une chaine vide */
    return '';
}

function walma_install($action){
    switch ($action){
        case 'test':
            //Contr™le du plugin ˆ chaque chargement de la page d'administration
            // doit retourner true si le plugin est proprement installŽ et ˆ jour, false sinon
        break;
        case 'install':
	simple_complet();
            //Appel de la fonction d'installation. Lors du clic sur l'ic™ne depuis le panel.
            //quand le plugin est activŽ et test retourne false
        break;
        case 'uninstall':
            //Appel de la fonction de suppression
            //quand l'utilisateur clickque sur "supprimer tout" (disponible si test retourne true)
        break;
    }
}

function simple_complet(){
	//on regarde si dossier noisettes existe, sinon walma est en mode simple et on remet ˆ 0 le mŽta
$filename = _DIR_PLUGIN_WALMA."noisettes";
if (file_exists($filename)) {
    // print "Le dossier noisettes existe";
} else {
    // print "Le dossier noisettes n'existe pas";
    include_spip('base/abstract_sql');
    $q = 'DELETE FROM spip_meta WHERE nom=\'walma\'';
    spip_query($q); 
}

}

//uniquement si on est sur ecrire/?exec=cfg&cfg=walma
//cercle chromatique farbtastic
function walma_header_prive($texte) {
	include_spip('inc/filtres_images');
	$texte.= '<link rel="stylesheet" type="text/css" href="' .generer_url_public('walma.css', $paramcss). '" />' . "\n";


if (($_GET['exec'] == "cfg") && ($_GET['cfg'] == "walma"))
{
$texte.= '
<script type="text/javascript" 
src= "'.find_in_path("javascript/farbtastic/farbtastic.js").'"
></script>
<link rel="stylesheet" href="'.find_in_path("javascript/farbtastic/farbtastic.css").'" type="text/css" />' . "\n";
$texte.= '
<script type="text/javascript" 
src= "'._DIR_PLUGIN_WALMA.'javascript/walma_farbatastic.js"
></script>' . "\n";
}

return $texte;
}

function detect_cfg(){
	if ($_GET['exec'] == "cfg"){
	$cfgon.= "cfgon";
	return $cfgon;
	}
}

//seulement si on est pas dans cfg car sinon redouble l'entete prive lors de la previsualisation cfg!
//[(#NOTCFG|detect_cfg|?{'',' '}) #PIPELINE{insert_walma} ]
function walma_insert_walma($flux){
	if ($_GET['exec'] != "cfg"){
	$flux.= "\n<!--walma_insert_head_public -->\n".
'<link rel="stylesheet" type="text/css" href="' .generer_url_public('walma.css'). '" />' . "\n";
$filename = _DIR_PLUGIN_WALMA."walma.js.html";
if (file_exists($filename)) {
$flux.= '<script src="' .generer_url_public('walma.js', $paramjs). '" type="text/javascript"></script>';
}
	return $flux;
	}
}

// #FORMULAIRE_UPLOAD
// voir http://doc.spip.org/@afficher_documents_colonne
//afficher_documents_walma pour telecharger uniquement les docs
// [(#ID_ARTICLE|upload_documents_walma)]
function upload_documents_walma($id, $type="article",$script=NULL) {
	include_spip('inc/autoriser');
	// il faut avoir les droits de modif sur l'article pour pouvoir uploader !
	if (!autoriser('modifier',$type, $id))
      return "";
		
	include_spip('inc/minipres'); // pour l'aide quand on appelle afficher_documents_colonne depuis un squelette
	include_spip('inc/presentation'); // pour l'aide quand on appelle afficher_documents_colonne depuis un squelette
	// seuls cas connus : article, breve ou rubrique
	if ($script==NULL){
		$script = $type.'s_edit';
		if (_DIR_RESTREINT)
			$script = parametre_url(self(),"show_docs",'');
	}
	$id_document_actif = _request('show_docs');

$joindre = charger_fonction('joindre', 'inc');

	if ($GLOBALS['meta']["documents_" . $type] == 'oui') {
		 $ret .= $joindre($script, "id_$type=$id", $id, _T('info_telecharger_ordinateur'), 'document',$type,'',0,generer_url_ecrire("documents_colonne","id=$id&type=$type",true));
	}

  if (!_DIR_RESTREINT){
	  $ret .= "<script src='"._DIR_JAVASCRIPT."async_upload.js' type='text/javascript'></script>\n";
	  $ret .= <<<EOF
	    <script type='text/javascript'>
	    $("form.form_upload").async_upload(async_upload_article_edit)
	    </script>
EOF;
  }
    
	return $ret;
}

/*renomme pour eviter croisement, merci au plugin multimedia*/
function joli_titrew($fichier){
$fichier=basename($fichier);
    //Si je trouve un point vers la fin du nom de fichier, je renvoie ce qui suit
    if (preg_match(',\.([^\.]+)$,', $fichier, $regs))
$extension=".".$regs[1];
$fichier=ereg_replace($extension,'',$fichier);
$fichier=ereg_replace('^ ','',$fichier);
$fichier = eregi_replace("_"," ", $fichier );
$fichier = eregi_replace("'"," ",$fichier );
return $fichier ;
}



?>