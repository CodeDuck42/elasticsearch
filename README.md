[![latest stable version](https://img.shields.io/packagist/v/codeduck/elasticsearch.svg?style=flat-square)](https://packagist.org/packages/codeduck/elasticsearch)
[![license](https://img.shields.io/packagist/l/codeduck/elasticsearch?style=flat-square)](https://packagist.org/packages/codeduck/elasticsearch)
[![php version](https://img.shields.io/packagist/php-v/codeduck/elasticsearch?style=flat-square)](https://packagist.org/packages/codeduck/elasticsearch)
[![codecov](https://img.shields.io/codecov/c/github/CodeDuck42/elasticsearch?style=flat-square&token=I8AVSCQONG)](https://codecov.io/gh/CodeDuck42/elasticsearch)
[![infection](https://img.shields.io/endpoint?style=flat-square&label=infection&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FCodeDuck42%2Felasticsearch%2Fmain)](https://infection.github.io)
[![unit tests](https://img.shields.io/github/workflow/status/CodeDuck42/elasticsearch/Unit%20tests/main?style=flat-square&label=unit%20tests)](https://github.com/CodeDuck42/elasticsearch)
[![psalm](https://img.shields.io/github/workflow/status/CodeDuck42/elasticsearch/Static%20analysis/main?style=flat-square&label=psalm)](https://github.com/CodeDuck42/elasticsearch)
[![elasticsearch](https://img.shields.io/github/workflow/status/CodeDuck42/elasticsearch/Elasticsearch/main?style=flat-square&label=elasticsearch)](https://github.com/CodeDuck42/elasticsearch)

# Minimalistic elasticsearch client

Born out of frustration about the dependency hell of the available client packages. I didn't need a library with all the
features, as a result this package was born. It provides the bare minimum to index, delete and query documents.

This library is compatible with elasticsearch 6.x and 7.x and has no dependencies on the official elasticsearch client
packages.

All issues should go to the [issue tracker from github](https://github.com/CodeDuck42/elasticsearch/issues).

## Features

- Adding a document to an elasticsearch index
- Delete a document from an elasticsearch index
- Send multiple adding and delete actions as a bulk action
- Run a query on an elasticsearch index

## Usage

~~~php
use CodeDuck\Elasticsearch\Client;
use CodeDuck\Elasticsearch\SimpleClient;
use Symfony\Component\HttpClient\HttpClient;

$client = new SimpleClient(
    new Client(HttpClient::create(), 'http://127.0.0.1:9200'),
    'my-index', '_doc'
);

$client->begin();
$client->add('ID-123', ['name' => 'foo', 'foo' => 12345]);
$client->add('ID-234', ['name' => 'bar', 'foo' => 12345]);
$client->commit();

$result = $client->query(['query' => ['term' => ['name' => 'bar']]]);

foreach ($result->getDocuments() as $document) {
    echo json_encode($document->getSource(), JSON_THROW_ON_ERROR) . PHP_EOL;
}

$client->begin();
$client->delete('ID-123');
$client->delete('ID-234');
$client->commit();

~~~

More detailed examples can be found here: [full client](docs/example-full-client.md), [simple client](docs/example-simple-client.md).

## TODO

- Complete documentation
- Actions should return the response from elasticsearch, especially for bulk actions
- Investigating options for authentication besides username and password in the server url (necessary?)
