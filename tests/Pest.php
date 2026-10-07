<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

function admin(): Pengguna
{
    return Pengguna::factory()->admin()->create();
}

function staf(): Pengguna
{
    return Pengguna::factory()->create();
}
