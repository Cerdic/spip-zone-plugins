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
	rt_attr_timestamp     = date_indexation
	rt_attr_string        = uri

	rt_attr_json          = properties

	rt_attr_string        = signature

	dict = keywords

	morphology = stem_en, libstemmer_fr
}

```



# stopwords

Dans certains cas on peut vouloir employer un fichier de stopwords (http://sphinxsearch.com/docs/current.html#conf-stopwords) configuré depuis le SPIP.

Côté SPIP, le plugin Indexer offre la possibilité de définir cette liste en séparant les mots par des virgules, par exemple:
>  au, aux, avec, ce, ces, dans, de, des, du, elle, en, et, eux, il, je, la, le, leur, lui, ma, mais, me, même, mes, moi, mon, ne, nos, notre, nous, on, ou, par, pas, pour, qu, que, qui, sa, se, ses, son, sur, ta, te, tes, toi, ton, tu, un, une, vos, votre, vous, c, d, j, l, à, m, n, s, t, y, été, étée, étées, étés, étant, étante, étants, étantes, suis, es, est, sommes, êtes, sont, serai, seras, sera, serons, serez, seront, serais, serait, serions, seriez, seraient, étais, était, étions, étiez, étaient, fus, fut, fûmes, fûtes, furent, sois, soit, soyons, soyez, soient, fusse, fusses, fût, fussions, fussiez, fussent, ayant, ayante, ayantes, ayants, eu, eue, eues, eus, ai, as, avons, avez, ont, aurai, auras, aura, aurons, aurez, auront, aurais, aurait, aurions, auriez, auraient, avais, avait, avions, aviez, avaient, eut, eûmes, eûtes, eurent, aie, aies, ait, ayons, ayez, aient, eusse, eusses, eût, eussions, eussiez, eussent

Cette config est exposée à l’adresse
spip.php?page=indexer-config-stopwords.json
sous forme d’une liste texte des mots, en minuscules et sans accents.

On peut importer cette configuration dans la configuration de Sphinx:
```
$stopfile = '/var/lib/sphinxsearch/data/spip-stopwords.txt';
$stopwords = @json_decode(file_get_contents('[URL]/spip.php?page=indexer-config-stopwords.json'), true);
if (is_array($stopwords)) {
	($fp = fopen($stopfile, 'w'))
  && fwrite($fp, join("\n", $stopwords))
  && fclose($fp);
}
echo "stopwords = $stopfile\n";
```


