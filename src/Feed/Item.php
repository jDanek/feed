<?php declare(strict_types=1);

namespace Danek\FeedIo\Feed;

use Danek\FeedIo\Feed\Item\Media;
use Danek\FeedIo\Feed\Item\MediaInterface;

class Item extends Node implements ItemInterface
{

    /**
     * @var \ArrayIterator
     */
    protected $medias;

    /**
     * @var string
     */
    protected $summary;

    /**
     * @var string
     */
    protected $content;

    public function __construct()
    {
        $this->medias = new \ArrayIterator();

        parent::__construct();
    }

    /**
     * @param MediaInterface $media
     * @return ItemInterface
     */
    public function addMedia(MediaInterface $media): ItemInterface
    {
        $this->medias->append($media);

        return $this;
    }

    /**
     * @return iterable
     */
    public function getMedias(): iterable
    {
        return $this->medias;
    }

    /**
     * @return boolean
     */
    public function hasMedia(): bool
    {
        return $this->medias->count() > 0;
    }

    /**
     * @return MediaInterface
     */
    public function newMedia(): MediaInterface
    {
        return new Media();
    }

    /**
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @param string|null $summary
     * @return ItemInterface
     */
    public function setSummary(string $summary = null): ItemInterface
    {
        $this->summary = $summary;

        return $this;
    }

    public function getDescription(): ?string
    {
        error_log('Method getDescription is deprecated and will be removed in feed-io 5.0. Please use getContent() instead', E_USER_DEPRECATED);
        return parent::getDescription();
    }

    /**
     * Returns the 'content' for Atom and JSONFeed formats, 'description' for RSS
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content ?? $this->description;
    }

    /**
     * @param string|null $content
     * @return ItemInterface
     */
    public function setContent(string $content = null): ItemInterface
    {
        $this->content = $content;
        // Will be removed in 5.0
        $this->setDescription($content);

        return $this;
    }
}
