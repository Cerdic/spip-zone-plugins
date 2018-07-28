# Sanitizer
This plugins prevents user to insert script tags into the content of an article. It checks chapo, ps, text, desciptif with the safehtml function for  	malicious code fragments. All other html tags are still allowed.

## Usage
Just activate the plugin and clear the cache. After that the plugins filter the inputs

## Why
This plugin is the (temporary) answer to a white hat attacker on one of the mutu site. Because spip trust the user he can insert javascript. The moderator the execute this javascript if he press preview.