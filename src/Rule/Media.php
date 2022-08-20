<?php declare(strict_types=1);

namespace Danek\FeedIo\Rule;

use Danek\FeedIo\Feed\Item;
use Danek\FeedIo\Feed\Item\MediaInterface;
use Danek\FeedIo\Feed\ItemInterface;
use Danek\FeedIo\Feed\NodeInterface;
use Danek\FeedIo\Parser\UrlGenerator;
use Danek\FeedIo\RuleAbstract;

class Media extends RuleAbstract
{
    const NODE_NAME = 'enclosure';

    const MRSS_NAMESPACE = "http://search.yahoo.com/mrss/";

    protected $urlAttributeName = 'url';

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    public function __construct(string $nodeName = null)
    {
        $this->urlGenerator = new UrlGenerator();
        parent::__construct($nodeName);
    }

    /**
     * @return string
     */
    public function getUrlAttributeName(): string
    {
        return $this->urlAttributeName;
    }

    /**
     * @param string $name
     */
    public function setUrlAttributeName(string $name): void
    {
        $this->urlAttributeName = $name;
    }

    /**
     * @param NodeInterface $node
     * @param \DOMElement $element
     */
    public function setProperty(NodeInterface $node, \DOMElement $element): void
    {
        if ($node instanceof ItemInterface) {
            $media = $node->newMedia();
            $media->setNodeName($element->nodeName);

            switch ($element->nodeName) {
                case 'media:group':
                    $this->initMedia($media, $element);
                    $this->setUrl($media, $node, $this->getChildAttributeValue($element, 'content', 'url', static::MRSS_NAMESPACE));
                    break;
                case 'media:content':
                    $this->initMedia($media, $element);
                    $this->setUrl($media, $node, $this->getAttributeValue($element, "url"));
                    break;
                default:
                    $media
                        ->setType($this->getAttributeValue($element, 'type'))
                        ->setLength($this->getAttributeValue($element, 'length'));
                    $this->setUrl($media, $node, $this->getAttributeValue($element, $this->getUrlAttributeName()));
                    break;
            }
            $node->addMedia($media);
        }
    }

    /**
     * @param MediaInterface $media
     * @param NodeInterface $node
     * @param string|null $url
     */
    protected function setUrl(MediaInterface $media, NodeInterface $node, string $url = null): void
    {
        if (!is_null($url)) {
            $media->setUrl(
                $this->urlGenerator->getAbsolutePath($url, $node->getHost())
            );
        }
    }

    /**
     * @param \DOMDocument $document
     * @param MediaInterface $media
     * @return \DOMElement
     */
    public function createMediaElement(\DOMDocument $document, MediaInterface $media): \DOMElement
    {
        $element = $document->createElement($this->getNodeName());
        $element->setAttribute($this->getUrlAttributeName(), $media->getUrl());
        $element->setAttribute('type', $media->getType() ?? '');
        $element->setAttribute('length', $media->getLength() ?? '');

        return $element;
    }

    /**
     * @param MediaInterface $media
     * @param \DOMElement $element
     */
    protected function initMedia(MediaInterface $media, \DOMElement $element): void
    {
        $media->setType($element->getAttribute('type'));
        $media->setTitle($this->getChildValue($element, 'title', static::MRSS_NAMESPACE));
        $media->setDescription($this->getChildValue($element, 'description', static::MRSS_NAMESPACE));
        $media->setThumbnail($this->getChildAttributeValue($element, 'thumbnail', 'url', static::MRSS_NAMESPACE));
    }

    /**
     * @inheritDoc
     */
    protected function hasValue(NodeInterface $node): bool
    {
        return $node instanceof ItemInterface && !!$node->getMedias();
    }

    /**
     * @inheritDoc
     */
    protected function addElement(\DOMDocument $document, \DOMElement $rootElement, NodeInterface $node): void
    {
        foreach ($node->getMedias() as $media) {
            $rootElement->appendChild($this->createMediaElement($document, $media));
        }
    }
}
