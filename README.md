# plg_editors-xtd_qlinsert

Joomla! extension. The editor button below inserts content into your article. You can define, what you wish.

You can use it for insertion of:

- your favourite plugin tag(s)
- dummie text

## Tests

Unit tests (PHPUnit) live in `tests/`. They run in Docker against the Joomla libraries of a real Joomla 6 installation, by default the site this plugin sits in (`../../..`):

```bash
docker compose run --rm test
# other Joomla install:
JOOMLA_ROOT=/path/to/joomla docker compose run --rm test

# a single test class or test
docker compose run --rm test vendor/bin/phpunit --filter QlinsertTest
docker compose run --rm test vendor/bin/phpunit --filter testAjaxRejectsGuest

# a different Joomla installation
JOOMLA_ROOT=/path/to/joomla docker compose run --rm test
```

## What about a coffee ..

I love coding. My extensions are for free. Wanna say thanks? You're welcome! 
<https://www.buymeacoffee.com/mareikeRiegel>
