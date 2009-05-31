<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
  <xsl:include href="Entete.xsl"/>
  <xsl:include href="Parametres.xsl"/>
  <xsl:include href="Pied.xsl"/>
  <xsl:output method="html"/>
  <xsl:template match="/">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="2">
            <div class="titre-texte">
              <xsl:value-of select="Noeud/TitreLong"/>
            </div>
        </td>
      </tr>
      <tr>
        <td rowspan="2" width="16" valign="bottom" align="right">
        </td>
      </tr>
      <tr>
        <td width="100%" valign="top">
          <table width="100%" border="0" cellspacing="0" cellpadding="4" vspace="0" hspace="0">
            <tr align="left" valign="top">
              <td width="76%">
                <table align="center" border="0" cellspacing="2">
                  <tr>
                    <td valign="top">
                      <xsl:apply-templates select="/Noeud/Descendance/Fils" mode="colonne1">
                        <xsl:sort select="Noeud/Descendance/Fils/@positionPrésentation" data-type="number" order="ascending"/>
                      </xsl:apply-templates>
                    </td>
                    <td valign="top">
                      <xsl:apply-templates select="/Noeud/Descendance/Fils" mode="colonne2">
                        <xsl:sort select="Noeud/Descendance/Fils/@positionPrésentation" data-type="number" order="ascending"/>
                      </xsl:apply-templates>
                    </td>
                  </tr>
                </table>
              </td>
              <td width="24%"/>
            </tr>
          </table>
        </td>
      </tr>
    </table>
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
<!--
                              <td width="30" align="LEFT" valign="BASELINE">
                                <img width="27" height="12" align="baseline" alt="">
                                  <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/m_orang2.gif</xsl:text></xsl:attribute>
                                </img>&#xA0;</td>
-->

                              <td width="97%" align="LEFT" valign="BASELINE">
                                <h4>
                                  <a>
                                    <xsl:attribute name="href"><xsl:value-of select="$REFERER"/><xsl:text>xml=MotsClesA.xml&#x26;xsl=MotsCles.xsl</xsl:text></xsl:attribute>
                                      INDEX ALPHABÉTIQUE PAR MOTS CLÉS
                                  </a>
                                </h4>
                              </td>
                            </tr>
                            <tr>
                              <td width="30" align="LEFT" valign="BASELINE">&#xA0;</td>
                            </tr>
                          </table>

    <br/>
    <xsl:call-template name="pied"/>
  </xsl:template>
  <xsl:template match="Fils" mode="colonne1">
    <xsl:if test="position() &lt;= (count(//Fils) div 2) + (count(//Fils) mod 2)">
      <ul>
        <li class="li_pb">
          <a>
            <xsl:choose>
              <xsl:when test="@nature='Noeud'">
                <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml</xsl:text>&#x26;xsl=Noeud.xsl</xsl:attribute>
              </xsl:when>
              <xsl:otherwise>
                <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml</xsl:text>&#x26;xsl=Fiche.xsl</xsl:attribute>
              </xsl:otherwise>
            </xsl:choose>
            <xsl:apply-templates select="TitreContextuel"/>
          </a>
        </li>
      </ul>
    </xsl:if>
  </xsl:template>
  <xsl:template match="Fils" mode="colonne2">
    <xsl:if test="position() &gt; (count(//Fils) div 2) + (count(//Fils) mod 2)">
      <ul>
        <li class="li_pb">
          <a>
            <xsl:choose>
              <xsl:when test="@nature='Noeud'">
                <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml</xsl:text>&#x26;xsl=Noeud.xsl</xsl:attribute>
              </xsl:when>
              <xsl:otherwise>
                <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml</xsl:text>&#x26;xsl=Fiche.xsl</xsl:attribute>
              </xsl:otherwise>
            </xsl:choose>
            <xsl:apply-templates select="TitreContextuel"/>
          </a>
        </li>
      </ul>
    </xsl:if>
  </xsl:template>
</xsl:stylesheet>
