# HYVOR SDKs

Official SDKs for HYVOR products (Talk, Blogs, Post, Relay) - one package per product, per
language, so you only install what you use. See [sdk.md](sdk.md) for the cross-language design
and conventions every SDK follows.

## Structure

```
openapi/      API specs (*.json) - the source of truth every SDK is generated from
generator/    the code generator: openapi/*.json -> php/, js/ SDK source
php/          PHP SDK - Composer packages (hyvor/sdk-core, hyvor/sdk-talk, hyvor/sdk-post, ...)
js/           JS/TS SDK - npm packages (@hyvor/sdk-core, @hyvor/sdk-talk, @hyvor/sdk-post, ...)
python/       Python SDK (not started yet)
sdk.md        cross-language SDK design/conventions
DEV.md        per-language dev/test workflow
```

## Install

```bash
composer require hyvor/sdk-talk   # PHP
npm install @hyvor/sdk-talk       # JS/TS
```

See [php/README.md](php/README.md) / [js/README.md](js/README.md) for usage.

## Generating the SDKs

Every product package's client/resource/DTO code (everything except each language's hand-written
`core` package) is generated from `openapi/*.json`, not hand-written. To add a new endpoint or
product, or pick up a spec change:

1. Add or edit the relevant `openapi/{product}.json`. Endpoints need a `group:{name}` tag (and
   `org-endpoint` if it's org-level, not scoped to one resource) and a `method:{name}` tag - see
   [sdk.md](sdk.md) for the full set of conventions the generator relies on.
2. Run the generator:

   ```bash
   cd generator
   npm install

   npm run generate:php            # regenerate every PHP product, in place
   npm run generate:ts             # regenerate every TS product, in place
   npm run generate:all            # both languages

   npm run generate:php -- talk    # just one product
   npm run generate:php -- --tmp   # preview under generator/tmp/php instead of touching php/
   ```

   Output only ever goes to one of two places: straight into the real package (`{php,js}/packages/{product}/src`,
   the default), or `generator/tmp/{php,js}` for review (`--tmp`). Regenerating overwrites a
   product's output directory entirely - commit the diff rather than hand-editing generated files.
3. Review the diff and run the affected language's tests (see [DEV.md](DEV.md)).

## Automation

Merging to `main` in `hyvor/post`, `hyvor/talk` or `hyvor/relay` automatically opens a PR here with
the updated spec and regenerated SDKs:

```
product repo merge to main
  -> openapi-export.yml: dump spec from Symfony -> commit openapi.json -> repository_dispatch
  -> this repo, sync-openapi.yml: fetch openapi.json -> npm run generate:all -> open PR
```

### Setting up a product repo

1. **Spec generation.** The Symfony app must be able to dump its spec without a database or
   network (we use NelmioApiDocBundle: `bin/console nelmio:apidoc:dump --format=json`). Endpoints
   need the `group:` / `method:` tags described in [sdk.md](sdk.md). Check the dump works locally.
2. **Workflow.** Copy [automation/openapi-export.yml](automation/openapi-export.yml) to
   `.github/workflows/openapi-export.yml` in the product repo and set `PRODUCT`. On every push to
   `main` it regenerates `openapi.json` (committed at the repo root, so the spec is reviewable
   and diffable), and if it changed, notifies this repo.
3. **Secret.** Create a fine-grained PAT (or GitHub App token) with `contents: write` on the
   product repo, `contents: read` on the product repos, and `contents: write` + `pull requests: write`
   on `hyvor/sdk`. Add it as `SDK_SYNC_TOKEN` in the product repo and in this repo. (A PAT is
   needed rather than `GITHUB_TOKEN` so the PR created here triggers the test workflows.)
4. If `main` is branch-protected in the product repo, allow the bot/PAT owner to bypass it for the
   `openapi.json` commit, or drop the commit step and instead upload the spec as a build artifact.

Run a sync manually from the **Sync OpenAPI spec & regenerate SDKs** workflow (Actions tab,
*Run workflow*). The PR must still be reviewed and merged by a human; publishing is separate
(see the `*-publish.yml` workflows).

## Development

See [DEV.md](DEV.md) for running each language's test suite (with or without Docker).

## License

MIT - see [LICENSE](LICENSE).
