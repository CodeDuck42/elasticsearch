# Exceptions

## Inheritance structure

~~~text
Throwable
└── RuntimeException
    └── CodeDuck\Elasticsearch\Exceptions\ElasticsearchException
        ├── CodeDuck\Elasticsearch\Exceptions\DataCouldNotBeDecodedException
        ├── CodeDuck\Elasticsearch\Exceptions\DataCouldNotBeEncodedException
        └── CodeDuck\Elasticsearch\Exceptions\TransportException
~~~
