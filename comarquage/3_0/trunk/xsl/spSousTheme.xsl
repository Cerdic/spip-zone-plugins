<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
	<xsl:template match="Publication" mode="Sous-theme">
		<xsl:call-template name="getBarre10Themes"/>
		<xsl:apply-templates select="FilDAriane"/>
		<xsl:call-template name="getTitre"/>
		
		
		<div class="spPublicationMenuGaucheForTheme">
			<xsl:call-template name="createSommaireTheme" mode="Theme"/>
			<xsl:call-template name="affCommentFaireSi" mode="Publication"/>
			<!--  <xsl:call-template name="affDossiersAZ"/> -->
		</div>	
		<div class="spPublicationMenuDroiteForTheme">
			<xsl:call-template name="affActualite" mode="Publication"/>
			<xsl:call-template name="affServiceEnLigne" mode="Publication"/>		
			<xsl:call-template name="affSiteInternetPublic" mode="Publication"/>
		</div>
		 
		
		<div class="spSousThemeDossier">
			<xsl:apply-templates select="Dossier" mode="Sous-theme"/>
		</div>
 		<div class="clearall">
			<xsl:call-template name="affVoirAussi" mode="Publication">
				<xsl:with-param name="titre"><xsl:text>dossiers</xsl:text></xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="affQuestionReponse" mode="Publication"/>		
			<xsl:call-template name="affCentreDeContact" mode="Publication"/>		
		</div>
	</xsl:template>

 	<xsl:template match="SousTheme" mode="Theme">
		<xsl:if test="count(Dossier) > 0">
			<div class="spThemeSousTheme">
				<xsl:attribute name="id">
					<xsl:call-template name="createThemeSousThemeId"/>
				</xsl:attribute>
				<!-- 
				<div class="entiteImageFloatLeft">
					<xsl:call-template name="imageOfATheme">
						<xsl:with-param name="id" select="//Publication/@ID"/>
					</xsl:call-template>
				</div>
				-->
				<xsl:apply-templates select="Titre" mode="Theme"/>
				<ul class="spThemeSousThemeDossier">
					<xsl:apply-templates select="Dossier" mode="Theme"/>
				</ul>
			</div>
		</xsl:if>
	</xsl:template>
	
</xsl:stylesheet>
