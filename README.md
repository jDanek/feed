# feed-io

> ### Notice:
> This is a fork of the version 4 branch, it exists because of the need to run the minimum version of PHP 7.1+ including later versions. The original library handles PHP environments with later versions.
> The contents of the fork have been purged. It has been stripped of tests and other tools I don't currently use.

This is a PHP library built to consume and serve news feeds. It features:

- JSONFeed / Atom / RSS read and write support
- Feeds auto-discovery through HTML headers
- Multiple feeds reading at once through asynchronous requests
- PSR-7 Response generation with accurate cache headers
- HTTP Headers support when reading feeds in order to save network traffic
- Detection of the format (RSS / Atom) when reading feeds
- Enclosure support to handle external medias like audio content
- Feed logo support (RSS + Atom)
- PSR compliant logging
- Content filtering to fetch only the newest items
- Malformed feeds auto correction
- DateTime detection and conversion
- A generic HTTP ClientInterface
- Guzzle Client integration


# Installation

Use Composer to add feed-io into your project's requirements :

```sh
    composer require danek/feed-io
 ```

# Requirements

feed-io requires :

- php 7.1+
- psr/log 1.0
- guzzlehttp/guzzle 6.2+


Monolog is not the only library suitable to handle feed-io's logs, you can use any PSR/Log compliant library instead.

# Fetching the repository

