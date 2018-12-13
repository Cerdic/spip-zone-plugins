<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/mailsubscriber?lang_cible=pt_br
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_importer' => 'Importar',
	'bouton_invitation' => 'Convidar alguém para se cadastrar na newsletter',
	'bouton_previsu_importer' => 'Pré-visualizar',

	// C
	'confirmsubscribe_invite_texte_email_1' => '@invite_email_from@ convida-o para se cadastrar na newsletter de @nom_site_spip@ com o email @email@.',
	'confirmsubscribe_invite_texte_email_3' => 'Caso tenha occorrido um erro, você pode ignorar essa messagem : o pedido será cancelado automaticamente.',
	'confirmsubscribe_invite_texte_email_liste_1' => '@invite_email_from@ convida você a se inscrever na lista «@titre_liste@» do site @nom_site_spip@ com o endereço de e-mail @email@.', # MODIF
	'confirmsubscribe_sujet_email' => '[@nom_site_spip@] Confirmação do cadastro na newsletter',
	'confirmsubscribe_texte_email_1' => 'Você solicitou seu cadastro na newsletter de @nom_site_spip@ com o email @email@.',
	'confirmsubscribe_texte_email_2' => 'Para confirmar seu cadastro, por favor clique aqui : 
	@url_confirmsubscribe@',
	'confirmsubscribe_texte_email_3' => 'Caso tenha ocorrido um erro, ou se você mudou de ideia, você pode ignorar essa messagem : o pedido será cancelado automaticamente.',
	'confirmsubscribe_texte_email_envoye' => 'Um e-mail foi enviado nesse endereço para confirmação.',
	'confirmsubscribe_texte_email_liste_1' => 'Você solicitou a sua inscriçào na lista «@titre_liste@» do site @nom_site_spip@ com o endereço de e-mail @email@.', # MODIF
	'confirmsubscribe_titre_email' => 'Confirmação do cadastro na newsletter',
	'confirmsubscribe_titre_email_liste' => 'Confirmação de inscrição na lista «<b>@titre_liste@</b>»', # MODIF

	// D
	'defaut_message_invite_email_subscribe' => 'Oi, eu estou cadastrado na newsletter do @nom_site_spip@ e gostaria de convidá-lo para fazer o cadastro tambem.',

	// E
	'erreur_adresse_existante' => 'Esse email já está na lista',
	'erreur_adresse_existante_editer' => 'Este endereço de e-mail já está cadastrado - <a href="@url@">Editar este usuário</a>',
	'erreur_technique_subscribe' => 'Seu cadastro não pode ser gravado em razão de um problema técnico.',
	'explication_listes_diffusion_option_defaut' => 'Um ou mais identificadores de listas separados por vírgula',
	'explication_listes_diffusion_option_statut' => 'Filtrar as listas de acordo com o status',
	'explication_to_email' => 'Enviar um email de proposta de pré inscrição aos seguintes endereços (varios endereços separados por uma vírgula si presica).',

	// I
	'icone_creer_mailsubscriber' => 'Incluir uma inscrição ',
	'icone_modifier_mailsubscriber' => 'Alterar essa inscrição',
	'info_1_adresse_a_importer' => '1 email para a importação',
	'info_1_mailsubscriber' => '1 inscrito para envio',
	'info_aucun_mailsubscriber' => 'Nenhum inscrito para envio ',
	'info_email_inscriptions' => 'Inscrições para @email@:',
	'info_email_limite_nombre' => 'Convite limitado à 5 pessoas.',
	'info_email_obligatoire' => 'E-mail obrigatório',
	'info_emails_invalide' => 'Um dos e-mails é inválido',
	'info_nb_adresses_a_importer' => '@nb@ emails para a importação',
	'info_nb_mailsubscribers' => '@nb@ inscritos para envio',
	'info_statut_poubelle' => 'lixeira',
	'info_statut_prepa' => 'não registrado',
	'info_statut_prop' => 'pendente',
	'info_statut_refuse' => 'suspenso',
	'info_statut_valide' => 'registrado',

	// L
	'label_desactiver_notif_1' => 'Desativar a notifição das inscrições para essa importação',
	'label_email' => 'E-mail',
	'label_file_import' => 'Arquivos para a importação',
	'label_from_email' => 'E-mail para convidar',
	'label_informations_liees' => 'Informações de segmentação',
	'label_inscription' => 'Inscrição',
	'label_lang' => 'Idioma',
	'label_listes' => 'Listas',
	'label_listes_diffusion_option_statut' => 'Estado',
	'label_listes_import_subscribers' => 'Cadastrar às listas',
	'label_mailsubscriber_optin' => 'Gostaria de receber a newsletter',
	'label_message_invite_email_subscribe' => 'Mensagem de convite personalizada ao e-mail de cadastro',
	'label_nom' => 'Nome',
	'label_optin' => 'Opt-in',
	'label_statut' => 'Estado',
	'label_to_email' => 'Email de convite',
	'label_toutes_les_listes' => 'Todas',
	'label_valid_subscribers_1' => 'Validar diretamente as inscrições sem pedido de confirmação',
	'label_vider_table_1' => 'Suprimir todos os endereços email no banco de dados antes da importação',

	// M
	'mailsubscribers_poubelle' => 'Excluidos',
	'mailsubscribers_prepa' => 'Não registrado',
	'mailsubscribers_prop' => 'Pendente a confirmação',
	'mailsubscribers_refuse' => 'Desistente',
	'mailsubscribers_tous' => 'Todos',
	'mailsubscribers_valide' => 'Registrados',

	// S
	'subscribe_deja_texte' => 'O email @email@ já está en nossa mailing list', # MODIF
	'subscribe_sujet_email' => '[@nom_site_spip@] Inscrição na newsletter',
	'subscribe_texte_email_1' => 'Sua inscriçao está confirmada na newsletter com esse email @email@.',
	'subscribe_texte_email_2' => 'Agradecemos seu interesse pelo @nom_site_spip@.',
	'subscribe_texte_email_3' => 'Caso tenha ocorrido um erro, ou se você mudou sua me, você pode cancelar a inscrição na newsletter na qualquer momento :
