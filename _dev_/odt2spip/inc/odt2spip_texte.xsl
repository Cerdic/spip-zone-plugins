<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0" 
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" 
xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" 
xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" 
xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" 
xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" 
xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" 
xmlns:xlink="http://www.w3.org/1999/xlink" 
xmlns:dc="http://purl.org/dc/elements/1.1/" 
xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" 
xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" 
xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" 
xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" 
xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" 
xmlns:math="http://www.w3.org/1998/Math/MathML" 
xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" 
xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" 
xmlns:ooo="http://openoffice.org/2004/office" 
xmlns:ooow="http://openoffice.org/2004/writer" 
xmlns:oooc="http://openoffice.org/2004/calc" 
xmlns:dom="http://www.w3.org/2001/xml-events" 
xmlns:xforms="http://www.w3.org/2002/xforms" 
xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" office:version="1.0"
exclude-result-prefixes="office style text table draw fo xlink svg">

<xsl:output method = "xml"
            encoding="ISO-8859-1"
            indent="yes" />

<xsl:strip-space elements="*" />

<xsl:template match="office:body">
<articles>
	<article>
		<id_article></id_article>
		<surtitre></surtitre>
    <titre>
        <xsl:apply-templates select="//text:p[@text:style-name='Heading'][1]"/>
    </titre>
		<soustitre></soustitre>
		<id_rubrique></id_rubrique>
		<descriptif></descriptif>
		<chapo></chapo>
		<texte>
        <xsl:apply-templates/>
    </texte>
		<ps></ps>
		<date></date>
		<statut></statut>
		<id_secteur></id_secteur>
		<date_redac></date_redac>
		<accepter_forum></accepter_forum>
		<date_modif></date_modif>
		<lang></lang>
		<langue_choisie></langue_choisie>
		<id_trad></id_trad>
		<extra></extra>
		<nom_site></nom_site>
		<url_site></url_site>
		<url_propre></url_propre>
	</article>
</articles>    
</xsl:template>

<xsl:template match="text:p[@text:style-name='Heading']">
  :::<xsl:value-of select="." />:::
</xsl:template>

<xsl:template match="text:p">
	<xsl:choose>
<!-- recuperer le titre et l'encadrer par ::: pour extraction par php 
		<xsl:when test="@text:style-name='Heading' and node()">
			:::TITRE|<xsl:value-of select="." />:::
		</xsl:when>
-->
<!-- ne pas mettre le titre dans le texte -->
  <xsl:when test="@text:style-name='Heading'">
    :::toto<xsl:apply-templates />:::
  </xsl:when>

<!-- les listes : template specifique -->		
		<xsl:when test="name(..)='text:list-item'">
			<xsl:apply-templates />
		</xsl:when>
<!-- le restant du texte -->		
		<xsl:otherwise>
            <xsl:text >&#xA; </xsl:text>
			    <xsl:apply-templates />
		</xsl:otherwise>
<!--			(sauf elements vides) 
					<xsl:if test="node()">
					</xsl:if>  
-->	
	</xsl:choose>		
  
</xsl:template>

<!-- les sauts de ligne -->
<xsl:template match="text:line-break">
_ <xsl:apply-templates />
</xsl:template>

<!-- gras et italiques -->
<xsl:template match="text:span">
	<xsl:variable name="StyleType" select="@text:style-name"/>
	<xsl:variable name="weight" select="/office:document-content/office:automatic-styles/style:style[@style:name=$StyleType]/style:text-properties/@fo:font-weight"/>
	<xsl:variable name="style" select="/office:document-content/office:automatic-styles/style:style[@style:name=$StyleType]/style:text-properties/@fo:font-style"/>
	<xsl:choose>
    <xsl:when test="$weight='bold'">{{<xsl:apply-templates />}}</xsl:when>
  	<xsl:when test="$style='italic'">{<xsl:apply-templates />}</xsl:when>
  	<xsl:otherwise>
   			<xsl:apply-templates />
		</xsl:otherwise>
	</xsl:choose>   
</xsl:template>

