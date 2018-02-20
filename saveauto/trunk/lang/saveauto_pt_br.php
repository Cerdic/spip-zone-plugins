<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/saveauto?lang_cible=pt_br
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'ajouter_webmestre' => 'Incluir o webmaster nos destinatários',

	// B
	'bouton_sauvegarder' => 'Fazer o backup da base',

	// C
	'colonne_auteur' => 'Criado por',
	'colonne_nom' => 'Nome',

	// E
	'erreur_impossible_creer_verifier' => 'Não foi possível criar o arquivo @fichier@, verifique os direitos de escrita do diretório @rep_bases@.',
	'erreur_impossible_liste_tables' => 'Não foi possível listar as tabelas da base.',
	'erreur_probleme_donnees_corruption' => 'Problema com os dados de @table@, possível corrupção!',
	'erreur_repertoire_inaccessible' => 'O diretório @rep@ não está acessível para escrita.',
	'erreur_repertoire_perso_inaccessible' => 'O diretório @rep@ configurado não está acessível: usando o diretório de backups do SPIP, no lugar',

	// H
	'help_cfg_generale' => 'Estes parâmetros de configuração se aplicam a todos os backups, manuais ou automáticos.',
	'help_contenu' => 'Escolha os parametros de conteúdo do seu arquivo de backup.',
	'help_contenu_auto' => 'Escolher o conteúdo dos backups automáticos.',
	'help_frequence' => 'Indique a frequência dos backups automáticos em dias.',
	'help_liste_tables' => 'Por padrão, todas as tabelas do SPIP são exportadas, exceto as tabelas @noexport@. Se você deseja selecionar precisamente as tabelas a serem incluídas (bem como tabelas não SPIP) abra a lista, marcando a opção abaixo.',
	'help_mail_max_size' => 'Informar o tamanho máximo em MB do arquivo de backup a partir do qual o e-mail não será enviado (valor a ser verificado com o seu fornecedor de e-mail).',
	'help_max_zip' => 'O arquivo de backup é compactado automaticamente se o seu tamanho é inferior a um patamar. Informe esse patamar em MB. (Este patamar é mecessário para não travar o servidor para a compactação de um zip grande demais)',
	'help_nbr_garder' => 'Informe o número mínimo de backups a manter, independentemente do critério de idade',
	'help_notif_active' => 'Se você deseja ser avisado dos tratamentos automáticos, ative as notificações. Para o backup automático você receberá o arquivo genado por e-mail se ele não for muito volumoso e o plugin Carteiro estiver ativo.',
	'help_notif_mail' => 'Informe os endereços, separando-os por vírgulas ",".',
	'help_obsolete' => 'Informe a validade dos backups, em dias',
	'help_prefixe' => 'Informe o prefixo associado ao nome de cada arquivo de backup',
	'help_repertoire' => 'Para usar um diretorio de gravação diferente do usado pelos backups do SPIP, informe o caminho, a partir da raiz do site (com / no final)',
	'help_restauration' => '<strong>Atenção!!!</strong> estes backups  <strong>não estão no formato dos do SPIP</strong> e não podem ser usados com a ferramenta de restauração da base do SPIP.<br /><br />

Para qualquer restauração, é necessário usar a interface <strong>phpmyadmin</strong> do seu servidor de dados.<br /><br />

Estas backups incluem os comendos que permitem <strong>apagar</strong> as tabelas da sua base do SPIP e de as <strong>substituir</strong> pelos dados arquivados. Os dados <strong>mais recentes</strong> que os do backup serão <strong>PERDIDOS</strong>!',
	'help_sauvegarde_1' => 'Esta opção permite fazer o backup da estrutura e do conteúdo da base num arquiv no formato MySQL, que será gravado no diretório tmp/dump/. O nome do arquivo é <em>@prefixe@_aaaammjj_hhmmss.</em>. O prefixo das tabelas é preservado.',
	'help_sauvegarde_2' => 'O backup automático está ativo (frequência em dias: @frequence@).',

	// I
	'info_sql_auteur' => 'Autor: ',
	'info_sql_base' => 'Base: ',
	'info_sql_compatible_phpmyadmin' => 'Arquivo SQL 100% compatível PHPMyadmin',
	'info_sql_date' => 'Data: ',
	'info_sql_debut_fichier' => 'Início do arquivo',
	'info_sql_donnees_table' => 'Dados da tabela @table@',
	'info_sql_fichier_genere' => 'Este arquivo é gerado pelo plugin Saveauto',
	'info_sql_fin_fichier' => 'Fim do arquivo',
	'info_sql_ipclient' => 'IP Cliente: ',
	'info_sql_mysqlversion' => 'Versão MySQL : ',
	'info_sql_os' => 'OS Servidor: ',
	'info_sql_phpversion' => 'Versão PHP: ',
	'info_sql_plugins_utilises' => '@nb@ plugins usados:',
	'info_sql_serveur' => 'Servidor: ',
	'info_sql_spip_version' => 'Versão do SPIP: ',
	'info_sql_structure_table' => 'Estrutura da tabela @table@',

	// L
	'label_donnees' => 'Dados das tabelas',
	'label_frequence' => 'Frequência dos backups',
	'label_mail_max_size' => 'Patamar de envio de e-mail',
	'label_max_zip' => 'Patamar dos zips',
	'label_nbr_garder' => 'Quantos backups devem ser preservados',
	'label_nettoyage_journalier' => 'Ativar a limpeza periódica dos arquivos',
	'label_notif_active' => 'Ativar as notificações',
	'label_notif_mail' => 'Endereços de e-mail a notificar',
	'label_obsolete_jours' => 'Preservação dos backups',
	'label_prefixe_sauvegardes' => 'Prefixo',
	'label_repertoire_sauvegardes' => 'Diretório',
	'label_sauvegarde_reguliere' => 'Ativar o backup regular',
	'label_structure' => 'Estrutura das tabelas',
	'label_tables_non_spip' => 'Tabelas não SPIP',
	'label_toutes_tables' => 'Fazer backup de todas as tabelas do SPIP',
	'legend_cfg_generale' => 'Parâmetros gerais dos backups',
	'legend_cfg_notification' => 'Notificações',
	'legend_cfg_sauvegarde_reguliere' => 'Tratamentos automáticos',

	// M
	'message_aucune_sauvegarde' => 'Nenum backup disponível para transferência.',
	'message_cleaner_sujet' => 'Limpeza dos backups',
	'message_notif_cleaner_intro' => 'A supressão automática dos backups obsoletos (em que a data é anterior a @duree@ dias) foi corretamente efetuada. Os arquivos a seguir foram excluídos: ',
	'message_notif_sauver_intro' => 'O backup da base @base@ foi feita corretamente pelo autor @auteur@.',
	'message_sauvegarde_nok' => 'Erro ao fazer o backup da base.',
	'message_sauvegarde_ok' => 'O backup da base foi corretamente realizado.',
	'message_sauver_sujet' => 'Backup da base @base@',
	'message_telechargement_nok' => 'Erro durante a transferência.',

	// T
	'titre_boite_historique' => 'Backups MySQL disponíveis para transferência no diretório @dossier@',
	'titre_boite_sauver' => 'Criar um backup MySQL',
	'titre_page_configurer' => 'Configuração do plugin Backup automático',
	'titre_page_saveauto' => 'Backup da  base no formato MySQL'
);
