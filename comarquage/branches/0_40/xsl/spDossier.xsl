<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
 	<!-- Dossier -->
	<xsl:template match="Dossier" mode="Sous-theme">
		<!--  <xsl:if test="count(Fiche) > 0"> -->
		<div class="spSousThemeDossier">
				<xsl:attribute name="id">
					<xsl:call-template name="createThemeDossierId"/>
				</xsl:attribute>
				<!-- 
				<div class="entiteImageFloatLeft">
					<xsl:call-template name="imageOfATheme">
						<xsl:with-param name="id">
							<xsl:choose>
								<xsl:when test="//Publication/@type = 'Theme'">
									 <xsl:value-of select="//Publication/@ID"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="//Publication/FilDAriane/Niveau/@ID"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:with-param>
					</xsl:call-template>
				</div>
				-->
				<xsl:variable name="title">
					<xsl:value-of select="../dc:title"/>
					<xsl:value-of select="$sepFilDAriane"/>
					<xsl:value-of select="Titre"/>
				</xsl:variable>
				<h4 class="spip"><span>
				
					<a>
			 			<xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@ID"/><xsl:text>.xml</xsl:text>&#x26;xsl=spNoeud.xsl</xsl:attribute>
	                	<xsl:value-of select="Titre"/>
	            	</a>
	    			<!--  
	    			<xsl:call-template name="getPublicationLink">
	    				<xsl:with-param name="href"><xsl:value-of select="@ID"/></xsl:with-param>
	    				<xsl:with-param name="title"><xsl:value-of select="$title"/></xsl:with-param>
	    				<xsl:with-param name="text"><xsl:value-of select="Titre"/></xsl:with-param>
					</xsl:call-template>
					-->
				</span></h4>
				<ul class="spSousThemeDossierFiche">
					<xsl:apply-templates select="Fiche" mode="Sous-theme"/>
				</ul>
			</div>
		<!--  </xsl:if> -->
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
                <xsl:value-of select="$titre"/> 
             </a>
			<!--  
			<xsl:call-template name="getPublicationLink">
   				<xsl:with-param name="href"><xsl:value-of select="@ID"/></xsl:with-param>
   				<xsl:with-param name="title"><xsl:value-of select="$title"/></xsl:with-param>
   				<xsl:with-param name="text"><xsl:value-of select="text()"/></xsl:with-param>
			</xsl:call-template>
			 -->		
		</li>
	</xsl:template>

</xsl:stylesheet>
