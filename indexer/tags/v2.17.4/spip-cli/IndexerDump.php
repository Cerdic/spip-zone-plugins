<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

class IndexerDump extends Command {
	protected function configure() {
		$this
			->setName('indexer:dump')
			->setDescription('Récupérer les contenus indexés.')
			->addOption(
				'index',
				'i',
				InputOption::VALUE_OPTIONAL,
				'nom de l’index sphinx',
				null
			)
			->addOption(
				'format',
				'f',
				InputOption::VALUE_OPTIONAL,
				'format de sortie : sphinx ou mysql',
				'sphinx'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		include_spip('inc/indexer');

		$index = $input->getOption('index');
		$format = $input->getOption('format');

		indexer_dumpsql($index, $format);
	}
}
