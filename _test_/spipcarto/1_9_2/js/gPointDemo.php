<?php
/*------------------------------------------------------------------------------
** File:		gPointDemo.php
** Description:	Simple demo & documentation of gPoint class capabilities
** Version:		1.1
** Author:		Brenor Brophy
** Email:		brenor dot brophy at gmail dot com
** Homepage:	www.brenorbrophy.com 
**------------------------------------------------------------------------------
** COPYRIGHT (c) 2005, 2006 BRENOR BROPHY
**
** The source code included in this package is free software; you can
** redistribute it and/or modify it under the terms of the GNU General Public
** License as published by the Free Software Foundation. This license can be
** read at:
**
** http://www.opensource.org/licenses/gpl-license.php
**
** This program is distributed in the hope that it will be useful, but WITHOUT 
** ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
** FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. 
**------------------------------------------------------------------------------
**
** A gPoint object is a point on the Earth's surface. Its location is defined
** by a Longitude and a Latitude coordinate. These coordinates define a point
** on the surface of a sphere. However, computer screens like paper are flat
** surfaces and so we face a problem if we wish to represent data whose location
** is defined by Lat/Longs onto a such a surface. For example, you have an
** array of Lat/Long points that you want to plot on an image of a map. So how
** do you calculate the X/Y pixel on the image to plot your point? What you
** need is a transformation from a Lat/Long coordinate to an X/Y coordinate.
** This is called a map projection. There are many different types of projection.
** This class provides functions for working with two of the most useful; 
** Universal Transverse Mercator (UTM) and Lambert Conformal Conic. The class
** also supports a varient of UTM, that I call Local Transverse Mercator. It
** is very useful when you just need to plot a few points on an arbitary image
** that covers a modest amount of the Earth (10x10 degrees) and you don't have
** to deal with UTM zones.
**
** At a high level converting a Long/Lat coordinate in degrees thru a
** projection will return an Easting/Northing coordinate in meters. That is
** meters measured on the 'flat' ground that you can convert to pixels and
** plot on an image. Broadly speaking Transverse Mercator (and UTM) is useful
** for modest sized areas of about 10x10degrees or less. Lambert is useful for
** large areas in the mid latitudes (Like the whole USA or Europe for example).
** Neither projection works well for areas near the poles.
**
** The key methods provided by the class are:
**
** setLongLat					Set the Long/Lat of the point
** Long, Lat					Get the Long/Lat of the point
** setUTM						Set UTM coordinates of the point
** E,N,Z						Get UTM coordinates
** configLambertProjection		Sets up a bunch of required parameters
** setLambert					Set Lambert coordinates
** lccE, lccN					Get Lambert coordinates
** setXY						Set X/Y - can be used for pixel coordinates
** Xp, Yp						Gets the X/Y coordinates
**
** convertLLtoTM		Convert Long/Lat to Universal/Local Transverse Mercator
** convertTMtoLL		Convert Universal/Local Transverse Mercator to Long/Lat
** convertLLtoLCC		Convert Long/Lat to Lambert Conformal Conic
** convertLCCtoLL		Convert Lambert Conformal Conic to Long/Lat
**
** distanceFrom			Calculates Great Circle distance from gPoint to a Lat/Long
** distanceFromTM		Same function using Pythagoras's theorm and TM coordinates
** gRef					Geo-Reference converts TM coordinates to a pixel X/Y given
**						The Lat/Long of the center pixel and the scale (meters/pixel)
**						of the image.
**
** printLatLong
** printUTM
** printLambert
*/
//error_reporting ( E_ALL ); // For debug

require ('gPoint.php');

//
// The example shows how a single point on the earth can be converted between
// Latitude/Longitude coordinates and the three map projections supported by
// the gPoint class.
//
	$myHome =& new gPoint();	// Create an empty point
//
//  We start by setting the points Longitude & Latitude. 
//
	$myHome->setLongLat(-121.85831, 37.42104);	// I live in sunny California :-)
	echo "I live at: "; $myHome->printLatLong(); echo "<br>";
//
// Calculate the coordinates of the point in a UTM projection 
//
	$myHome->convertLLtoTM();
	echo "Which in a UTM projection is: "; $myHome->printUTM(); echo "<br>";
//
// Set the UTM coordinates of the point to check the reverse conversion
//
	$myHome->setUTM( 601034, 4142188, "10S");	// Easting/Northing from a GPS
	echo "My GPS says it is this: "; $myHome->printUTM(); echo "<br>";
//
// Calculate the Longitude Latitude of the point
//
	$myHome->convertTMtoLL();
	echo "Which converts back to: "; $myHome->printLatLong(); echo "<br>";
//
// Now lets try the same conversion, only this time we will user a "Local"
// Transverse Mercator projection. -122 degrees longitude is close to the
// area of interest so lets use that as our Longitude of Origin
//
	$longOrigin = -122;
	$myHome->convertLLtoTM($longOrigin);
	echo "In a Local TM projection centered at longitude $longOrigin it is: "; $myHome->printUTM(); echo "<br>";
//
// Now check the reverse conversion
//
	$myHome->convertTMtoLL($longOrigin);
	echo "Converting back gives us: "; $myHome->printLatLong(); echo "<br>";
//
// Lets setup a Lambert Conformal Conic projection for Northern California
//
// falseEasting = 20000000
// falseNorthing = 0
// Longitude of origin = -122
// First Standard Parallel = 33 20'
// Second Standard Parallel = 38 40'
//
	$myHome->configLambertProjection(2000000, 0, -122, 35.5, 33.33333, 38.6666);
	$myHome->convertLLtoLCC();
	echo "In a Lambert Projection: "; $myHome->printLambert(); echo "<br>";
//
// And convert back to Longitude / Latitude
//
	$myHome->convertLCCtoLL();
	echo "And is still: "; $myHome->printLatLong(); echo "<br>";
?>
