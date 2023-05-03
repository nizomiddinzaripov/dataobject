<?php

namespace Programm011\Dataobject;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class DataObjectPagination
{
    public string $view = 'pagination';

    public function __construct(
        protected int $totalCount,
        protected int $limit,
        protected int $page
    ) {
    }

    public function render(): View|Application|Factory|null
    {
        if ($this->limit >= $this->totalCount) {
            return null;
        }
        $items = $this->make();

        return view($this->view, compact(['items']));
    }

    /**
     * @return array
     */
    protected function make(): array
    {
        $pagesCount = intval($this->totalCount / $this->limit);
        if ($this->totalCount % $this->limit != 0) {
            $pagesCount++;
        }
        $pre  = $this->page == 1 ? 1 : $this->page - 1;
        $next = $this->page == $pagesCount ? $pagesCount : $this->page + 1;

        $start = $this->page > 2 ? $this->page - 2 : 1;
        $end   = $pagesCount > $this->page + 2 ? $this->page + 2 : $pagesCount;

        $items[] = [
            'label'     => '&laquo;',
            'url'       => $this->makeUrl($pre),
            'is_active' => $this->page == 1,
        ];

        for ($i = $start; $i <= $end; $i++) {
            $items[] = [
                'label'     => $i,
                'url'       => $this->makeUrl($i),
                'is_active' => $i == $this->page,
            ];
        }

        $items[] = [
            'label'     => '&raquo;',
            'url'       => $this->makeUrl($next),
            'is_active' => $this->page == $pagesCount,
        ];

        return $items;
    }

    protected function makeUrl(int $id): string
    {
        $queries = request()->all();
        unset($queries['page']);

        if (sizeof($queries) == 0) {
            return "?page=" . $id;
        }

        return http_build_query($queries) . '&page=' . $id;
    }
}
