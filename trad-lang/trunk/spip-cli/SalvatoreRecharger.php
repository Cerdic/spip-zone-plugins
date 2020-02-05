<?php

/*
    This file is part of Salvatore, the translation robot of Trad-lang (SPIP)

    Salvatore is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Trad-Lang is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Trad-Lang; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    Copyright 2003-2020
        Florent Jugla <florent.jugla@eledo.com>,
        Philippe Riviere <fil@rezo.net>,
        Chryjs <chryjs!@!free!.!fr>,
        kent1 <kent1@arscenic.info>
        Cerdic <cedric@yterium.com>
*/

/**
 * Prend les fichiers de langue de référence de salvatore/modules/ et met à jour la base de données
 * pour les modules decrits dans le fichier traductions/traductions.txt
 *
 */


use Spip\Cli\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;


class SalvatoreRecharger extends Command {
	protected function configure(){
		$this
			->setName('salvatore:recharger')
			->setDescription('Tirer&Lire les modules modifies des fichiers de langue de référence de salvatore/modules/')
			->addOption(
				'traductions',
				null,
				InputOption::VALUE_REQUIRED,
				'Chemin vers le fichier traductions.txt a utiliser [salvatore/traductions/traductions.txt]',
				null
			)
			->addOption(
				'changelog',
				null,
				InputOption::VALUE_REQUIRED,
				'Chemin vers le fichier JSON changelog des dernières modifications sur la zone',
				null
			)
			->addOption(
				'from',
				null,
				InputOption::VALUE_REQUIRED,
				'Date ou intervalle de temps sur lequel considerer les changements. Defaut : -1hour',
				null
			)
			->addOption(
				'module',
				null,
				InputOption::VALUE_REQUIRED,
				'Un ou plusieurs modules a traiter (par defaut tous les modules du fichier de traduction seront traites)',
				null
			)
			->addOption(
				'force',
				null,
				InputOption::VALUE_NONE,
				'Forcer la relecture du ou des modules et la mise a jour en base indépendament de la date de dernière mise a jour des fichiers',
				null
			)
			->addOption(
				'time',
				null,
				InputOption::VALUE_NONE,
				'Ajouter date/heure sur les sorties pour les logs',
				null
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		global $spip_racine;
		global $spip_loaded;

		include_spip('inc/salvatore');

		$time = $input->getOption('time');
		salvatore_init(array($output, 'writeln'), !!$time);

		salvatore_log("<comment>=======================================</comment>");
		salvatore_log("<comment>RECHARGER [Tirer&Lire les modules modifies sur la zone]</comment>");
		salvatore_log("<comment>=======================================</comment>");


		$traductions = $input->getOption('traductions');
		$liste_trad = salvatore_charger_fichier_traductions($traductions);
		$n = count($liste_trad);
		salvatore_log("<info>$n modules dans le fichier traductions " . ($traductions ? $traductions : '') . "</info>");

		$modules = $input->getOption('module');
		if ($modules = trim($modules)) {
			$liste_trad = salvatore_filtrer_liste_traductions($liste_trad, $modules);
			$n = count($liste_trad);
			salvatore_log("<info>$n modules à traiter : " . $modules . "</info>");
		}

		$changelogFile = $input->getOption('changelog');
		if (!$changelogFile or !file_exists($changelogFile)) {
			salvatore_log("<error>Indiquez un fichier valide comme changelog</error>");
			exit(1);
		}
		$changelog = file_get_contents($changelogFile);
		if (!$changelog or !$changelog = json_decode($changelog, true)) {
			salvatore_log("<info>Rien a faire, changelog vide</info>");
			return;
		}

		$from = $input->getOption('from');
		if (!$from) {
			$from = '-1hour';
		}
		$t_since = strotime($from);
		if (!$t_since) {
			salvatore_log("<error>Indiquez un temps valide pour l'option --from</error>");
			exit(1);
		}

		$changed_trad = $this->filter_changed_traductions($liste_trad, $changelog, $t_since);
		if ($changed_trad) {
			$n = count($changed_trad);
			$changed_modules = array_column($changed_trad, 'module');
			$changed_modules = array_unique($changed_modules);
			salvatore_log("<info>$n modules modifiés à recharger : " . implode(',', $changed_modules) . "</info>");

			include_spip('salvatore/lecteur');
			include_spip('salvatore/tireur');
			salvatore_tirer($changed_trad);

			$force = $input->getOption('force');
			salvatore_lire($liste_trad, $force);
		}
		else {
			salvatore_log("<info>Rien a faire, aucun fichier correspondant a un module</info>");
		}

	}

	protected function filter_changed_traductions($liste_trad, $changelog, $t_since) {

		$changed_trad = array();
		$changed = array();
		foreach($changelog as $file => $lastmodified) {
			if ($lastmodified > $t_since
			  and strpos($file, "/lang") !== false
			) {
				$changed[] = $file;
			}
		}
		$changed = array_unique($changed);
		salvatore_log(count($changed) . " fichiers changés");

		foreach($changed as $c) {
			$depots_possibles = [
				"svn://zone.spip.org/spip-zone/" . rtrim($c, '/'),
			];
			if (strpos($c, "spip-zone/_core_/plugins/") === 0) {
				$r = explode('/', $c);
				array_shift($r);
				array_shift($r);
				array_shift($r);
				$r = array_shift($r);
				$depots_possibles[] = "https://git.spip.net/spip/$r.git";
			}
			if (strpos($c, "spip-zone/_plugins_/") === 0) {
				$r = explode('/', $c);
				array_shift($r);
				array_shift($r);
				$r = array_shift($r);
				$depots_possibles[] = "https://git.spip.net/spip-contrib-extensions/$r.git";
			}
			if (strpos($c, "spip-zone/_squelette_/") === 0) {
				$r = explode('/', $c);
				array_shift($r);
				array_shift($r);
				$r = array_shift($r);
				$depots_possibles[] = "https://git.spip.net/spip-contrib-squelettes/$r.git";
			}
			foreach ($liste_trad as $k=>$source) {
				if (in_array(rtrim($source['url'], '/'), $depots_possibles)) {
					$changed_trad[] = $source;
					unset($liste_trad[$k]);
					break;
				}
			}
		}

		return $changed_trad;
	}
}

