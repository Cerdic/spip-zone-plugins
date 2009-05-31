<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
  <xsl:include href="Publication.xsl"/>
  <xsl:include href="RessourceRattachee.xsl"/>

<xsl:output method="html" encoding="UTF-8" doctype-public="-//W3C//DTD HTML 4.01 Transitional//EN" indent="yes"/> 
  <xsl:template match="/">
    <html>
      <head>
        <title>
          <xsl:value-of select="//TitreLong"/>
        </title>
<style type="TEXT/CSS">
<xsl:comment xml:space="preserve">
.tippopup { POSITION: absolute; VISIBILITY: hidden }
</xsl:comment>
</style>
        <xsl:if test="/Ressource/Indexation">
          <meta name="keywords">
            <xsl:attribute name="content"><xsl:apply-templates select="/Ressource/Indexation/MotClé"/></xsl:attribute>
          </meta>
        </xsl:if>
        <meta name="robots" content="index,nofollow"/>
        <meta name="Publisher" content="La Documentation Française"/>
        <meta name="Copyright">
          <xsl:attribute name="content"><xsl:choose><xsl:when test="/Ressource/Coproducteur"><xsl:value-of select="/Ressource/Coproducteur"/></xsl:when><xsl:otherwise>
                La Documentation française,CIRA
              </xsl:otherwise></xsl:choose></xsl:attribute>
        </meta>
      </head>
      <body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
        <xsl:comment>
          <xsl:value-of select="Ressource/@ID"/>
        </xsl:comment>
        <xsl:choose>
          <xsl:when test="Ressource/Type/Nom='Définition de glossaire'">
            <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0" height="95%">
              <tr valign="top">
                <td height="15">
                  <div class="box_sp_titre_b">Définition</div>
                </td>
              </tr>
              <tr valign="top">
                <td>
                  <table class="box_sp_b">
                    <xsl:apply-templates select="Ressource" mode="Ressource"/>
                  </table>
                </td>
              </tr>
            </table>
            <a href="#" onclick="window.close()">
              <span class="texte1" align="center">Fermer cette fenêtre</span>
            </a>
          </xsl:when>
          <xsl:when test="Ressource/Type/Nom='Site Web'">
            <table borderColor="#ff9900" cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0" height="95%">
              <tr valign="top">
                <td height="15">
                  <div class="box_sp_titre_o">Pour en savoir plus</div>
                </td>
              </tr>
              <tr valign="top">
                <td>
                  <table class="box_sp_o">
                    <tr>
                      <td colspan="2" align="left" valign="baseline">
                        <b>
                          <font color="#000000">Site internet public</font>
                        </b>
                      </td>
                    </tr>
                    <xsl:apply-templates select="Ressource" mode="Ressource_Web"/>
                  </table>
                </td>
              </tr>
            </table>
            <a href="#" onclick="window.close()">
              <span class="texte1" align="center">Fermer cette fenêtre</span>
            </a>
          </xsl:when>
          <xsl:when test="Ressource/Type/Nom='Téléservice'">
            <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0" height="95%">
              <tr valign="top">
                <td height="15">
                  <div class="box_sp_titre_b">Démarche en ligne</div>
                </td>
              </tr>
              <tr valign="top">
                <td>
                  <table class="box_sp_b">
                    <xsl:apply-templates select="Ressource" mode="Ressource_Teleservice"/>
                  </table>
                </td>
              </tr>
            </table>
            <a href="#" onclick="window.close()">
              <span class="texte1" align="center">Fermer cette fenêtre</span>
            </a>
          </xsl:when>
          <xsl:when test="Ressource/Type/Nom='Formulaire'">
            <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0" height="95%">
              <tr valign="top">
                <td height="15">
                  <div class="box_sp_titre_b">Formulaire</div>
                </td>
              </tr>
              <tr valign="top">
                <td>
                  <table class="box_sp_titre_b">
                    <xsl:apply-templates select="Ressource" mode="Ressource_Formulaire"/>
                  </table>
                </td>
              </tr>
            </table>
            <a href="#" onclick="window.close()">
              <span class="texte1" align="center">Fermer cette fenêtre</span>
            </a>
          </xsl:when>
          <xsl:when test="Ressource/Type/Nom='Texte de référence'">
            <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0" height="95%">
              <tr valign="top">
                <td height="15">
                  <div class="box_sp_titre_b">Texte de référence</div>
                </td>
              </tr>
              <tr valign="top">
                <td>
                  <table class="box_sp_b">
                    <xsl:apply-templates select="Ressource" mode="Ressource_Texte_Reference"/>
                  </table>
                </td>
              </tr>
            </table>
            <a href="#" onclick="window.close()">
              <span class="texte1" align="center">Fermer cette fenêtre</span>
            </a>
          </xsl:when>
          <xsl:when test="Ressource/Type/Nom='Avertissement'">
            <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0" height="95%">
              <tr valign="top">
                <td height="15">
                  <div class="box_sp_titre_o">Avertissement</div>
                </td>
              </tr>
              <tr valign="top">
                <td>
                  <table class="box_sp_o">
                    <xsl:apply-templates select="Ressource" mode="Ressource_Avertissement"/>
                  </table>
                </td>
              </tr>
            </table>
            <a href="#" onclick="window.close()">
              <span class="texte1" align="center">Fermer cette fenêtre</span>
            </a>
          </xsl:when>
          <xsl:when test="Ressource/Type/Nom='Organisme national'">
            <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0" height="95%">
              <tr valign="top">
                <td height="15">
                  <div class="box_sp_titre_r">Organisme national</div>
                </td>
              </tr>
              <tr valign="top">
                <td>
                  <table class="box_sp_r">
                    <xsl:apply-templates select="Ressource" mode="Ressource_Organisme_national"/>
                  </table>
                </td>
              </tr>
            </table>
            <a href="#" onclick="window.close()">
              <span class="texte1" align="center">Fermer cette fenêtre</span>
            </a>
          </xsl:when>
          <xsl:when test="Ressource/Type/Nom='Organisme local Web'">
            <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0" height="95%">
              <tr valign="top">
                <td height="15">
                  <div class="box_sp_titre_r">Adresse locale</div>
                </td>
              </tr>
              <tr valign="top">
                <td>
                  <table class="box_sp_r">
                    <xsl:apply-templates select="Ressource" mode="Ressource_Organisme_local_Web"/>
                  </table>
                </td>
              </tr>
            </table>
            <a href="#" onclick="window.close()">
              <span class="texte1" align="center">Fermer cette fenêtre</span>
            </a>
          </xsl:when>
          <xsl:when test="Ressource/Type/Nom='Organisme local SPL'">
            <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0" height="95%">
              <tr valign="top">
                <td height="15">
                  <div class="box_sp_titre_r">Adresse locale</div>
                </td>
              </tr>
              <tr valign="top">
                <td>
                  <table class="box_sp_r">
                    <xsl:apply-templates select="Ressource" mode="Ressource_Organisme_local_SPL"/>
                  </table>
                </td>
              </tr>
            </table>
            <a href="#" onclick="window.close()">
              <span class="texte1" align="center">Fermer cette fenêtre</span>
            </a>
          </xsl:when>
          <xsl:when test="Ressource/Type/Nom='Lettre type'">
            <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0" height="95%">
              <tr valign="top">
                <td height="15">
                  <div class="box_sp_titre_v">Lettre type</div>
                </td>
              </tr>
              <tr valign="top">
                <td>
                  <table class="box_sp_v">
                    <xsl:apply-templates select="Ressource" mode="Ressource_Lettre_type"/>
                  </table>
                </td>
              </tr>
            </table>
            <a href="#" onclick="window.close()">
              <span class="texte1" align="center">Fermer cette fenêtre</span>
            </a>
          </xsl:when>
        </xsl:choose>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
