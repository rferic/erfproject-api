<?php


namespace App\Http\Requests;


use Illuminate\Validation\Rule;

abstract class CollectionRequest extends ApiFormRequest
{
    protected function getCollectionRules ( Array $rules ): array
    {
        $collection_rules = [
            'page' => 'numeric',
            'per_page' => 'numeric',
            'filter_text' => 'string',
            'order_by' => 'array',
            'order_column' => 'string',
            'order_direction' => [
                Rule::in(['asc', 'desc'])
            ],
            'with' => 'array'
        ];

        return array_merge($collection_rules, $rules);
    }
}
