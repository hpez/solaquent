<?php

namespace hpez\Solaquent;

use Illuminate\Database\Query\Builder;
use Solarium\Client;

require_once __DIR__ . '/../vendor/autoload.php';

class Solaquent
{
    private $eloquentQuery;
    private $client;

    /**
     * Solaquent constructor.
     * @param Builder|null $eloquentQuery
     * @param array|null $solariumConfig
     * @param string $solariumEndpoint
     */
    public function __construct(Builder $eloquentQuery = null, array $solariumConfig = null, string $solariumEndpoint)
    {
        $this->eloquentQuery = $eloquentQuery;
        $this->client = new Client($solariumConfig);
        $this->client->setDefaultEndpoint($solariumEndpoint);
    }

    /**
     * Set eloquent query.
     * @param Builder $eloquentQuery
     */
    public function setQuery(Builder $eloquentQuery)
    {
        $this->eloquentQuery = $eloquentQuery;
    }

    /**
     * Set solarium config.
     * @param array $solariumConfig
     */
    public function setConfig(array $solariumConfig)
    {
        $this->client = new Client($solariumConfig);
    }

    /**
     * @param string $query
     * @return array
     */
    private function sqlToArray(string $query)
    {
        $queryArray = explode(' ', $query);
        $last = '';
        $res = ['table' => '', $query => ''];
        foreach ($queryArray as $item) {
            if ($last == 'from')
                $res['table'] = substr($item, 1, strlen($item) - 2);
            elseif ($last == 'where')
                $res['query'] = substr($item, 1, strlen($item) - 2).':';
            elseif ($last == 'and')
                $res['query'] .= ' AND ' . substr($item, 1, strlen($item) - 2) . ':';
            elseif ($last == 'or')
                $res['query'] .= ' OR ' . substr($item, 1, strlen($item) - 2) . ':';
            elseif ($last == '=')
                $res['query'] .= substr($item, 1, strlen($item) - 2);
            $last = $item;
        }
        return $res;
    }

    /**
     * @param Builder|null $eloquentQuery
     * @param string|null $solariumEndpoint
     * @return \Solarium\QueryType\Select\Result\Result
     */
    public function get(Builder $eloquentQuery = null, string $solariumEndpoint = null)
    {
        if ($eloquentQuery !== null)
            $this->eloquentQuery = $eloquentQuery;

        $sqlQuery = str_replace(array('?'), array('\'%s\''), $this->eloquentQuery->toSql());
        $sqlQuery = vsprintf($sqlQuery, $this->eloquentQuery->getBindings());
        $sqlArray = $this->sqlToArray($sqlQuery);
        if ($solariumEndpoint === null)
            $this->client->setDefaultEndpoint($sqlArray['table']);
        else
            $this->client->setDefaultEndpoint($solariumEndpoint);
        $query = $this->client->createSelect();
        $query->setQuery($sqlArray['query']);
        return $this->client->select($query);
    }
}