## Pilulka - Twitter Mentions

An assignment from Pilulka for a PHP developer job interview.

Shows up to 100 Tweets where Pilulka is mentioned either by hashtags (#pilulka, #pilulkacz), or by a link to [pilulka.cz](https://www.pilulka.cz/)

## Frameworks used

Laravel 8.82 with Bootstrap 5.1.3. 

## Server Installation

This small installation tutorial is going to apply to Ubuntu 20.04 LTS. 

### Requirements

- PHP 8.0 with extensions matching [extension requirements](https://laravel.com/docs/8.x/deployment#server-requirements) of Laravel 8.
- NPM 8.3.1, Node 17.4.0 (best installed through [NVM](https://github.com/nvm-sh/nvm))
- Composer 2.* - [installation instructions](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)

You can use any web server you like. Here, I opted for **nginx**.

We need to add `ppa:ondrej/php` repository for PHP 8 packages.

```console
# add-apt-repository ppa:ondrej/php
# apt update
```

Install nginx:

```console
# apt install nginx
```

Install PHP-FPM (PHP 8) with all the required PHP extensions. 

```console
# apt install php8.0-fpm php8.0-common php8.0-dom php8.0-curl php8.0-mbstring php8.0-xml php8.0-zip
```

After the web server and PHP-FPM installation is complete, we need to configure nginx.

Open `/etc/nginx/sites-available/default` and copy the config below in it:

```
# Default server configuration
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    
    root /var/www/pilulka/public;
    index index.php index.html index.htm index.nginx-debian.html;
    server_name pill.avdonin.cz;

    # Pretty URLs
    location / {
        try_files $uri /index.php?$query_string;
    }

    # pass PHP scripts to FastCGI server
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;

        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # deny access to .htaccess files, if Apache's document root
    # concurs with nginx's one
    location ~ /\.ht {
        deny all;
    }
}
```

`root` should correspond with the public directory of the project. Example:
```
root /var/www/pilulka/public;
```

`server_name` should correspond with the domain you have pointed to the server running the project. Example:
```
server_name pill.avdonin.cz;
```

Make sure you reset nginx after changing the config by running:

```
# nginx -s reload
```

## Project Installation

Clone the GIT repository in your web server's root directory. Example: 

```
# cd /var/www
# git clone https://github.com/amike91/pilulka-twitter-mention.git
```

Rename the directory:

```
# mv pilulka-twitter-mention/ pilulka
```

Rename the `.env.example` file to `.env`

```
# cd /var/www/pilulka
# mv .env.example .env
```

Now we need to set a few environment variables.

Open the `.env` file in your favorite text editor and set the following environment variables to their respective values:

- `APP_ENV=production`
- `APP_DEBUG=false`

If you already have Twitter API v2 Bearer Token, you can set it in the `.env` file like that:

- `TWITTER_API_BEARER_TOKEN=<your Bearer token>`

If not, continue reading.

### Composer

Run Composer from the project root to install all the necessary PHP packages:

```
# composer install
```

### NPM

Run NPM from the project root to install all the necessary JS packages:

```
# npm install
```

### Compiling JavaScript assets

```
# npm run prod
```

### Storage directory

The directory called `storage` is used by Laravel, among other things, to store cached views, logs and so on. We need to have it writeable by the web server. In case of Ubuntu 20.04, by default, `nginx` is installed under `www-data` user. Given that, let's recursively change ownership of the `storage` directory by running:

```
# cd /var/www/pilulka
# chown -R www-data:www-data storage
```

This concludes the installation.

## Twitter API v2

To get the latest tweets, [Recent tweets](https://developer.twitter.com/en/docs/twitter-api/tweets/search/api-reference/get-tweets-search-recent) endpoint is used. It accepts a mandatory query it is going to select the tweets by, and some optional fields to enhance the resulting response.

In our case, we use:

```php
$params             = [
    'query'             => '#pilulka OR #pilulkacz OR (has:links pilulka.cz)',
    'max_results'       => 100,
    'tweet.fields'      => 'created_at',
    'expansions'        => 'author_id',
    'user.fields'       => 'created_at,name,profile_image_url,verified',
];
```

The query is going to select tweets where Pilulka hashtags and links to Pilulka's website are mentioned. It is going to return up to 100 results, which is the API's hard limit. 

We enhance our query by forcing it to return the date tweet was posted:

```php
    ...
    'tweet.fields'      => 'created_at',
    ...
```

and who was the tweets author:

```php
    ...
    'expansions'        => 'author_id',
    ...
```

Twitter API v2 is going to return `includes` array in its response. In it, there is going to be the `users` array because we forced the API to return them by passing in `author_id` to `expansions`.

The following tells the Twitter API v2 what fields to return in the `users` array.

```php
    ...
    'user.fields'       => 'created_at,name,profile_image_url,verified',
    ...
```

One by one:

- `created_at` - date profile (user) was created
- `name` - full, "nice name" of the profile
- `profile_image_url` - URL to the profile avatar (the image resolution is very low - 48x48 px. To get a larger image, remove `_normal` from the URL)
- `verified` - tells us whether the profile has the blue verification checkmark

### Getting Bearer Token

Getting the very basic access to the Twitter API v2 is quite straightforward. You are going to need to have/create a Twitter account. Then, you have to sign up for the [developer account](https://developer.twitter.com/en/docs/twitter-api/getting-started/getting-access-to-the-twitter-api). [Essential access](https://developer.twitter.com/en/docs/twitter-api/getting-started/about-twitter-api) is enough for our use case.

You then need to create a new project and get the Bearer Token. Be advised that once you have it, store it somewhere safe. You can't get to it afterwards. If you lose it, just regenerate it from [your dashboard](https://developer.twitter.com/en/portal/dashboard)

Once you have the Bearer Token, add it to the `.env` file like this:

```
TWITTER_API_BEARER_TOKEN=<your Bearer token>
```

## Usage

For GUI version, go to `/pilulka/twitter-mentions`.

## REST API

Endpoint: `/api/pilulka/twitter-mentions`

### Responses

200 OK

```json
{
  "success": true,
  "message": "Twitter mentions of Pilulka for the last 7 days",
  "payload": [
    {
      "id": "1490354949301100560",
      "text": "https://t.co/nKuTcSX1y2",
      "created_at": "2022-02-06T16:01:24.000000Z",
      "author": {
        "id": "1490354564427653125",
        "username": "CHEBBIMoez7",
        "nice_name": "CHEBBI Moez",
        "verified": false,
        "avatar_url": "https://pbs.twimg.com/profile_images/1490354649391681549/IQRbUF0e_normal.jpg",
        "created_at": "2022-02-06T16:00:12.000000Z"
      }
    },
    {
      "id": "1489743341457747968",
      "text": "dobre to robí tá #czech #pilulka https://t.co/8J4MF4q6rV",
      "created_at": "2022-02-04T23:31:05.000000Z",
      "author": {
        "id": "1015415083",
        "username": "pushcatch",
        "nice_name": "pushcatch",
        "verified": false,
        "avatar_url": "https://pbs.twimg.com/profile_images/1481015148718571525/dmVJC6CR_normal.jpg",
        "created_at": "2012-12-16T15:12:28.000000Z"
      }
    },
      
    ...
  ]
}
```

500 Error

```json
{
  "success": false,
  "message": "Error fetching mentions from Twitter. Try checking authentication Bearer token."
}
```
