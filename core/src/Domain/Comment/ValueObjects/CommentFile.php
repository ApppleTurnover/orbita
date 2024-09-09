<?php
namespace App\Domain\Comment\ValueObjects;

class CommentFile
{
    private int $commentId;
    private string $fileId;
    private string $type;

    public function __construct(int $commentId, string $fileId, string $type)
    {
        $this->commentId = $commentId;
        $this->fileId = $fileId;
        $this->type = $type;
    }

    public function getCommentId(): int
    {
        return $this->commentId;
    }

    public function getFileId(): string
    {
        return $this->fileId;
    }

    public function getFileType(): string
    {
        return $this->type;
    }
}