<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Charger la classe geoPHP qui sert.
if (!class_exists('geoPHP')) {
	find_in_path(_DIR_LIB_GEOPHP . 'geoPHP.inc', '', true);
}

/**
 * Simplifier une géométrie de geoPHP
 *
 * Pratique lorsqu'on n'a pas GEOS d’installé ni mysql 5.7…
 *
 * @note
 *     Code emprunté à un portage PHP de simplify-js,
 *     et adapté pour les classes de geoPHP.
 *
 * Simplify path by removing extra points with given tolerance
 * Port of simplify.js algorithm
 * http://github.com/andreychumak/simplify-php
 *
 * (c) 2013, Vladimir Agafonkin
 * Simplify.js, a high-performance JS polyline simplification library
 * http://mourner.github.io/simplify-js
 */
class geoPHP_Simplify {
	/**
	 * Simplifier une Geometry de geoPHP en réduisant le nombre de points
	 *
	 * L’intérêt est de pouvoir réduire le nombre de points des LineString ou MultiPoint
	 * contenus dans cette géométrie.
	 *
	 * @example
	 *     ```
	 *     $geometry = geoPHP::load($geojson, 'json');
	 *     $geometry = geoPHP_Simplify::run($geometry, 0.05);
	 *     $json = $geoPHP->out($geometry, 'json');
	 *     ```
	 *
	 * @param Collection $geometry
	 * @param int|float|null $tolerance
	 * @return geoPHP
	 */
	public static function geometrySimplify($geometry, $tolerance = 1, $highestQuality = false) {
		if ($geometry instanceof Collection) {
			if (
				$geometry instanceof LineString
				or $geometry instanceof MultiPoint
			) {
				$geometry->components = geoPHP_Simplify::run($geometry->getComponents(), $tolerance, $highestQuality);
			} else {
				foreach ($geometry->getComponents() as $i => $component) {
					$geometry->components[$i] = geoPHP_Simplify::geometrySimplify($component, $tolerance, $highestQuality);
				}
			}
		}
		return $geometry;
	}

	/**
	 * Simplifie une liste de points donnée.
	 *
	 * @param Point[] $points
	 * @param float|int $tolerance
	 * @param bool $highestQuality
	 * @return array
	 */
	public static function run(array $points, $tolerance = 1, $highestQuality = false) {
		if (count($points) <= 1) return $points;

		$sqTolerance = $tolerance*$tolerance;

		$points = $highestQuality ? $points : self::simplifyRadialDist($points, $sqTolerance);
		$points = self::simplifyDouglasPeucker($points, $sqTolerance);

		return $points;
	}

	// basic distance-based simplification
	private static function simplifyRadialDist($points, $sqTolerance) {

		$prevPoint = $points[0];
		$newPoints = array($prevPoint);
		$point = null;

		for ($i = 1, $len = count($points); $i < $len; $i++) {
			$point = $points[$i];

			if (self::getSqDist($point, $prevPoint) > $sqTolerance) {
				$newPoints[] = $point;
				$prevPoint = $point;
			}
		}

		if ($prevPoint !== $point) $newPoints[] = $point;

		return $newPoints;
	}

	// square distance between 2 points
	private static function getSqDist($p1, $p2) {

		$dx = $p1->x() - $p2->x();
		$dy = $p1->y() - $p2->y();

		return $dx * $dx + $dy * $dy;
	}

	// simplification using optimized Douglas-Peucker algorithm with recursion elimination
	private static function simplifyDouglasPeucker($points, $sqTolerance) {

		$len = count($points);
		$markers = array_fill(0, $len-1, null);
		$first = 0;
		$last = $len - 1;
		$stack = array();
		$newPoints = array();
		$index = null;

		$markers[$first] = $markers[$last] = 1;

		while ($last) {

			$maxSqDist = 0;

			for ($i = $first + 1; $i < $last; $i++) {
				$sqDist = self::getSqSegDist($points[$i], $points[$first], $points[$last]);

				if ($sqDist > $maxSqDist) {
					$index = $i;
					$maxSqDist = $sqDist;
				}
			}

			if ($maxSqDist > $sqTolerance) {
				$markers[$index] = 1;
				array_push($stack, $first, $index, $index, $last);
			}

			$last = array_pop($stack);
			$first = array_pop($stack);
		}

		//var_dump($markers, $points, $i);
		for ($i = 0; $i < $len; $i++) {
			if ($markers[$i]) $newPoints[] = $points[$i];
		}

		return $newPoints;
	}

	// square distance from a point to a segment
	private static function getSqSegDist($p, $p1, $p2) {
		$x = $p1->x();
		$y = $p->y();
		$dx = $p2->x() - $x;
		$dy = $p2->y() - $y;

		if ($dx !== 0 || $dy !== 0) {

			$t = (($p->x() - $x) * $dx + ($p->y() - $y) * $dy) / ($dx * $dx + $dy * $dy);

			if ($t > 1) {
				$x = $p2->x();
				$y = $p2->y();

			} else if ($t > 0) {
				$x += $dx * $t;
				$y += $dy * $t;
			}
		}

		$dx = $p->x() - $x;
		$dy = $p->y() - $y;

		return $dx * $dx + $dy * $dy;
	}
}