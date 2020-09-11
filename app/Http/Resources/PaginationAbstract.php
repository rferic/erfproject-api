<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @method total()
 * @method perPage()
 * @method currentPage()
 * @method lastPage()
 * @method url(int $int)
 * @method previousPageUrl()
 * @method nextPageUrl()
 */
class PaginationAbstract extends ResourceCollection
{
    protected function getPaginationData (): array
    {
        return [
            'total' => $this->total(),
            'count' => $this->count(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'total_pages' => $this->lastPage(),
            'first_page_url' => $this->url(1),
            'previous_page_url' => $this->previousPageUrl(),
            'next_page_url' => $this->nextPageUrl(),
            'last_page_url' => $this->url($this->lastPage()),
        ];
    }
}
