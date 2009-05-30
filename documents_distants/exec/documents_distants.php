<?

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_documents_distants()
{
	global $documents;
	global $id;
	global $type_lien;
	global $valider;
	

	global $retour;

	$retour = $_POST['retour'];
	$vignette = $_POST['vignette'];

	$retour =="oui" ? $retour="oui" : $retour = "non";
	$vignette =="oui" ? $vignette="oui" : $retour = "non";

	if ($valider)
	{
		$erreur=importer_document($documents,$type_lien,$id,$vignette,$retour);
	}
	
	$commencer_page= charger_fonction('commencer_page', 'inc');
	
	
	
	include_spip('public/assembler');
	echo $commencer_page($titre=_T('documentsdistants:importer'));
	
	
	debut_gauche();
	debut_droite();
	echo (_T('documentsdistants:'.$erreur));
	debut_cadre_formulaire();
	echo gros_titre(_T('documentsdistants:importer'));
	
	echo recuperer_fond('documents_distants',Array('documents'=>$documents,'id'=>$id,'type_lien'=>$type_lien,'retour'=>$retour,'vignette'=>$vignette));
	fin_cadre_formulaire();
	
	echo fin_gauche();
	echo fin_page();
	}


function importer_document($documents_distants,$type_lien,$id,$vignette,$retour)
{
	include_spip('inc/autoriser');
	
	$id2=$id;
	settype($id,'integer');
	settype($id,'string');// amha il y a moyen de faire plus simple
	
	$tableau =explode(";",$documents_distants);
	
	$autoriser=autoriser('joindredocument',str_replace('s', '',$type_lien),$id);
	
	
	if (!($documents_distants and $id and $id2==$id)){return 'completer';}
	
	if (!$autoriser) {return 'pasdroits';}
	include_spip('inc/distant');
	include_spip('inc/action');
	include_spip('inc/ajouter_documents');
	include_spip('inc/indexation');

	$dir_ftp = determine_upload();

	foreach ($tableau as $documents_distants)
	{
		$docs_actifs = array();
		inc_ajouter_documents_dist(	$documents_distants,		/* $source = http://.../ */
						basename($documents_distants),	/* $nom_envoye = 'nimportequoi.pdf' */
						$type_lien,			/* $type_lien = 'article' | 'rubrique' */
						$id,				/* $id_lien = id_article */
						'distant',			/* $mode = 'distant' */
						'',				/* $id_document = '' (car c'est le doc principal, pas la vignette */
						$docs_actifs);			/* $actifs = array() */

	

		if ( $vignette == "oui" )
		{
			$dernier_document = $docs_actifs[0];
			/* idealement, cela se passe ainsi */
			/* mais comme il est impossible de copier un fichier dont la destination est ../ */
			/* et bien ca ne fonctionne pas */
			/* d'après _fil_ cela est corrigé pour la version 1.9.3 */
			/* affaire a suivre donc */
			/* En attendant, je code ma propre fonction */

			$fichier_vignette = $dir_ftp ."/". basename($documents_distants);

			importer_vignette(	$fichier_vignette,		// $source = tmp/upload/nimportequoi.jpg
						basename($documents_distants),	// $nom_envoye = 'nimportequoi.pdf'
						$type_lien,			// $type_lien = 'article' | 'rubrique'
						$id,				// $id_lien = id_article
						'vignette',			// $mode = 'distant' 
						$dernier_document);		// $id_document = $dernier_document (car c'est le doc principal, pas la vignette
		}
	}

	if($retour=="oui")
	{
		include_spip('inc/headers');
		$type_lien == 'article' ?  $url = './?exec=articles&id_article='.$id: pass ;
		$type_lien == 'rubrique' ? $url = './?exec=naviguer&id_rubrique='.$id : pass;
		redirige_par_entete($url); //pas sur
	}
}

