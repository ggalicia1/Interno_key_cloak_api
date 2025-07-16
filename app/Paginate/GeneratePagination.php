<?php

namespace App\Paginate;

class GeneratePagination
{
    public static function pagination(array $data, int|null $total) : Pagination
    {
        return new Pagination($data['page_size'], $data['page'], $total);
    }
}
