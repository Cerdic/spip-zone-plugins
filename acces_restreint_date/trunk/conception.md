# Accès restreint par date

## Base

| spip_zones_dates    | |
|---------------------|-|
| id_zones_date : int | |
| objet : string      | |
| id_objet : int      | |
| id_zone : int       | |
| quand : string      | |
| duree : int         | |
| periode : string    | |

```php
array(
	'id_zone' => 1,
	'objet' => 'rubrique',
	'id_objet' => 123,
	'id_zone' => 1,
	'quand' => 'apres',
	'duree' => 3,
	'periode' => 'mois',
)
```

## Requête attendue

AND (
	articles.id_rubrique IN (123)
	and articles.date < XXXX.XX.XX
)
AND (
	articles.id_rubrique IN (321)
	and articles.date < XXXX.XX.XX
)
