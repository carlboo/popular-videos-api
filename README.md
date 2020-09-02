# Popular Videos API

This is a demo app that collects data from YouTube API, in convination with information from Wikipedia per country, including the following: uk,nl,de,fr,es,it,gr.
It is based on [Lumen PHP Framework](https://lumen.laravel.com)

## Installation

The project includes the basic settings to run with Docker, using docker-compose. Both need to be installed.
First build and start the project
```shell
docker-compose up
```

Then install the project dependencies via Composer
```shell
docker-compose run --rm composer install
```

After we can set the settings for our project, A valid key for Google API with access to YouTube Data API is needed.
```shell
cp .env.example .env
```
And inside the `.env` file, add the API token to the variable.
```shell
GOOGLE_API_KEY=[your_key]
```

Now we can create the database table for failed jobs and start processing jobs on the queue
```shell
docker-compose exec -T php-fpm php artisan migrate
docker-compose exec -T php-fpm php artisan queue:work
```

This project can also be executed as a lumen application outside Docker, for more info read the docs.

## How to use
Request the data to `http://localhost/api/v1/popular-videos`. The application will start collecting the data and saving into the cache, visit it a couple of times and it should be available after processing.

A couple of parameters are available to use as url query string: `offset` and `size` for page size (countries per page)