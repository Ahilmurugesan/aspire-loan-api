<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MigrateFreshSeedOnce;

    /**
     * Pagination data structure
     *
     * @return array
     */
    public function paginationStructure(): array
    {
        return [
            'data',
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'path',
                'per_page',
                'to',
                'total',
            ],
        ];
    }

    /**
     * Loan Resource data structure
     *
     * @return array
     */
    public function loanResourceStructure(): array
    {
        return [
            'data' => [
                'id',
                'user_id',
                'user',
                'amount',
                'period',
                'status',
                'analyst',
                'comments',
                'repayments'
            ]
        ];
    }
}
