# Example for using the full client

~~~php
use CodeDuck\Elasticsearch\Actions\Bulk;
use CodeDuck\Elasticsearch\Actions\Delete;
use CodeDuck\Elasticsearch\Actions\Index;
use CodeDuck\Elasticsearch\Actions\Query;
use CodeDuck\Elasticsearch\Client;
use CodeDuck\Elasticsearch\ValueObjects\Document;
use CodeDuck\Elasticsearch\ValueObjects\Identifier;
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

// alternative for bulk index
$documents = [
    new Index($document1),
    new Index($document2),
    new Index($document3),
];

$client->execute(new Bulk(...$documents));

// doing a search
$result = $client->execute(
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