<!-- les titres (niveau 1 a 10) : Titre 1 passe en intertitre, 
		 		 					  Titre 2 en gras + retours lignes, 
                                  Titre 3 et suivants en italique + retours lignes
									  les autres sont ignores -->
<xsl:template match="text:h">
	<xsl:choose>
		<xsl:when test='@text:style-name="Heading_20_1"'>
				{{{<xsl:apply-templates />}}}
		</xsl:when>
		<xsl:when test='@text:style-name="Heading_20_2"'>

				{{<xsl:apply-templates />}} 
	  </xsl:when>
		<xsl:when test='@text:style-name="Heading_20_3" 
                    or @text:style-name="Heading_20_4" 
                    or @text:style-name="Heading_20_5"
                    '>

				{<xsl:apply-templates />}
		</xsl:when>
		<xsl:otherwise>
				<xsl:apply-templates />
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- les liens -->
<xsl:template match="text:a">[<xsl:apply-templates />-><xsl:value-of select="@xlink:href" />]</xsl:template>

<!-- les listes a puces et numerotees -->
<xsl:template match="text:list">
	<xsl:variable name="StyleType" select="@text:style-name"/>
	<xsl:variable name="liste_ordonnee" select="/office:document-content/office:automatic-styles/text:list-style[@style:name=$StyleType]/text:list-level-style-number/@text:level"/>
	<xsl:variable name="liste_puce" select="/office:document-content/office:automatic-styles/text:list-style[@style:name=$StyleType]/text:list-level-style-bullet/@text:level"/>
	<xsl:choose>
    <xsl:when test="$liste_ordonnee &lt;= 10"><xsl:call-template name="l_ordonnee" /></xsl:when>
  	<xsl:when test="$liste_puce &lt;= 10"><xsl:call-template name="l_puce" /></xsl:when>
  	<xsl:otherwise>
   			<xsl:apply-templates />
		</xsl:otherwise>
	</xsl:choose>   	
</xsl:template>

<xsl:template name="l_ordonnee"> 
			<xsl:for-each select="descendant::text:list-item/text:p">
-# <xsl:apply-templates /></xsl:for-each>
</xsl:template>
<xsl:template name="l_puce"> 
			<xsl:for-each select="descendant::text:list-item/text:p">
-* <xsl:apply-templates /></xsl:for-each>
</xsl:template> 

<!-- nettement plus bricolage : les images... -->
<!-- on met le nom de fichier de l'image qu'il faudra echanger en php par son id document spip une fois qu'il sera reference dans la table document -->	
<xsl:template match="draw:image">
   <xsl:call-template name="img2texte" />
</xsl:template>

<xsl:template name="img2texte">&#60;img<xsl:value-of select="substring(@xlink:href,10)"/>;;;<xsl:value-of select="substring-before(parent::draw:frame/@svg:width,'cm')" />;;;<xsl:value-of select="substring-before(parent::draw:frame/@svg:height,'cm')" />;;;|<xsl:choose>
<!-- sale bidouille pour approximer la position de l'image (|left |center |right) -->
<xsl:when test="substring-before(parent::draw:frame/@svg:x, 'cm') &lt;= 2">left</xsl:when>
<xsl:when test="substring-before(parent::draw:frame/@svg:x, 'cm') &gt;= 5">right</xsl:when>
<xsl:otherwise>center</xsl:otherwise>
</xsl:choose>&#62;</xsl:template>

<!-- conversion cm/pixels sur une base de 150 dpi
<xsl:template name="img2traite">
:::IMG|<xsl:value-of select="substring(@xlink:href,10)"/>|<xsl:value-of select="substring-after(@xlink:href,'.')"/>|<xsl:value-of select="round(substring-before(parent::draw:frame/@svg:width,'cm')*59)" />|<xsl:value-of select="round(substring-before(parent::draw:frame/@svg:height,'cm')*59)" />:::
</xsl:template>
 -->
 
<!-- notes de bas de page 	
		<xsl:when test="@text:style-name='Quotations' and node()">
			[[
			<xsl:apply-templates />
			]]
		</xsl:when>
-->	
</xsl:stylesheet>
