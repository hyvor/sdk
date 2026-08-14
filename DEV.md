## PHP (`php/`)

`php/` is a monorepo of Composer packages, one per publishable unit: `packages/core`
(`hyvor/sdk-core`, shared infra, not installed directly by consumers), `packages/talk`
(`hyvor/sdk-talk`), and `packages/post` (`hyvor/sdk-post`). The root `php/composer.json` is a
dev-only aggregator that installs all three from disk (via Composer path repositories) so the
whole suite can be built and tested in one command:

```bash
cd php

# first, run the container
docker compose up -d --build

# run tests (across all packages)
docker compose run --rm php ./vendor/bin/phpunit

# run phpstan (across all packages)
docker compose run --rm php ./vendor/bin/phpstan
```

To verify a single product package installs and tests standalone (with no dependency on the
other product package or the aggregator):

```bash
docker compose exec php sh -c "cd packages/talk && composer install && vendor/bin/phpunit"
docker compose exec php sh -c "cd packages/post && composer install && vendor/bin/phpunit"
```