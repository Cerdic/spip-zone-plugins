<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
 	<xsl:template match="SousDossier" mode="Noeud-dossier">
		<xsl:if test="count(Fiche) > 0">
			<div class="spNoeudDossierDossier">
				<xsl:attribute name="id"><xsl:call-template name="createSousDossierId"/></xsl:attribute>
				<xsl:apply-templates select="Titre" mode="Noeud-dossier"/>
				<ul class="spNoeudDossierDossierFiche">
					<xsl:apply-templates select="Fiche" mode="Noeud-dossier"/>
				</ul>
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="Dossier" mode="Theme">
		<xsl:variable name="title">
			<xsl:value-of select="/Publication/dc:title"/>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:value-of select="../Titre"/>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:value-of select="text()"/>
		</xsl:variable>
		<li class="spThemeSousThemeDossier">
			
			<a>
				<xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@ID"/><xsl:text>.xml</xsl:text>&#x26;xsl=spNoeud.xsl</xsl:attribute>
				<xsl:value-of select="text()"/>
			</a>	
    			<!-- <xsl:call-template name="getPublicationLink">
    				<xsl:with-param name="href"><xsl:value-of select="@ID"/></xsl:with-param>
    				<xsl:with-param name="title"><xsl:value-of select="$title"/></xsl:with-param>
    				<xsl:with-param name="text"><xsl:value-of select="text()"/></xsl:with-param>
				</xsl:call-template> -->
			
		</li>
	</xsl:template>

</xsl:stylesheet>
