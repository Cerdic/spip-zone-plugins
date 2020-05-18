<?php
/*
 * Plugin Scss
 * Distribue sous licence MIT
 *
 */

include_spip('lib/scssphp/scss.inc');

/**
 * Class SPIPScssPhpCompiler
 * Ajoute une fonction find-in-path() que l'on peut utiliser pour les fichers medias
 *  - ils sont retrouvés dans le path SPIP
 *  - le chemin est mis relatif au fichier SCSS principal compilé
 *  - les medias deviennent simplement surchargeable
 */
class SPIPScssPhpCompiler extends ScssPhp\ScssPhp\Compiler {

	protected static $libFindInPath = ['filepath'];
	protected function libFindInPath($args){

		$quote = '';
		$str = $this->reduce($args[0]);
		if ($str[0]===ScssPhp\ScssPhp\Type::T_STRING){
			$quote = $str[1];
			$str[1] = '';
		}

		$filepath = $this->compileValue($str);

		if ($filepath) {
			$filepath = find_in_path($filepath);
			if ($filepath) {
				$filepath = timestamp($filepath);
			}
		}

		// rendre le chemin relatif au fichier scss principal compile
		if ($filepath and $this->sourceNames) {
			$sourceFile = reset($this->sourceNames);
			if ($sourceFile) {
				$sourceFile = explode('/', dirname($sourceFile));
				$relativePath = explode('/', $filepath);
				while (count($sourceFile)
					and reset($sourceFile) === reset($relativePath)) {
					array_shift($sourceFile);
					array_shift($relativePath);
				}
				while (count($sourceFile)) {
					array_unshift($relativePath, '..');
					array_shift($sourceFile);
				}
				$filepath = implode('/', $relativePath);
			}
		}

	  return [ScssPhp\ScssPhp\Type::T_STRING, $quote, [$filepath ? $filepath : '']];
	}
}