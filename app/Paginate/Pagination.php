<?php

namespace App\Paginate;

use App\Http\Resources\ClientResource;

class Pagination
{
    public int $page_size;
    public int $previous_page;
    public int $current_page;
    public int $page_index;
    public int $next_page;
    public int|null $total_page = null;
    public int|null $total = null;
    public array $data;
    public function __construct(int $page_size, int $current_page, int|null $total)
    {
        $this->page_size = $page_size;
        $this->current_page = $current_page;
        $this->page_index = ($current_page - 1) * $page_size;
        $this->previous_page = $current_page - 1;
        $this->next_page = $current_page + 1;
        $this->total = $total;
    }
}
