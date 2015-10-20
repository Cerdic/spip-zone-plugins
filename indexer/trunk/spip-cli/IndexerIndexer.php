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
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		include_spip('inc/indexer');
		global $spip_racine;
		global $spip_loaded;
		
		// Appeler la fonction qui donne l'indexeur configuré pour ce SPIP
		$indexer = indexer_indexer();
		// Appeler la fonction qui liste les sources et qui comporte un pipeline pour étendre
		$sources = indexer_sources();
		
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
