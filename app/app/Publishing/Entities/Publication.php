<?php


namespace App\Publishing\Entities;


class Publication
{
    protected $key;
    protected $pageTemplate = 'publishing.page';
    protected $pages = [];

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getPageTemplate()
    {
        return $this->pageTemplate;
    }

    public function getPages(): array
    {
        return $this->pages;
    }

    public function getPage(int $index): ?Page
    {
        return $this->getPages()[$index];
    }

    public function pageIndex(Page $page): int
    {
        $pages = $this->getPages();

        for ($i = 0; $i < count($pages); $i++) {
            if ($pages[$i] === $page) {
                return $i;
            }
        }

        throw new \DomainException('Could not find page');
    }

    public function addPage(array $model = []): Page
    {
        $page = new Page($this, $model);
        $this->pages[] = $page;

        return $page;
    }

    public function getPageCount(): int
    {
        return count($this->getPages());
    }
}
