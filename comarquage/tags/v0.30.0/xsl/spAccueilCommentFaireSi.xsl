<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">
	
	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
	<!-- Noeud de type Accueil Coment faire si -->
	<xsl:template match="Publication" mode="Accueil-comment-faire-si">
		
	<!--  <xsl:call-template name="getBarre10Themes"/> -->
	
	<!--	<xsl:apply-templates select="FilDAriane"/> -->
		<xsl:call-template name="getTitre"/>
		
		<!-- <xsl:call-template name="createSommaireNoeud" mode="Noeud-dossier"/> -->
		<xsl:apply-templates select="Introduction"/>
		<div class="spAccueilCommentFaireSiMain">
			<xsl:for-each select="Fiche">
				<xsl:if test="((position() mod 2) = 1) and (position() > 1)">
					<br class="clearall"/>
				</xsl:if>
				<xsl:apply-templates select="." mode="Accueil-comment-faire-si"/>
			</xsl:for-each>
			<br class="clearall"/>
		</div>
		<xsl:call-template name="affActualite" mode="Publication"/>		
		<xsl:call-template name="affServiceEnLigne" mode="Publication"/>		
		<xsl:call-template name="affCentreDeContact" mode="Publication"/>		
	</xsl:template>
	
</xsl:stylesheet>
