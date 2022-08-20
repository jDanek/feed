<?php declare(strict_types=1);

namespace Danek\FeedIo\Rule\Atom;

use Danek\FeedIo\Feed\Item\MediaInterface;
use Danek\FeedIo\Rule\Media as BaseMedia;

class Media extends BaseMedia
{
    const NODE_NAME = 'link';

    /**
     * @inheritDoc
     */
    public function createMediaElement(\DOMDocument $document, MediaInterface $media): \DOMElement
    {
        $element = parent::createMediaElement($document, $media);
        $element->setAttribute('rel', 'enclosure');

        return $element;
    }
}
