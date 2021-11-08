## About BookStore

Simple but powerfull Rest Api Playground.

Bookstore Api implements Json Api Specification https://jsonapi.org/

- [Made with Laravel](https://laravel.com).
- [Json Api Specification](https://jsonapi.org).
- [Demo](https://playground-bookstore.herokuapp.com).

Deploy to Heroku

- [![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/carlosvazquez/bookstore-api/tree/main)

- [Laravel at Heroku](https://devcenter.heroku.com/articles/getting-started-with-laravel)

Run the following commands from your terminal:

`heroku config:set APP_NAME="Bookstore Api"`

`heroku config:set APP_ENV=production`

`heroku config:set APP_KEY="Your own key"`

`heroku config:set APP_DEBUG=false`

`heroku config:set APP_URL="heroku domain"`

`heroku config:set LOG_CHANNEL=stack`

`heroku config:set DB_CONNECTION=pgsql`

`heroku config:set DB_HOST="From Heroku DB credentials"`

`heroku config:set DB_PORT=5432`

`heroku config:set DB_DATABASE="From Heroku DB credentials"`

`heroku config:set DB_USERNAME="From Heroku DB credentials"`

`heroku config:set DB_PASSWORD="From Heroku DB credentials"`

`heroku config:set BROADCAST_DRIVER=log`

`heroku config:set CACHE_DRIVER=file`

`heroku config:set QUEUE_CONNECTION=sync`

`heroku config:set SESSION_DRIVER=file`

`heroku config:set SESSION_LIFETIME=120`

`heroku config:set MAIL_MAILER=smtp`

`heroku config:set MAIL_HOST=smtp.mailtrap.io`

`heroku config:set MAIL_PORT=2525`

`heroku config:set MAIL_USERNAME=null`

`heroku config:set MAIL_PASSWORD=null`

`heroku config:set MAIL_ENCRYPTION=null`

`heroku config:set MAIL_FROM_ADDRESS=null`

`heroku config:set MAIL_FROM_NAME="${APP_NAME}"`
