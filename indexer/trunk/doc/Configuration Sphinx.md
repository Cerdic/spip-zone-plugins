# Configuration de Sphinx pour l'index

```

index spip {
	type = rt
	path = /var/lib/sphinxsearch/data/spip

	rt_field              = title
	rt_attr_string        = title

	rt_field              = summary
	rt_attr_string        = summary

	rt_field              = content
	rt_attr_string        = content

	rt_attr_timestamp     = date
	rt_attr_string        = uri

	rt_attr_json          = properties

	dict = keywords
}

```
