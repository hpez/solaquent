<?php

namespace hpez\Solaquent;

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Illuminate\Database\Capsule\Manager as Capsule;

class SolaquentTest extends TestCase
{
    private $client;

    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->client = new Client([
            'endpoint' => [
                'products' => [
                    'host' => '127.0.0.1',
                    'port' =>  '8983',
                    'path' => '/solr/',
                    'core' => 'colletion1'
                ]
            ]
        ]);
        $this->client->setDefaultEndpoint('products');
        $capsule = new Capsule;

        $capsule->addConnection([

            "driver" => "mysql",

            "host" =>"127.0.0.1",

            "database" => "acl",

            "username" => "root",

            "password" => ""

        ]);

        $capsule->setAsGlobal();

        $capsule->bootEloquent();
    }

    public function testFlatNonNested()
    {
        $update = $this->client->createUpdate();
        $productDoc = $update->createDocument();
        $productDoc->id = 1;
        $productDoc->a = 'a';
        $productDoc->b = 'b';
        $productDoc->c = 'c';
        $update->addDocument($productDoc);

        $productDoc = $update->createDocument();
        $productDoc->id = 2;
        $productDoc->a = 'a';
        $productDoc->b = 'e';
        $productDoc->c = 'd';
        $update->addDocument($productDoc);

        $productDoc = $update->createDocument();
        $productDoc->id = 3;
        $productDoc->a = 'd';
        $productDoc->b = 'e';
        $productDoc->c = 'f';
        $update->addDocument($productDoc);

        $update->addCommit();

        $this->client->update($update);

        $query = Capsule::table('products')
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
        foreach ($result as $field => $item)
            $this->assertEquals($item['id'], 2);
    }
}