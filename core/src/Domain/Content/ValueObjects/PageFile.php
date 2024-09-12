<?php
namespace App\Domain\Content\ValueObjects;

use App\Domain\Content\Entities\Page;
use App\Domain\File\Entities\File;

class PageFile
{
    private Page $page;
    private File $file;

    public function __construct(Page $page, File $file)
    {
        $this->page = $page;
        $this->file = $file;
    }
}