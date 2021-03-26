[![latest stable version](https://img.shields.io/packagist/v/codeduck/elasticsearch.svg?style=flat-square)](https://packagist.org/packages/codeduck/elasticsearch)
[![license](https://img.shields.io/packagist/l/codeduck/elasticsearch?style=flat-square)](https://packagist.org/packages/codeduck/elasticsearch)
[![php version](https://img.shields.io/packagist/php-v/codeduck/elasticsearch?style=flat-square)](https://packagist.org/packages/codeduck/elasticsearch)
[![codecov](https://img.shields.io/codecov/c/github/CodeDuck42/elasticsearch?style=flat-square&logo=codecov&token=I8AVSCQONG)](https://codecov.io/gh/CodeDuck42/elasticsearch)
[![unit tests](https://img.shields.io/github/workflow/status/CodeDuck42/elasticsearch/Unit%20tests/main?style=flat-square&label=unit%20tests&logo=github)](https://github.com/CodeDuck42/elasticsearch)
[![psalm](https://img.shields.io/github/workflow/status/CodeDuck42/elasticsearch/Static%20analysis/main?style=flat-square&label=psalm&logo=github)](https://github.com/CodeDuck42/elasticsearch)
[![elasticsearch](https://img.shields.io/github/workflow/status/CodeDuck42/elasticsearch/Elasticsearch/main?style=flat-square&label=elasticsearch&logo=github)](https://github.com/CodeDuck42/elasticsearch)

# Minimalistic elasticsearch client

Born out of frustration about the dependency hell of the available client packages. I didn't need a library with all the
features, as a result this package was born. It provides the bare minimum to index, delete and query documents.

All issues should go to the [issue tracker from github](https://github.com/CodeDuck42/elasticsearch/issues).

## Features

- Adding a document to an elasticsearch index
- Delete a document from an elasticsearch index
- Send multiple adding and delete actions as a bulk action
- Run a query on an elasticsearch index

## TODO

- Complete documentation
- Actions should return the response from elasticsearch, especially for bulk actions
- Investigating options for authentication besides username and password in the server url (necessary?)

## Compatibility

- PHP 7.4 / PHP 8.0
- Elasticsearch 6.x + 7.x

## Usage

~~~php
use CodeDuck\Elasticsearch\Action\Bulk;
use CodeDuck\Elasticsearch\Action\Delete;
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
