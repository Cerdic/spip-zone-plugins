<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
  	<!-- Affiche la barre des 10 thèmes principaux -->
  	<xsl:template name="getBarre10Themes">
		<xsl:if test="$CATEGORIE = 'particuliers'">
			<xsl:variable name="file">
				<xsl:value-of select="$XMLURL"/>
				<xsl:text>Themes.xml</xsl:text>
			</xsl:variable>
			<div class="spBarre10Themes">
		    	<xsl:for-each select="document($file)/Noeud/Descendance/Fils">
		    		<xsl:variable name="titre">
		    			<xsl:value-of select="TitreContextuel"/>
		    		</xsl:variable>
		    		<div class="spBarre10ThemesFils">
						<xsl:call-template name="imageOfATheme">
							<xsl:with-param name="id" select="@lien"/>
						</xsl:call-template>
		    			<a>
							<xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml</xsl:text>&#x26;xsl=spNoeud.xsl</xsl:attribute>
							<xsl:value-of select="$titre"/>
						</a>
<!--
		    			<xsl:call-template name="getPublicationLink">
		    				<xsl:with-param name="href"><xsl:value-of select="@lien"/></xsl:with-param>
		    				<xsl:with-param name="title"><xsl:value-of select="$titre"/></xsl:with-param>
		    				<xsl:with-param name="text">
			    				<xsl:call-template name="imageOfATheme">
			    					<xsl:with-param name="id" select="@lien"/>
			    				</xsl:call-template>
		    				</xsl:with-param>
		    			</xsl:call-template>
