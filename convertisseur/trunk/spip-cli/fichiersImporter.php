<?php

/***

Importer en masse des fichiers txt dans spip_articles.

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
			->setName('fichiers:importer')
			->setDescription('Importer des fichiers texte SPIP dans spip_articles.')
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
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;
		global $spip_version_branche ;		
	
		include_spip("iterateur/data");
		
		$source = $input->getOption('source') ;
		$id_parent = $input->getOption('dest') ;		
				
		// Répertoire source, ou arrivent les fichiers Quark (/exports_quark par défaut).
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

				/*
				if(!in_array('signature', $champs))
					sql_query("alter table spip_articles add signature MEDIUMTEXT NOT NULL DEFAULT ''");
				
				if(!in_array('pages', $champs))
					sql_query("alter table spip_articles add pages TINYTEXT NOT NULL DEFAULT ''");

				if(!in_array('free', $champs))
					sql_query("alter table spip_articles add pages TINYTEXT NOT NULL DEFAULT ''");
				*/
				
				// Ajout d'un champ la premiere fois pour stocker les éventuelles ins qui n'ont pas de champs (peut-être long).
				if(!in_array('metadonnees', $champs))
					sql_query("alter table spip_articles add metadonnees MEDIUMTEXT NOT NULL DEFAULT ''");

				$fichiers = preg_files($source . "/", "(?:(?<!\.metadata\.)txt$)", 100000);

				// start and displays the progress bar
				$progress = new ProgressBar($output, sizeof($fichiers));
				$progress->setBarWidth(100);
				$progress->setRedrawFrequency(1);
				$progress->setMessage(" Import de $source/*.txt en cours dans la rubrique $id_parent ... ", 'message'); /**/  
				$progress->setMessage("", 'inforub');
				$progress->start();

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
					
					// Si des <ins> qui correspondent à des champs metadonnees connus,on les ajoute.
					$champs_metadonnees = array("mots_cles", "auteurs", "hierarchie");
					$hierarchie = "" ;
					$auteurs = "" ;
					$mots_cles = "" ;					
	
					if (preg_match_all(",<ins[^>]+class='(.*?)'>(.*?)</ins>,ims", $texte, $z, PREG_SET_ORDER)){
						foreach($z as $d){
							if(in_array($d[1], $champs_metadonnees)){
								// class="truc" => $truc
								$$d[1] = split("@@", $d[2]);
								// virer du texte
								$texte = substr_replace($texte, '', strpos($texte, $d[0]), strlen($d[0]));
							}
						}
					}
					
					if($hierarchie){
						$titre_parent = $hierarchie[0] ;
						$titre_rubrique = $hierarchie[1] ;

						$titre_rubrique = preg_replace(",^(\d{4})/(\d{2})$,", "\\2", $titre_rubrique); // perso diplo

					}else{
						$titre_parent = $annee ;
						$titre_rubrique = "$annee-$mois" ;
					}
					
					include_spip("inc/rubriques");
					$id_rubrique = creer_rubrique_nommee("$titre_parent/$titre_rubrique", $id_parent);
					$progress->setMessage(" Création de rubrique $titre_parent/$titre_rubrique => $id_rubrique ", 'inforub');
																
					$progress->setMessage("", 'mot');
					$progress->setMessage("", 'auteur');
					
					// inserer l'article
					include_spip("inc/convertisseur");

					// auteur par défaut 
					$id_admin = sql_getfetsel("id_auteur", "spip_auteurs", "id_auteur=1");
					$id_admin = ($id_admin)? $id_admin : 12166 ;

					$GLOBALS['auteur_session']['id_auteur'] = $id_admin ;
			
					if($id_article = inserer_conversion($texte, $id_rubrique, $f)){
						
						// l'auteur existe t'il ? Le créer.
						if($auteurs){

							foreach($auteurs as $auteur){
								$id_auteur = sql_getfetsel("id_auteur", "spip_auteurs", "nom=" . sql_quote($auteur));
								if(!$id_auteur){
									$id_auteur = sql_insertq("spip_auteurs", array(
    										"nom" => $auteur,
    										"statut" => "1comite"
    								));
    								$progress->setMessage("Création de l'auteur " . $auteur, 'auteur');
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
   									$progress->setMessage("Création du mot " . $titre_mot . " (" . $type_mot .")", 'mot');
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

						// Si tout s'est bien passé, on avance la barre
						$progress->setMessage($f, 'filename');
						$progress->setFormat("<fg=white;bg=blue>%message%</>\n" . "<fg=white;bg=red>%inforub% %auteur% %mot%</>\n" . '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%' . "\n  %filename%\n\n");
						$progress->advance();
											
					}else{
						$output->writeln("<error>échec de l'import de $f</error>");
						exit ;
					}
				}	

				// ensure that the progress bar is at 100%
				$progress->finish();
				$output->writeln("");

			}
		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}
