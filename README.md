# Solaquent [![Build Status](https://travis-ci.org/hpez/Solaquent.svg?branch=master)](https://travis-ci.org/hpez/Solaquent)
A driver translating Eloquent queries to Solarium queries and returns the result. For now, it's only supporting flat non-nested queries.

## Installation
You can install the package using `composer require hpez/solaquent` or check out the [packagist page](https://packagist.org/packages/hpez/solaquent)

## Usage
You can pass an instance of Eloquent query builder (`Illuminate\Database\Query\Builder`) to the constructor or add it using `setQuery` method. Also you can set solarium config array in both the constructor and the `get` method.
To get the Solr result you should call the `get` method which has optional inputs `$eloquentQuery` and `$solariumEndpoint`.
Here are the docs for [Solarium](https://solarium.readthedocs.io) and [Solr](http://lucene.apache.org/solr/guide/).

## Example
```
$query = DB::table('products')
         ->where('a', 'a')
         ->where('b', 'e')
         ->orWhere('c', 'f');
 
 $solaquent = new Solaquent($query, [
     'endpoint' => [
         'products' => [
             'host' => '127.0.0.1',
             'port' =>  '8983',
             'path' => '/solr/',
             'core' => 'colletion1'
         ]
     ]
 ],'products');
 $result = $solaquent->get();
 ```
 
 ## Contribution
 Feel free to create issues or make any pull requests.