-->  			 
		    		</div>
		  		</xsl:for-each>		
			</div>
		</xsl:if>		
  	</xsl:template>
	
	
	<xsl:template name="getPublicationLink">
		<xsl:param name="href"/>
		<xsl:param name="title"/>
		<xsl:param name="text"/>
		<xsl:param name="xml"/>
		<xsl:param name="xsl"/>
		<a>
			<xsl:attribute name="title">
				<xsl:value-of select="normalize-space($title)"/>
			</xsl:attribute>
			<xsl:attribute name="href">
				<xsl:value-of select="$REFERER"/>
				<xsl:text>xml=</xsl:text>
				<xsl:value-of select="$xml"/>
				<xsl:text>.xml</xsl:text>
				<xsl:text>&amp;xsl=</xsl:text>
				<xsl:value-of select="$xsl"/>
				<xsl:text>.xsl</xsl:text>
			</xsl:attribute>
			<xsl:copy-of select="$text"/>
		</a>
	</xsl:template>
	
	<!-- Création du lien vers une autre publication 
	<xsl:template name="getPublicationLink">
		<xsl:param name="href"/>
		<xsl:param name="title"/>
		<xsl:param name="text"/>
		<xsl:param name="xml"/>
		<xsl:param name="xsl"/>
		<xsl:choose>
			<xsl:when test="$href = 'Theme'">
				<a href="{$REFERER}">
					<xsl:attribute name="title">
						<xsl:value-of select="normalize-space($title)"/>
					</xsl:attribute>
					<xsl:copy-of select="$text"/>
				</a>
			</xsl:when>
			<xsl:when test="$href = 'Dossiersaz'">
				<a href="{$REFERER}-Dossiers-az">
					<xsl:attribute name="title">
						<xsl:value-of select="normalize-space($title)"/>
					</xsl:attribute>
					<xsl:value-of select="$text"/>
				</a>
			</xsl:when>
			<xsl:when test="$href = 'Dossierscat'">
				<a href="{$REFERER}-Dossiers-themes">
					<xsl:attribute name="title">
						<xsl:value-of select="normalize-space($title)"/>
					</xsl:attribute>
					<xsl:value-of select="$text"/>
				</a>
			</xsl:when>
			<xsl:when test="$href = 'Glossaire'">
				<a href="{$REFERER}-Glossaire">
					<xsl:attribute name="title">
						<xsl:value-of select="normalize-space($title)"/>
					</xsl:attribute>
					<xsl:value-of select="$text"/>
				</a>
			</xsl:when>
			<xsl:when test="$href = 'Administrations'">
				<a href="{$REFERER}-Administrations-locales">
					<xsl:attribute name="title">
						<xsl:value-of select="normalize-space($title)"/>
					</xsl:attribute>
					<xsl:value-of select="$text"/>
				</a>
			</xsl:when>
			<xsl:when test="contains($href,'LieuLocal-')">
				<a href="{$REFERER}-{$href}">
					<xsl:attribute name="title">
						<xsl:value-of select="normalize-space($title)"/>
					</xsl:attribute>
					<xsl:value-of select="$text"/>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="linkTitle">
					<xsl:call-template name="getTitle">
						<xsl:with-param name="id" select="$href"/>
					</xsl:call-template>
				</xsl:variable>
				<a>
					<xsl:attribute name="title">
						<xsl:value-of select="normalize-space($title)"/>
					</xsl:attribute>
					<xsl:attribute name="href">
						<xsl:value-of select="$REFERER"/>
						<xsl:text>-</xsl:text>
						<xsl:value-of select="$href"/>
						<xsl:text>-</xsl:text>
						<xsl:call-template name="textWithoutAccent">
							<xsl:with-param name="string" select="$linkTitle"/>
						</xsl:call-template>
					</xsl:attribute>
					<xsl:copy-of select="$text"/>
				</a>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	-->
	
	<!-- Création du lien vers un autre site -->
	<xsl:template name="getSiteLink">
		<xsl:param name="href"/>
		<xsl:param name="title"/>
		<xsl:param name="text"/>
		<a rel="nofollow" class="spTexteLienExterne" target="_blank">
			<xsl:attribute name="title">
				<xsl:value-of select="normalize-space($title)"/> (nouvelle fenêtre)
			</xsl:attribute>
			<xsl:attribute name="href">
				<xsl:value-of select="normalize-space($href)"/>
			</xsl:attribute>
			<xsl:value-of select="normalize-space($text)"/>
		</a>
	</xsl:template>

	<!-- Renvoie la description d'une publication -->
	<xsl:template name="getDescription">
		<xsl:param name="id"/>
		<xsl:variable name="file">
			<xsl:value-of select="$XMLURL"/>
			<xsl:value-of select="$id"/>
			<xsl:text>.xml</xsl:text>
		</xsl:variable>
		<xsl:variable name="desc">
			<xsl:value-of select="normalize-space(document($file)/*/dc:description)"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="substring($desc,string-length($desc)-1) = '.'">
				<xsl:value-of select="$desc"/>
				<xsl:text>..</xsl:text> 
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$desc"/>
				<xsl:text>...</xsl:text> 
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- Renvoie le titre d'une publication -->
	<xsl:template name="getTitle">
		<xsl:param name="id"/>
		<xsl:variable name="file">
			<xsl:value-of select="$XMLURL"/>
			<xsl:value-of select="$id"/>
			<xsl:text>.xml</xsl:text>
		</xsl:variable>
		<xsl:value-of select="document($file)/*/dc:title"/>
	</xsl:template>

	<!-- Transforme une date au format RSS sous forme d'une chaîne de caractères -->
	<xsl:template name="transformRssDate">
		<xsl:param name="date"/>
		<xsl:choose>
			<xsl:when test="contains($date,'Z')">
				<xsl:variable name="onlyDate">
					<xsl:value-of select="substring-before($date,'T')"/>
				</xsl:variable>
				<xsl:variable name="year">
					<xsl:value-of select="substring-before($onlyDate,'-')"/>
				</xsl:variable>
				<xsl:variable name="month">
					<xsl:value-of select="substring-before(substring-after($onlyDate,'-'),'-')"/>
				</xsl:variable>
				<xsl:variable name="day">
					<xsl:value-of select="substring-after(substring-after($onlyDate,'-'),'-')"/>
				</xsl:variable>		
				<xsl:value-of select="$day"/>
				<xsl:choose>
					<xsl:when test="$month ='01'"><xsl:text> janvier </xsl:text></xsl:when>
					<xsl:when test="$month ='02'"><xsl:text> février </xsl:text></xsl:when>
					<xsl:when test="$month ='03'"><xsl:text> mars </xsl:text></xsl:when>
					<xsl:when test="$month ='04'"><xsl:text> avril </xsl:text></xsl:when>
					<xsl:when test="$month ='05'"><xsl:text> mai </xsl:text></xsl:when>
					<xsl:when test="$month ='06'"><xsl:text> juin </xsl:text></xsl:when>
					<xsl:when test="$month ='07'"><xsl:text> juillet </xsl:text></xsl:when>
					<xsl:when test="$month ='08'"><xsl:text> août </xsl:text></xsl:when>
					<xsl:when test="$month ='09'"><xsl:text> septembre </xsl:text></xsl:when>
					<xsl:when test="$month ='10'"><xsl:text> octobre </xsl:text></xsl:when>
					<xsl:when test="$month ='11'"><xsl:text> novembre </xsl:text></xsl:when>
					<xsl:when test="$month ='12'"><xsl:text> décembre </xsl:text></xsl:when>
				</xsl:choose>
				<xsl:value-of select="$year"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="onlyDate">
					<xsl:value-of select="substring-after($date,', ')"/>
				</xsl:variable>
				<xsl:variable name="day">
					<xsl:value-of select="substring-before($onlyDate,' ')"/>
				</xsl:variable>		
				<xsl:variable name="month">
					<xsl:value-of select="substring-before(substring-after($onlyDate,' '),' ')"/>
				</xsl:variable>
				<xsl:variable name="year">
					<xsl:value-of select="substring-before(substring-after(substring-after($onlyDate,' '),' '),' ')"/>
				</xsl:variable>
				<xsl:value-of select="$day"/>
				<xsl:choose>
					<xsl:when test="$month ='Jan'"><xsl:text> janvier </xsl:text></xsl:when>
					<xsl:when test="$month ='Feb'"><xsl:text> février </xsl:text></xsl:when>
					<xsl:when test="$month ='Mar'"><xsl:text> mars </xsl:text></xsl:when>
					<xsl:when test="$month ='Apr'"><xsl:text> avril </xsl:text></xsl:when>
					<xsl:when test="$month ='May'"><xsl:text> mai </xsl:text></xsl:when>
					<xsl:when test="$month ='Jun'"><xsl:text> juin </xsl:text></xsl:when>
					<xsl:when test="$month ='Jul'"><xsl:text> juillet </xsl:text></xsl:when>
					<xsl:when test="$month ='Aug'"><xsl:text> août </xsl:text></xsl:when>
					<xsl:when test="$month ='Sep'"><xsl:text> septembre </xsl:text></xsl:when>
					<xsl:when test="$month ='Oct'"><xsl:text> octobre </xsl:text></xsl:when>
					<xsl:when test="$month ='Nov'"><xsl:text> novembre </xsl:text></xsl:when>
					<xsl:when test="$month ='Dec'"><xsl:text> décembre </xsl:text></xsl:when>
				</xsl:choose>
				<xsl:value-of select="$year"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<!-- Renvoie l'image associée à un thème principal -->
	<xsl:template name="imageOfATheme">
		<xsl:param name="id"/>
		<xsl:choose>
			<xsl:when test="$CATEGORIE = 'particuliers'">
			    <xsl:variable name="file">
			    	<xsl:value-of select="$XMLURL"/>
			    	<xsl:value-of select="$id"/>
			    	<xsl:text>.xml</xsl:text>
			    </xsl:variable>
				<img width="40" height="40">
					<xsl:attribute name="alt">
						<xsl:value-of select="document($file)/Publication/dc:title"/>
					</xsl:attribute>
					<xsl:attribute name="src">
						<xsl:value-of select="$IMAGES"/>
						<xsl:choose>
							<!-- Thèmes principaux -->							
							<xsl:when test="$id = 'N19803'"><xsl:text>argent.jpg</xsl:text></xsl:when>
							<xsl:when test="$id = 'N19804'"><xsl:text>europe.jpg</xsl:text></xsl:when>
							<xsl:when test="$id = 'N19805'"><xsl:text>famille.jpg</xsl:text></xsl:when>
							<xsl:when test="$id = 'N19806'"><xsl:text>formation.jpg</xsl:text></xsl:when>
							<xsl:when test="$id = 'N19807'"><xsl:text>justice.jpg</xsl:text></xsl:when>
							<xsl:when test="$id = 'N19808'"><xsl:text>logement.jpg</xsl:text></xsl:when>
							<xsl:when test="$id = 'N19809'"><xsl:text>loisirs.jpg</xsl:text></xsl:when>
							<xsl:when test="$id = 'N19810'"><xsl:text>citoyennete.jpg</xsl:text></xsl:when>
							<xsl:when test="$id = 'N19811'"><xsl:text>sante.jpg</xsl:text></xsl:when>
							<xsl:when test="$id = 'N19812'"><xsl:text>transport.jpg</xsl:text></xsl:when>
							<!-- Comment faire si -->
							<xsl:when test="$id = 'F14128'"><xsl:text>demenager.png</xsl:text></xsl:when>
							<xsl:when test="$id = 'F16225'"><xsl:text>enfant.png</xsl:text></xsl:when>
							<xsl:when test="$id = 'F17556'"><xsl:text>emploi.png</xsl:text></xsl:when>
							<xsl:when test="$id = 'F14485'"><xsl:text>marie.png</xsl:text></xsl:when>
							<xsl:when test="$id = 'F16507'"><xsl:text>deces.png</xsl:text></xsl:when>
							<xsl:when test="$id = 'F1700'"><xsl:text>administration.png</xsl:text></xsl:when>
							<xsl:when test="$id = 'F15913'"><xsl:text>achat-logement.png</xsl:text></xsl:when>
							<xsl:when test="$id = 'F17904'"><xsl:text>retraite.png</xsl:text></xsl:when>
							<xsl:when test="$id = 'F17649'"><xsl:text>succession.png</xsl:text></xsl:when>
						</xsl:choose>
					</xsl:attribute>
				</img>
			</xsl:when>
			<xsl:when test="$CATEGORIE = 'associations'">
				<img width="40" height="40" alt="Associations">
					<xsl:attribute name="src">
						<xsl:value-of select="$IMAGES"/>
						<xsl:text>associations.png</xsl:text>
					</xsl:attribute>
				</img>
			</xsl:when>
			<xsl:when test="$CATEGORIE = 'entreprises'">
				<img width="40" height="40" alt="Entreprises">
					<xsl:attribute name="src">
						<xsl:value-of select="$IMAGES"/>
						<xsl:text>entreprises.jpg</xsl:text>
					</xsl:attribute>
				</img>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<!-- Renvoie l'image associée à une partie de la publication -->
	<xsl:template name="imageOfAPartie">
		<xsl:param name="nom"/>
		<img width="40" height="40" class="entiteImageFloatLeft">
			<xsl:attribute name="alt">
				<xsl:value-of select="$nom"/>
			</xsl:attribute>	
			<xsl:attribute name="src">
				<xsl:value-of select="$IMAGES"/>
				<xsl:value-of select="$nom"/>
				<xsl:text>.jpg</xsl:text>
			</xsl:attribute>
		</img>
	</xsl:template>

	<xsl:template name="createSommaireTheme" mode="Theme">
		<div class="spPublicationSommaire">
