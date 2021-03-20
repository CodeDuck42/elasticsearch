[![PHP 7.4](https://img.shields.io/badge/php-7.4-8892BF.svg?style=flat)](https://php.net/)
[![PHP 8.0](https://img.shields.io/badge/php-8.0-8892BF.svg?style=flat)](https://php.net/)
[![Elasticsearch 6](https://img.shields.io/badge/elasticsearch-6-A88700.svg?style=flat)](https://www.elastic.co/)
[![Elasticsearch 7](https://img.shields.io/badge/elasticsearch-7-A88700Bad.svg?style=flat)](https://www.elastic.co/)
[![Latest Stable Version](https://img.shields.io/packagist/v/codeduck/elasticsearch.svg?style=flat)](https://packagist.org/packages/codeduck/elasticsearch)
![test workflow](https://github.com/CodeDuck42/elasticsearch/actions/workflows/test.yaml/badge.svg)
![test workflow](https://github.com/CodeDuck42/elasticsearch/actions/workflows/psalm.yaml/badge.svg)
![elasicsearch workflow](https://github.com/CodeDuck42/elasticsearch/actions/workflows/elasticsearch.yaml/badge.svg)

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

## Compatibility

- PHP 7.4 + PHP 8.0
- Elasticsearch 6 + 7

## Usage

~~~php
use CodeDuck\Elasticsearch\Action\Delete;use CodeDuck\Elasticsearch\Action\Index;
use CodeDuck\Elasticsearch\Action\Query;use CodeDuck\Elasticsearch\Client;
use Symfony\Component\HttpClient\HttpClient;

$index = 'my-index';
$type = '_doc'; // default value

$id1 = 'ID-123';
$id2 = 'ID-234';
$id3 = 'ID-341';

$document1 = ['name' => 'foo', 'foo' => 12345];
$document2 = ['name' => 'bar', 'foo' => 12345];
$document3 = ['name' => 'foobar', 'foo' => 12345];

$client = new Client(HttpClient::create(), 'http://127.0.0.1:9200');

// index one document
$client->action(new Index($index, $id1, $document1));

// bulk index
$client->bulkAction([
    new Index($index, $id1, $document1),
    new Index($index, $id2, $document2),
    new Index($index, $id3, $document3),
]);

echo json_encode(
    $client->query(
        new Query(['query' => ['term' => ['name' => 'foobar']]], $index)
    ),
    JSON_THROW_ON_ERROR
);

// bulk delete
$client->bulkAction([
    new Delete($index, $id1),
    new Delete($index, $id2),
    new Delete($index, $id3),
]);

~~~

## Roadmap

- Simplified query result parser to provide easier access
- DSL builder
