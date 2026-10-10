<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
 * Every test in tests/Feature boots the Laravel application (TestCase)
 * and runs inside a database transaction that is rolled back afterwards.
 */
pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');
