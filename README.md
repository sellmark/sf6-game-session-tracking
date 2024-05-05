# To start the project

```
cp _.env.local .env.local
docker compose up -d --build
docker compose exec app /bin/bash
bin/phpunit
```

Then start sending requests using examples from `example.http`, generate your Uuid in the GET route, then use returned UUID for POST requests