@url_unsubscribe@',
	'subscribe_texte_email_liste_1' => 'A sua inscrição na lista «@titre_liste@» com o endereço de e-mail @email@ foi realizada.', # MODIF
	'subscribe_titre_email' => 'Inscrição na newsletter',
	'subscribe_titre_email_liste' => 'Inscrição na lista «<b>@titre_liste@</b>»', # MODIF

	// T
	'texte_ajouter_mailsubscriber' => 'Incluir uma inscrição na newsletter',
	'texte_avertissement_import' => 'Uma coluna <tt>estado</tt> é fornecida : os dados serão importados dessa forma, e substituirão os outros dados que poderiam existir em alguns endereços.',
	'texte_changer_statut_mailsubscriber' => 'Essa inscrição é :',
	'texte_import_export_bonux' => 'Para importar ou exportar a lista de incritos, instale o plugin <a href="https://plugins.spip.net/spip_bonux">SPIP-Bonux</a>',
	'texte_statut_en_attente_confirmation' => 'pendente de confirmação',
	'texte_statut_pas_encore_inscrit' => 'não registrada',
	'texte_statut_refuse' => 'excluida',
	'texte_statut_valide' => 'ativa',
	'texte_vous_avez_clique_vraiment_tres_vite' => 'Você clicou muito rápido no botão de confirmação. Você é realmente humano?',
	'titre_bonjour' => 'Oi',
	'titre_export_mailsubscribers' => 'Exportar as inscrições',
	'titre_export_mailsubscribers_all' => 'Exportar todos os endereços',
	'titre_import_mailsubscribers' => 'Importar endereços',
	'titre_langue_mailsubscriber' => 'Idioma associado a essa inscrição',
	'titre_listes_de_diffusion' => 'Mailing lists',
	'titre_logo_mailsubscriber' => 'Logo associado a esse inscrição',
	'titre_mailsubscriber' => 'Inscrição na newsletter',
	'titre_mailsubscribers' => 'Inscrições na newsletter',

	// U
	'unsubscribe_deja_texte' => 'O endereço email @email@ não esta incluido em nossa mailing list.', # MODIF
	'unsubscribe_sujet_email' => '[@nom_site_spip@] Cancelar a inscrição na newsletter',
	'unsubscribe_texte_confirmer_email_1' => 'Por favor, confirme o cancelamento da inscrição na newsletter por o email @email@, e clique no botão : ',
	'unsubscribe_texte_confirmer_email_liste_1' => 'Por favor, confirme o cancelamento da inscrição do endereço de e-mail @email@ da lista <b>@titre_liste@</b>, clicando no botão: ', # MODIF
	'unsubscribe_texte_email_1' => 'Esse endereço email @email@ foi retirado da nossa mailing list.', # MODIF
	'unsubscribe_texte_email_2' => 'A gente espera de te encontrar de novo em breve no @nom_site_spip@.',
	'unsubscribe_texte_email_3' => 'No caso de nosso erro, ou caso você tenha mudado de ideia, você pode fazer uma nova inscrição na newsletter, clique aqui :
@url_subscribe@',
	'unsubscribe_texte_email_liste_1' => 'O endereço de e-mail @email@ foi excluído da lista de difusão <b>@titre_liste@</b>.', # MODIF
	'unsubscribe_titre_email' => 'Cancelar a inscrição na newsletter',
	'unsubscribe_titre_email_liste' => 'Cancelamento da inscrição na lista <b>@titre_liste@</b>' # MODIF
);
