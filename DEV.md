## PHP (`php/`)

```bash
cd php
docker compose run --rm test
```

Rebuild after changing `composer.json`/`composer.lock` (`docker compose build test`) — the
container's `vendor/` lives in an anonymous volume and won't pick up new dependencies otherwise.