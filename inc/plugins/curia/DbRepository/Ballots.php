<?php

namespace curia\DbRepository;

class Ballots extends \curia\DbEntityRepository
{
    public const TABLE_NAME = 'curia_ballots';
    public const COLUMNS = [
        'id' => [
            'type' => 'integer',
            'primaryKey' => true
        ],
        'election_id' => [
            'type' => 'integer',
            'foreignKeys' => [
                [
                    'table' => 'curia_elections',
                    'column' => 'id',
                    'onDelete' => 'cascade'
                ]
            ]
        ],
        'user_id' => [
            'type' => 'integer',
            'foreignKeys' => [
                [
                    'table' => 'users',
                    'column' => 'uid',
                    'noReference' => true
                ]
            ]
        ],
        'choices' => [
            'type' => 'text'
        ],
        'anonymous' => [
            'type' => 'bool'
        ]
    ];

    public function create()
    {
    }
}
