<?php
function faviconz_insert_head ($flux)
	{
	$path_favicon = find_in_path('favicon.ico');
	if ($path_favicon)
		{
		$flux .= "\n<!-- Insertion par FaviconZ -->\n";
		$flux .= '<link rel="icon" type="image/x-icon" href="';
		$flux .= $path_favicon . '" />';
		$flux .= "\n";
		$flux .= '<link rel="shortcut icon" type="image/x-icon" href="';
		$flux .= $path_favicon . '" />';
		$flux .= "\n<!-- Fin d'insertion par FaviconZ -->\n";
		}
	return $flux;
	}

