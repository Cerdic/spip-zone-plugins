<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html"  encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
	<!-- Noeud de type Thème -->
	<xsl:template match="Publication" mode="Theme">
		<xsl:call-template name="getBarre10Themes"/>

		


   		<div class="spFilDAriane">
			<xsl:variable name="title">
				<xsl:text>Vos droits et vos démarches en tant que particuliers : Liste des thèmes</xsl:text>
			</xsl:variable>
 			<!-- 
 			<div class="entiteImageFloatRight">
				<xsl:call-template name="imageOfATheme">
					<xsl:with-param name="id" select="@ID"/>
				</xsl:call-template>
			</div>
			-->
			<a>
				<xsl:attribute name="href"><xsl:value-of select="$REFERER"/></xsl:attribute>
				<xsl:attribute name="title"><xsl:value-of select="$title"/></xsl:attribute>
				<xsl:text>Accueil</xsl:text>
			</a>
			<!-- 
			<xsl:call-template name="getPublicationLink">
   				<xsl:with-param name="href"><xsl:text>Theme</xsl:text></xsl:with-param>
   				<xsl:with-param name="title"><xsl:value-of select="$title"/></xsl:with-param>
   				<xsl:with-param name="text"><xsl:text>Accueil</xsl:text></xsl:with-param>
			</xsl:call-template>
			-->
   			<xsl:value-of select="$sepFilDAriane"/><xsl:value-of select="dc:title"/>
	   	</div>
	   	
	   	<xsl:call-template name="getTitre"/>
	   	
	   		
 		
		<div class="spPublicationMenuGaucheForTheme">
			  <xsl:call-template name="createSommaireTheme" mode="Theme"/> 
			  <xsl:call-template name="affCommentFaireSi" mode="Publication"/> 
		</div>
		<div class="spPublicationMenuDroiteForTheme">
			<xsl:call-template name="affActualite" mode="Publication"/>
			<xsl:call-template name="affServiceEnLigne" mode="Publication"/>		
		</div>
		
		
		<div class="clearall">
			<xsl:choose>
				<xsl:when test="count(SousTheme) > 0">
					<div class="spThemeSousThemeMain">
						<xsl:apply-templates select="SousTheme" mode="Theme"/> 
					</div>
				</xsl:when>
				<xsl:otherwise>
					<div class="spSousThemeDossier">
						<xsl:apply-templates select="Dossier" mode="Sous-theme"/>
					</div>
				</xsl:otherwise>
			</xsl:choose>
		</div>
 		<div class="clearall">
 	
	
			<xsl:call-template name="affServiceEnLigne" mode="Publication"/>
		
			<xsl:call-template name="affVoirAussi" mode="Publication">		
				<xsl:with-param name="titre"><xsl:text>thèmes</xsl:text></xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="affQuestionReponse" mode="Publication"/>		
			<xsl:call-template name="affCentreDeContact" mode="Publication"/>		
			<xsl:call-template name="affSiteInternetPublic" mode="Publication"/>
		</div>	
	</xsl:template>
	
</xsl:stylesheet>
