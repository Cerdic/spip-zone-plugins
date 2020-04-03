<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function rezosocios_liste() {
	$rezosocios = array(
		'facebook' => array(
			'nom' => 'Facebook',
			'url' => 'https://www.facebook.com/'
		),
		'twitter' => array(
			'nom' => 'Twitter',
			'url' => 'https://twitter.com/'
		),
		'twitter_hashtag' => array(
			'nom' => 'Twitter Hashtag',
			'url' => 'https://twitter.com/hashtag/'
		),
		'google+' => array(
			'nom' => 'Google+',
			'url' => 'https://plus.google.com/+'
		),
		'youtube' => array(
			'nom' => _T('rezosocios:youtube_user'),
			'url' => 'https://www.youtube.com/user/'
		),
		'youtube_channel' => array(
			'nom' => _T('rezosocios:youtube_channel'),
			'url' => 'https://www.youtube.com/channel/'
		),
		'dailymotion' => array(
			'nom' => 'Dailymotion',
			'url' => 'http://www.dailymotion.com/'
		),
		'vimeo' => array(
			'nom' => 'Vimeo',
			'url' => 'https://vimeo.com/'
		),
		'instagram' => array(
			'nom' => 'Instagram',
			'url' => 'https://www.instagram.com/'
		),
		'pinterest' => array(
			'nom' => 'Pinterest',
			'url' => 'https://www.pinterest.com/'
		),
		'flickr' => array(
			'nom' => 'Flickr',
			'url' => 'https://www.flickr.com/photos/'
		),
		'storify' => array(
			'nom' => 'Storify',
			'url' => 'https://storify.com/'
		),
		'tumblr' => array(
			'nom' => 'tumblr.',
			'url' => 'https://www.tumblr.com/'
		),
		'linkedin' => array(
			'nom' => 'Linkedin',
			'url' => 'https://www.linkedin.com/profile/view?id='
		),
		'linkedin_company' => array(
			'nom' => 'Linkedin (company)',
			'url' => 'https://www.linkedin.com/company/'
		),
		'weibo' => array(
			'nom' => 'Weibo',
			'url' => 'http://www.weibo.com/'
		),
		'vk' => array(
			'nom' => 'VK',
			'url' => 'http://vk.com/'
		)
	);
	$rezosocios = pipeline('rezosocios_liste', $rezosocios);
	return $rezosocios;
}
