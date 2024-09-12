<?php
namespace App\Domain\Content\ValueObjects;

use App\Domain\Content\Entities\Topic;
use App\Domain\File\Entities\File;
use App\Domain\User\Entities\User;

class TopicFile
{
    private Topic $topic;
    private File $file;

    public function __construct(Topic $topic, File $file)
    {
        $this->topic = $topic;
        $this->file = $file;
    }

    public function hasAccess(User $user): bool
    {
        return $this->topic->hasAccess($user);
    }
}