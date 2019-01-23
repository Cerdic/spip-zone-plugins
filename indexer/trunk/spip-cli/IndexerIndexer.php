<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

class IndexerIndexer extends Command {
	protected function configure() {
		$this
			->setName('indexer:indexer')
			->setDescription('Lancer l’indexation des contenus SPIP configurés.')
			->addOption(
				'table',
				null,
				InputOption::VALUE_OPTIONAL,
				'Indexer les contenus d\'une table en particulier',
				null
			)
			->addOption(
				'source',
				null,
				InputOption::VALUE_OPTIONAL,
				'Indexer les contenus d\'une source en particulier',
				null
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		include_spip('inc/indexer');
		global $spip_racine;
		global $spip_loaded;
		
		// Appeler la fonction qui donne l'indexeur configuré pour ce SPIP
		$indexer = indexer_indexer();

		if ($tables = $input->getOption('table')) {

			$tables = explode(',', $tables);
			$tables = array_map('trim', $tables);
			$tables = array_filter($tables);
			if (!$tables) {
				$output->writeln("<error>Indiquez une table</error>");
				exit(1);
			}

			include_spip('base/objets');
			include_spip('inc/config');

			// On crée la liste des sources
			$sources = new Indexer\Sources\Sources();

			// On ajoute chaque objet configuré aux sources à indexer
			// Par défaut on enregistre les articles s'il n'y a rien
			foreach ($tables as $table) {
				if ($table) {
					$sources->register(
						table_objet($table),
						new Spip\Indexer\Sources\SpipDocuments(objet_type($table))
					);
				}
			}

		}
		else {
			// Appeler la fonction qui liste les sources et qui comporte un pipeline pour étendre
			$sources = indexer_sources();
		}

		if ($source = $input->getOption('source')) {

			$remove = array();
			$found = false;

			$i = $sources->getIterator();
			while ($i->valid()){
				$skey = $i->key();
				$ssource = $i->current();
				if ($skey==$source){
					$found = true;
				} else {
					$remove[] = $skey;
				}
				$i->next();
			}
			if ($found){
				foreach ($remove as $key){
					$sources->unregister($key);
				}
			} else {
				$output->writeln("<error>Source $source inconnue</error>");
				$output->writeln(implode(', ', $remove));
				exit(1);
			}

		}
		
		$SpipSourcesIndexer = new Spip\Indexer\Sources\SpipSourcesIndexer($indexer, $sources);
		$SpipSourcesIndexer->resetIndexesStats();
		
		$res = $SpipSourcesIndexer->indexAll();
		
		//~ $progress = $this->getHelperSet()->get('progress');
		//~ $progress->setFormat(ProgressHelper::FORMAT_VERBOSE);
		//~ $progress->setBarWidth(100);
		//~ $progress->setRedrawFrequency(100);
		//~ $progress->start($output, count($produits));
		//~ 
		//~ $progress->advance();
		//~ $output->writeln("\n<info>{$message}</info>");
	}
}
