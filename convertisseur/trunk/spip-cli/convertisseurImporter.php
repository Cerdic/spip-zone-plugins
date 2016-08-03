<?php

/***

Importer en masse des fichiers txt dans spip_articles.
Les articles sont ajoutés avec leurs info sauf id_article dans des rubriques créées si besoin. 
Les documents <docxxx> sont gérés

Pour ajouter des champs à la rache :
// sql_query("alter table spip_articles add signature MEDIUMTEXT NOT NULL DEFAULT ''");


*/


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class fichiersImporter extends Command {
	protected function configure() {
		$this
			->setName('convertisseur:importer')
			->setDescription('Importer des fichiers SPIP txt dans la table spip_articles (ou autre).')
			->setAliases(array(
				'import' // abbréviation commune pour "import"
			))
			->addOption(
				'source',
				's',
				InputOption::VALUE_OPTIONAL,
				'Répertoire source',
				'conversion_spip'
			)
			->addOption(
				'dest',
				'd',
				InputOption::VALUE_OPTIONAL,
				'id_rubrique de la rubrique où importer les numéros et leurs articles',
				'0'
			)
			->addOption(
				'racine_documents',
				'r',
				InputOption::VALUE_OPTIONAL,
				'path ajouté devant le `fichier` des documents joints importés',
				''
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;
		global $spip_version_branche ;		
	
		include_spip("iterateur/data");
		
		$source = $input->getOption('source') ;
		$id_parent = $input->getOption('dest') ;
		$racine_documents = $input->getOption('racine_documents') ;	
				
		// Répertoire source
		if(!is_dir($source)){
			$output->writeln("<error>Préciser le répertoire avec les fichiers à importer. spip import -s repertoire </error>\n");
			exit ;
		}	

		if($id_parent == 0){
			$output->writeln("<error>Préciser dans quelle rubrique importer les articles. spip import -d `id_rubrique` </error>\n");
			exit ;
		}	

		
		if ($spip_loaded) {
			chdir($spip_racine);

			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			// Si c'est bon on continue
			else{

				// Champs d'un article
				include_spip("base/abstract_sql");
				$show = sql_showtable("spip_articles");
				$champs = array_keys($show['field']);
				
				$show = sql_showtable("spip_documents");
				$champs_documents = array_keys($show['field']);
				
				// Ajout d'un champ la premiere fois pour stocker les éventuelles <ins> qui n'ont pas de champs (peut-être long).
				if(!in_array('metadonnees', $champs)){
					$output->writeln("MAJ BDD : alter table spip_articles add metadonnees MEDIUMTEXT NOT NULL DEFAULT ''");
					sql_query("alter table spip_articles add metadonnees MEDIUMTEXT NOT NULL DEFAULT ''");
				}
				// Ajout d'un champ la premiere fois pour stocker l'id_article original (pour ensuite remapper les liens [->123]).
				if(!in_array('id_source', $champs)){
					$output->writeln("MAJ BDD : alter table spip_articles add id_source BIGINT(21) NOT NULL DEFAULT ''");
					sql_query("alter table spip_articles add id_source BIGINT(21) NOT NULL DEFAULT ''");
				}
				// Ajout d'un champ la premiere fois pour stocker le nom du fichier source, pour reconnaitre un article déjà importé.
				if(!in_array('fichier_source', $champs)){
					$output->writeln("MAJ BDD : alter table spip_articles add fichier_source MEDIUMTEXT NOT NULL DEFAULT ''");
					sql_query("alter table spip_articles add fichier_source MEDIUMTEXT NOT NULL DEFAULT ''");
				}
				// on prends tous les fichiers txt dans la source, sauf si metadata.txt a la fin.
				$fichiers = preg_files($source . "/", "(?:(?<!\.metadata\.)txt$)", 100000);

				// start and displays the progress bar
				$progress = new ProgressBar($output, sizeof($fichiers));
				$progress->setBarWidth(100);
				$progress->setRedrawFrequency(1);
				$progress->setMessage(" Import de $source/*.txt en cours dans la rubrique $id_parent ... ", 'message'); /**/  
				$progress->setMessage("", 'inforub');
				$progress->start();

				if(is_file("liens_a_corriger.txt"))
					unlink("liens_a_corriger.txt");
				if(is_file("liens_non_corriges.txt"))
					unlink("liens_non_corriges.txt");
				if(is_file("liens_corriges.txt"))
					unlink("liens_corriges.txt");

				foreach($fichiers as $f){

					$fichier = 	basename($f) ;
					preg_match("/^(\d{4})-\d{2}/", $fichier, $m);
					$mois = $m[0];
					$annee = $m[1] ;
					
					// chopper l'id_parent dans le fichier ?
					include_spip("inc/flock");										
					lire_fichier($f, $texte);
					
					// menage
					//@@COLLECTION:esRetour ligne automatique
					//@@SOURCE:article914237.html
					
					$texte = preg_replace("/@@COLLECTION.*/", "", $texte);
					$texte = preg_replace("/@@SOURCE.*/", "", $texte);
					
					
					// Si des <ins> correspondent à des champs metadonnees connus, on les ajoute.
					$champs_metadonnees = array("mots_cles", "auteurs", "hierarchie", "documents");
					$hierarchie = "" ;
					$auteurs = "" ;
					$mots_cles = "" ;
					$documents = "" ;					

					if (preg_match_all(",<ins[^>]+class='(.*?)'[^>]*?>(.*?)</ins>,ims", $texte, $z, PREG_SET_ORDER)){ 
						foreach($z as $d){ 
							if(in_array($d[1], $champs_metadonnees)){ 
								// class="truc" => $truc 
								$$d[1] = split("@@", $d[2]); 
								// virer du texte 
								$texte = substr_replace($texte, '', strpos($texte, $d[0]), strlen($d[0])); 
							} 
						} 
					}
					
					if (preg_match(",<ins class='id_article'>(.*?)</ins>,ims", $texte, $z))
							$id_source = $z[1] ;
					
					// dans quelle rubrique importer ?
					if($hierarchie){
						$titre_parent = $hierarchie[0] ;
						$titre_rubrique = $hierarchie[1] ;
						
						// hack perso diplo 2006/02 => 02 ou 2006-02 => 02
						$titre_rubrique = preg_replace(",^(\d{4})(?:/|-)(\d{2})$,", "\\2", $titre_rubrique); 

					}else{
						$titre_parent = $annee ;
						$titre_rubrique = "$annee-$mois" ;
					}
					
					include_spip("inc/rubriques");
					$id_rubrique = creer_rubrique_nommee("$titre_parent/$titre_rubrique", $id_parent);
					$up = sql_updateq('spip_rubriques', array('statut' => 'publie'), "id_rubrique=$id_rubrique");
					
					if($up)
						$progress->setMessage(" Rubrique $titre_parent/$titre_rubrique => $id_rubrique ", 'inforub');
					
					$progress->setMessage("", 'docs');											
					$progress->setMessage("", 'mot');
					$progress->setMessage("", 'auteur');
					
					// inserer l'article
					include_spip("inc/convertisseur");

					// auteur par défaut (admin)
					$id_admin = sql_getfetsel("id_auteur", "spip_auteurs", "id_auteur=1");
					$id_admin = ($id_admin)? $id_admin : 12166 ;

					$GLOBALS['auteur_session']['id_auteur'] = $id_admin ;
			
					if($id_article = inserer_conversion($texte, $id_rubrique, $f)){
						// Créer l'auteur ?
						if($auteurs){

							foreach($auteurs as $auteur){
								
								list($nom_auteur,$bio_auteur) = explode("::", $auteur);
								// On essaie de trouver un nom*prénom dans les auteurs
								$a_nom = explode(" ", $nom_auteur);
								$prenom_nom = array_pop($a_nom) . "*" . join(" ", $a_nom);
								
								// echo "\n$prenom_nom\n" ;
								
								if($id_auteur = sql_getfetsel("id_auteur", "spip_auteurs", "nom=" . sql_quote($prenom_nom))){
									
								}else	
									$id_auteur = sql_getfetsel("id_auteur", "spip_auteurs", "nom=" . sql_quote($nom_auteur));
								
								if(!$id_auteur){
									$id_auteur = sql_insertq("spip_auteurs", array(
    										"nom" => $nom_auteur,
    										"statut" => "1comite",
    										"bio" => $bio_auteur
    								));
    								
    								$auteur_m = substr("Création de l'auteur " . $auteur, 0, 100) ;
    								$progress->setMessage($auteur_m, 'auteur');
								}
							
								if($spip_version_branche > "3"){
									if(!sql_getfetsel("id_auteur", "spip_auteurs_liens", "id_auteur=$id_auteur and id_objet=$id_article and objet='article'"))
   										sql_insertq("spip_auteurs_liens", array(
	    									"id_auteur" => $id_auteur,
	    									"id_objet" => $id_article,
	    									"objet" => "article"
	    								));
	    						}else // spip 2
									if(!sql_getfetsel("id_auteur", "spip_auteurs_articles", "id_auteur=$id_auteur and id_article=$id_article"))
										sql_insertq("spip_auteurs_articles", array(
    										"id_auteur" => $id_auteur,
    										"id_article" => $id_article
    									));
								
							}
						}
				
						// Créer des mots clés ?
						if($mots_cles){
							foreach($mots_cles as $mot){
								// groupe mot-clé
								list($type_mot,$titre_mot) = explode("::", $mot);
								$type_mot = ($type_mot)? $type_mot : "Mots importés" ;

								$id_groupe_mot = sql_getfetsel("id_groupe", "spip_groupes_mots", "titre=" . sql_quote($type_mot));
								if(!$id_groupe_mot)
									$id_groupe_mot = sql_insertq("spip_groupes_mots", array("titre" => $type_mot));								

								$id_mot = sql_getfetsel("id_mot", "spip_mots", "titre=" . sql_quote($titre_mot));
								if(!$id_mot){
									$id_mot = sql_insertq("spip_mots", array(
    									"titre" => $titre_mot,
    									"type" => $type_mot,
    									"id_groupe" => $id_groupe_mot
    								));
    								$mot_m = substr("Création du mot " . $titre_mot . " (" . $type_mot .")", 0, 100) ;
   									$progress->setMessage($mot_m, 'mot');
								}

								if($spip_version_branche > "3"){
									if(!sql_getfetsel("id_mot", "spip_mots_liens", "id_mot=$id_mot and id_objet=$id_article and objet='article'"))
										sql_insertq("spip_mots_liens", array(
	    									"id_mot" => $id_mot,
	    									"id_objet" => $id_article,
	    									"objet" => "article"
	    								));
								}else // spip 2
									if(!sql_getfetsel("id_mot", "spip_mots_articles", "id_mot=$id_mot and id_article=$id_article"))
										sql_insertq("spip_mots_articles", array(
    										"id_mot" => $id_mot,
    										"id_article" => $id_article
    									));
							}
						}

						// Créer des documents ?
						if($documents){
							foreach($documents as $doc){
								$d = json_decode($doc, true);
								$id_doc = $d['id_document'] ;
								unset($d['id_document']);
								$d['fichier'] = $racine_documents . $d['fichier'] ;
								
								// champs ok dans les documents ?
								foreach($d as $k => $v)
									if(in_array($k, $champs_documents))
										$document_a_inserer[$k] = $v ;
								
								// insertion du doc
								$id_document = sql_getfetsel("id_document", "spip_documents", "fichier=" . sql_quote($d['fichier']));
								if(!$id_document){									
									$id_document = sql_insertq("spip_documents", $document_a_inserer);
   									$progress->setMessage("Création du document " . $d['titre'] . " (" . $d['fichier'] .")", 'docs');
								}
								if($id_document AND !sql_getfetsel("id_document", "spip_documents_liens", "id_document=$id_document and id_objet=$id_article and objet='article'"))
									sql_insertq("spip_documents_liens", array(
	    									"id_document" => $id_document,
	    									"id_objet" => $id_article,
	    									"objet" => "article"
	    						));
	    						
	    						// modifier le texte qui appelle peut etre un <doc123>
	    						if($id_document){
	    							// ressortir le texte propre...
	    							$texte = sql_getfetsel("texte", "spip_articles", "id_article=$id_article");
	    							$texte = preg_replace("/(<(doc|img|emb))". $id_doc . "/i", "\${1}" . $id_document, $texte);
									sql_update("spip_articles", array("texte" => sql_quote($texte)), "id_article=$id_article");
	    						}
							}
						}
						
						// recaler des liens [->123456] ?
						include_spip("inc/lien");
						if(preg_match(_RACCOURCI_LIEN, $texte))
							passthru("echo '$id_article	$id_source' >> liens_a_corriger.txt");

						// Si tout s'est bien passé, on avance la barre
						$progress->setMessage($f, 'filename');
						$progress->setFormat("<fg=white;bg=blue>%message%</>\n" . "<fg=white;bg=red>%inforub% %auteur% %mot%</>\n" . '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%' . "\n  %filename%\n%docs%\n\n");
						$progress->advance();
											
					}else{
						$output->writeln("<error>échec de l'import de $f</error>");
						exit ;
					}
				}	

				// ensure that the progress bar is at 100%
				$progress->finish();
				
				// remapper les liens [->12345]
				lire_fichier("liens_a_corriger.txt", $articles);
				$corrections_liens = inc_file_to_array_dist($articles);
				foreach($corrections_liens as $k => $v){
					if($v){
						list($id_article, $id_source) = explode("\t", $v);
						$texte = sql_getfetsel("texte", "spip_articles", "id_article=$id_article") ;
						// recaler des liens [->123456] ?
						include_spip("inc/lien");
						if(preg_match_all(_RACCOURCI_LIEN, $texte, $liens, PREG_SET_ORDER)){
							foreach($liens as $l){
								if(preg_match("/^[0-9]+$/", $l[4])){	
									// trouver l'article dont l'id_source est $l[4] dans le secteur
									if($id_dest = sql_getfetsel("id_article", "spip_articles", "id_source=" . trim($l[4]) . " and id_secteur=$id_parent")){
										$lien = escapeshellarg("$id_article : " . $l[0] . " => " . str_replace($l[4], $id_dest, $l[0]));
										passthru("echo $lien >> liens_corriges.txt");
									}else{
										$commande = escapeshellarg("Dans $id_article (source $id_source)" . $l[0] . " : lien vers " . $l[4] . " non trouvé") ;
										passthru("echo $commande >> liens_non_corriges.txt");
									}	
									
								}
							}	
						}		
												
					}
				}

				$output->writeln("");
				if(is_file("liens_a_corriger.txt"))
					unlink("liens_a_corriger.txt");

			}
		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}
