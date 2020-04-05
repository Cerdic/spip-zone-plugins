<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

  	<xsl:import href="spCommon.xsl"/>
  	<xsl:import href="spFilDAriane.xsl"/>

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
   	<xsl:template match="/">
   		<h3 class="spip"><span>Vos droits et démarches en tant que particulier</span></h3>
		<div id="noeudThemes">
			<xsl:call-template name="noeudThemes"/>
 		</div>
  	</xsl:template>
	
	<!-- Rubriques principales -->
	<xsl:template name="noeudThemes">
		<ul class="noeudThemes">
    	<xsl:for-each select="/Noeud/Descendance/Fils">
    		<xsl:call-template name="mainTheme"/> 
  		</xsl:for-each>
  		</ul>
	</xsl:template>

	<!-- Thème principal -->
	<xsl:template name="mainTheme">
	    <xsl:variable name="href">
	    	<xsl:value-of select="$XMLURL"/>
	    	<xsl:value-of select="@lien"/>
	    	<xsl:text>.xml</xsl:text>
	    </xsl:variable>
	    <xsl:variable name="titre">
             <xsl:apply-templates select="TitreContextuel" />
	    </xsl:variable>
   		<li class="noeudThemesFils">
			  
			<div class="noeudThemesFilsImage">
				<xsl:call-template name="imageOfATheme">
					<xsl:with-param name="id" select="@lien"/>
				</xsl:call-template>
			</div>
			
			<h4 class="spip">
			 <a>
			 	<xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml</xsl:text>&#x26;xsl=spNoeud.xsl</xsl:attribute>
                <xsl:value-of select="$titre"/> 
             </a>
				<!-- <xsl:call-template name="getPublicationLink">
	   				<xsl:with-param name="href"><xsl:value-of select="@lien"/></xsl:with-param>
	   				<xsl:with-param name="title"><xsl:value-of select="$titre"/></xsl:with-param>
	   				<xsl:with-param name="text"><xsl:value-of select="$titre"/></xsl:with-param>
				</xsl:call-template>
				-->
			</h4>		
			<ul class="noeudsousThemesFils">
			<xsl:call-template name="sousTheme">
				<xsl:with-param name="nameFile"><xsl:value-of select="$href"/></xsl:with-param>
			</xsl:call-template>
			</ul>
    	</li>	
	</xsl:template>

	<!-- Sous-thème d'un thème principal -->
	<xsl:template name="sousTheme">
	   	<xsl:param name="nameFile"/>
	   	<xsl:variable name="Titre"><xsl:value-of select="TitreContextuel"/></xsl:variable>
	   	<xsl:for-each select="document($nameFile)/Publication/SousTheme">
	   		<xsl:call-template name="sousThemeLien">
	   			<xsl:with-param name="parentTitre"><xsl:value-of select="$Titre"/></xsl:with-param>
	   		</xsl:call-template>
  		</xsl:for-each>
	   	<xsl:for-each select="document($nameFile)/Publication/Dossier">
	   		<xsl:call-template name="sousThemeLien">
	   			<xsl:with-param name="parentTitre"><xsl:value-of select="$Titre"/></xsl:with-param>
	   		</xsl:call-template>
  		</xsl:for-each>	
	</xsl:template>
	
	<!-- Lien vers un sous-thème du thème principal -->
	<xsl:template name="sousThemeLien">
		<xsl:param name="parentTitre"/>
	    <xsl:variable name="titre">
             <xsl:apply-templates select="Titre"/>
	    </xsl:variable>
   		<!--
   		<xsl:if test="position() > 1">
   			<xsl:text> - </xsl:text>
   		</xsl:if>
   		-->
   		<li>
   			<a>
		 		<xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@ID"/><xsl:text>.xml</xsl:text>&#x26;xsl=spNoeud.xsl</xsl:attribute>
                <xsl:value-of select="$titre"/> 
            </a> 
            <xsl:if test = "not (position()=last())"> / </xsl:if>
		<!-- 	<xsl:call-template name="getPublicationLink">
   				<xsl:with-param name="href"><xsl:value-of select="@ID"/></xsl:with-param>
   				<xsl:with-param name="title">
   					<xsl:value-of select="$parentTitre"/>
   					<xsl:value-of select="$sepFilDAriane"/>
   					<xsl:value-of select="$titre"/>
   				</xsl:with-param>
   				<xsl:with-param name="text"><xsl:value-of select="$titre"/></xsl:with-param>
			</xsl:call-template>
		 -->
   		</li>
	</xsl:template>
	
</xsl:stylesheet>
