<?php declare(strict_types=1);

namespace Danek\FeedIo\Feed\Item;

use Danek\FeedIo\Feed\ArrayableInterface;

class Media implements MediaInterface, ArrayableInterface
{
    /**
     * @var string
     */
    protected $nodeName;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $length;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $thumbnail;

    /**
     * @return string
     */
    public function getNodeName(): ?string
    {
        return $this->nodeName;
    }

    /**
     * @param string $nodeName
     * @return MediaInterface
     */
    public function setNodeName(string $nodeName): MediaInterface
    {
        $this->nodeName = $nodeName;

        return $this;
    }

    /**
     * @return bool
     * @deprecated
     */
    public function isThumbnail(): bool
    {
        error_log('Method isThumbnail is deprecated and will be removed in feed-io 5.0', E_USER_DEPRECATED);
        return $this->nodeName === 'media:thumbnail';
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return MediaInterface
     */
    public function setType(?string $type): MediaInterface
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     * @return MediaInterface
     */
    public function setUrl(?string $url): MediaInterface
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLength(): ?string
    {
        return $this->length;
    }

    /**
     * @param mixed $length
     * @return MediaInterface
     */
    public function setLength($length): MediaInterface
    {
        $this->length = (string)intval($length);

        return $this;
    }


    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return MediaInterface
     */
    public function setTitle(?string $title): MediaInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return MediaInterface
     */
    public function setDescription(?string $description): MediaInterface
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * @param string|null $thumbnail
     * @return MediaInterface
     */
    public function setThumbnail(?string $thumbnail): MediaInterface
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
