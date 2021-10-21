# Mobile Ticker Web Application
This web application was made as a test to using a PHP
interface with a MySQL Backend. Information such as the
name and price is parsed from Yahoo Finance.
## TO SET UP:
Requires a  Apache Web Server with PHP Installed
Use Config.php in the root and tools directory, configuring
this to a MYSQL Server.
Two SQL Files are provided to set up the database with the 
table names 'users' for login information and 'stocks' for
holding the stock data.
## Requirements
This package requires composer installed and the QR Library which
can be installed by the command.

This package also requires Version 6+ of NodeJS in order to properly function.

Remember to enable the extension gd (or gd2 depending on PHP Version)
in the php.ini config file.

```
composer require chillerlan/php-qrcode
```