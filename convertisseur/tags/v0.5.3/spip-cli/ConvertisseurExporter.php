<?php

/***

Exporter la table spip_articles en format txt

Lancer la commande spip-cli : spip export -d `repertoire destination`

Les fichiers txts sont placés dans le repertoire `repertoire destination` sur le disque dur.

Si un repertoire git est trouvé dans /dest alors on prend le repertoire. todo

Voir aussi fichiersImporter.

*/

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class ConvertisseurExporter extends Command {
	protected function configure() {
		$this
			->setName('convertisseur:exporter')
			->setDescription('Exporter la table spip_articles (ou autre) au format SPIP txt.')
			->setAliases(array(
				'export'
			))
			->addOption(
				'source',
				's',
				InputOption::VALUE_OPTIONAL,
				'Table à exporter',
				'spip_articles'
			)
			->addOption(
				'dest',
				'd',
				InputOption::VALUE_OPTIONAL,
				'Répertoire où exporter au format texte',
				'spip_articles'
			)
			->addOption(
				'branche',
				'b',
				InputOption::VALUE_OPTIONAL,
				'branche à exporter (id_secteur ou id_rubrique)',
				'0'
			)
			->addOption(
				'statuts',
				't',
				InputOption::VALUE_OPTIONAL,
				'statuts des articles a exporter (séparé par une virgule)',
				'prop,prepa,publie'
			)
			->addOption(
				'modif',
				'm',
				InputOption::VALUE_OPTIONAL,
				'date_modif après laquelle exporter',
				''
			)
		;
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;
		$spip_version_branche = $GLOBALS['spip_version_branche'] ;
		
		include_spip("iterateur/data");
		
		$source = $input->getOption('source') ;
		$dest = $input->getOption('dest') ;
		$branche = $input->getOption('branche') ;
		$date_modif = $input->getOption('modif') ;
		$statuts = explode(',', $input->getOption('statuts'));
		foreach($statuts as $s)
			$statuts_exportes[]= _q($s);
		
		// Secteur ou rubrique à exporter.
		if(!$branche OR !intval($branche)){
			$output->writeln("<error>Préciser l'id du secteur ou de la rubrique à exporter. spip export -b 123 </error>");
			exit();
		}
		
		// demande t'on un secteur ou une rubrique ?
		$parent = sql_getfetsel("id_parent", "spip_rubriques", "id_rubrique=$branche");
		
		include_spip("inc/rubriques");
		if($parent == 0)
			$critere_export = "where id_secteur=" . intval($branche) ;
		else{
			// y'a t'il des sous rubriques ?
			$sous_rubriques = calcul_branche_in($branche);
			if($sous_rubriques){
				$critere_export = "where id_rubrique in ($sous_rubriques)" ;
			}
			else
				$critere_export = "where id_rubrique=" . intval($branche) ;
		}
		
		if($date_modif)
			$critere_date_modif = "and date_modif > '$date_modif'" ;
		
		$critere_statut = "and statut in(". implode(",", $statuts_exportes) .")" ;
		
		// Répertoire dest, ou arrivent les fichiers txt.
		if(!is_dir($dest)){
			$output->writeln("<error>Préciser le répertoire où exporter les fichiers de $source au format txt. spip export -d `repertoire` </error>");
			exit();
		}
		
		if ($spip_loaded) {
			chdir($spip_racine);
			
			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			// Si c'est bon on continue
			else{
				// chopper les articles en sql.
				$req = "select * from spip_articles $critere_export $critere_date_modif $critere_statut order by date_redac asc" ;
				// var_dump($req);
				$query = sql_query($req); 
				
				if(sql_count($query) > 0){
					// start and displays the progress bar
					$progress = new ProgressBar($output, sql_count($query));
					$progress->setBarWidth(100);
					$progress->setRedrawFrequency(1);
					$progress->setMessage(" Export de `spip_articles` branche $branche en cours dans $dest ... ", 'message');
					$progress->start();
					
					include_spip("action/exporter");
					while($f = sql_fetch($query)){
						
						$progress->setMessage('', 'motscles');
						$progress->setMessage('', 'docs');
						$progress->setMessage('', 'auteurs');
						
						if($e=exporter_article($f,$dest)){
							// Si tout s'est bien passé, on avance la barre
							$progress->setMessage($e['docs_m'], 'docs');
							$progress->setMessage($e['motscles_m'], 'motscles');
							$progress->setMessage($e['auteurs_m'], 'auteurs');
							$nom_fichier_m = substr($e['nom_fichier'], 0, 100) ;
							$progress->setMessage($nom_fichier_m, 'filename');
							$progress->setFormat("<fg=white;bg=blue>%message%</>\n" . '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%' . "\n %auteurs% %motscles% \n %filename% \n\n");
							$progress->advance();
						}else{
							$output->writeln("<error>échec de l'export de $nom_fichier</error>");
							exit ;
						}
					}
					
					// ensure that the progress bar is at 100%
					$progress->finish();
					
				}else{
					$output->writeln("<error>Rien à exporter dans la branche $branche depuis $date_modif</error>");
				}
			}
			$output->writeln("\n");
		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}

// compat spip 2
if($GLOBALS['spip_version_branche'] < 3){
	function calcul_hierarchie_in($id, $tout = true) {
		
		static $b = array();
		
		// normaliser $id qui a pu arriver comme un array, comme un entier, ou comme une chaine NN,NN,NN
		if (!is_array($id)) {
			$id = explode(',', $id);
		}
		$id = join(',', array_map('intval', $id));
		if (isset($b[$id])) {
			// Notre branche commence par la rubrique de depart si $tout=true
			return $tout ? (strlen($b[$id]) ? $b[$id] . ",$id" : $id) : $b[$id];
		}
		
		$hier = "";
		
		// On ajoute une generation (les filles de la generation precedente)
		// jusqu'a epuisement, en se protegeant des references circulaires
		
		$ids_nouveaux_parents = $id;
		$maxiter = 10000;
		while ($maxiter-- and $parents = sql_allfetsel(
			'id_parent',
			'spip_rubriques',
			sql_in('id_rubrique', $ids_nouveaux_parents) . " AND " . sql_in('id_parent', $hier, 'NOT')
		)) {
			$ids_nouveaux_parents = join(',', array_map('reset', $parents));
			$hier = $ids_nouveaux_parents . (strlen($hier) ? ',' . $hier : '');
		}
		
		# securite pour ne pas plomber la conso memoire sur les sites prolifiques
		
		if (strlen($hier) < 10000) {
			$b[$id] = $hier;
		}
		// Notre branche commence par la rubrique de depart si $tout=true
		$hier = $tout ? (strlen($hier) ? "$hier,$id" : $id) : $hier;
		return $hier;
	}
}
