<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:template match="/">
    <html>
      <head><title>RSS kanál</title></head>
      <body>
        <h2><xsl:value-of select="rss/channel/title"/></h2>
        <ul>
          <xsl:for-each select="rss/channel/item">
            <li>
              <a href="{link}"><xsl:value-of select="title"/></a><br/>
              <xsl:value-of select="description"/>
            </li>
          </xsl:for-each>
        </ul>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
 
