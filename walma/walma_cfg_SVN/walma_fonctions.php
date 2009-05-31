<?php
//unefonction pour définir l'array des articles demandés
/*
La boucle demande  arrayarticles pour y puiser les documents
- cet array regroupe par defaut tous les articles du site mais peut être défini par cfg
- walma affiche donc les documents seulement si leurs articles sont dans cet array depuis
-la  rubrique en cours
-l'article en cours
-si ni id_article ni id_rubrique dans l'url alors sur tout les articles de cet array (voir icone W)
-et la recherche s'effectue à l'intérieur de cet array
[(#SET{arraywalma,[(#NOOP|arrayarticles{#ENV{id_article},#ENV{id_rubrique}})]})]
puis dans la boucle
{id_article IN #GET{arraywalma}}
*/

function arrayarticles($noop,$id_art,$id_rub){
	//on teste si cfg est actif
	if (function_exists(lire_config)) {
	$arracfgarts=lire_config("walma/walma_articles",' ');
	$arracfgrubs=lire_config("walma/espace_walma",' ');
	} else {
	$arracfgarts=' ';
	$arracfgrubs=' ';
	}

	//article demandé
	if(isset($id_art)){
	//si il y a deja un arraycfg on teste si article est bien dedans sinon rien
		if ($arracfgarts!=' '){
			if (in_array($id_art,$arracfgarts)){
			$listearticles = array($id_art);
			}
		}
		else 
		$listearticles = array($id_art);
	}

	//rubrique demandé
	else if(isset($id_rub)){
	//si il y a deja un arraycfg on teste si la rubrique est bien dedans ( et on retourne ses articles avec documents ) sinon on passe
		if ($arracfgrubs!=' '){
			if (in_array($id_rub,$arracfgrubs)){
			$rub_articles = spip_query("SELECT articles.id_article, articles.statut FROM spip_articles AS articles, spip_documents_articles AS lien, spip_documents AS extens WHERE articles.id_rubrique=$id_rub AND articles.statut = 'publie' AND articles.id_article=lien.id_article AND lien.id_document = extens.id_document
AND ((extens.extension = 'png') OR (extens.extension = 'gif') OR (extens.extension = 'jpg')) ");

				if (spip_num_rows($rub_articles) > 0){
				while($row=sql_fetch($rub_articles)){
								$listearticles[]=$row['id_article'];
								}
				}
			}
		}
	//arraycfg n'existe pas, on prend les articles de la rubrique qui contiennent des documents images
		else 
		$rub_articles = spip_query("SELECT articles.id_article, articles.statut FROM spip_articles AS articles, spip_documents_articles AS lien, spip_documents AS extens WHERE articles.id_rubrique=$id_rub AND articles.statut = 'publie' AND articles.id_article=lien.id_article AND lien.id_document = extens.id_document
AND ((extens.extension = 'png') OR (extens.extension = 'gif') OR (extens.extension = 'jpg')) ");

				if (spip_num_rows($rub_articles) > 0) {
				while($row=sql_fetch($rub_articles)){
								$listearticles[]=$row['id_article'];
								}
		}
	}
	//si ni article ni rubrique demandé et que cfg n'est pas vide, on prend liste cfg
	else if($arracfgarts!=' '){
	$listearticles = $arracfgarts;
	}
	else{
	//si rien n'existe on tape dans tous les articles du site (un peu lourd...), on affine qd même sur ceux qui ont des documents..images... 
	$tous_articles = spip_query("
SELECT articles.id_article, articles.statut
FROM spip_articles AS articles, spip_documents_articles AS lien, spip_documents AS extens
WHERE articles.statut = 'publie'
AND articles.id_article = lien.id_article
AND lien.id_document = extens.id_document
AND ((extens.extension = 'png') OR (extens.extension = 'gif') OR (extens.extension = 'jpg')) ");

		if (spip_num_rows($tous_articles) > 0) {
		while($row=sql_fetch($tous_articles)){
						$listearticles[]=$row['id_article'];
						}
		}
	}
return $listearticles;
}

/*uniquement pour objet ayant table adequate! */	 
function titre_depuis_id($id_objet,$objet) {
    /* par précaution, on vérifié que le paramètre est
    une valeur numérique entière, */
    if(!($id_objet = intval($id_objet))) return '';
    /* on rédige puis on exécute la requête pour la base de données */
    $q = 'SELECT titre FROM spip_'.$objet.'s WHERE id_'.$objet.'='.$id_objet;
    if($r = spip_query($q))
        /* si cette requête renvoie un résultat pour le champ demandé,
        on le retourne */
        if($row = sql_fetch($r))
            return $row['titre'];
    /* sinon, on renvoie une chaine vide */
    return '';
}	 
	 
function walma_header_prive($texte) {
	include_spip('inc/filtres_images');
	$texte.= '<link rel="stylesheet" type="text/css" href="' .generer_url_public('walmacss', $paramcss). '" />' . "\n";

//uniquement si on est sur ecrire/?exec=cfg&cfg=walma
//cercle chromatique farbtastic
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

//seulement si on est pas dans cfg car sinon redouble l'entete prive!
//[(#NOTCFG|detect_cfg|?{'',' '}) #INSERT_HEAD ]
function walma_insert_head($flux){
	if ($_GET['exec'] != "cfg"){
global $id_article, $id_rubrique;
if (isset($id_article)) {
$paramjs="id_article=$id_article";
}
if (isset($id_rubrique)) {
$paramjs="id_rubrique=$id_rubrique";
} else $paramjs="";

	$flux.= "\n<!--walma_insert_head_public -->\n".
'<link rel="stylesheet" type="text/css" href="' .generer_url_public('walmacss'). '" />' . "\n";
$flux.= '<script src="' .generer_url_public('walmajs', $paramjs). '" type="text/javascript"></script>';
	return $flux;
	}
}
/*
function walma_insert_head($flux){
$mode=$_GET['mode'];
if (!$_GET['mode']){
if (function_exists(lire_config)) {
$mode=lire_config('walma/walma_mode');
else
$mode=droite;
} }
if ($_GET['diapo']){ $diapourl='&amp;diapo='.$_GET['diapo'];}
$paramcss="mode=$mode";
$paramcss.=$diapourl;
	if ($_GET['exec'] != "cfg"){
	$flux.= "\n<!--walma_insert_head_public-->\n".
'<link rel="stylesheet" type="text/css" href="' .generer_url_public('walma_cssconfig', $paramcss). '" />' . "\n";
	return $flux;
	}
}
*/

/*pour la redirection non valide actuellement!*/
function boite_article_virtuelb($id_article, $virtuel)
{
	if (!$virtuel
	AND $GLOBALS['meta']['articles_redirection'] != 'oui')
		return '';

	$invite = '<b>'
	._T('bouton_redirection')
	. '</b>'
	. aide ("artvirt");

	$virtualiser = charger_fonction('virtualiser', 'inc');

	return cadre_depliable("site-24.gif",
		$invite,
		$virtuel,
		$virtualiser($id_article, $virtuel, "articles", "id_article=$id_article"),
		'redirection');
}

function virtualwalma($id_article, $virtuel)
{
	$virtuel = preg_replace(",^ *https?://$,i", "", rtrim($virtuel));
	if ($virtuel) $virtuel = corriger_caracteres("=$virtuel");
	spip_query("UPDATE spip_articles SET chapo=" . _q($virtuel) . ", date_modif=NOW() WHERE id_article=" . $id_article);
}

/*automatiquement dans les pages publiques pour les rediriger au besoin*/
function walma_insert_head_old($flux){
/*
$virtualiser = charger_fonction('virtualiser', 'inc');

	return cadre_depliable("site-24.gif",
		$invite,
		$virtuel,
		$virtualiser($id_article, $virtuel, "articles", "id_article=$id_article"),
		'redirection');
*/
$arrayrub=lire_config('walma/espace_walma');
//gestion du cache pour cet insert?
global $id_article, $id_rubrique;
if ($arrayrub!=''){
//quels sont les secteurs demandés
//$flux.=  print_r($arrayrub);
 
if ($id_article!=''){
$flux.=  "<br />l'article actuel est $id_article";
//on cherche le secteur de l'article
$result = spip_query("SELECT id_secteur FROM spip_articles WHERE id_article = $id_article");
while ($row= spip_fetch_array($result)) {
		$idrubart = $row['id_secteur'];
}
$flux.= "<br />secteur = $idrubart";
//si le secteur est dans l'arrayrub
if (in_array($idrubart, $arrayrub)) {
    echo "<br />article demandé pour walma!";
}
}

if ($id_rubrique!=''){
$flux.=  "<br />la rubrique actuelle est $id_rubrique";
//on cherche le secteur de la rubrique
$result = spip_query("SELECT id_secteur FROM spip_rubriques WHERE id_rubrique = $id_rubrique");
while ($row= spip_fetch_array($result)) {
		$idrub = $row['id_secteur'];
}
$flux.= "<br />secteur = $idrub";
//si le secteur est dans l'arrayrub
if (in_array($idrub, $arrayrub)) {
    echo "<br />secteur avec squelette demandé pour walma!";
}
}


/*include_spip('inc/headers');
$url="pagewalma&id_article=".$id_article;
redirige_par_entete($url);
*/

}

	$flux.= "\n<!--walma_insert_head-->\n".
'<link rel="stylesheet" type="text/css" href="' .generer_url_public('walmacss', $paramcss). '" />' . "\n";
	return $flux;
}


// #FORMULAIRE_UPLOAD
// se r�f�rer � http://doc.spip.org/@afficher_documents_colonne
//afficher_documents_walma pour telecharger uniquement les docs
// [(#ID_ARTICLE|upload_documents_walma)]
function upload_documents_walma($id, $type="article",$script=NULL) {
	include_spip('inc/autoriser');
	// il faut avoir les droits de modif sur l'article pour pouvoir uploader !
	if (!autoriser('joindredocument',$type,$id))
		return "";

	include_spip('inc/presentation'); // pour l'aide quand on appelle afficher_documents_colonne depuis un squelette
	// seuls cas connus : article, breve ou rubrique
	if ($script==NULL){
		$script = $type.'s_edit';
		if (!test_espace_prive())
			$script = parametre_url(self(),"show_docs",'');
	}
	$id_document_actif = _request('show_docs');


	// Ajouter nouvelle image
	$joindre = charger_fonction('joindre', 'inc');
	// Ajouter nouveau document
	//$ret .= "<div id='documents'></div>\n<div id='portfolio'></div>\n";
	if (!isset($GLOBALS['meta']["documents_$type"]) OR $GLOBALS['meta']["documents_$type"]!='non') {
		$ret .= $joindre(array(
			'cadre' => 'enfonce',
			'icone' => 'doc-24.gif',
			'fonction' => 'creer.gif',
			'titre' => _T('bouton_ajouter_document').aide("ins_doc"),
			'script' => $script,
			'args' => "id_$type=$id",
			'id' => $id,
			'intitule' => _T('info_telecharger'),
			'mode' => 'document',
			'type' => $type,
			'ancre' => '',
			'id_document' => 0,
			'iframe_script' => generer_url_ecrire("documents_colonne","id=$id&type=$type",true)
		));
	}
    
	return $ret;
}

?>