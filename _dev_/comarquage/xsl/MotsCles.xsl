<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
  <xsl:include href="Entete.xsl"/>
  <xsl:include href="Parametres.xsl"/>
  <xsl:include href="Pied.xsl"/>

  <xsl:output method="html" encoding="UTF-8" doctype-public="-//W3C//DTD HTML 4.01 Transitional//EN" indent="yes"/>
  <xsl:template match="/">
        <xsl:call-template name="entete"/>
        <br/>
        <xsl:choose>
          <xsl:when test="$MOTCLE">
            <xsl:apply-templates mode="publications"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:apply-templates mode="motscles"/>
          </xsl:otherwise>
        </xsl:choose>
        <br/>
        <xsl:call-template name="pied"/>
  </xsl:template>
  <xsl:template match="MotsClés" mode="motscles">
    <h4>
      <a name="haut"/>
      <font color="#cc3333">
        <b>VOS DROITS ET DEMARCHES : MOTS-CLÉS</b>
      </font>
    </h4>
    <br/>
    <table border="0" cellPadding="0" cellSpacing="0" width="100%">
      <tbody>
        <tr>
          <td rowSpan="2" vAlign="bottom" width="16">
          </td>
        </tr>
        <tr>
          <td align="left" vAlign="top" width="100%" height="282">
            <table border="0" width="100%">
              <tbody>
                <tr>
                  <td colSpan="3">
                    <xsl:call-template name="abecedaire"/>
                  </td>
                </tr>
                <tr>
                  <td colSpan="3"> </td>
                </tr>
                <tr>
                  <td colSpan="3">
                    <table width="100%" border="0" cellspacing="3" cellpadding="0">
                      <tr>
                        <td width="50%">
                          <xsl:apply-templates select="MotClé" mode="colonne1"/>
                        </td>
                        <td width="50%">
                          <xsl:apply-templates select="MotClé" mode="colonne2"/>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </xsl:template>
  <xsl:template match="MotsClés" mode="publications">
    <h4>
      <a name="haut"/>
      <font color="#cc3333">
        <b> VOS DROITS ET DEMARCHES : MOTS-CLÉS</b>
      </font>
    </h4>
    <p> </p>
    <p>
      <xsl:text>Votre sélection</xsl:text>
      <b>
        <xsl:text> : </xsl:text>
        <xsl:value-of select="$MOTCLE"/>
      </b>
    </p>
    <table border="0" cellPadding="0" cellSpacing="0" width="100%">
      <tbody>
        <tr>
          <td rowSpan="2" vAlign="bottom" width="16">
          </td>
        </tr>
        <tr>
          <td align="left" vAlign="top" width="100%" height="282">
            <table border="0" width="100%">
              <tbody>
                <tr>
                  <td colSpan="2"> </td>
                </tr>
                <xsl:apply-templates select="MotClé[@libellé = $MOTCLE]/Publication[@type != 'Fiche Question-réponse']" mode="VD">
                  <xsl:sort select="Titre" order="ascending"/>
                </xsl:apply-templates>
                <xsl:apply-templates select="MotClé[@libellé = $MOTCLE]/Publication[@type = 'Fiche Question-réponse']" mode="QR">
                  <xsl:sort select="Titre" order="ascending"/>
                </xsl:apply-templates>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </xsl:template>
  <xsl:template match="MotClé" mode="colonne1">
    <xsl:if test="position() &lt;= (count(//MotsClés/MotClé) div 2) + (count(//MotsClés/MotClé) mod 2)">
      <p>
        <img alt="">
          <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/puce_r.gif</xsl:text></xsl:attribute>
        </img> 
        <a>
          <xsl:attribute name="href"><xsl:value-of select="$REFERER"/><xsl:text>xml=MotsCles</xsl:text><xsl:value-of select="$LETTRE"/><xsl:text>.xml&#x26;xsl=MotsCles.xsl&#x26;motcle=</xsl:text><xsl:value-of select="@libellé"/><xsl:text>&#x26;lettre=</xsl:text><xsl:value-of select="$LETTRE"/></xsl:attribute>
          <xsl:value-of select="@libellé"/>
        </a>
      </p>
    </xsl:if>
  </xsl:template>
  <xsl:template match="MotClé" mode="colonne2">
    <xsl:if test="position() &gt; (count(//MotsClés/MotClé) div 2) + (count(//MotsClés/MotClé) mod 2)">
      <p>
        <img alt="">
          <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/puce_r.gif</xsl:text></xsl:attribute>
        </img> 
        <a>
          <xsl:attribute name="href"><xsl:value-of select="$REFERER"/><xsl:text>xml=MotsCles</xsl:text><xsl:value-of select="$LETTRE"/><xsl:text>.xml&#x26;xsl=MotsCles.xsl&#x26;motcle=</xsl:text><xsl:value-of select="@libellé"/><xsl:text>&#x26;lettre=</xsl:text><xsl:value-of select="$LETTRE"/></xsl:attribute>
          <xsl:value-of select="@libellé"/>
        </a>
      </p>
    </xsl:if>
  </xsl:template>
  <xsl:template match="Publication" mode="VD">
    <tr>
      <td width="19" valign="baseline">
        <img width="18" height="18" alt="">
          <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/chapitre_ferme.gif</xsl:text></xsl:attribute>
        </img>
      </td>
      <td width="714" align="left">
        <a>
          <xsl:choose>
            <xsl:when test="@nature='Noeud'">
              <xsl:attribute name="href"><xsl:value-of select="$REFERER"/><xsl:text>xml=</xsl:text><xsl:value-of select="@ID"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text></xsl:attribute>
            </xsl:when>
            <xsl:otherwise>
              <xsl:attribute name="href"><xsl:value-of select="$REFERER"/><xsl:text>xml=</xsl:text><xsl:value-of select="@ID"/><xsl:text>.xml&#x26;xsl=Fiche.xsl</xsl:text></xsl:attribute>
            </xsl:otherwise>
          </xsl:choose>
          <xsl:value-of select="Titre"/>
        </a>
      </td>
    </tr>
  </xsl:template>
  <xsl:template match="Publication" mode="QR">
    <tr>
      <td width="19" valign="baseline">
        <img width="15" height="16" alt="">
          <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/puce_rouge2.gif</xsl:text></xsl:attribute>
        </img>
      </td>
      <td width="714" align="left">
        <a>
          <xsl:choose>
            <xsl:when test="@nature='Noeud'">
              <xsl:attribute name="href"><xsl:value-of select="$REFERER"/><xsl:text>xml=</xsl:text><xsl:value-of select="@ID"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text></xsl:attribute>
            </xsl:when>
            <xsl:otherwise>
              <xsl:attribute name="href"><xsl:value-of select="$REFERER"/><xsl:text>xml=</xsl:text><xsl:value-of select="@ID"/><xsl:text>.xml&#x26;xsl=Fiche.xsl</xsl:text></xsl:attribute>
            </xsl:otherwise>
          </xsl:choose>
          <xsl:value-of select="Titre"/>
        </a>
      </td>
    </tr>
  </xsl:template>
  <xsl:template name="header">
    <table cellSpacing="0" cellPadding="0" width="100%" border="0">
      <tr>
        <td colSpan="3">
          <xsl:for-each select="//CheminPréf/*">
            <xsl:if test="position()>1">
              <xsl:text> > </xsl:text>
            </xsl:if>
            <a>
              <xsl:attribute name="href"><xsl:value-of select="$REFERER"/><xsl:text>xml=</xsl:text><xsl:value-of select="@ID"/><xsl:text>.xml&#x26;xsl=MotsCles.xsl&#x26;lettre=</xsl:text><xsl:value-of select="$LETTRE"/></xsl:attribute>
              <xsl:value-of select="."/>
            </a>
          </xsl:for-each>
          <xsl:if test="//CheminPréf/*">
            <xsl:text> > </xsl:text>
          </xsl:if> 
          <xsl:choose>
            <xsl:when test="$MOTCLE">
              <a>
                <xsl:attribute name="href"><xsl:value-of select="$REFERER"/><xsl:text>xml=MotsCles</xsl:text><xsl:value-of select="$LETTRE"/><xsl:text>.xml&#x26;xsl=MotsCles.xsl&#x26;lettre=</xsl:text><xsl:value-of select="$LETTRE"/></xsl:attribute>
                <xsl:text>Mots-clés</xsl:text>
              </a>
              <font color="#000000"> > </font>
              <font color="#CC3333">
                <xsl:value-of select="$MOTCLE"/>
              </font>
            </xsl:when>
            <xsl:otherwise>
              <font color="#CC3333">
                <xsl:text>Mots-clés</xsl:text>
              </font>
            </xsl:otherwise>
          </xsl:choose>
        </td>
      </tr>
    </table>
  </xsl:template>

  <xsl:template name="lettreBlock">
    <xsl:param name="lettre"/>
    <xsl:variable name="separator">
      <xsl:if test="$lettre != 'Z'">
      |
      </xsl:if>
    </xsl:variable>
    <xsl:choose>
      <xsl:when test="$LETTRE = $lettre">
        <font color="#cc3333">
          <b><xsl:value-of select="$lettre"/></b>
        </font> <xsl:value-of select="$separator"/>
      </xsl:when>
      <xsl:otherwise>
        <a>
          <xsl:attribute name="href">
            <xsl:value-of select="$REFERER"/>xml=MotsCles<xsl:value-of select="$lettre"/>.xml&#x26;xsl=MotsCles.xsl&#x26;lettre=<xsl:value-of select="$lettre"/>
          </xsl:attribute>
          <xsl:value-of select="$lettre"/>
        </a> <xsl:value-of select="$separator"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="abecedaire">
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">A</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">B</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">C</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">D</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">E</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">F</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">G</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">H</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">I</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">J</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">K</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">L</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">M</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">N</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">O</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">P</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">Q</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">R</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">S</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">T</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">U</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">V</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">X</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">Y</xsl:with-param></xsl:call-template>
    <xsl:call-template name="lettreBlock"><xsl:with-param name="lettre">Z</xsl:with-param></xsl:call-template>
  </xsl:template>
</xsl:stylesheet>
