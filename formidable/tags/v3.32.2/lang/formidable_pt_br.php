<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/formidable?lang_cible=pt_br
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_pages_explication' => 'Por padrão, as páginas públicas dos formulários não são autorizadas',
	'activer_pages_label' => 'Autorizar a criação de páginas publicas para os formulários',
	'admin_reponses_auteur' => 'Autorizar os autores dos formulários a alterar as respostas',
	'admin_reponses_auteur_explication' => 'Apenas os administradores podem alterar as respostas enviadas por um formulário (Na lixeira, publicado, proposto para avaliação). Essa opção permite que o autor de um formulário modifique o status (com risco de distorcer eventuais dados estatísticos).',
	'analyse_avec_reponse' => 'Respostas não vazias',
	'analyse_exclure_champs_explication' => 'Liste os nomes de campos a serem excluídos da análise, separados por <code>|</code>. Não inserir <code>@</code>.', # MODIF
	'analyse_exclure_champs_label' => 'Campos a excluir', # MODIF
	'analyse_exporter' => 'Exportar análise',
	'analyse_longueur_moyenne' => 'Comprimento médio em número de palavras',
	'analyse_nb_reponses_total' => '@nb@ pessoas responderam a este formulário.',
	'analyse_sans_reponse' => 'Deixados em branco',
	'analyse_une_reponse_total' => 'Uma pessoa respondeu a este formulário.',
	'analyse_zero_reponse_total' => 'Ninguém respondeu a este formulário.',
	'aucun_traitement' => 'Nenhum tratamento',
	'autoriser_admin_restreint' => 'Autorizar administradores restritos a criar e alterar os formulários',
	'autoriser_admin_restreint_explication' => 'Por padrão, somente administradores têm acesso à criação e alteração dos formulários',

	// B
	'bouton_formulaires' => 'Formulários',
	'bouton_revert_formulaire' => 'Reverter à última versão gravada',

	// C
	'cfg_analyse_classe_explication' => 'Você pode informar as classes CSS que serão adicionadas ao conteúdo de cada gráfico, como <code>gray</code>,<code>blue</code>,<code>orange</code>,<code>green</code> ou como preferir!',
	'cfg_analyse_classe_label' => 'Classe CSS da barra de progresso',
	'cfg_objets_explication' => 'Escolher os conteúdos a que os formulários podem ser vinculados.',
	'cfg_objets_label' => 'Vincular os formulários aos conteúdos',
	'cfg_titre_page_configurer_formidable' => 'Configurar Formidable',
	'cfg_titre_parametrages_analyse' => 'Parâmetros de análise das respostas',
	'champs' => 'Campos',
	'changer_statut' => 'Este formulário está:',
	'creer_dossier_formulaire_erreur_impossible_creer' => 'Não foi possível criar a pasta @dossier@,  necessária para armazenar os arquivos. Verifique os direitos de acesso.',
	'creer_dossier_formulaire_erreur_impossible_ecrire' => 'Não foi possível escrever na pasta  @dossier@, necessária para armazenar os arquivos. Verifique os direitos de acesso.',
	'creer_dossier_formulaire_erreur_possible_lire_exterieur' => 'É possível acessar remotamente o conteúdo da pasta @dossier@. Isto é um problema, em termos de confidencialidade dos dados.',

	// E
	'echanger_formulaire_forms_importer' => 'Forms & Tables (.xml)',
	'echanger_formulaire_wcs_importer' => 'W.C.S. (.wcs)',
	'echanger_formulaire_yaml_importer' => 'Formidable (.yaml)',
	'editer_apres_choix_formulaire' => 'Retornar ao formulário',
	'editer_apres_choix_redirige' => 'Redirecionar a outro endereço',
	'editer_apres_choix_rien' => 'Nada',
	'editer_apres_choix_stats' => 'Estatísticas de respostas',
	'editer_apres_choix_valeurs' => 'Valores informados',
	'editer_apres_explication' => 'Após validação, exibir no lugar do formulário:',
	'editer_apres_label' => 'Exibir em seguida',
	'editer_css' => 'Classes CSS',
	'editer_descriptif' => 'Descrição',
	'editer_descriptif_explication' => 'Explicação do formulário destinada ao espaço privado.',
	'editer_identifiant' => 'Identificador',
	'editer_identifiant_explication' => 'Insira um identificador textual único que lhe permitirá chamar mais facilmente o formulário. O identificador só pode conter números, letras não acentuadas e o caracter "_".',
	'editer_menu_auteurs' => 'Configurar autores',
	'editer_menu_champs' => 'Configurar campos',
	'editer_menu_formulaire' => 'Configurar o formulário',
	'editer_menu_traitements' => 'Configurar os tratamentos',
	'editer_message_erreur_unicite_explication' => 'Se você deixar o campo em branco, a mensagem de erro padrão do Formidable será exibida',
	'editer_message_erreur_unicite_label' => 'Mensagem de erro quando um campo não é único',
	'editer_message_ok' => 'Mensagem de retorno',
	'editer_message_ok_explication' => 'Personalize a mensagem que será exibida ao usuário após o envio de um formulário válido.', # MODIF
	'editer_modifier_formulaire' => 'Modificar formulário',
	'editer_nouveau' => 'Novo formulário',
	'editer_redirige_url' => 'Endereço de redirecionamento após validação',
	'editer_redirige_url_explication' => 'Deixar em branco se quiser que fique na mesma página',
	'editer_titre' => 'Título',
	'editer_unicite_explication' => 'Gravar o formulário apenas se um campo for único', # MODIF
	'editer_unicite_label' => 'Verificar a unicidade do campo',
	'erreur_autorisation' => 'Você não tem permissão para editar os formulários do site.',
	'erreur_base' => 'Aconteceu um erro técnico durante a gravação.',
	'erreur_deplacement_fichier' => 'O arquivo « @nom@ » não pode ser armazenado corretamente pelo sistema. Contate o webmaster.',
	'erreur_fichier_expire' => 'O link para a transferência do arquivo é muito antigo.',
	'erreur_fichier_introuvable' => 'O arquivo solicitado não foi encontrado.',
	'erreur_generique' => 'Há erro(s) no preenchimento do(s) campo(s) indicado(s) abaixo.',
	'erreur_identifiant' => 'Esse identificador já está em uso.',
	'erreur_identifiant_format' => 'O identificador só pode conter números, letras não acentuadas e o caracter "_"',
	'erreur_importer_forms' => 'Erro na importação do formulário Forms&Tables',
	'erreur_importer_wcs' => 'Erro na importação do formulário W.C.S',
	'erreur_importer_yaml' => 'Erro na importação do arquivo YAML',
	'erreur_inexistant' => 'Formulário inexistente.',
	'erreur_unicite' => 'Este valor já está sendo usado',
	'exporter_formulaire_date_debut_label' => 'A partir de', # MODIF
	'exporter_formulaire_date_erreur' => 'A data de início deve ser anterior à data de fim',
	'exporter_formulaire_date_fin_label' => 'Até', # MODIF
	'exporter_formulaire_format_label' => 'Formato do arquivo',
	'exporter_formulaire_statut_label' => 'Respostas',

	// F
	'formulaire_anonyme_explication' => 'Este formulário é anônimo, ou seja, a identificação de usuário não será gravada.',
	'formulaires_aucun' => 'Ainda não há nenhum formulário.',
	'formulaires_aucun_champ' => 'Ainda não há nenhum campo de entrada para esse formulário.',
	'formulaires_corbeille_tous' => '@nb@ formulários na lixeira',
	'formulaires_corbeille_un' => 'Um formulário na lixeira',
	'formulaires_dupliquer' => 'Duplicar formulário',
	'formulaires_dupliquer_copie' => '(cópia)',
	'formulaires_introduction' => 'Crie e configure aqui os formulários do site.',
	'formulaires_nouveau' => 'Criar um novo formulário',
	'formulaires_reponses_corbeille_tous' => '@nb@ respostas de formulário na lixeira',
	'formulaires_reponses_corbeille_un' => 'Uma resposta de formulário na lixeira',
	'formulaires_supprimer' => 'Apagar formulário',
	'formulaires_supprimer_confirmation' => 'Atenção, isto apagará também todos os resultados. Você quer realmente excluir este formulário?',
	'formulaires_tous' => 'Todos os formulários',

	// H
	'heures_minutes_secondes' => '@h@h @m@min @s@s',

	// I
	'id_formulaires_reponse' => 'Identificador da resposta',
	'identification_par_cookie' => 'Por cookie',
	'identification_par_id_auteur' => 'Por identificação (id_auteur) de usuário autenticado',
	'importer_formulaire' => 'Importar um formulário',
	'importer_formulaire_fichier_label' => 'Arquivo a importar',
	'importer_formulaire_format_label' => 'Formato do arquivo',
	'info_1_formulaire' => '1 formulário',
	'info_1_reponse' => '1 resposta',
	'info_aucun_formulaire' => 'Nenhum formulário',
	'info_aucune_reponse' => 'Nenhuma resposta',
	'info_formulaire_refuse' => 'Arquivado',
	'info_formulaire_utilise_par' => 'Formulário utilizado por:',
	'info_nb_formulaires' => '@nb@ formulários',
	'info_nb_reponses' => '@nb@ respostas',
	'info_reponse_proposee' => 'A moderar',
	'info_reponse_proposees' => 'A moderar',
	'info_reponse_publiee' => 'Validada',
	'info_reponse_publiees' => 'Validadas',
	'info_reponse_refusee' => 'Recusada',
	'info_reponse_refusees' => 'Recusadas',
	'info_reponse_supprimee' => 'Na lixeira',
	'info_reponse_supprimees' => 'Na lixeira',
	'info_reponse_toutes' => 'Todas',
	'info_utilise_1_formulaire' => 'Formulário utilizado:',
	'info_utilise_nb_formulaires' => 'Formulários utilizados:',

	// J
	'jours_heures_minutes_secondes' => '@j@d @h@h @m@min @s@s',

	// L
	'lien_expire' => 'Link vencendo em @delai@',
	'liens_ajouter' => 'Incluir um formulário',
	'liens_ajouter_lien' => 'Incluir este formulário',
	'liens_creer_associer' => 'Criar e vincular um formulário',
	'liens_retirer_lien_formulaire' => 'Aposentar este formulário',
	'liens_retirer_tous_liens_formulaires' => 'Aposentar todos os formulários',

	// M
	'minutes_secondes' => '@m@min @s@s',
	'modele_label_formulaire_formidable' => 'Qual formulário?',
	'modele_nom_formulaire' => 'um formulário',

	// N
	'noisette_label_afficher_titre_formulaire' => 'Exibir título do formulário?',
	'noisette_label_identifiant' => 'Formulário a exibir:',
	'noisette_nom_noisette_formulaire' => 'Formulário',

	// P
	'pas_analyse_fichiers' => 'Formidable não propõe (ainda) a análise dos arquivos enviados',

	// R
	'reponse_aucune' => 'Nenhuma resposta',
	'reponse_intro' => '@auteur@ respondeu ao formulário @formulaire@',
	'reponse_maj' => 'Última alteração',
	'reponse_numero' => 'Resposta número:',
	'reponse_statut' => 'Esta resposta está:',
	'reponse_supprimer' => 'Excluir esta resposta',
	'reponse_supprimer_confirmation' => 'Você quer realmente excluir esta resposta?',
	'reponse_une' => '1 resposta',
	'reponses_analyse' => 'Análise das respostas',
	'reponses_anonyme' => 'Anônimo',
	'reponses_auteur' => 'Usuário',
	'reponses_exporter' => 'Exportar as respostas',
	'reponses_exporter_format_csv' => 'Arquivo .CSV',
	'reponses_exporter_format_xls' => 'Excel .XLS',
	'reponses_exporter_statut_publie' => 'Publicados',
	'reponses_exporter_statut_tout' => 'Todos',
	'reponses_exporter_telecharger' => 'Transferir',
	'reponses_ip' => 'Endereço IP',
	'reponses_liste' => 'Lista de respostas',
	'reponses_liste_prop' => 'Respostas propostas para aprovação',
	'reponses_liste_publie' => 'Todas as respostas validadas',
	'reponses_nb' => '@nb@ respostas',
	'reponses_supprimer' => 'Excluir todas as respostas',
	'reponses_supprimer_confirmation' => 'Você quer realmente excluir todas as respostas a este formulário?',
	'reponses_voir_detail' => 'Ver a resposta',
	'retour_aucun_traitement' => 'A sua resposta foi enviada corretamente, mas nenhum processamento foi definido para este formulário.',

	// S
	'sans_reponses' => 'Sem resposta',
	'secondes' => '@s@s',

	// T
	'texte_statut_poubelle' => 'na lixeira',
	'texte_statut_propose_evaluation' => 'proposto',
	'texte_statut_publie' => 'validado',
	'texte_statut_refuse' => 'arquivado',
	'texte_statut_refusee' => 'recusado',
	'titre_cadre_raccourcis' => 'Atalhos',
	'titre_formulaires_archives' => 'Arquivos',
	'titre_formulaires_poubelle' => 'na lixeira',
	'titre_reponses' => 'Respostas',
	'traitements_actives' => 'Tratamentos ativados',
	'traitements_aide_memoire' => 'Atalhos:',
	'traitements_avertissement_creation' => 'As alterações nos campos do formulário foram gravadas corretamente. Defina agora quais tratamentos serão efetuados na submissão do formulário.',
	'traitements_avertissement_modification' => 'As alterações nos campos do formulário foram gravadas corretamente. <strong>Alguns tratamentos podem ter que ser reconfigurados consequentemente.</strong>',
	'traitements_champ_aucun' => 'Nenhum',
	'traiter_email_description' => 'Postar o resultado do formulário por e-mail para uma lista de destinatários.',
	'traiter_email_horodatage' => 'Formulário "@formulaire@" postado em @date@ às @heure@.',
	'traiter_email_message_erreur' => 'Aconteceu um erro ao enviar o e-mail.',
	'traiter_email_message_ok' => 'Sua mensagem foi enviada corretamente por e-mail.',
	'traiter_email_option_activer_accuse_label_case' => 'Enviar também um e-mail para o remetente com uma mensagem de confirmação.',
	'traiter_email_option_activer_ip_label_case' => 'Enviar o endereço IP do remetente aos destinatários.',
	'traiter_email_option_courriel_envoyeur_accuse_explication' => 'Indique o e-mail utilizado para enviar o aviso de recebimento. Por padrão, o destinatário será o remetente.', # MODIF
	'traiter_email_option_courriel_envoyeur_accuse_label' => 'E-mail de aviso de recebimento', # MODIF
	'traiter_email_option_destinataires_champ_form_explication' => 'Se um dos campos é um endereço de email ao qual você queira enviar o formulário, selecione o campo.',
	'traiter_email_option_destinataires_champ_form_label' => 'Destinatário presente em um dos campos dos formulários',
	'traiter_email_option_destinataires_explication' => 'Escolha o campo que corresponderá aos destinatários da mensagem.', # MODIF
	'traiter_email_option_destinataires_label' => 'Destinatários',
	'traiter_email_option_destinataires_plus_explication' => 'Uma lista de e-mails separados por vírgulas.',
	'traiter_email_option_destinataires_plus_label' => 'Destinatários suplementares',
	'traiter_email_option_destinataires_selon_champ_explication' => 'Permite indicar um ou mais destinatários em função do valor de um campo. Indique o campo, o seu valor, e o(s) respectivo(s) endereço(s) de e-mail (separados por uma vírgula), de acordo com o formato a seguir: "@selection_1@/choix1 : mail@exemplo.tld". Você pode indicar diversos testes, um por linha.', # MODIF
	'traiter_email_option_destinataires_selon_champ_label' => 'Destinatários em função de um campo',
	'traiter_email_option_envoyeur_courriel_explication' => 'Escolha o campo que conterá o e-mail do remetente.',
	'traiter_email_option_envoyeur_courriel_label' => 'E-mail do remetente',
	'traiter_email_option_envoyeur_nom_explication' => 'Construa o nome com ajuda dos @raccourcis@ (ver atalhos). Se for deixado em branco, será o nome do site.',
	'traiter_email_option_envoyeur_nom_label' => 'Nome do remetente',
	'traiter_email_option_exclure_champs_email_explication' => 'Se desejar que certos campos não sejam exibidos nas mensagens enviadas (por exemplo, campos escondidos), basta declará-los aqui, separados por vírgula.',
	'traiter_email_option_exclure_champs_email_label' => 'Campos a excluir do conteúdo da mensagem',
	'traiter_email_option_masquer_liens_label_case' => 'Ocultar os links de administração na mensagem.',
	'traiter_email_option_nom_envoyeur_accuse_explication' => 'Indique o nome do remetente utilizado para enviar o aviso de recebimento. Por padrão, o destinatário será o remetente.', # MODIF
	'traiter_email_option_nom_envoyeur_accuse_label' => 'Nome do remetente do aviso de recebimento',
	'traiter_email_option_pj_explication' => 'Se os documentos postados pesarem menos de _FORMIDABLE_TAILLE_MAX_FICHIERS_EMAIL Mio (constante modificável pelo webmaster).',
	'traiter_email_option_pj_label' => 'Anexar os arquivos na mensagem',
	'traiter_email_option_sujet_accuse_label' => 'Assunto do aviso de recebimento',
	'traiter_email_option_sujet_explication' => 'Construa o assunto com ajuda de @raccourcis@. Se for deixando em branco, o assunto será construído automaticamente.',
	'traiter_email_option_sujet_label' => 'Assunto da mensagem', # MODIF
	'traiter_email_option_vrai_envoyeur_explication' => 'Alguns servidores SMTP não permitem a utilização de um e-mail arbitrário para o campo "From". Por isso, Formidable insere por padrão o e-mail do remetente no campo "Reply-To". Marce esta opção para inserir o e-mail no campo "From".', # MODIF
	'traiter_email_option_vrai_envoyeur_label' => 'Inserir o e-mail do remetente no campo "From"',
	'traiter_email_page' => '<a href="@url@">A partir desta página</a>.',
	'traiter_email_sujet' => '@nom@  enviou uma mensagem',
	'traiter_email_sujet_accuse' => 'Obrigado pela sua resposta.',
	'traiter_email_sujet_courriel_label' => 'Assunto da mensagem', # MODIF
	'traiter_email_titre' => 'Enviar por e-mail',
	'traiter_email_url_enregistrement' => 'Você pode gerenciar as respostas <a href="@url@">nesta página</a>.',
	'traiter_email_url_enregistrement_precis' => 'Você pode visualizar estar resposta <a href="@url@">nesta página</a>.',
	'traiter_enregistrement_description' => 'Gravar os resultados do formulário na base de dados',
	'traiter_enregistrement_erreur_base' => 'Aconteceu um erro técnico durante a gravação na base de dados',
	'traiter_enregistrement_erreur_deja_repondu' => 'Você já respondeu a este formulário.',
	'traiter_enregistrement_erreur_edition_reponse_inexistante' => 'A resposta a ser editada não foi encontrada.',
	'traiter_enregistrement_message_ok' => 'Obrigado. Suas respostas foram registradas corretamente',
	'traiter_enregistrement_option_anonymiser_explication' => 'Resultados anônimos (não guardar traços de identificação das pessoas que responderam).', # MODIF
	'traiter_enregistrement_option_anonymiser_label' => 'Tornar o formulário anônimo',
	'traiter_enregistrement_option_anonymiser_variable_explication' => 'Que variável do sistema usar para calcular um valor único para cada autor sem revelar sua identidade.', # MODIF
	'traiter_enregistrement_option_anonymiser_variable_label' => 'Variável a usar para tornar o formulário anônimo', # MODIF
	'traiter_enregistrement_option_auteur' => 'Utilizar autores para os formulários',
	'traiter_enregistrement_option_auteur_explication' => 'Atribuir um ou vários autores a um formulário. Se esta opção estiver ativada, somente os autores do formulário podem acessar esses dados.',
	'traiter_enregistrement_option_effacement_delai_label' => 'Número de dias antes de apagar',
	'traiter_enregistrement_option_effacement_label' => 'Excluir regularmente os resultados mais antigos',
	'traiter_enregistrement_option_identification_explication' => 'Se as respostas são alteráveis, que procedimento usar preferencialmente para identificar a resposta a ser alterada?', # MODIF
	'traiter_enregistrement_option_identification_label' => 'Identificação',
	'traiter_enregistrement_option_ip_label' => 'Gravar os IPs (mascarados após um intervalo de segurança)',
	'traiter_enregistrement_option_moderation_label' => 'Moderação',
	'traiter_enregistrement_option_modifiable_explication' => 'Alterável: os visitantes podem modificar as suas respostas posteriormente.',
	'traiter_enregistrement_option_modifiable_label' => 'Respostas alteráveis',
	'traiter_enregistrement_option_multiple_explication' => 'Múltiplas: Uma mesma pessoa pode responder várias vezes.',
	'traiter_enregistrement_option_multiple_label' => 'Respostas múltiplas',
	'traiter_enregistrement_titre' => 'Gravar os resultados',

	// V
	'voir_exporter' => 'Exportar o formulário',
	'voir_numero' => 'Formulário número:',
	'voir_reponses' => 'Ver as respostas',
	'voir_traitements' => 'Tratamentos'
);