/*
 * Cette fonction se charge de trouver une vignette pour le document distant indiqué
 * la vignette est une image de type .jpg, .png ou .gif (dans cet ordre).
 * par exemple pour spip.pdf les vignettes cherchees sont spip.jpg, jpip.png et spip.gif
 * une fois trouvée, la vignette est copié dans le répertoire IMG/*extension* en prenant soin
 * de ne pas écraser une image existante (copier_vignette() )
 * Puis on appelle inc_ajouter_documents_dist() pour intégrer la vignette.
 *
 * Note : la fonction "deplacer_fichier_upload()" ne supporte pas les "../" dans la destiantion
 * du fichier, d'ou la creation de la fonction copier_vignette().
 * le passage de parametre du nom de fichier avec "../" permet a copier_document() de ne pas
 * appeler la fonction deplacer_fichier_upload() et du meme coup, de ne pas terminer le
 * script prematurément.
 * Pour etre complet ce hack necessite la mise a jour du nom du fichier pour la vignette concernee
 * car sinon, des "../" trainent dans le nom et empeche l'affichage.
*/
function importer_vignette($source, $nom_envoye, $type_lien, $id_lien, $mode, $id_document)
{
	/* liste des extensions de vignettes a chercher dans l'upload */
	$array_ext = array("jpg","png","gif");

	$dir_ftp = determine_upload();

	preg_match(",\.([^.]+)$,", $nom_envoye, $match);
	$ext = corriger_extension(strtolower($match[1]));
	$fichier = basename($source);
	$fichier_nu = substr($fichier,0,strrpos($fichier,'.'));

	foreach ( $array_ext as $extension_vignette )
	{
		$fichier_vignette = $dir_ftp.'/'.$fichier_nu .'.'.$extension_vignette;
		if (@file_exists($fichier_vignette) )
		{
			$doc_actifs = array();
			$fichier_dest = copier_vignette($extension_vignette,$fichier,$fichier_vignette);
			if ( strlen($fichier_dest) == 0 ) 
			{
				spip_log("erreur dans la copie de la vignette: $fichier_vignette");
				break;
			}

			inc_ajouter_documents_dist(	$fichier_dest,		// $source = IMG/$ext/fichier_trouve
							basename($fichier_dest),// $nom_envoye 
							$type_lien,		// $type_lien = 'article' | 'rubrique'
							$id,			// $id_lien = id_article
							'vignette',		// $mode = 'distant' 
							$id_document,		// $id_document = $dernier_document (car c'est le doc principal, pas la vignette
							$docs_actifs);		// $actifs = array()

			if ( count($docs_actifs) != 1 )
			{
				spip_log("erreur, pas de document actifs pour la vignette");
				break;
			}

			$dernier_document = $docs_actifs[0];
			$q = spip_query("SELECT id_vignette FROM spip_documents WHERE id_document = '$dernier_document'");
			if ( !$row = spip_fetch_array($q))
			{
				spip_log("erreur dans la restitution de l'id_vignette");
				break;
			}
			$derniere_vignette = $row['id_vignette'];
			if ( $derniere_vignette == '' )
			{
				spip_og("pas de vignette dans le resultat");
				break;
			}
			$fichier_dest = substr($fichier_dest,3,strlen($fichier_dest)-3); // nom du fichier dans les "../" devant
			spip_log("mise a jour : id: $derniere_vignette fichier: $fichier_dest");
			spip_query("UPDATE spip_documents SET fichier = '$fichier_dest' WHERE id_document = '$derniere_vignette'");

			// copier l'image
			// copier_vignette($ext,,$
			// appeler la fonction officielle
			break;

		}
	}
}

/* 
 * je pompe le code de inc/getdocument.php:copier_document() mais en corrigeant le souci
 * de la destination qui contient ../
*/
function copier_vignette($ext, $orig, $source)
{
	$dir = creer_repertoire_documents($ext);
	$dest = ereg_replace("[^.a-zA-Z0-9_=-]+", "_", 
			translitteration(ereg_replace("\.([^.]+)$", "", 
						      ereg_replace("<[^>]*>", '', basename($orig)))));

	// ne pas accepter de noms de la forme -r90.jpg qui sont reserves
	// pour les images transformees par rotation (action/documenter)
	$dest = preg_replace(',-r(90|180|270)$,', '', $dest);

	// Si le document "source" est deja au bon endroit, ne rien faire
	if ($source == ($dir . $dest . '.' . $ext))
		return $source;

	// sinon tourner jusqu'a trouver un numero correct
	$n = 0;
	while (@file_exists($newFile = $dir . $dest .($n++ ? ('-'.$n) : '').'.'.$ext));

	return copy($source, $newFile) ? $newFile : '';

}

?>
