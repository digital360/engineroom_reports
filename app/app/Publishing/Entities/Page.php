<?php


namespace App\Publishing\Entities;


use Illuminate\View\View;

class Page
{
    protected $publication;
    protected $pageTemplate;
    protected $model;

    public function __construct(Publication $publication, array $model = [])
    {
        $this->publication = $publication;
        $this->model = $model;
    }

    public function getPublication(): Publication
    {
        return $this->publication;
    }

    public function makeViewModel(): array
    {
        return array_merge(
            $this->model,
            [
                'key' => $this->publication->getKey(),
                'page' => $this->getPageNumber(),
                'page_count' => $this->publication->getPageCount(),
            ]
        );
    }

    public function getPageTemplate(): string
    {
        return $this->pageTemplate ?? $this->publication->getPageTemplate();
    }

    public function getPageNumber(): int
    {
        return $this->publication->pageIndex($this) + 1;
    }

    public function output(): View
    {
        return view($this->getPageTemplate(), $this->makeViewModel());
    }
}
