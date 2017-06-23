# IdeaPar Project

/var/ideapar
ssh://git@git.ideapar.com:3222/kp/ideapar.git

## composer

* dev/composer.json php packages

```
cd dev
composer install
```

## nodejs

dev/front

### install nodejs (npm)

[Debian and Ubuntu based Linux distributions](https://nodejs.org/en/download/package-manager/#debian-and-ubuntu-based-linux-distributions)
```
curl -sL https://deb.nodesource.com/setup_4.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### prepare

```
sudo npm i -g gulp
sudo npm i -g bower
sudo npm i -g webpack-stream
sudo npm i -g phantomjs
```

tips: Listing globally installed NPM packages and version
```
npm list -g --depth=0
```

```
cd /ipar/ideapar/dev/front
npm i
bower i
```

## Nginx conf

/usr/loca/sites-available/ideapar.do.conf
```
server {
    listen    80;
    server_name    ideapar.do;
    return 301 $scheme://www.ideapar.do$request_uri;
}

server {
    listen  80;
    server_name *.ideapar.do;

    index   index.html index.php;
    root    /var/ideapar/site/public;

    access_log  /var/ideapar/log/access.log.gz combined gzip;
    error_log /var/ideapar/log/error.log;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }   

    location ~ \.php(/|$) {
        try_files $uri = 404;

        fastcgi_split_path_info ^(.+\.php)(/.*)$;

        include fastcgi.conf;

        fastcgi_connect_timeout 60;
        fastcgi_send_timeout 180;
        fastcgi_read_timeout 180;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;

        fastcgi_index   index.php;
        fastcgi_pass    127.0.0.1:9000;

        if ($http_origin ~* '^https?://[^/]+\.ideapar\.do$') {
            add_header Access-Control-Allow-Origin $http_origin;
            add_header Access-Control-Allow-Credentials true;
        }   

        location ~ /\.ht {
            deny all;
        }   
    }   
}

server {
    listen      80;
    server_name static.ideapar.do;

    index index.html index.htm;
    root /var/ideapar/site/static;

    access_log /var/ideapar/log/static.access.log.gz combined gzip;
    error_log /var/ideapar/log/static.error.log;

    client_max_body_size 20M;

    location / {
    }

    location ~* \.(eot|svg|ttf|woff|woff2)$ {
        #gzip_static on;
        if ($http_origin ~* '^https?://[^/]+\.ideapar\.do$') {
            add_header Access-Control-Allow-Origin $http_origin;
        }
    }

    location ~ /\.ht {
        deny all;
    }
}

server {
    listen      80;
    server_name page.ideapar.do;

    index index.html index.htm;
    root /var/ideapar/site/page;

    access_log /var/log/page.access.log.gz combined gzip;
    error_log /var/log/page.error.log;

    client_max_body_size 20M;

    location / {
    }

    location ~ /\.ht {
        deny all;
    }
}

```

## gulp

```
cd /var/ideapar/front

// app-ipar
gulp ipar-scss
gulp ipar-webpack

// app-admin
gulp admin-scss
gulp admin-webpack

// app-user
gulp user-scss
gulp user-webpack
```

## subtree

git subtree push

```
git subtree push --prefix=dev/lib/gap gap master
git subtree push --prefix=dev/lib/mars mars master
```

git subtree pull

```
git subtree pull --prefix=dev/lib/gap gap master --squash
git subtree pull --prefix=dev/lib/mars mars master --squash
```

test arc diff
