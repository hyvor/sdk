## PHP (`php/`)

```bash
cd php

# first, run the container
docker compose up -d --build

# run tests
docker compose exec php ./vendor/bin/phpunit

# run phpstan
docker compose exec php ./vendor/bin/phpstan
```