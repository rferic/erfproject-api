<?php


namespace App\Http\Commands\Interfaces;


interface SaveModel
{
    public function getValidator (): \Illuminate\Contracts\Validation\Validator;
    public function getRules (): array;
}
