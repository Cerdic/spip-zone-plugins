<?php

die('ce brouillon code ne marche pas encore ; a revoir');

// Open an inotify instance
$fd = inotify_init();

// Watch __FILE__ for metadata changes (e.g. mtime)
$watch_descriptor = inotify_add_watch($fd, __FILE__, IN_ATTRIB);

while(true) {
	echo time()."\n";
	$queue_len = inotify_queue_len($fd);

	echo "queue_len = $queue_len\n";
	sleep(3);
}
