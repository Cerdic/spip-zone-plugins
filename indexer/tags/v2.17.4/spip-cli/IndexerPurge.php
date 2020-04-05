<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

class IndexerPurge extends Command {
	protected function configure() {
		$this
			->setName('indexer:purge')
			->setDescription('Purger l’indexation des contenus SPIP de ce site.')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		include_spip('inc/indexer');
		$indexer = indexer_indexer();
		$ret = $indexer->purgeDocuments();
		if (!$ret) die("Erreur lors de la requete\n");
	}
}
