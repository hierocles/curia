<?php

namespace curia\DbRepository;

class Elections extends \curia\DbEntityRepository
{
    public const TABLE_NAME = 'curia_elections';
    public const COLUMNS = [
        'id' => [
            'type' => 'integer',
            'primaryKey' => true
        ],
        'methodology' => [
            'type' => 'varchar',
            'length' => 255
        ],
        'active' => [
            'type' => 'bool',
            'notNull' => true
        ],
        'start_date' => [
            'type' => 'integer',
            'notNull' => true
        ],
        'end_date' => [
            'type' => 'integer'
        ],
        'title' => [
            'type' => 'varchar',
            'length' => 255
        ],
        'description' => [
            'type' => 'text'
        ],
        'candidates' => [
            'type' => 'text'
        ],
        'permissions' => [
            'type' => 'varchar',
            'length' => 255
        ]
    ];

    public function create()
    {
    }
    public function delete()
    {
    }
    public function change()
    {
    }
    public function archive()
    {
    }
}
