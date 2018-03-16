PHP Spider
==========
_URL spider which crawls a page and all its subpages_

* [Installation](#installation)
* [Usage](#usage)
* [Processors](#processors)
* [URL Handlers](#url-handlers)
* [Alternatives](#alternatives)

Installation
------------

Make sure you have [Composer] installed. Then execute:

    composer require baqend/spider
    
This package requires at least **PHP 5.5.9** and has **no package dependencies!**


Usage
-----

The entry point is the `Spider` class. For it to work, it requires the following services:

* **Queue:** Collects URLs to be processed. This package comes with a breadth-first and a depth-first implementation.
* **URL Handler:** Checks if a URL should be processed. If no URL handler is provided, every URL is processed. [More about URL handlers](#url-handlers) 
* **Downloader:** Takes URLs and downloads them. To have no dependency on a HTTP client library like [Guzzle], you have to implement this class by yourself.
* **Processor:** Retrieves downloaded assets and performs operations on it. [More about Processors](#processors) 

You initialize the spider in the following way:

```php
<?php
use Baqend\Component\Spider\Processor;
use Baqend\Component\Spider\Queue\BreadthQueue;
use Baqend\Component\Spider\Spider;
use Baqend\Component\Spider\UrlHandler\BlacklistUrlHandler;

// Use the breadth-first queue
$queue = new BreadthQueue();

// Implement the DownloaderInterface
$downloader /* your downloader implementation */;

// Create a URL handler, e.g. the provided blacklist URL handler
$urlHandler = new BlacklistUrlHandler(['**.php']);

// Create some processors which will be executed after another
// More details on the processors below!
$processor = new Processor\Processor();
$processor->addProcessor(new Processor\UrlRewriteProcessor('https://example.org', 'https://example.com/archive'));
$processor->addProcessor($cssProcessor = new Processor\CssProcessor());
$processor->addProcessor(new Processor\HtmlProcessor($cssProcessor));
$processor->addProcessor(new Processor\ReplaceProcessor('https://example.org', 'https://example.com/archive'));
$processor->addProcessor(new Processor\StoreProcessor('https://example.com/archive', '/tmp/output'));

// Create the spider instance
$spider = new Spider($queue, $downloader, $urlHandler, $processor);

// Enqueue some URLs
$spider->queue('https://example.org/index.html');
$spider->queue('https://example.org/news/other-landingpage.html');

// Execute the crawling
$spider->crawl();
``` 


Processors
----------

This package comes with the following built-in processors.

### `Processor`

This is an aggregate processor which allows adding and removing other processors which it will execute one after the other.

```php
<?php
use Baqend\Component\Spider\Processor\Processor;

$processor = new Processor();
$processor->addProcessor($firstProcessor);
$processor->addProcessor($secondProcessor);
$processor->addProcessor($thirdProcessor);

// This will call `process` on $firstProcessor, $secondProcessor, and finally on $thirdProcessor:
$processor->process($asset, $queue);
```

### `HtmlProcessor`

This processor can process HTML assets and enqueue its containing URLs.
It will also modify all relative URLs and make them absolute.
Also, if you provide a [CssProcessor](#cssprocessor), `style` attributes are found and URLs within CSS will be resolved.
 
### `CssProcessor`

This processor can process CSS assets and enqueue its containing URLs from `@import`s and `url(...)` statements.

### `ReplaceProcessor`

Performs simple `str_replace` operations on asset contents:

```php
<?php
use Baqend\Component\Spider\Processor\ReplaceProcessor;

$processor = new ReplaceProcessor('Hello World', 'Hallo Welt');

// This will replace all occurrences of
// "Hello World" in the asset with "Hallo Welt":
$processor->process($asset, $queue);
```

The `ReplaceProcessor` does not enqueue other URLs.

### `StoreProcessor`

Takes a URL _prefix_ and a _directory_ and will store all assets relative to the _prefix_ in the according file structure in _directory_.

The `StoreProcessor` does not enqueue other URLs.

### `UrlRewriteProcessor`

Changes the URL of an asset to another prefix.
Use this to let [HtmlProcessor](#htmlprocessor) and [CssProcessor](#cssprocessor) resolve relative URLs from a different origin.

The `UrlRewriteProcessor` does not enqueue other URLs.
Also, it does not modify the asset's content – only its URL.


URL Handlers
------------

URL handlers tell the spider whether to download and process a URL.
There are the following built-in URL handlers:

### `OriginUrlHandler`

Handles only URLs coming from some given origin, i.e. "https://example.org". 

### `BlacklistUrlHandler`

Does not handle URLs being part of some blacklist.
You can use glob patterns to provide a blacklist:

```php
<?php
use Baqend\Component\Spider\UrlHandler\BlacklistUrlHandler;

$blacklist = [
    'https://other.org/**',     // Don't handle anything from other.org over HTTPS    
    'http{,s}://other.org/**',  // Don't handle anything from other.org over HTTP or HTTPS    
    '**.{png,gif,jpg,jpeg}',    // Don't handle any image files    
];

$urlHandler = new BlacklistUrlHandler($blacklist);
```
 

Alternatives
------------

If this project does not match your needs, check the following other projects:

* [spatie/crawler](https://packagist.org/packages/spatie/crawler) (Requires PHP 7)
* [vdb/php-spider](https://packagist.org/packages/vdb/php-spider)


[Composer]: https://getcomposer.org/
[Guzzle]: https://packagist.org/packages/guzzlehttp/guzzle
