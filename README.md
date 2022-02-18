# General notes
* Twig
	* The app supports twig templates, although its not used it can be added to `app\Views` and called from resources
* React app can be found at assests/reactapp

# Instructions for deployment

## Backend
1. Clone the git repo
2. Create a MySQL database
3. Copy .env.example to .env and change MySQL URI
4. Run `composer install` on root directroy
5. Run `./vendor/bin/doctrine-migrations migrations:migrate` to execute database migrations
6. point your webserver to public directory and configure it so it passes all requests to `index.php`

### Nginx config example
```
location / {
	try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
	include snippets/fastcgi-php.conf;
	fastcgi_pass unix:/run/php/php8.2-fpm.sock;
}
```

## Frontned
1. Clone the git repo
2. change directory to `assests/reactapp`
3. Edit `.env.production` and change backend URL
4. Run `npm install`
5. Run `npm run build`
6. point your webserver to dist directory and configure it so it passess all requests to `index.html`

### Nginx config example
```
location / {
	try_files $uri $uri/ /index.html?$query_string;
}
```
