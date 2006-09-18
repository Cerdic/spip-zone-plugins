<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  <xsl:template name="entete">
    <table cellSpacing="0" cellPadding="0" width="100%" border="0">
      <tr>
        <td colSpan="3">
          <a>
            <xsl:attribute name="href">
              <xsl:value-of select="$REFERER"/>
              <xsl:text>xml=Themes.xml&#x26;xsl=Themes.xsl</xsl:text>
            </xsl:attribute>
            <xsl:text>Liste des thèmes</xsl:text>
          </a><br />
          <xsl:choose>
            <xsl:when test="//Navigation">
              <xsl:for-each select="//Navigation/*">
		<!--
                <xsl:if test="position()>1">
                  <xsl:text>&#xA0;&gt;&#xA0;</xsl:text>
                </xsl:if>
		-->
                <a>
                  <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@href"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text><xsl:call-template name="navigationparams"><xsl:with-param name="pos" select="position()"/></xsl:call-template></xsl:attribute>
                  <xsl:value-of select="."/>
                </a>
              </xsl:for-each>
              <xsl:text>&#xA0;&gt;&#xA0;</xsl:text>
            </xsl:when>
            <xsl:otherwise>
              <xsl:for-each select="//CheminPréf/*">
		<!--
                <xsl:if test="position()>1">
                  <xsl:text>&#xA0;&gt;&#xA0;</xsl:text>
                </xsl:if>
		-->
		<br />
                <a>
                  <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@ID"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text></xsl:attribute>
                  <xsl:value-of select="."/>
                </a>
              </xsl:for-each>
              <xsl:if test="//CheminPréf/*">
		<br />
                <!-- <xsl:text>&#xA0;&gt;&#xA0;</xsl:text> -->
              </xsl:if> 
           </xsl:otherwise>
          </xsl:choose>
            <xsl:value-of select="//TitreLong"/>
        </td>
      </tr>
    </table>
  </xsl:template>
  <!-- Pour le passage de paramètres -->
  <xsl:template name="parametres"/>
  <xsl:template name="navigationparams"/>
</xsl:stylesheet>
