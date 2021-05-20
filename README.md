# BusFactor

[![Latest Version](https://img.shields.io/github/release/busfactor/busfactor.svg)](https://github.com/busfactor/busfactor/releases)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

BusFactor is a modern PHP library providing several components you can mix and match to implement the CQRS and Event Sourcing patterns in your application.

## Install

Using [Composer](https://getcomposer.org/):

```
$ composer require busfactor/busfactor
```

## Requirements

- PHP >=7.3 with `json` and `pdo` extensions enabled.

## Components

| Component | Description |
| --- | --- |
| `Aggregate` | Interfaces and traits for plain DDD aggregates and domain events. | 
| `AggregateStore` | Persistence for aggregates. | 
| `CacheProjectionStoreMiddleware` | Caching middleware for `ProjectionStore`. | 
| `CommandBus` | Implementation of the Command Bus pattern. | 
| `EventBus` | Implementation of the Publish-Subscribe pattern for event streams. | 
| `EventSourcedAggregate` | Interface and trait for event-sourced aggregates. | 
| `EventSourcedAggregateStore` | `AggregateStore` adapter for event-sourced aggregates persistence. | 
| `EventStore` | Persistence for event streams. | 
| `EventStoreReductionInspection` | Output single value from `EventStore` inspection. | 
| `EventStream` | Event streams for event-sourced aggregates. | 
| `LaravelCacheProjectionStore` | Laravel Cache adapter for `ProjectionStore`. | 
| `MemcachedProjectionStore` | Memcached adapter for `ProjectionStore`. | 
| `ObjectSerializer` | Interface for object serialization. | 
| `Pdo` | Decorating interface for PHP Data Objects (PDO). | 
| `PdoAggregateStore` | PDO adapter for `AggregateStore`. | 
| `PdoEventStore` | PDO adapter for `EventStore`. | 
| `PdoProjectionStore` | PDO adapter for `ProjectionStore`. | 
| `PdoProxy` | Lazy-connecting PDO proxy. | 
| `Problem` | Interface for Problem Details aware exceptions. | 
| `Projection` | Interface for projections. | 
| `ProjectionStore` | Persistence for projections. | 
| `ReflectionObjectSerializer` | Reflection-based adapter for `ObjectSerializer`. | 
| `Scenario` | Testing infrastructure on top of PHPUnit. | 
| `SnapshotAggregateStoreMiddleware` | `AggregateStore` middleware for event-sourced aggregate snapshots. | 
| `StreamEnricher` | Interface for event stream enrichers. | 
| `StreamEnricherEventBusMiddleware` | `EventBus` middleware for event stream enrichment with `StreamEnricher`. | 
| `StreamEnricherEventStoreMiddleware` | `EventStore` middleware for event stream enrichment with StreamEnricher`. | 
| `StreamPublishingInspection` | Publish event streams from `EventStore` inspection. | 
| `Uuid` | Universally Unique IDentifier (UUID) generation. | 

## Testing

```bash
$ vendor/bin/phpunit
```

## Credits

- [Maxime Gosselin](https://github.com/maximegosselin)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
