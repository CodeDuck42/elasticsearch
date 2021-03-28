# Example for using the simple client

The simple client differs from the full client in the following features:

- The index information is given on client creation
- Bulk actions are wrapped with a begin/commit/rollBack construct like in sql

~~~php
use CodeDuck\Elasticsearch\Client;
use CodeDuck\Elasticsearch\SimpleClient;
use Symfony\Component\HttpClient\HttpClient;

$id1 = 'ID-123';
$id2 = 'ID-234';
$id3 = 'ID-341';

$document1 = ['name' => 'foo', 'foo' => 12345];
$document2 = ['name' => 'bar', 'foo' => 12345];
$document3 = ['name' => 'foobar', 'foo' => 12345];

$client = new SimpleClient(
    new Client(HttpClient::create(), 'http://127.0.0.1:9200'),
    'my-index', '_doc'
);

// index one document
$client->add($id1, $document1);

// bulk actions
$client->begin();
$client->delete($id1);
$client->add($id2, $document2);
$client->add($id3, $document3);
$client->commit();

// do a search
$result = $client->query(['query' => ['term' => ['name' => 'foobar']]]);

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

~~~
