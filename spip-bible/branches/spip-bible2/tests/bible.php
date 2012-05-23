<?php
/**
 * Test unitaire de la fonction bible
 * du fichier ../plugins/spip-bible/bible_fonctions.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-02-27 23:50
 */
    global $spip_lang;
    $spip_lang = 'fr';
	$test = 'bible';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/spip-bible/bible_fonctions.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('bible', essais_bible());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_bible(){
		$essais = array (
  0 => 
  array (
    0 => '<quote>
Au commencement, Dieu créa le ciel et la terre.Or la terre était vide et vague, les ténèbres couvraient l\'abîme, un vent de Dieu tournoyait sur les eaux.Dieu dit : Que la lumière soit et la lumière fut.Dieu vit que la lumière était bonne, et Dieu sépara la lumière et les ténèbres.Dieu appela la lumière jour et les ténèbres nuit . Il y eut un soir et il y eut un matin : premier jour.Dieu dit : Qu\'il y ait un firmament au milieu des eaux et qu\'il sépare les eaux d\'avec les eaux et il en fut ainsi.Dieu fit le firmament, qui sépara les eaux qui sont sous le firmament d\'avec les eaux qui sont au-dessus du firmament,et Dieu appela le firmament ciel . Il y eut un soir et il y eut un matin : deuxième jour.Dieu dit : Que les eaux qui sont sous le ciel s\'amassent en une seule masse et qu\'apparaisse le continent et il en fut ainsi.Dieu appela le continent terre et la masse des eaux mers, et Dieu vit que cela était bon.Dieu dit : Que la terre verdisse de verdure : des herbes portant semence et des arbres fruitiers donnant sur la terre selon leur espèce des fruits contenant leur semence et il en fut ainsi.La terre produisit de la verdure : des herbes portant semence selon leur espèce, des arbres donnant selon leur espèce des fruits contenant leur semence, et Dieu vit que cela était bon.Il y eut un soir et il y eut un matin : troisième jour.Dieu dit : Qu\'il y ait des luminaires au firmament du ciel pour séparer le jour et la nuit; qu\'ils servent de signes, tant pour les fêtes que pour les jours et les années;qu\'ils soient des luminaires au firmament du ciel pour éclairer la terre et il en fut ainsi.Dieu fit les deux luminaires majeurs : le grand luminaire comme puissance du jour et le petit luminaire comme puissance de la nuit, et les étoiles.Dieu les plaça au firmament du ciel pour éclairer la terre,pour commander au jour et à la nuit, pour séparer la lumière et les ténèbres, et Dieu vit que cela était bon.Il y eut un soir et il y eut un matin : quatrième jour.Dieu dit : Que les eaux grouillent d\'un grouillement d\'êtres vivants et que des oiseaux volent au-dessus de la terre contre le firmament du ciel et il en fut ainsi.Dieu créa les grands serpents de mer et tous les êtres vivants qui glissent et qui grouillent dans les eaux selon leur espèce, et toute la gent ailée selon son espèce, et Dieu vit que cela était bon.Dieu les bénit et dit : Soyez féconds, multipliez, emplissez l\'eau des mers, et que les oiseaux multiplient sur la terre.Il y eut un soir et il y eut un matin : cinquième jour.Dieu dit : Que la terre produise des êtres vivants selon leur espèce : bestiaux, bestioles, bêtes sauvages selon leur espèce et il en fut ainsi.Dieu fit les bêtes sauvages selon leur espèce, les bestiaux selon leur espèce et toutes les bestioles du sol selon leur espèce, et Dieu vit que cela était bon.Dieu dit : Faisons l\'homme à notre image, comme notre ressemblance, et qu\'ils dominent sur les poissons de la mer, les oiseaux du ciel, les bestiaux, toutes les bêtes sauvages et toutes les bestioles qui rampent sur la terre.Dieu créa l\'homme à son image, à l\'image de Dieu il le créa, homme et femme il les créa.Dieu les bénit et leur dit : Soyez féconds, multipliez, emplissez la terre et soumettez-la; dominez sur les poissons de la mer, les oiseaux du ciel et tous les animaux qui rampent sur la terre.Dieu dit : Je vous donne toutes les herbes portant semence, qui sont sur toute la surface de la terre, et tous les arbres qui ont des fruits portant semence : ce sera votre nourriture.A toutes les bêtes sauvages, à tous les oiseaux du ciel, à tout ce qui rampe sur la terre et qui est animé de vie, je donne pour nourriture toute la verdure des plantes et il en fut ainsi.Dieu vit tout ce qu\'il avait fait : cela était très bon. Il y eut un soir et il y eut un matin : sixième jour.</quote>',
    1 => 'Gn1',
    2 => 'jerusalem',
  ),
  1 => 
  array (
    0 => '<quote>
 Vision d\'Abdias. Voici ce que dit le Seigneur DIEU à Edom&nbsp;: Nous avons appris une nouvelle
de la part du SEIGNEUR, 
et un émissaire a été envoyé parmi les nations&nbsp;: 
Levez-vous&nbsp;! 
Levons-nous contre elle&nbsp;! 
Au combat&nbsp;! </quote>',
    1 => 'Ab1',
    2 => 'NBS',
  ),
  2 => 
  array (
    0 => '<div lang="hbo" dir="rtl"><quote>

עַד־אָ֧נָה יְהוָ֛ה שִׁוַּ֖עְתִּי וְלֹ֣א תִשְׁמָ֑ע אֶזְעַ֥ק אֵלֶ֛יךָ חָמָ֖ס וְלֹ֥א תֹושִֽׁיעַ׃
לָ֣מָּה תַרְאֵ֤נִי אָ֨וֶן֙ וְעָמָ֣ל תַּבִּ֔יט וְשֹׁ֥ד וְחָמָ֖ס לְנֶגְדִּ֑י וַיְהִ֧י רִ֦יב וּמָדֹ֖ון יִשָּֽׂא׃
עַל־כֵּן֙ תָּפ֣וּג תֹּורָ֔ה וְלֹֽא־יֵצֵ֥א לָנֶ֖צַח מִשְׁפָּ֑ט כִּ֤י רָשָׁע֙ מַכְתִּ֣יר אֶת־הַצַּדִּ֔יק עַל־כֵּ֛ן יֵצֵ֥א מִשְׁפָּ֖ט מְעֻקָּֽל׃
רְא֤וּ בַגֹּויִם֙ וְֽהַבִּ֔יטוּ וְהִֽתַּמְּה֖וּ תְּמָ֑הוּ כִּי־פֹ֨עַל֙ פֹּעֵ֣ל בִּֽימֵיכֶ֔ם לֹ֥א תַאֲמִ֖ינוּ כִּ֥י יְסֻפָּֽר׃
כִּֽי־הִנְנִ֤י מֵקִים֙ אֶת־הַכַּשְׂדִּ֔ים הַגֹּ֖וי הַמַּ֣ר וְהַנִּמְהָ֑ר הַֽהֹולֵךְ֙ לְמֶרְחֲבֵי־אֶ֔רֶץ לָרֶ֖שֶׁת מִשְׁכָּנֹ֥ות לֹּא־לֹֽו׃
אָיֹ֥ם וְנֹורָ֖א ה֑וּא מִמֶּ֕נּוּ מִשְׁפָּטֹ֥ו וּשְׂאֵתֹ֖ו יֵצֵֽא׃
וְקַלּ֨וּ מִנְּמֵרִ֜ים סוּסָ֗יו וְחַדּוּ֙ מִזְּאֵ֣בֵי עֶ֔רֶב וּפָ֖שׁוּ פָּֽרָשָׁ֑יו וּפָֽרָשָׁיו֙ מֵרָחֹ֣וק יָבֹ֔אוּ יָעֻ֕פוּ כְּנֶ֖שֶׁר חָ֥שׁ לֶאֱכֹֽול׃</quote></div>',
    1 => 'Ha1,2-8',
    2 => 'BHS',
  ),
);
		return $essais;
	}
















?>