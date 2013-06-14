<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

  	<xsl:import href="spCommon.xsl"/>
  	<xsl:import href="spTitre.xsl"/>
  	<xsl:import href="spFilDAriane.xsl"/>

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
   	<xsl:template match="/">
 		<xsl:call-template name="getBarre10Themes"/>
		<xsl:call-template name="getFilDArianeOfDossiersaz"/>
		<xsl:call-template name="getTitre"/>
		<xsl:call-template name="affLetters"/>
		<xsl:apply-templates select="Publication/DossierTheme"/>
  	</xsl:template>
	
	<xsl:template name="affLetters">
		<div class="spLetters">
			<xsl:variable name="nb">
				<xsl:value-of select="count(//Publication/DossierLetter)"/>
			</xsl:variable>
			<xsl:for-each select="//Publication/DossierTheme">
				<div class="spLetter">
					<xsl:attribute name="style">
						<xsl:text>width:</xsl:text>
						<xsl:value-of select="100 div 5"/>
						<xsl:text>%</xsl:text>
					</xsl:attribute>
					<a>
						<xsl:attribute name="title">
							<xsl:text>Tous les dossiers appartenant au thème : </xsl:text>
							<xsl:value-of select="Titre"/>
						</xsl:attribute>						
						<xsl:attribute name="href">
							<xsl:text>#</xsl:text>
							<xsl:call-template name="createDossierAzId"/>
						</xsl:attribute>
						<xsl:value-of select="Titre"/>
					</a>
				</div>
			</xsl:for-each>
		</div>
	</xsl:template>
	
	<xsl:template match="DossierTheme">
		<div class="spDossierLetter">
			<xsl:attribute name="id">
				<xsl:call-template name="createDossierAzId"/>
			</xsl:attribute>
			<h2><xsl:value-of select="Titre"/></h2>
			<ul class="spDossierLetter">
				<xsl:for-each select="Dossier">
					<xsl:apply-templates select="."/>
				</xsl:for-each>
			</ul>
		</div>
  		<xsl:call-template name="ancreTop"/>
  		<xsl:call-template name="affiche_tag_xiti"/>
	</xsl:template>
	
	<xsl:template match="Dossier">
		<li class="spDossierLetter">
			<h3>
				<xsl:call-template name="getPublicationLink">
	   				<xsl:with-param name="href"><xsl:value-of select="@ID"/></xsl:with-param>
	   				<xsl:with-param name="title"><xsl:value-of select="Titre"/></xsl:with-param>
	   				<xsl:with-param name="text"><xsl:value-of select="Titre"/></xsl:with-param>
				</xsl:call-template>
			</h3>
		</li>
	</xsl:template>

</xsl:stylesheet>
