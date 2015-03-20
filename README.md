# Bedrock plugin control

Are you using some special plugins on your development machine, for example [Query Monitor](https://github.com/johnbillion/query-monitor)? 

The problem, as you probably already know, is that these development-related plugins are also deployed to production machine. And we don't need development plugins there.

A partial solution is to include these plugins in the `require-dev` part of `composer.json`. That way, they get installed only locally.  However, if you also deploy the database to the production server,they are still activated there.

This **must use** plugin activates or deactivates the development plugins based on the environment. The only requirement is to add a new variable `$BEDROCK_DEV_PLUGINS` into `config/application.php` in your Bedrock-powered web application and add this plugin into the `require` part of the `composer.json`.

## Examples

I usually use Query Monitor, Debug Bar Console, P3 Profiler and Rewrite Rules Inspector on my dev machine. My project's `composer.json` thus looks like this:

```
[...]
  "require": {
      "lamosty/bedrock-plugin-control": "~0.1.1"
  },
  "require-dev": {
        "wpackagist-plugin/query-monitor": "dev-trunk",
        "wpackagist-plugin/debug-bar-console": "dev-trunk",
        "wpackagist-plugin/rewrite-rules-inspector": "dev-trunk",
        "wpackagist-plugin/p3-profiler": "dev-trunk"
    }
  [...]
```

`config/application.php` will then look like this:
```
[...]
**
 * Plugins which get force-enabled in development environment.
 * Include them in your composer.json "require-dev" so they get installed only on dev machine.
 *
 * Specify relative path to plugin's main PHP file.
 */

$BEDROCK_DEV_PLUGINS = array(
	'query-monitor/query-monitor.php',
	'debug-bar-console/debug-bar-console.php',
	'p3-profiler/p3-profiler.php',
	'rewrite-rules-inspector/rewrite-rules-inspector.php'
);
```
