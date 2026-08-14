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

## JavaScript/TypeScript (`js/`)

`js/` is an npm workspaces monorepo, one package per publishable unit: `packages/core`
(`@hyvor/sdk-core`, shared infra, not installed directly by consumers), `packages/talk`
(`@hyvor/sdk-talk`), and `packages/post` (`@hyvor/sdk-post`). The root `js/package.json` is a
dev-only aggregator (`"private": true`, no version) that links all three from disk (via npm
workspaces) so the whole suite can be built and tested in one command:

```bash
cd js

# first, run the container
docker compose up -d --build

# build (typecheck + emit) every package
docker compose exec node npm run build

# run tests (across all packages)
docker compose exec node npm run test

# typecheck only, no emit
docker compose exec node npm run typecheck
```

To verify a single product package installs and tests standalone (with no dependency on the
other product package or the aggregator):

```bash
docker compose exec node sh -c "cd packages/talk && npm install && npm test"
docker compose exec node sh -c "cd packages/post && npm install && npm test"
```

## Generator (`generator/`)

Both the PHP and JS SDKs' product packages (everything under `Dto/`, `Org/`, `{Resource}/`, plus
the resource/product client classes) are generated from `openapi/*.json` by `generator/`, not
hand-written - see `generator.md`/`tmp/generator.md` for how it was built. Hand-written code
(`php/packages/core`, `js/packages/core`, and the product client's own doc comment/constructor
shape) is never touched by the generator.

```bash
cd generator
npm install

npm run generate              # PHP: writes to tmp/{Talk,Post} by default
npm run generate -- ../php/packages/talk/src talk   # or straight into a package's src/

npm run generate:ts           # TS: writes to tmp/js/{Talk,Post} by default
npm run generate:ts -- ../js/packages/talk/src talk # or straight into a package's src/
```

Regenerating into a package's `src/` overwrites it entirely (the generator `rm -rf`s the product's
output directory first) - re-run it and commit the diff rather than hand-editing generated files.