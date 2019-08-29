# PHP Raspberry Pi GPIO Wrapper
This library defines a php stream wrapper that allows you to access the gpio interface on your raspberry pi device.  
## Install
```bin
$ composer require tasoft/gpio-wrapper
```
## Usage
```php
\TASoft\GPIO\LiveGPIOWrapper::register();

// Now all commands are available using protocol gpio://

file_put_contents("gpio://export", 24);
// Exports pin 24 and make it available here

echo file_get_contents("gpio://gpio24/direction"); // in or out

echo file_get_contents("gpio://gpio24/value"); // 1 or 0
```