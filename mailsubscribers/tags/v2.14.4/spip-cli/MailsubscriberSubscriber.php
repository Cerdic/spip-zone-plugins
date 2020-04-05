<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

class MailsubscriberSubscriber extends Command {
	protected function configure() {
		$this
			->setName('mailsubscriber:subscriber')
			->setDescription('Afficher les informations d\'un subscriber')
			->addOption(
				'email',
				null,
				InputOption::VALUE_REQUIRED,
				'email a inscrire',
				null
			)
			->addOption(
				'listes',
				null,
				InputOption::VALUE_OPTIONAL,
				'Listes (separées par des virgules si plusieurs)',
				null
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		include_spip('inc/filtres');

		global $spip_racine;
		global $spip_loaded;

		$email = $input->getOption('email');
		if (!$email) {
			$output->writeln("<error>Indiquez un email</error>");
			exit(1);
		}
		if (!$email = email_valide($email)) {
			$output->writeln("<error>Indiquez un email valide</error>");
			exit(1);
		}

		$options = array();
		$listes = $input->getOption('listes');
		if (!is_null($listes)) {
			$options['listes'] = explode(',', $listes);
		}

		$subscriber = charger_fonction('subscriber', 'newsletter');
		$infos = $subscriber($email, $options);

		$output->writeln(var_export($infos, true));
	}
}
