# Pinger

- [Installation](#installation)
- [Introduction](#introduction)
- [Basic usage](#basic-usage)

## Installation

`composer require raitocz/pinger`

## Introduction

**This project is under development, it is recommended to wait with usage for stable release (soon).**

Tiny PHP library used to ping desired URLs. You can use proxy list to ping as from different computer making this tool
handy when you wan to test the server load from different IPs. Use at your own risk.

The user agent is generated from random numbers so the server is tricked to be believing that each request came from
different computer (although from same IP if no proxy list specified).

This script is working well for example with unprotected polls for adding votes (ones where links are not generated
for each page reload) as this was the reason why it was created & later transformed to this library.

## Warnings

This script can also clog your computer fast as it is making each request as separate PHP process, so too fast settings
can flood your RAM. That's why the Wait time is in Seconds. Although you can set it for example to 0.000001 I highly 
don't recommend that. Setting it to 0.1 will flood free 16GB RAM in few seconds, for example.

Please also note the limitations of Windows systems for usleep() which is used: 
http://php.net/manual/en/function.usleep.php


## Basic usage

Simplest request to one url 100x each second:

```php
// As object:
$pinger = new Pinger();
$pinger->setUrls(array('http://localhost/'))
    ->setRepeat(100)
    ->setWait(1)
    ->start();
    
// As static method:
Pinger::run(array('http://localhost/'), 100, 1);
```

Here are examples of diferent modes (used only static method for shorter code):

#### Random mode (default)
```php
$urls = array(
    'http://localhost/1',
    'http://localhost/02',
    'http://localhost/003',
    'http://localhost/0004',    
);

Pinger::run($urls, 5, 1, Pinger::MODE_RANDOM);
```
result:
```text
ping: http://localhost/003
ping: http://localhost/003
ping: http://localhost/1
ping: http://localhost/003
ping: http://localhost/0004
ping: http://localhost/02
ping: http://localhost/003
ping: http://localhost/1
ping: http://localhost/1
ping: http://localhost/0004
ping: http://localhost/0004
ping: http://localhost/1
ping: http://localhost/0004
ping: http://localhost/02
ping: http://localhost/02
ping: http://localhost/02
```

#### Random mode No repeat
```php
$urls = array(
    'http://localhost/1',
    'http://localhost/02',
    'http://localhost/003',
    'http://localhost/0004',    
);

Pinger::run($urls, 5, 1, Pinger::MODE_RANDOM_NOREPEAT);
```
result:
```text
ping: http://localhost/02
ping: http://localhost/0004
ping: http://localhost/1
ping: http://localhost/02
ping: http://localhost/1
ping: http://localhost/003
ping: http://localhost/1
ping: http://localhost/02
ping: http://localhost/003
ping: http://localhost/0004
ping: http://localhost/02
ping: http://localhost/0004
ping: http://localhost/003
ping: http://localhost/0004
ping: http://localhost/1
ping: http://localhost/003
```

#### Batch URL
```php
$urls = array(
    'http://localhost/1',
    'http://localhost/02',
    'http://localhost/003',
    'http://localhost/0004',    
);

Pinger::run($urls, 5, 1, Pinger::MODE_BATCH_URL);
```
result:
```text
ping: http://localhost/1
ping: http://localhost/1
ping: http://localhost/1
ping: http://localhost/1
ping: http://localhost/1
ping: http://localhost/1
ping: http://localhost/02
ping: http://localhost/02
ping: http://localhost/02
ping: http://localhost/02
ping: http://localhost/02
ping: http://localhost/02
ping: http://localhost/003
ping: http://localhost/003
ping: http://localhost/003
ping: http://localhost/003
ping: http://localhost/003
ping: http://localhost/003
ping: http://localhost/0004
ping: http://localhost/0004
ping: http://localhost/0004
ping: http://localhost/0004
ping: http://localhost/0004
ping: http://localhost/0004
```

#### Batch Array
```php
$urls = array(
    'http://localhost/1',
    'http://localhost/02',
    'http://localhost/003',
    'http://localhost/0004',    
);

Pinger::run($urls, 5, 1, Pinger::MODE_BATCH_ARRAY);
```
result:
```text
ping: http://localhost/1
ping: http://localhost/02
ping: http://localhost/003
ping: http://localhost/0004
ping: http://localhost/1
ping: http://localhost/02
ping: http://localhost/003
ping: http://localhost/0004
ping: http://localhost/1
ping: http://localhost/02
ping: http://localhost/003
ping: http://localhost/0004
ping: http://localhost/1
ping: http://localhost/02
ping: http://localhost/003
ping: http://localhost/0004
ping: http://localhost/1
ping: http://localhost/02
ping: http://localhost/003
ping: http://localhost/0004
ping: http://localhost/1
ping: http://localhost/02
ping: http://localhost/003
ping: http://localhost/0004
```