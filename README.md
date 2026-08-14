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

## Development

See [DEV.md](DEV.md) for running each language's test suite (with or without Docker).

## License

MIT - see [LICENSE](LICENSE).
