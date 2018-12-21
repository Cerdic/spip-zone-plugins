<?php
namespace Spip\Cli\Command;

use Spip\Cli\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

class MailsubscribinglistClean extends Command {
	protected function configure() {
		$this
			->setName('mailsubscribinglist:clean')
			->setDescription('Nettoyer les listes en desinscrivant tous les subscribers qui n\'ont pas été vu vivants depuis plus de N mois')
			->addOption(
				'from',
				null,
				InputOption::VALUE_OPTIONAL,
				'Duree d\'inactivité à prendre en compte au format strtotime : \'10 days\', \'6 months\'…',
				''
			)
			->addOption(
				'listes',
				null,
				InputOption::VALUE_OPTIONAL,
				'Listes à nettoyer, séparées par des virgules. Par défaut, toutes',
				null
			)
			->addOption(
				'notify',
				null,
				InputOption::VALUE_NONE,
				'indique si on veut ou non notifier par email les désabonnements, ce qui permettrait aux personnes de se réinscrire si besoin'
			)
			->addOption(
				'yes',
				'y',
				InputOption::VALUE_NONE,
				'Desinscrire sans demander confirmation'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		include_spip('inc/filtres');

		#global $spip_racine;
		#global $spip_loaded;

		$from = $input->getOption('from');
		if (!$from) {
			$from = strtotime('-6 months');
		}
		else {
			$from = strtotime('-' . $from);
		}
		if (!$from) {
			$output->writeln("<error>from invalide</error>");
			exit(1);
		}
		$from = date('Y-m-d H:i:s', $from);

		$options = array();
		if ($input->getOption('notify')) {
			$options['notify'] = true;
		}
		else {
			$options['notify'] = false;
		}

		$in_listes = '';
		$listes = $input->getOption('listes');
		if (!is_null($listes)) {
			$listes = explode(',', $listes);
			$options['listes'] = $listes;

			$id_mailsubscribinglists = sql_allfetsel('id_mailsubscribinglist', 'spip_mailsubscribinglists', sql_in('identifiant', $listes));
			$id_mailsubscribinglists = array_column($id_mailsubscribinglists, 'id_mailsubscribinglist');
			$in_listes = " AND " . sql_in('id_mailsubscribinglist', $id_mailsubscribinglists);
		}

		// charger en memoire TOUS les emails ayant ete vu vivants depuis $from
		$email_alive = sql_allfetsel('distinct email', 'spip_mailshots_destinataires', sql_in('statut', array('clic', 'read')) . ' AND date>' . sql_quote($from));
		$email_alive = array_column($email_alive, 'email');
		$output->writeln("<info>Emails vus vivants depuis $from : " . count($email_alive) . "</info>");


		$id_mailsubscribers_alive = sql_allfetsel("id_mailsubscriber", "spip_mailsubscribers", sql_in('email', $email_alive));
		$id_mailsubscribers_alive = array_column($id_mailsubscribers_alive, 'id_mailsubscriber');
		$output->writeln("<info>Mailsubscribers vus vivants depuis $from : " . count($id_mailsubscribers_alive) . "</info>");

		// trouver tous les zombies
		$zombies = sql_allfetsel('id_mailsubscriber, email', 'spip_mailsubscribers', "statut='valide' AND " . sql_in('id_mailsubscriber', $id_mailsubscribers_alive, 'NOT'));
		$id_mailsubscribers_zombies = array_column($zombies, 'id_mailsubscriber');

		// ceux a qui on a rien envoye depuis from ne sont pas des vrais zombies mais surement des nouveaux inscrits, on les enleve donc
		$email_zombies = array_column($zombies, 'email');
		$email_vrai_zombies = sql_allfetsel('distinct email', 'spip_mailshots_destinataires', sql_in('statut', array('todo', 'fail'), 'NOT') . ' AND ' .sql_in('email', $email_zombies) . ' AND date>' . sql_quote($from));
		$email_vrai_zombies = array_column($email_vrai_zombies, 'email');
		$zombies = sql_allfetsel('id_mailsubscriber, email', 'spip_mailsubscribers', "statut='valide' AND " . sql_in('email', $email_vrai_zombies));
		$id_mailsubscribers_zombies = array_column($zombies, 'id_mailsubscriber');


		$output->writeln("Mailsubscribers zombies depuis $from : " . count($id_mailsubscribers_zombies));


		// et restreindre aux listes demandées uniquement (si besoin)
		$id_mailsubscribers_unsub = sql_allfetsel("DISTINCT id_mailsubscriber", 'spip_mailsubscriptions', sql_in('id_mailsubscriber',$id_mailsubscribers_zombies). " AND statut='valide' AND id_segment=0" . $in_listes);
		$id_mailsubscribers_unsub = array_column($id_mailsubscribers_unsub, 'id_mailsubscriber');

		// trouver les inscrits a rien
		if (!$in_listes
		  and count($id_mailsubscribers_inscrits_a_rien = array_diff($id_mailsubscribers_zombies, $id_mailsubscribers_unsub))) {

			// il y en a peut etre des prop dans le lot, on les repasse en prop
			$id_mailsubscribers_prop = sql_allfetsel("DISTINCT id_mailsubscriber", 'spip_mailsubscriptions', sql_in('id_mailsubscriber',$id_mailsubscribers_inscrits_a_rien). " AND statut='prop' AND id_segment=0");
			$id_mailsubscribers_prop = array_column($id_mailsubscribers_prop, 'id_mailsubscriber');
			if (count($id_mailsubscribers_prop)) {
				$this->io->care(count($id_mailsubscribers_prop) . ' sont en fait en attente de confirmation : ' . MailsubscribinglistClean::extraitListe($id_mailsubscribers_prop));
				if (
					$input->getOption('yes')
					or $this->io->confirm("Repasser les " . count($id_mailsubscribers_prop) . " subscribers en prop ?", false)
				){
					sql_updateq("spip_mailsubscribers", array('statut' => 'prop'), sql_in('id_mailsubscriber', $id_mailsubscribers_prop));
					$this->io->check(count($id_mailsubscribers_prop) . ' corrigés');
				}
				$id_mailsubscribers_inscrits_a_rien = array_diff($id_mailsubscribers_inscrits_a_rien, $id_mailsubscribers_prop);
			}

			// desinscrire ceux qui ne sont vraiment inscrits a riens
			if (count($id_mailsubscribers_inscrits_a_rien)) {
				$this->io->care(count($id_mailsubscribers_inscrits_a_rien) . ' ne sont inscrits a rien : ' . MailsubscribinglistClean::extraitListe($id_mailsubscribers_inscrits_a_rien));
				if (
					$input->getOption('yes')
					or $this->io->confirm("Desinscrire les " . count($id_mailsubscribers_inscrits_a_rien) . " inscrits a rien ?", false)
				){
					$emails_arien = sql_allfetsel('email', 'spip_mailsubscribers', sql_in('id_mailsubscriber', $id_mailsubscribers_inscrits_a_rien));
					$emails_arien = array_column($emails_arien, 'email');
					MailsubscribinglistClean::unsubscribeAll($this->io, $emails_arien, $options);
					$this->io->check(count($id_mailsubscribers_inscrits_a_rien) . ' corrigés');
				}
			}
		}


		$nb_unsub = count($id_mailsubscribers_unsub);
		$this->io->care("Mailsubscribers a désabonner".($listes ? " des listes ". implode(',',$listes) : '') . " : " . $nb_unsub);
		$this->io->text(MailsubscribinglistClean::extraitListe($id_mailsubscribers_unsub, 20));

		// compter par liste pour indication
		$details = sql_allfetsel("id_mailsubscribinglist, count(id_mailsubscriber) as N", 'spip_mailsubscriptions', sql_in('id_mailsubscriber',$id_mailsubscribers_zombies). " AND statut='valide' AND id_segment=0" . $in_listes,'id_mailsubscribinglist');
		foreach ($details as $d) {
			if ($l = sql_fetsel('identifiant, titre', 'spip_mailsubscribinglists', 'id_mailsubscribinglist='.intval($d['id_mailsubscribinglist']))) {
				$this->io->care("#".$d['id_mailsubscribinglist'] . " " . $l['titre'] . " : " . $d['N']);
			}
		}


		// verifier que aucun des emails qu'on va unsub n'a ete vu vivant (double check donc, qu'on a pas fait d'erreur dans la selection)
		$emails_unsub = sql_allfetsel('email', 'spip_mailsubscribers', sql_in('id_mailsubscriber', $id_mailsubscribers_unsub));
		$emails_unsub = array_column($emails_unsub, 'email');
		$alive_unsub = sql_allfetsel('email, max(date) as date_max, statut', 'spip_mailshots_destinataires', sql_in('statut', array('clic', 'read')) . ' AND ' . sql_in('email', $emails_unsub) . ' AND date>' . sql_quote($from), 'email');
		if (count($alive_unsub)) {
			$output->writeln("<error>Probleme de selections : on retrouve ".count($alive_unsub)." emails vivants dans ceux qu'on veut desinscrire</error>");
			exit(1);
		}

		if (!count($emails_unsub)) {
			$this->io->check("La base est propre, tous les inscrits sont vivants");
			return;
		}

		if (
			!$input->getOption('yes')
			and !$this->io->confirm("Désinscrire les $nb_unsub subscribers".($listes ? " des listes ". implode(',',$listes) : '')." ?", false)
		){
			$this->io->care("Action annulée");
			return;
		}


		MailsubscribinglistClean::unsubscribeAll($this->io, $emails_unsub, $options);

		if (!count($emails_unsub)) {
			$this->io->check("Nettoyage terminé");
		}
	}

	public static function logRecord($io, $fichier_log, $texte) {
		$io->text($texte);
		file_put_contents($fichier_log, rtrim($texte) . "\n",FILE_APPEND);
	}

	public static function unsubscribeAll($io, $emails, $options) {

		$now = time();
		$d = date('Ymd-His', $now);
		$fichier_log = _DIR_LOG . 'mailsubscribinglist-clean-'.$d.'.log';
		$io->care("Ecriture des desinscriptions dans $fichier_log");

		MailsubscribinglistClean::logRecord($io, $fichier_log,'# '. date('Y-m-d H:i:s', $now) . ' Desinscriptions ' . json_encode($options));

		$nb_total = count($emails);
		$nb = 0;
		$unsubscribe = charger_fonction('unsubscribe', 'newsletter');
		foreach ($emails as $email) {
			$nb++;
			MailsubscribinglistClean::logRecord($io, $fichier_log,"$nb/$nb_total: $email");
			$unsubscribe($email, $options);
		}

	}

	public static function extraitListe($liste, $nb = 10) {

		if (count($liste)<= $nb) {
			return implode(', ', $liste);
		}
		$out =
			implode(', ', array_slice($liste,0, intval($nb/2)))
			. ' ... '
			. implode(', ', array_slice($liste,-intval($nb/2)))
		;
		return $out;
	}
}
