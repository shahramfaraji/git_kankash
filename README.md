# kankash rayaneh alvand

## Authors

-   [@mehrdadtaghizadeh](https://github.com/mehrdadtaghizadeh)

## vue.js

```bash
  npm install
  npm run dev
  npm run build
```

## php

```bash
  php artisan serve
  php artisan cache:clear
  php artisan config:clear
  php artisan view:clear

```

## xampp

```bash
  if you have problem for running mysql in xampp you should change mysql port for example port=3360

  if you want to run project in localhost
  1= copy all file project after npm run biuld in htdocs.
  2= add blew code to httpd-vhosts.conf for opening project with localhost

  <VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot "C:\xampp\htdocs\public"
    ServerName kankash.local
    ErrorLog "logs/your-project-name-error.log"
    CustomLog "logs/your-project-name-access.log" common
  </VirtualHost>

  3= you can add 127.0.0.1    kankash.local to hostfile in windows so that open with domain


note= for change mysql port you should go php.ini file
note= if you change port mysql, you most change port mysql number in .env file
note= if you change port mysql, you most change port mysql number in config.inc.php file that is C:\xampp\phpMyAdmin and add $cfg['Servers'][$i]['port'] = '3360';
```