Do this if you want to contribute (and you're welcome to do so):

```sh
    git clone https://github.com/jDanek/feed-io.git

    cd feed-io/

    composer install
```

Usage
=====

## reading

feed-io is designed to read feeds across the internet and to publish your own. Its main class is [FeedIo](https://github.com/alexdebril/feed-io/blob/master/src/FeedIo/FeedIo.php) :

```php

// create a simple FeedIo instance
$feedIo = \Danek\FeedIo\Factory::create()->getFeedIo();

// read a feed
$result = $feedIo->read($url);

// or read a feed since a certain date
$result = $feedIo->readSince($url, new \DateTime('-7 days'));

// get title
$feedTitle = $result->getFeed()->getTitle();

// iterate through items
foreach( $result->getFeed() as $item ) {
    echo $item->getTitle();
}

```
In order to save bandwidth, feed-io estimates the next time it will be relevant to read the feed and get new items from it.

```php
$nextUpdate = $result->getNextUpdate();
echo "computed next update: {$nextUpdate->format(\DATE_ATOM)}";

// you may need to access the statistics
$updateStats = $result->getUpdateStats();
echo "average interval in seconds: {$updateStats->getAverageInterval()}";
```

feed-io calculates the next update time by first detecting if the feed was active in the last 7 days and if not we consider it as sleepy. The next update date for a sleepy feed is set to the next day at the same time. If the feed isn't sleepy we use the average interval and the median interval by adding those intervals to the feed's last modified date and compare the result to the current time. If the result is in the future, then it's returned as the next update time. If none of them are in the future, we considered the feed will be updated quite soon, so the next update time is one hour later from the moment of the calculation.

Please note: the fixed delays for sleepy and closed to be updated feeds can be set through `Result::getNextUpdate()` arguments, see [Result](src/Reader/Result.php) for more details.


### Asynchronous reading of several feeds at once

Thanks to Guzzle, feed-io is able to fetch several feeds at once through asynchronous requests. If you're willing to get more information about the way it works, you can read [Guzzle's documentation](http://docs.guzzlephp.org/en/stable/quickstart.html#async-requests).

To read feeds using asynchronous requests with feed-io, you need to send a pool of `\Danek\FeedIo\Async\Request` objects to `\Danek\FeedIo\FeedIo::readAsync` and handle the result with a `\Danek\FeedIo\Async\CallbackInterface` of your own. You can also use `\Danek\FeedIo\Async\DefaultCallback` in order to test the feature.

Each `\Danek\FeedIo\Async\Request` is a request you want to perform, it embeds the feed's URL and optionnally a `\DateTime` to define the `modified-since` attribute of the request.

The `CallbackInterface` instance needs two methods :

```php

  /**
   * @param Result $result
   */
  public function process(Result $result) : void;

  /**
   * @param Request $request
   * @param \Exception $exception
   */
  public function handleError(Request $request, \Exception $exception) : void;

```

`process()` is called on successful reading and parsing to let you process the result. Otherwise `handleError()` will be triggered on faulty calls. Here is an example : [PDOCallback](examples/PDOCallback.php)

## Feeds discovery

A web page can refer to one or more feeds in its headers, feed-io provides a way to discover them :

```php

$feedIo = \Danek\FeedIo\Factory::create()->getFeedIo();

$feeds = $feedIo->discover($url);

foreach( $feeds as $feed ) {
    echo "discovered feed : {$feed}";
}

```
Or you can use feed-io's command line :

```shell
./vendor/bin/feedio discover https://a-website.org
```

You'll get all discovered feeds in the output.

## formatting an object into a XML stream

```php

// build the feed
$feed = new Danek\Danek\FeedIo\Feed;
$feed->setTitle('...');

// convert it into Atom
$atomString = $feedIo->toAtom($feed);

// or ...
$atomString = $feedIo->format($feed, 'atom');

```

## Adding a StyleSheet

```php

$feed = new \Danek\FeedIo\FeedIo\Feed;
$feed->setTitle('...');
$styleSheet = new StyleSheet('http://url-of-the-xsl-stylesheet.xsl');
$feed->setStyleSheet($styleSheet);

```

## building a feed including medias

```php
// build the feed
$feed = new \Danek\FeedIo\FeedIo\Feed;
$feed->setTitle('...');

$item = $feed->newItem();

// add namespaces
$feed->setNS(
    'itunes', //namespace
    'http://www.itunes.com/dtds/podcast-1.0.dtd' //dtd for the namespace
        );
$feed->set('itunes,title', 'Sample Title'); //OR any other element defined in the namespace.
$item->addElement('itunes:category', 'Education');

// build the media
$media = new \Danek\FeedIo\Feed\Item\Media
$media->setUrl('http://yourdomain.tld/medias/some-podcast.mp3');
$media->setType('audio/mpeg');

// add it to the item
$item->addMedia($media);

$feed->add($item);

```

## Creating a valid PSR-7 Response with a feed

You can turn a `\Danek\FeedIo\FeedInstance` directly into a PSR-7 valid response using `\Danek\FeedIo\FeedIo::getPsrResponse()` :

```php

$feed = new \Danek\FeedIo\Feed;

// feed the beast ...
$item = new \Danek\FeedIo\Feed\Item;
$item->set ...
$feed->add($item);

$atomResponse = $feedIo->getPsrResponse($feed, 'atom');

$jsonResponse = $feedIo->getPsrResponse($feed, 'json');

```

## Configure feed-io using the Factory

We saw in the [reading section](#reading) that to get a simple `FeedIo` instance we can simply call the `Factory` statically and let it return a fresh `FeedIo` composed of the main dependencies it needs to work. The problem is that we may want to inject configuration to its underlying components, such as configuring Guzzle to ignore SSL errors.

For that, we will inject the configuration through `Factory::create()` parameters, first one being for the logging system, and the second one for the HTTP Client (well, Guzzle).

### Configure Guzzle through the Factory

A few lines above, we talked about ignoring ssl errors, let's see how to configure Guzzle to do this:

```php
$feedIo = \Danek\FeedIo\Factory::create(
        ['builder' => 'NullLogger'], // assuming you want feed-io to keep quiet
        ['builder' => 'GuzzleClient', 'config' => ['verify' => false]]
    )->getFeedIo();
```

It's important to specify the "builder", as it's the class that will be in charge of actually building the instance.

### activate logging

feed-io natively supports PSR-3 logging, you can activate it by choosing a 'builder' in the factory :

```php

$feedIo = \Danek\FeedIo\Factory::create(['builder' => 'monolog'])->getFeedIo();

```

feed-io only provides a builder to create Monolog\Logger instances. You can write your own, as long as the Builder implements BuilderInterface.

## Building a FeedIo instance without the factory

To create a new FeedIo instance you only need to inject two dependencies :

 - an HTTP Client implementing FeedIo\Adapter\ClientInterface. It can be wrapper for an external library like FeedIo\Adapter\Guzzle\Client
- a PSR-3 logger implementing Psr\Log\LoggerInterface

```php

// first dependency : the HTTP client
// here we use Guzzle as a dependency for the client
$guzzle = new GuzzleHttp\Client();
// Guzzle is wrapped in this adapter which is a FeedIo\Adapter\ClientInterface  implementation
$client = new \Danek\FeedIo\FeedIo\Adapter\Guzzle\Client($guzzle);

// second dependency : a PSR-3 logger
$logger = new Psr\Log\NullLogger();

// now create FeedIo's instance
$feedIo = new \Danek\FeedIo\FeedIo\FeedIo($client, $logger);

```

Another example with Monolog configured to write on the standard output :

```php
use \Danek\FeedIo\FeedIo\FeedIo;
use \Danek\FeedIo\FeedIo\Adapter\Guzzle\Client;
use GuzzleHttp\Client as GuzzleClient;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$client = new Client(new GuzzleClient());
$logger = new Logger('default', [new StreamHandler('php://stdout')]);

$feedIo = new FeedIo($client, $logger);

```

### Guzzle Configuration

You can configure Guzzle before injecting it to `FeedIo` :

```php
use \Danek\FeedIo\FeedIo\FeedIo;
use \Danek\FeedIo\FeedIo\Adapter\Guzzle\Client;
use GuzzleHttp\Client as GuzzleClient;
use \Psr\Log\NullLogger;

// We want to timeout after 3 seconds
$guzzle = new GuzzleClient(['timeout' => 3]);
$client = new Client($guzzle);

$logger = new NullLogger();

$feedIo = new \Danek\FeedIo\FeedIo($client, $logger);

```
Please read [Guzzle's documentation](http://docs.guzzlephp.org/en/stable/index.html) to get more information about its configuration.

#### Caching Middleware usage

To prevent your application from hitting the same feeds multiple times, you can inject [Kevin Rob's cache middleware](https://github.com/Kevinrob/guzzle-cache-middleware) into Guzzle's instance :

```php
use \Danek\FeedIo\FeedIo\FeedIo;
use \Danek\FeedIo\FeedIo\Adapter\Guzzle\Client;
use GuzzleHttp\Client As GuzzleClient;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Psr\Log\NullLogger;

// Create default HandlerStack
$stack = HandlerStack::create();

// Add this middleware to the top with `push`
$stack->push(new CacheMiddleware(), 'cache');

// Initialize the client with the handler option
$guzzle = new GuzzleClient(['handler' => $stack]);
$client = new Client($guzzle);
$logger = new NullLogger();

$feedIo = new \Danek\FeedIo\FeedIo($client, $logger);

```

As feeds' content may vary often, caching may result in unwanted behaviors.

### Inject a custom Logger

You can inject any Logger you want as long as it implements `Psr\Log\LoggerInterface`. Monolog does, but it's the only library : https://packagist.org/providers/psr/log-implementation

```php
use \Danek\FeedIo\FeedIo\FeedIo;
use \Danek\FeedIo\FeedIo\Adapter\Guzzle\Client;
use GuzzleHttp\Client as GuzzleClient;
use Custom\Logger;

$client = new Client(new GuzzleClient());
$logger = new Logger();

$feedIo = new FeedIo($client, $logger);

```

### Inject a custom HTTP Client

Warning : it is highly recommended to use the default Guzzle Client integration.

If you really want to use another library to read feeds, you need to create your own `FeedIo\Adapter\ClientInterface` class to embed interactions with the library :

```php
use \Danek\FeedIo\FeedIo\FeedIo;
use Custom\Adapter\Client;
use Library\Client as LibraryClient;
use Psr\Log\NullLogger;

$client = new Client(new LibraryClient());
$logger = new NullLogger();

$feedIo = new FeedIo($client, $logger);

```

### Factory or Dependency Injection ?

Choosing between using the Factory or build `FeedIo` without it is a question you must ask yourself at some point of your project. The Factory is mainly designed to let you use feed-io with the lesser efforts and get your first results in a small amount of time. However, it doesn't let you benefit of all Monolog's and Guzzle's features, which could be annoying. Dependency injection will also let you choose another library to handle logs if you need to.

## Dealing with missing timezones

Sometimes you have to consume feeds in which the timezone is missing from the dates. In some use-cases, you may need to specify the feed's timezone to get an accurate value, so feed-io offers a workaround for that :

```php
$feedIo->getDateTimeBuilder()->setFeedTimezone(new \DateTimeZone($feedTimezone));
$result = $feedIo->read($feedUrl);
$feedIo->getDateTimeBuilder()->resetFeedTimezone();
```

Don't forget to reset `feedTimezone` after fetching the result, or you'll end up with all feeds located in the same timezone.