<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  <xsl:param name="LETTRE">A</xsl:param>
  <xsl:param name="MOTCLE"></xsl:param>
  <xsl:param name="typepublication"><xsl:value-of select="/*/Type/Nom" /></xsl:param>
  <xsl:param name="REFERER">%%REFERER%%</xsl:param>
  <xsl:param name="ADRESSESLOCALES">yes</xsl:param>
  <xsl:param name="IMAGESURL"/>
</xsl:stylesheet>
