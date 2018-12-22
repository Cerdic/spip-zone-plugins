<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/mailshot?lang_cible=pt_br
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_exemple' => 'Exemplo',
	'cfg_exemple_explication' => 'Explicação deste exemplo',
	'cfg_titre_parametrages' => 'Configurar o envio de e-mails em massa',

	// E
	'erreur_aucun_service_configure' => 'Nenhum serviço de e=mail configurado. <a href="@url@">Configurar um serviço</a>',
	'erreur_envoi_mail_bloque_debug' => 'Envio da mensagem bloqueada por <tt>_TEST_EMAIL_DEST</tt>', # MODIF
	'erreur_envoi_mail_force_debug' => 'Envio de mensagem forçada para @email@ por <tt>_TEST_EMAIL_DEST</tt>', # MODIF
	'erreur_generation_newsletter' => 'Ocorreu um erro na geração da newsletter', # MODIF
	'explication_boost_send' => 'Neste modo, as mensagens serão enviados tão rapidamente quanto possível. Nenhum limite de cadência é levado em conta.

O envio rápido é desaconselhado por aumentar o risco de ser classificado como SPAM.',
	'explication_purger_historique' => 'Para cada envio em massa, o conjunto de destinatários é conservado em base, com as informações relativas ao status do seu envio.

Isto pode representar um volume importante de dados; se você faz muitos envios, é aconselhávek purgar o detalhe dos envios antigos,',
	'explication_rate_limit' => 'Informe o número máximo de mensagens enviadas por dia, ou deixe em branco para não fixar um limite',

	// I
	'info_1_mailshot' => '1 envio',
	'info_1_mailshot_destinataire' => '1 destinatario',
	'info_1_mailsubscriber' => '1 inscrito',
	'info_annuler_envoi' => 'Cancelar o envio',
	'info_archiver' => 'Archiver',
	'info_aucun_destinataire' => 'Nenhum destinatário',
	'info_aucun_envoi' => 'Nenhum envio',
	'info_envoi_programme_1_destinataire' => 'Envio programado para 1 destinatário',
	'info_envoi_programme_nb_destinataires' => 'Envio programado para @nb@ destinatátios',
	'info_mailshot_no' => 'Envio n° @id@',
	'info_nb_mailshots' => '@nb@ envios',
	'info_nb_mailshots_destinataires' => '@nb@ destinatários',
	'info_nb_mailsubscribers' => '@nb@ inscritos',
	'info_statut_archive' => 'arquivado',
	'info_statut_cancel' => 'Anulado',
	'info_statut_destinataire_clic' => 'Clicado',
	'info_statut_destinataire_fail' => 'Falhado',
	'info_statut_destinataire_read' => 'Aberto',
	'info_statut_destinataire_sent' => 'Enviado',
	'info_statut_destinataire_spam' => '>Spam',
	'info_statut_destinataire_todo' => 'A enviar',
	'info_statut_end' => 'Terminado',
	'info_statut_init' => 'Agendado',
	'info_statut_pause' => 'Em Pausa',
	'info_statut_poubelle' => 'Lixeira',
	'info_statut_processing' => 'Em andamento',

	// L
	'label_avancement' => 'Avanço',
	'label_boost_send_oui' => 'Envio rápido',
	'label_control_pause' => 'Pausa',
	'label_control_play' => 'Relançar',
	'label_control_stop' => 'Abandonar',
	'label_date_fin' => 'Data de fim de envio',
	'label_date_start' => 'Data de início de envio',
	'label_envoi' => 'Envio',
	'label_from' => 'Remetente',
	'label_html' => 'Versão HTML',
	'label_listes' => 'Listas',
	'label_mailer_defaut' => 'Usar o mesmo serviço de envio dos outros e-mails',
	'label_mailer_defaut_desactive' => 'Impossível: nenhum serviço de envio de e-mail foi configurado',
	'label_mailer_mailjet' => 'Serviço Mailjet',
	'label_mailer_mandrill' => 'Serviço Mandrill',
	'label_mailer_smtp' => 'Servidor SMTP',
	'label_mailer_sparkpost' => 'Sparkpost',
	'label_mailjet_api_key' => 'Chave API Mailjet',
	'label_mailjet_api_version' => 'API Versão',
	'label_mailjet_secret_key' => 'Chave secreta Mailjet',
	'label_mandrill_api_key' => 'Mandrill API Key',
	'label_purger_historique_delai' => 'Anterior a',
	'label_purger_historique_oui' => 'Excluir os detalhes dos envios antigos',
	'label_rate_limit' => 'Limitar a cadência de envio',
	'label_sparkpost_api_key' => 'Sparkpost API Key',
	'label_sujet' => 'Assunto',
	'label_texte' => 'Versão Texto',
	'legend_configuration_adresse_envoi' => 'Endereço de envio',
	'legend_configuration_historique' => 'Histórico dos envios',
	'legend_configuration_mailer' => 'Serviço de envio de e-mails',
	'lien_voir_newsletter' => 'Ver newsletter',

	// M
	'mailshot_titre' => 'MailShot',

	// T
	'texte_changer_statut_mailshot' => 'Este envio está:',
	'texte_statut_archive' => 'arquivado',
	'texte_statut_cancel' => 'anulado',
	'texte_statut_end' => 'terminado',
	'texte_statut_init' => 'agendado',
	'texte_statut_pause' => 'em pausa',
	'texte_statut_processing' => 'em andamento',
	'titre_envois_archives' => 'Envios arquivados',
	'titre_envois_destinataires_fail' => 'Envios falhados',
	'titre_envois_destinataires_init_encours' => 'Nenhum destinatário programado (inicialisação em andamento)',
	'titre_envois_destinataires_ok' => 'Envios com êxito',
	'titre_envois_destinataires_sent' => 'Envios com êxito',
	'titre_envois_destinataires_todo' => 'Envios futuros',
	'titre_envois_en_cours' => 'Envios em andamento',
	'titre_envois_planifies' => 'Envios agendados',
	'titre_envois_termines' => 'Envios terminados',
	'titre_mailshot' => 'Envios em massa',
	'titre_mailshots' => 'Emvios em massa',
	'titre_menu_mailshots' => 'Acompanhamento dos envios em massa',
	'titre_page_configurer_mailshot' => 'MailShot'
);
