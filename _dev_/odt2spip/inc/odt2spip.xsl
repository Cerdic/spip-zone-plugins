<?xml version="1.0" encoding="iso-8859-1"?>

<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
    xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0"
    xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0"
    xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
    xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"
    xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0"
    xmlns:presentation="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0"
    xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0"
    xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0"
    xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0"
    xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0"
    xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"
    xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0"
    xmlns:anim="urn:oasis:names:tc:opendocument:xmlns:animation:1.0"

    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:math="http://www.w3.org/1998/Math/MathML"
    xmlns:xforms="http://www.w3.org/2002/xforms"

    xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"
    xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0"
    xmlns:smil="urn:oasis:names:tc:opendocument:xmlns:smil-compatible:1.0"
	
	xmlns:ooo="http://openoffice.org/2004/office"
	xmlns:ooow="http://openoffice.org/2004/writer"
	xmlns:oooc="http://openoffice.org/2004/calc"
	xmlns:int="http://catcode.com/odf_to_xhtml/internal"
    xmlns="http://www.w3.org/1999/xhtml"
	exclude-result-prefixes="office meta config text table draw presentation
		dr3d chart form script style number anim dc xlink math xforms fo
		svg smil ooo ooow oooc int #default"
>

<xsl:output method = "xml"
            encoding="ISO-8859-1"
            indent="yes" />

<xsl:strip-space elements="*" />

<xsl:variable name="lineBreak"><xsl:text>
</xsl:text></xsl:variable>

<xsl:key name="listTypes" match="text:list-style" use="@style:name"/>

<xsl:template match="/office:document-content">

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
        <xsl:apply-templates select="office:body/office:text"/>
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
		<xsl:apply-templates/>
		<xsl:if test="count(node())=0">&#xA; </xsl:if>
</xsl:template>

<xsl:template match="text:h">
<xsl:variable name="niv_titre" select="1"/>
<xsl:text >&#xA;</xsl:text>
		<xsl:choose>
			<xsl:when test="@text:outline-level = $niv_titre or @text:style-name = 'Heading_20_$niv_titre'">
{{{<xsl:apply-templates/>}}}
        </xsl:when>
			<xsl:when test="@text:outline-level = ($niv_titre + 1) or @text:style-name = 'Heading_20_($niv_titre + 1)'">
{{<xsl:apply-templates/>}}
        </xsl:when>
			<xsl:otherwise>
{<xsl:apply-templates/>}
			</xsl:otherwise>
		</xsl:choose>
<xsl:text >&#xA;</xsl:text>    
</xsl:template>

<!--
	When processing a list, you have to look at the parent style
	*and* level of nesting
-->
<xsl:template match="text:list">
  <xsl:variable name="level" select="count(ancestor::text:list)+1"/>

	<!-- the list class is the @text:style-name of the outermost <text:list> element -->
	<xsl:variable name="listClass">
		<xsl:choose>
			<xsl:when test="$level=1">
				<xsl:value-of select="@text:style-name"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="ancestor::text:list[last()]/@text:style-name"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	
	<!-- Now select the <text:list-level-style-foo> element at this
		level of nesting for this list -->
	<xsl:variable name="node" select="key('listTypes', $listClass)/*[@text:level='$level']"/>

	<!-- emit appropriate list type -->
	<xsl:choose>
		<xsl:when test="local-name($node)='list-level-style-number'">
			<xsl:call-template name="l_ordonnee"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="l_puce"/>
		</xsl:otherwise>
	</xsl:choose>
  <xsl:text >&#xA;</xsl:text>
  <xsl:text >&#xA;</xsl:text>
</xsl:template>

<xsl:template name="l_ordonnee"> 
			<xsl:for-each select="descendant::text:list-item/text:p">
-# <xsl:apply-templates /></xsl:for-each>
</xsl:template>
<xsl:template name="l_puce"> 
			<xsl:for-each select="descendant::text:list-item/text:p">
-* <xsl:apply-templates /></xsl:for-each>
</xsl:template> 


<xsl:template match="table:table">
<xsl:text >&#xA;</xsl:text>
		<xsl:if test="table:table-header-rows/table:table-row">
			<xsl:apply-templates select="table:table-header-rows/table:table-row"/>
		</xsl:if>
		<xsl:apply-templates select="table:table-row"/>
<xsl:text >&#xA;</xsl:text>        
</xsl:template>

<xsl:template match="table:table-row">
<xsl:text >&#xA;</xsl:text>|<xsl:apply-templates select="table:table-cell"/><xsl:apply-templates select="table:covered-table-cell"/></xsl:template>

<xsl:template match="table:table-cell">
	<xsl:variable name="n">
		<xsl:choose>
			<xsl:when test="@table:number-columns-repeated != 0">
				<xsl:value-of select="@table:number-columns-repeated"/>
			</xsl:when>
			<xsl:otherwise>1</xsl:otherwise>
		</xsl:choose>
	</xsl:variable><xsl:call-template name="process-table-cell">
		<xsl:with-param name="n" select="$n"/>
	</xsl:call-template>
</xsl:template>

<xsl:template match="table:covered-table-cell">&lt;|</xsl:template>

<xsl:template name="process-table-cell">
	<xsl:param name="n"/><xsl:if test="$n != 0"><xsl:if test="@table:number-columns-spanned"></xsl:if><xsl:if test="@table:number-rows-spanned"></xsl:if>
<xsl:apply-templates/>|<xsl:call-template name="process-table-cell">
			<xsl:with-param name="n" select="$n - 1"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>


<!-- les liens -->
<xsl:template match="text:a">[<xsl:apply-templates />-><xsl:value-of select="@xlink:href" />]</xsl:template>

<!-- les ancres -->
<xsl:template match="text:bookmark-start|text:bookmark">
[<xsl:value-of select="@text:name" />&lt;-]
</xsl:template>

<!--
	This template is too dangerous to leave active...
<xsl:template match="text()">
	<xsl:if test="normalize-space(.) !=''">
		<xsl:value-of select="normalize-space(.)"/>
	</xsl:if>
</xsl:template>
-->

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

<!-- notes de bas de page 	-->
<xsl:template match="text:note-citation"/>
<xsl:template match="text:note-body">[[<xsl:apply-templates />]]</xsl:template>
	

</xsl:stylesheet>
