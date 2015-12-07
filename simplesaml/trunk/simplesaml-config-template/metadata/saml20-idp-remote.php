<?php

// Le contenu du fichier peut être généré automatiquement
// par simplesamlphp à partir de l'URL metadata du fournisseur d'identité.
// http://votre-domaine.tld/simplesaml/admin/metadata-converter.php

$metadata['https://connexion-domaine.tld/idp/saml2/metadata'] = array (
  'entityid' => 'https://connexion-domaine.tld/idp/saml2/metadata',
  'contacts' => 
  array (
  ),
  'metadata-set' => 'saml20-idp-remote',
  'SingleSignOnService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://connexion-domaine.tld/idp/saml2/sso',
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://connexion-domaine.tld/idp/saml2/sso',
    ),
  ),
  'SingleLogoutService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://connexion-domaine.tld/idp/saml2/slo',
      'ResponseLocation' => 'https://connexion-domaine.tld/idp/saml2/slo_return',
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://connexion-domaine.tld/idp/saml2/slo',
      'ResponseLocation' => 'https://connexion-domaine.tld/idp/saml2/slo_return',
    ),
    2 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
      'Location' => 'https://connexion-domaine.tld/idp/saml2/slo/soap',
    ),
  ),
  'ArtifactResolutionService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
      'Location' => 'https://connexion-domaine.tld/idp/saml2/artifact',
      'index' => 0,
    ),
  ),
  'NameIDFormats' => 
  array (
  ),
  'keys' => 
  array (
    0 => 
    array (
      'encryption' => true,
      'signing' => true,
      'type' => 'X509Certificate',
      'X509Certificate' => 'MIICNjCCAZ+gAwIBAgIJAIZwTFbJkKkCMA0GCSqGSIb3DQEBBQUAMDQxMjAwBgNV
BAMMKWNvbm5leGlvbi1hbGZvcnR2aWxsZS50ZXN0LmVudHJvdXZlcnQub3JnMB4X
DTE1MTAyMTA3NTQyOFoXDTI1MTAyMDA3NTQyOFowNDEyMDAGA1UEAwwpY29ubmV4
aW9uLWFsZm9ydHZpbGxlLnRlc3QuZW50cm91dmVydC5vcmcwgZ8wDQYJKoZIhvcN
AQEBBQADgY0AMIGJAoGBAL+Kujl97kKpz5m9lKlrJZhiRCfyzpG59nQKB53Qxl6d
PUKSuLAWembsqbm+5FsQbt2TI1qVqtzYZ6evCRTYe6FogAZWX3W8Al6exR6FhraF
GeTcNF5+0wodb5HgIuSH3GGMsRtgw/uAkCgInm1GP+tadzgLnWQgMFiaPi/W6tI/
AgMBAAGjUDBOMB0GA1UdDgQWBBRUqAqWAcjVWhS/j2OQpGKPc7kKaDAfBgNVHSME
GDAWgBRUqAqWAcjVWhS/j2OQpGKPc7kKaDAMBgNVHRMEBTADAQH/MA0GCSqGSIb3
DQEBBQUAA4GBAF4QjvD2u7joy7CJbBssIeibwV8FdSouCKJyXc1pA15O7O5CI9To
saOdQmFY12kv+ufZdRA5+u/9rUYBTQ74pM5RAecLvNuK/nJNqtVUXj28fcmxng6u
JkQ021HNLnN8eBJ5EFOhbPhWkluHJoSRpzW8sYKyNBtqLo387E2vgAD2',
    ),
  ),
);
