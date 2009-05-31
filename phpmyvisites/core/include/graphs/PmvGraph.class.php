<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: PmvGraph.class.php,v 1.7 2005/12/24 02:59:45 matthieu_ Exp $

class PmvGraph
{
	var $title;

	function PmvGraph( $width, $height )
	{	
		$this->width = $width;
		$this->height = $height;
		$this->graph = new Graph($width, $height);
		$this->fontPath = FONTS_PATH.Lang::getFontName();
		if(!is_file($this->fontPath))
		{
			$this->fontPath = FONTS_PATH . $GLOBALS['defaultFont'];
		}
		$this->font8 = new TTFFont( $this->fontPath, 8);
		$this->font9 = new TTFFont( $this->fontPath, 9);
		$this->font10 = new TTFFont( $this->fontPath, 10);
		$this->font16 = new TTFFont( $this->fontPath, 16);
	}
	
	function process( )
	{
		if(DEBUG)
		{
			$this->graph->setTiming(TRUE);
		}
		$this->graph->setAntiAliasing(TRUE);
		$this->graph->border->setColor( new Color ( 98, 123, 163 ));
		$this->graph->shadow->setSize(1);
		$this->graph->shadow->setPosition( SHADOW_RIGHT_BOTTOM );
		$this->graph->shadow->smooth(TRUE);
		$this->graph->shadow->setColor(new Color(83, 104, 138));
	}
	
	function setPmvTitle( &$o )
	{
		$o->title->set($this->title);
		$o->title->move(0, -6);
		$o->title->setFont($this->font16);
		$o->title->setColor(new Color(0, 0, 165, 10));

	}
	
	function setPmvPadding( &$o )
	{
		$o->setPadding(35, 15, 40, 25);
	}
	
	function setPmvBackgroundGradient( &$o )
	{
		$o->setBackgroundGradient(
			new LinearGradient(
				new Color(241, 241, 241),
				new Color(255, 255, 255),
				0
			)
		);
	}
	
	function setPmvBarGradient( &$o )
	{
		$o->setBarGradient(
			new LinearGradient(
				new Color(255, 165, 0),
				new Color(255, 207, 94),
				0
			)
		);
	}
	
	function setPmvLabelProperties( &$o, &$y )
	{
		$o->label->set( $y );
		$o->label->move(0, -5);
		$o->label->setFont(new TTFFont( $this->fontPath, 10));
		$o->label->setColor(new Color( 0, 0, 139));
		$o->label->setAngle(0);
		$o->label->setAlign(NULL, LABEL_TOP);
		$o->label->setPadding(3, 1, 0, 6);
	}
	
	function setPmvBarShadowProperties( &$o )
	{
		$o->barShadow->setSize(0);
		$o->barShadow->setPosition(SHADOW_RIGHT_TOP);
		$o->barShadow->setColor(new Color(180, 180, 180));
		$o->barShadow->smooth(TRUE);
	}

	function setPmvBarBorderProperties( &$o )
	{
		$o->barBorder->setColor(new Color(0, 0, 0));
	}
	
	function setPmvBarSize( &$o )
	{
		$o->setBarSize( 0.5 );
	}
	
	function setPmvGroupProperties( &$o )
	{
			

		$o->setSize(0.8, 1);
		$o->setCenter(0.41, 0.5);
		$o->setPadding(35, 26, 40, 27);
		$o->setSpace(2, 2);

		$o->grid->setType(LINE_DASHED);
		$o->grid->hideVertical(TRUE);
		$o->grid->setBackgroundColor(new White);

		$o->axis->left->setColor(new Blue);
		$o->axis->left->label->setFont(new Font2);

		$o->axis->bottom->label->setFont(new Font2);

		$o->legend->setPosition(1.22, 0.2);
		$o->legend->setTextFont($this->font9);
		$o->legend->setSpace(10);
		
	}
	function draw()
	{
		if(!PROFILING)
		{
			$this->graph->draw();
		}
	}
}

?>