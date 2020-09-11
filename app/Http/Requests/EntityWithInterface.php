<?php


namespace App\Http\Requests;


interface EntityWithInterface
{
    public function getWiths (): array;
}