<!--
			<xsl:call-template name="imageOfAPartie">
				<xsl:with-param name="nom">sommaire</xsl:with-param>
			</xsl:call-template>
-->
			<h4 class="spip"><span>Sommaire</span></h4>
			<ul class="spPublicationSommaire">
				<xsl:if test="count(SousTheme) > 0">
					<xsl:for-each select="SousTheme">
						<xsl:variable name="title">
							<xsl:value-of select="../dc:title"/>
							<xsl:value-of select="$sepFilDAriane"/>
							<xsl:value-of select="Titre"/>
						</xsl:variable>
						<xsl:variable name="nbDossiers">
							<xsl:value-of select="count(Dossier)"/>
						</xsl:variable>
						<xsl:if test="$nbDossiers > 0">
							<li class="spPublicationSommaire">
				    			<a title="{$title}">
				    				<xsl:attribute name="href">
				    					<xsl:text>#</xsl:text>
				    					<xsl:call-template name="createThemeSousThemeId"/>
				    				</xsl:attribute>
				    				<xsl:value-of select="Titre"/>
				    			</a>
				    			<xsl:text> (</xsl:text>
				    			<xsl:value-of select="$nbDossiers"/>
				    			<xsl:text>)</xsl:text>
							</li>
						</xsl:if>
					</xsl:for-each>
				</xsl:if>
				<xsl:if test="count(Dossier) > 0">
					<xsl:for-each select="Dossier">
						<xsl:variable name="title">
							<xsl:value-of select="../dc:title"/>
							<xsl:value-of select="$sepFilDAriane"/>
							<xsl:value-of select="Titre"/>
						</xsl:variable>
						<xsl:variable name="nbFiches">
							<xsl:value-of select="count(Fiche)"/>
						</xsl:variable>
						<!--  <xsl:if test="$nbFiches > 0"> -->
							<li class="spPublicationSommaire">
				    			<a title="{$title}">
				    				<xsl:attribute name="href">
				    					<xsl:text>#</xsl:text>
				    					<xsl:call-template name="createThemeDossierId"/>
				    				</xsl:attribute>
				    				<xsl:value-of select="Titre"/>
				    			</a>
				    			<xsl:text> (</xsl:text>
				    			<xsl:value-of select="$nbFiches"/>
				    			<xsl:text>)</xsl:text>
							</li>
						<!--  </xsl:if> -->
					</xsl:for-each>
				</xsl:if>
				<xsl:if test="count(Actualite)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Actualités</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-actualite</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Actualités</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(Actualite)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(ServiceEnLigne)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Services en ligne</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">					
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-service-en-ligne</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Services en ligne</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(ServiceEnLigne)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(QuestionReponse)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Questions - réponses</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-question-reponse</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Questions - réponses</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(QuestionReponse)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(CentreDeContact)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Centres d'appel et de contact</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-centre-de-contact</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Centres d'appel et de contact</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(CentreDeContact)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(VoirAussi)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Voir aussi</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-voir-aussi</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Voir aussi</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(VoirAussi/Dossier)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(SiteInternetPublic)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Sites internet publics</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-site-internet-public</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Sites internet publics</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(SiteInternetPublic)"/>
		    			<xsl:text>)</xsl:text>

					</li>					
				</xsl:if>
			</ul>
		</div>		
	</xsl:template>

	<xsl:template name="createSommaireNoeud" mode="Noeud-dossier">
		<div class="spPublicationSommaire">
			<!--
			<xsl:call-template name="imageOfAPartie">
				<xsl:with-param name="nom">sommaire</xsl:with-param>
			</xsl:call-template>
			-->
			<h4 class="spip"><span>Sommaire</span></h4>
			<ul class="spPublicationSommaire">		
				<xsl:if test="Texte">			
					<xsl:for-each select="Texte/Chapitre">
						<xsl:if test="Titre">
							<xsl:variable name="title">
								<xsl:value-of select="../../dc:title"/>
								<xsl:value-of select="$sepFilDAriane"/>
								<xsl:value-of select="normalize-space(Titre)"/>
							</xsl:variable>
							<li class="spPublicationSommaire">
				    			<a title="{$title}">
				    				<xsl:attribute name="href">
				    					<xsl:text>#</xsl:text>
				    					<xsl:call-template name="createChapitreId"/>
				    				</xsl:attribute>
				    				<xsl:value-of select="Titre"/>
				    			</a>
							</li>
						</xsl:if>
					</xsl:for-each>
				</xsl:if>
				<xsl:if test="count(SousDossier) > 0">
					<xsl:for-each select="SousDossier">
						<xsl:variable name="title">
							<xsl:value-of select="../dc:title"/>
							<xsl:value-of select="$sepFilDAriane"/>
							<xsl:value-of select="Titre"/>
						</xsl:variable>
						<xsl:variable name="nbFiches">
							<xsl:value-of select="count(Fiche)"/>
						</xsl:variable>
						<xsl:if test="$nbFiches > 0">
							<li class="spPublicationSommaire">
				    			<a title="{$title}">
				    				<xsl:attribute name="href">
				    					<xsl:text>#</xsl:text>
				    					<xsl:call-template name="createSousDossierId"/>
				    				</xsl:attribute>
				    				<xsl:value-of select="Titre"/>
				    			</a>
				    			<xsl:text> (</xsl:text>
				    			<xsl:value-of select="$nbFiches"/>
				    			<xsl:text>)</xsl:text>
							</li>
						</xsl:if>
					</xsl:for-each>
				</xsl:if>
				<xsl:if test="count(Fiche) > 0">
					<xsl:for-each select="Fiche">
						<xsl:variable name="title">
							<xsl:value-of select="../dc:title"/>
							<xsl:value-of select="$sepFilDAriane"/>
							<xsl:value-of select="normalize-space(text())"/>
						</xsl:variable>
						<li class="spPublicationSommaire">
							<a title="{$title}">
								<xsl:attribute name="href">
									<xsl:text>#</xsl:text>
			    					<xsl:call-template name="createSousDossierId"/>
              					</xsl:attribute>
               					<xsl:value-of select="text()"/>  
							</a> 
				    		<!-- 
			    			<xsl:call-template name="getPublicationLink">
			    				<xsl:with-param name="href"><xsl:value-of select="@ID"/></xsl:with-param>
			    				<xsl:with-param name="title"><xsl:value-of select="$title"/></xsl:with-param>
			    				<xsl:with-param name="text"><xsl:value-of select="text()"/></xsl:with-param>
							</xsl:call-template>
							-->
						</li>
					</xsl:for-each>
				</xsl:if>
				<xsl:if test="count(OuSAdresser)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Ou s'adresser</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-ou-sadresser</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Ou s'adresser</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(OuSAdresser)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(Reference)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Références</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-reference</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Références</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(Reference)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(Partenaire)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Partenaires</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-partenaire</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Partenaires</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(Partenaire)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(Actualite)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Actualités</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-actualite</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Actualités</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(Actualite)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(InformationComplementaire)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Informations complémentaires</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-information-complementaire</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Informations complémentaires</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(InformationComplementaire)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(Montant)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Montants</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-montant</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Montants</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(Montant)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(ServiceEnLigne)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Services en ligne</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-service-en-ligne</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Services en ligne</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(ServiceEnLigne)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(QuestionReponse)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Questions - réponses</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-question-reponse</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Questions - réponses</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(QuestionReponse)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(PourEnSavoirPlus)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Pour en savoir plus</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-pour-en-savoir-plus</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Pour en savoir plus</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(PourEnSavoirPlus)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
				<xsl:if test="count(SiteInternetPublic)">
					<xsl:variable name="title">
						<xsl:value-of select="dc:title"/>
						<xsl:value-of select="$sepFilDAriane"/>
						<xsl:text>Sites internet publics</xsl:text>
					</xsl:variable>
					<li class="spPublicationSommaire">
		    			<a title="{$title}">
		    				<xsl:attribute name="href">
		    					<xsl:text>#sp-site-internet-public</xsl:text>
		    				</xsl:attribute>
		    				<xsl:text>Sites internet publics</xsl:text>
		    			</a>
		    			<xsl:text> (</xsl:text>
		    			<xsl:value-of select="count(SiteInternetPublic)"/>
		    			<xsl:text>)</xsl:text>
					</li>					
				</xsl:if>
			</ul>
		</div>		
	</xsl:template>

	<xsl:template name="createSommaireFiche" mode="Fiche">
		<xsl:if test="(count(Texte/Chapitre/Titre)+count(OuSAdresser)+count(Reference)+count(Actualite)+count(InformationComplementaire)+count(Montant)+count(ServiceEnLigne)+count(QuestionReponse)+count(EnSavoirPlus)+count(SiteInternetPublic)) > 0">
			<div class="spPublicationSommaire">
				<!--  
				<xsl:call-template name="imageOfAPartie">
					<xsl:with-param name="nom">sommaire</xsl:with-param>
				</xsl:call-template>
				-->
				<h4 class="spip">Sommaire</h4>
				<ul class="spPublicationSommaire">
					<xsl:for-each select="Texte/Chapitre">
						<xsl:if test="Titre">
							<xsl:variable name="title">
								<xsl:value-of select="../../dc:title"/>
								<xsl:value-of select="$sepFilDAriane"/>
								<xsl:value-of select="normalize-space(Titre)"/>
							</xsl:variable>
							<li class="spPublicationSommaire">				
				    			<a title="{$title}">
				    				<xsl:attribute name="href">
				    					<xsl:text>#</xsl:text>
				    					<xsl:call-template name="createChapitreId"/>
				    				</xsl:attribute>
				    				<xsl:value-of select="Titre"/>
				    			</a>
							</li>
						</xsl:if>
					</xsl:for-each>
					<xsl:if test="count(OuSAdresser)">
						<xsl:variable name="title">
							<xsl:value-of select="dc:title"/>
							<xsl:value-of select="$sepFilDAriane"/>
							<xsl:text>Ou s'adresser</xsl:text>
						</xsl:variable>
						<li class="spPublicationSommaire">
			    			<a title="{$title}">
			    				<xsl:attribute name="href">
			    					<xsl:text>#sp-ou-sadresser</xsl:text>
			    				</xsl:attribute>
			    				<xsl:text>Ou s'adresser</xsl:text>
			    			</a>
			    			<xsl:text> (</xsl:text>
			    			<xsl:value-of select="count(OuSAdresser)"/>
			    			<xsl:text>)</xsl:text>
						</li>					
					</xsl:if>
					<xsl:if test="count(Reference)">
						<xsl:variable name="title">
							<xsl:value-of select="dc:title"/>
							<xsl:value-of select="$sepFilDAriane"/>
							<xsl:text>Références</xsl:text>
						</xsl:variable>
						<li class="spPublicationSommaire">
			    			<a title="{$title}">
			    				<xsl:attribute name="href">
			    					<xsl:text>#sp-reference</xsl:text>
			    				</xsl:attribute>
			    				<xsl:text>Références</xsl:text>
			    			</a>
			    			<xsl:text> (</xsl:text>
			    			<xsl:value-of select="count(Reference)"/>
			    			<xsl:text>)</xsl:text>
						</li>					
					</xsl:if>
					<xsl:if test="count(Actualite)">
						<xsl:variable name="title">
							<xsl:value-of select="dc:title"/>
							<xsl:value-of select="$sepFilDAriane"/>
							<xsl:text>Actualités</xsl:text>
						</xsl:variable>
						<li class="spPublicationSommaire">
			    			<a title="{$title}">
			    				<xsl:attribute name="href">
			    					<xsl:text>#sp-actualite</xsl:text>
			    				</xsl:attribute>
			    				<xsl:text>Actualités</xsl:text>
			    			</a>
			    			<xsl:text> (</xsl:text>
			    			<xsl:value-of select="count(Actualite)"/>
			    			<xsl:text>)</xsl:text>
						</li>					
					</xsl:if>
					<xsl:if test="count(InformationComplementaire)">
						<xsl:variable name="title">
							<xsl:value-of select="dc:title"/>
							<xsl:value-of select="$sepFilDAriane"/>
							<xsl:text>Informations complémentaires</xsl:text>
						</xsl:variable>
						<li class="spPublicationSommaire">
			    			<a title="{$title}">
			    				<xsl:attribute name="href">
			    					<xsl:text>#sp-information-complementaire</xsl:text>
			    				</xsl:attribute>
			    				<xsl:text>Informations complémentaires</xsl:text>
			    			</a>
			    			<xsl:text> (</xsl:text>
			    			<xsl:value-of select="count(InformationComplementaire)"/>
			    			<xsl:text>)</xsl:text>
						</li>					
					</xsl:if>
					<xsl:if test="count(Montant)">
						<xsl:variable name="title">
							<xsl:value-of select="dc:title"/>
							<xsl:value-of select="$sepFilDAriane"/>
							<xsl:text>Montants</xsl:text>
						</xsl:variable>
						<li class="spPublicationSommaire">
			    			<a title="{$title}">
			    				<xsl:attribute name="href">
			    					<xsl:text>#sp-montant</xsl:text>
			    				</xsl:attribute>
			    				<xsl:text>Montants</xsl:text>
			    			</a>
			    			<xsl:text> (</xsl:text>
			    			<xsl:value-of select="count(Montant)"/>
			    			<xsl:text>)</xsl:text>
						</li>					
					</xsl:if>
					<xsl:if test="count(ServiceEnLigne)">
						<xsl:variable name="title">
							<xsl:value-of select="dc:title"/>
							<xsl:value-of select="$sepFilDAriane"/>
							<xsl:text>Services en ligne</xsl:text>
						</xsl:variable>
						<li class="spPublicationSommaire">
			    			<a title="{$title}">
			    				<xsl:attribute name="href">
			    					<xsl:text>#sp-service-en-ligne</xsl:text>
			    				</xsl:attribute>
			    				<xsl:text>Services en ligne</xsl:text>
			    			</a>
			    			<xsl:text> (</xsl:text>
			    			<xsl:value-of select="count(ServiceEnLigne)"/>
			    			<xsl:text>)</xsl:text>
						</li>					
					</xsl:if>
					<xsl:if test="count(QuestionReponse)">
						<xsl:variable name="title">
							<xsl:value-of select="dc:title"/>
							<xsl:value-of select="$sepFilDAriane"/>
							<xsl:text>Questions - réponses</xsl:text>
						</xsl:variable>
						<li class="spPublicationSommaire">
			    			<a title="{$title}">
			    				<xsl:attribute name="href">
			    					<xsl:text>#sp-question-reponse</xsl:text>
			    				</xsl:attribute>
			    				<xsl:text>Questions - réponses</xsl:text>
			    			</a>
			    			<xsl:text> (</xsl:text>
			    			<xsl:value-of select="count(QuestionReponse)"/>
			    			<xsl:text>)</xsl:text>
						</li>					
					</xsl:if>
					<xsl:if test="count(PourEnSavoirPlus)">
						<xsl:variable name="title">
							<xsl:value-of select="dc:title"/>
							<xsl:value-of select="$sepFilDAriane"/>
							<xsl:text>Pour en savoir plus</xsl:text>
						</xsl:variable>
						<li class="spPublicationSommaire">
			    			<a title="{$title}">
			    				<xsl:attribute name="href">
			    					<xsl:text>#sp-pour-en-savoir-plus</xsl:text>
			    				</xsl:attribute>
			    				<xsl:text>Pour en savoir plus</xsl:text>
			    			</a>
			    			<xsl:text> (</xsl:text>
			    			<xsl:value-of select="count(PourEnSavoirPlus)"/>
			    			<xsl:text>)</xsl:text>
						</li>					
					</xsl:if>
					<xsl:if test="count(SiteInternetPublic)">
						<xsl:variable name="title">
							<xsl:value-of select="dc:title"/>
							<xsl:value-of select="$sepFilDAriane"/>
							<xsl:text>Sites internet publics</xsl:text>
						</xsl:variable>
						<li class="spPublicationSommaire">
			    			<a title="{$title}">
			    				<xsl:attribute name="href">
			    					<xsl:text>#sp-site-internet-public</xsl:text>
			    				</xsl:attribute>
			    				<xsl:text>Sites internet publics</xsl:text>
			    			</a>
			    			<xsl:text> (</xsl:text>
			    			<xsl:value-of select="count(SiteInternetPublic)"/>
			    			<xsl:text>)</xsl:text>
						</li>					
					</xsl:if>
				</ul>
			</div>		
		</xsl:if>
	</xsl:template>

	<xsl:template name="createThemeSousThemeId">
		<xsl:text>sp-theme-sous-theme-</xsl:text>
		<xsl:call-template name="lowerCase">
			<xsl:with-param name="string">
				<xsl:call-template name="textWithoutAccent">
					<xsl:with-param name="string">
						<xsl:value-of select="Titre"/>
					</xsl:with-param>
				</xsl:call-template>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:template>
	
	<xsl:template name="createThemeDossierId">
		<xsl:text>sp-theme-dossier-</xsl:text>
		<xsl:call-template name="lowerCase">
			<xsl:with-param name="string">
				<xsl:call-template name="textWithoutAccent">
					<xsl:with-param name="string">
						<xsl:value-of select="Titre"/>
					</xsl:with-param>
				</xsl:call-template>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:template>

	<xsl:template name="createSousDossierId">
		<xsl:text>sp-noeud-dossier-</xsl:text>
		<xsl:call-template name="lowerCase">
			<xsl:with-param name="string">
				<xsl:call-template name="textWithoutAccent">
					<xsl:with-param name="string">
						<xsl:value-of select="text()"/>
					</xsl:with-param>
				</xsl:call-template>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:template>

	<xsl:template name="createChapitreId">
		<xsl:text>sp-chapitre-</xsl:text>
		<xsl:call-template name="lowerCase">
			<xsl:with-param name="string">
				<xsl:call-template name="textWithoutAccent">
					<xsl:with-param name="string">
						<xsl:value-of select="Titre/Paragraphe"/>
					</xsl:with-param>
				</xsl:call-template>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:template>

	<xsl:template name="textWithoutAccent">
		<xsl:param name="string"/>
		<xsl:variable name="stringFrom"> ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ</xsl:variable>
		<xsl:variable name="stringTo">-aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn</xsl:variable>
		<xsl:variable name="stringDel">"?',:%&#8217;&#8211;</xsl:variable>
	  	<xsl:variable name="twastring">
	  		<xsl:value-of select="translate(translate($string,$stringDel,''),$stringFrom,$stringTo)"/>
	  	</xsl:variable>
		<xsl:call-template name="string-replace">
			<xsl:with-param name="string"><xsl:value-of select="$twastring"/></xsl:with-param>
		</xsl:call-template>
	</xsl:template>

	<xsl:template name="string-replace">
		<xsl:param name="string"/>
		<xsl:variable name="nstring"><xsl:value-of select="normalize-space($string)"/></xsl:variable>
		<xsl:value-of select="$nstring"/>
	</xsl:template>
	
	<xsl:template name="upperCase">
		<xsl:param name="string"/>
		<xsl:value-of select="translate($string,'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')"/>
	</xsl:template>
	
	<xsl:template name="lowerCase">
		<xsl:param name="string"/>
		<xsl:value-of select="translate($string,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')"/>
	</xsl:template>

	<xsl:template name="getMAJDate">
		<xsl:variable name="date"><xsl:value-of select="//*/dc:date"/></xsl:variable>
		<xsl:text>Mis à jour le </xsl:text>
		<xsl:call-template name="transformRssDate">
			<xsl:with-param name="date">
				<xsl:value-of select="substring-after($date,' ')"/>
				<xsl:text>TZ</xsl:text>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:template>

	<xsl:template name="getMAJDateContributor">
		<xsl:call-template name="getMAJDate"/>
		<xsl:text> - </xsl:text>
		<xsl:value-of select="//*/dc:contributor"/>
	</xsl:template>

	<xsl:template name="ancreTop">
		<div class="clearall">
			<br class="clearall"/>
			<p class="retour">
			<a href="#colonne_contenu" title="Retour vers le haut de la page">
				<img class="entiteImageFloatRight" alt="Retour vers le haut de la page" >
					<xsl:attribute name="src">
						<xsl:value-of select="$IMAGES"/>
						<xsl:text>picto_haut.png</xsl:text>
					</xsl:attribute>					
				</img>
			</a>
			</p>
		</div>
	</xsl:template>
	
	<xsl:template name="createDossierAzId">
		<xsl:text>sp-dossieraz-</xsl:text>
		<xsl:call-template name="lowerCase">
			<xsl:with-param name="string"><xsl:value-of select="Titre"/></xsl:with-param>
		</xsl:call-template>
	</xsl:template>

	<xsl:template name="affDossiersAZ">
		<xsl:if test="$CATEGORIE = 'particuliers'">
			<div class="spTousDossiersAZ">
				<xsl:call-template name="imageOfAPartie">
					<xsl:with-param name="nom">dossiers</xsl:with-param>
				</xsl:call-template>
				<ul class="spTousDossiersAZ">
					<li class="spTousDossiersAZ">
						<xsl:call-template name="getPublicationLink">
			   				<xsl:with-param name="href"><xsl:text>Dossiersaz</xsl:text></xsl:with-param>
			   				<xsl:with-param name="title"><xsl:text>Tous les dossiers de A à Z</xsl:text></xsl:with-param>
			   				<xsl:with-param name="text"><xsl:text>Tous les dossiers classés par ordre alphabétique</xsl:text></xsl:with-param>
						</xsl:call-template>
					</li>
					<li class="spTousDossiersAZ">
						<xsl:call-template name="getPublicationLink">
			   				<xsl:with-param name="href"><xsl:text>Dossierscat</xsl:text></xsl:with-param>
			   				<xsl:with-param name="title"><xsl:text>Tous les dossiers de A à Z</xsl:text></xsl:with-param>
			   				<xsl:with-param name="text"><xsl:text>Tous les dossiers classés par thème</xsl:text></xsl:with-param>
						</xsl:call-template>
					</li>
				</ul>
			</div>
		</xsl:if>
	</xsl:template>

	<!--  Insère le tag XITI -->
	<xsl:template name="affiche_tag_xiti">
		<xsl:element name="div">
			<xsl:attribute name="id">xiti-logo</xsl:attribute>
			<xsl:element name="script">
				<xsl:attribute name="type">text/javascript</xsl:attribute>
				xtnv = document; //parent.document or top.document or document
				xtsd = "http://logi3";
				xtsite = "257817";
				xtn2 = "17"; // level 2 site
				xtpage = "<xsl:value-of select="$CODE_INSEE"/>::<xsl:value-of select="$TAG_ID"/>"; // page name - laisser les :: (creation de chapitres)
				xtdi = ""; //implication degree
			</xsl:element>
	
			<xsl:element name="script">
			<xsl:attribute name="type">text/javascript</xsl:attribute>
				<xsl:attribute name="src"><xsl:value-of select="$URL_SCRIPT_XITI" /></xsl:attribute>
				
			</xsl:element>
			<xsl:element name="noscript">
				<xsl:element name="div">
					<xsl:attribute name="id">xiti-logo-noscript</xsl:attribute>
					<xsl:element name="img">
						<xsl:attribute name="src">http://logi3.xiti.com/hit.xiti?s=257817&amp;s2=17&amp;p=<xsl:value-of select="$CODE_INSEE"/>::<xsl:value-of select="$TAG_ID"/>&amp;di=&amp;</xsl:attribute>
					</xsl:element>
				</xsl:element>
			</xsl:element>
		</xsl:element>
	</xsl:template>
</xsl:stylesheet>
