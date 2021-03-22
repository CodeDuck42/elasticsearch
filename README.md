[![Latest Stable Version](https://img.shields.io/packagist/v/codeduck/elasticsearch.svg?style=flat)](https://packagist.org/packages/codeduck/elasticsearch)
![test workflow](https://github.com/CodeDuck42/elasticsearch/actions/workflows/test.yaml/badge.svg)
![test workflow](https://github.com/CodeDuck42/elasticsearch/actions/workflows/psalm.yaml/badge.svg)
![elasicsearch workflow](https://github.com/CodeDuck42/elasticsearch/actions/workflows/elasticsearch.yaml/badge.svg)
[![PHP 7.4](https://img.shields.io/badge/php-7.4-8892BF.svg?style=flat)](https://php.net/)
[![PHP 8.0](https://img.shields.io/badge/php-8.0-8892BF.svg?style=flat)](https://php.net/)

# Minimalistic elasticsearch client

Born out of frustration about the dependency hell of the available client packages. I didn't need a library with all the
features, as a result this package was born. It provides the bare minimum to index, delete and query documents.

All issues should go to the [issue tracker from github](https://github.com/CodeDuck42/elasticsearch/issues).

## Features

- Add document to an index
- Delete document from an index
- Bulk action for index and delete
- Run a query on an index
- The only external dependencies are symfony/http-client, ext-json

## TODO

- Actions should return the response from elasticsearch, especially for bulk actions
- Investigating options for authentication besides username and password in the server url (necessary?)

## Compatibility

- PHP 7.4 + PHP 8.0
- Elasticsearch 6 + 7

## Usage

~~~php
use CodeDuck\Elasticsearch\Action\Bulk;use CodeDuck\Elasticsearch\Action\Delete;
use CodeDuck\Elasticsearch\Action\Index;
use CodeDuck\Elasticsearch\Action\Query;
use CodeDuck\Elasticsearch\Client;
use CodeDuck\Elasticsearch\Document;
use CodeDuck\Elasticsearch\Identifier;
use Symfony\Component\HttpClient\HttpClient;

$id1 = new Identifier('my-index', 'ID-123', '_doc');
$id2 = new Identifier('my-index', 'ID-234', '_doc');
$id3 = new Identifier('my-index', 'ID-341', '_doc');

$document1 = new Document($id1, ['name' => 'foo', 'foo' => 12345]);
$document2 = new Document($id2, ['name' => 'bar', 'foo' => 12345]);
$document3 = new Document($id3, ['name' => 'foobar', 'foo' => 12345]);

$client = new Client(HttpClient::create(), 'http://127.0.0.1:9200');

// index one document
$client->execute(new Index($document1));

// bulk index
$client->execute(new Bulk(
    new Index($document1),
    new Index($document2),
    new Index($document3),
));

// or
$documents = [
    new Index($document1),

    new Index($document2),
    new Index($document3),
];

$client->execute(new Bulk(...$documents));

// do a search
$result = $client->query(
    new Query(['query' => ['term' => ['name' => 'foobar']]], 'my-index')
);

echo sprintf(
    'It took %f ms to query %d documents, the highest score was %f' . PHP_EOL,
    $result->getTook(),
    $result->getCount(),
    $result->getMaxScore()
);

foreach ($result->getDocuments() as $document) {
    echo sprintf(
        'Score: %f, Json: %s' . PHP_EOL,
        $document->getScore(),
        json_encode($document->getSource(), JSON_THROW_ON_ERROR)
    );
}

// bulk delete
$client->execute(new Bulk(
    new Delete($id1),
    new Delete($id2),
    new Delete($id3),
));

~~~
