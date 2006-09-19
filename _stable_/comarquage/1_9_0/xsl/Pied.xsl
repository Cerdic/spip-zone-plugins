<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  <xsl:template name="pied">
    <table cellspacing="0" boder="0" cellpadding="0" width="100%">
      <tr align="center">
        <td valign="top">
          <a href="http://www.service-public.fr">
          <img align="middle" border="0" alt="">
             <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/logo1.jpg</xsl:text></xsl:attribute>
           </img></a> ©&#xA0;
          <xsl:choose>
            <xsl:when test="//Coproducteur">
              <xsl:value-of select="//Coproducteur"/>
            </xsl:when>
            <xsl:otherwise>
              La Documentation française / CIRA
            </xsl:otherwise>
          </xsl:choose>
        </td>
      </tr>
      <xsl:if test="contains(/Fiche/Type/Nom, 'Question-réponse')">
      <tr align="left">
        <td valign="top">
	    <h6>
	      Les informations de cette fiche ne sauraient préjuger de l'examen individuel de votre situation par l'administration compétente.
          </h6>
        </td>
      </tr>
      </xsl:if>
    </table>
  </xsl:template>
</xsl:stylesheet>
