<?php


namespace App\Http\Commands\Interfaces;


interface GetCollection
{
    public function __construct ( array $filters = [], array $with = [], int $page = 1, int $per_page = 20, string $order_column = null, string $order_direction = null );
    public function getPage (): int;
    public function getPerPage(): int;
    public function getOrderColumn(): ?string;
    public function getOrderDirection(): ?string;
    public function filter (): \Illuminate\Database\Eloquent\Builder;
}
