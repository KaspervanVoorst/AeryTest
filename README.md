# Symfony Assessment

I started from a clone of https://github.com/dunglas/symfony-docker for the docker configuration.

From that README:

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up --pull always -d --wait` to set up and start a fresh Symfony project
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

To populate the database, Messengers will have to be consumed:
1. `docker compose exec php bash`
2. `php bin/console messenger:consume`

(Or add `"messenger:setup-transports": "symfony-cmd"` to the `auto-scripts` section of `composer.json` - it was a lot more convenient to start and stop these manually during development)

As this is a REST API, a home page has not been set up, only API routes.
The routes and their parameters and responses can be found at `/api/doc/`.

Some process notes:

* I used the OpenLibrary API as an example API implementation to work with. OpenLibrary uses Solr as its search engine and as such accepts Solr queries, 
  which aren't suitable for exact queries to a Doctrine database. My workaround for this is not particularly robust (it was, however, very fast to implement).
* Symfony Flex was very convenient for setting things up here
* I probably didn't need to add PHPDocs everywhere when everything is type-hinted, but I suppose old PHP7 habits die hard
* As far as I can tell from the documentation, the OpenLibrary API doesn't actually have a rate limit. I set the current retry time to 5 minutes based on the rate limit of their Cover API
  (However, that API returns a 403 FORBIDDEN response instead of a 429 TOO MANY REQUESTS response when the rate limit is exceeded, which would make it difficult to decide whether to retry).
  Therefore, the rate limit handling in this assessment is essentially arbitrary (I did test it by hardcoding the exception, though).
