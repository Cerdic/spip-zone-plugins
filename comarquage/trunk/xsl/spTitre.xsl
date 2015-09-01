<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="ISO-8859-15" cdata-section-elements="script" indent="yes"/> 
  	
	<xsl:template name="getTitre">
		<div class="spCenter"><h1><xsl:value-of select="//Publication/dc:title"/></h1></div>
	</xsl:template>
	
	<xsl:template name="getTitreOfRessource">
		<div class="spCenter">
			<h1>
				<xsl:value-of select="//ServiceComplementaire/dc:title"/>
				<xsl:if test="//ServiceComplementaire/NumeroCerfa">
					<xsl:text> - Cerfa </xsl:text>
					<xsl:value-of select="//ServiceComplementaire/NumeroCerfa"/>
				</xsl:if>
			</h1>
			<xsl:value-of select="//ServiceComplementaire/dc:type"/>
			<xsl:if test="//ServiceComplementaire/NumeroCerfa">
				<xsl:text> - Cerfa n°</xsl:text>
				<xsl:value-of select="//ServiceComplementaire/NumeroCerfa"/>
			</xsl:if>
			<xsl:if test="//ServiceComplementaire/AutreNumero">
				<xsl:text> - N°</xsl:text>
				<xsl:value-of select="//ServiceComplementaire/AutreNumero"/>
			</xsl:if>
			<br/>
			<xsl:call-template name="getMAJDateContributor"/><br/><br/>
		</div>
	</xsl:template>

	<xsl:template match="Titre" mode="Sous-theme">
		<h2><xsl:value-of select="text()"/></h2>
	</xsl:template>

	<xsl:template match="Titre" mode="Theme">
		<h2><xsl:value-of select="text()"/></h2>
	</xsl:template>

	<xsl:template match="Titre" mode="Definition">
		<h2><xsl:value-of select="text()"/></h2>
	</xsl:template>

	<xsl:template match="Titre" mode="OuSAdresser">
		<h4><xsl:apply-templates/></h4>
	</xsl:template>

	<xsl:template match="Titre" mode="Noeud-dossier">
		<div class="entiteImageFloatLeft">
			<xsl:choose>
				<xsl:call-template name="imageOfATheme">
					<xsl:with-param name="id" select="//Publication/FilDAriane/Niveau/@ID"/>
				</xsl:call-template>
			</xsl:choose>
		</div>
		<h2><xsl:value-of select="text()"/></h2>
	</xsl:template>

	<xsl:template match="Titre">
		<xsl:choose>
			<xsl:when test="name(..) = 'Chapitre'">
				<h2>
					<xsl:call-template name="imageOfATheme">
						<xsl:with-param name="id">
							<xsl:choose>
								<xsl:when test="//Publication/dc:type = 'Comment faire si'">
									<xsl:value-of select="//Publication/@ID"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="//Publication/FilDAriane/Niveau/@ID"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:with-param>
						<xsl:with-param name="class" select="'entiteImageFloatLeft'"/>
					</xsl:call-template>
					<xsl:apply-templates/>
				</h2>
			</xsl:when>
			<xsl:when test="name(..) = 'SousChapitre'">
				<h3><xsl:apply-templates/></h3>
			</xsl:when>
			<xsl:when test="name(..) = 'Tableau'">
				<caption><xsl:apply-templates/></caption>
			</xsl:when>
			<xsl:otherwise>
				<h2><xsl:apply-templates/></h2>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>
