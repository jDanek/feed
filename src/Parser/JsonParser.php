<?php declare(strict_types=1);

namespace Danek\FeedIo\Parser;

use Danek\FeedIo\Feed\Item;
use Danek\FeedIo\Feed\Item\Author;
use Danek\FeedIo\Feed\ItemInterface;
use Danek\FeedIo\Feed\NodeInterface;
use Danek\FeedIo\FeedInterface;
use Danek\FeedIo\ParserAbstract;
use Danek\FeedIo\Reader\Document;

class JsonParser extends ParserAbstract
{

    /**
     * @param Document $document
     * @param FeedInterface $feed
     * @return FeedInterface
     * @throws \Exception
     */
    public function parseContent(Document $document, FeedInterface $feed): FeedInterface
    {
        $data = $document->getJsonAsArray();
        $feed->setTitle($this->readOffset($data, 'title'));
        $feed->setDescription($this->readOffset($data, 'description'));
        $feed->setLink($this->readOffset($data, 'home_page_url'));
        $feed->setUrl($this->readOffset($data, 'feed_url'));
        $feed->setLogo($this->readOffset($data, 'icon'));
        $this->readAuthor($feed, $data);

        if (array_key_exists('items', $data)) {
            $this->parseItems($data['items'], $feed);
        }

        return $feed;
    }

    /**
     * @param Document $document
     * @param iterable $mandatoryFields
     * @return bool
     * @throws MissingFieldsException
     */
    public function checkBodyStructure(Document $document, iterable $mandatoryFields): bool
    {
        $data = $document->getJsonAsArray();

        foreach ($mandatoryFields as $mandatoryField) {
            if (!array_key_exists($mandatoryField, $data)) {
                throw new MissingFieldsException("Missing {$mandatoryField} in the JSON Feed");
            }
        }

        return true;
    }

    /**
     * @param iterable $items
     * @param FeedInterface $feed
     * @return JsonParser
     * @throws \Exception
     */
    public function parseItems(iterable $items, FeedInterface $feed): JsonParser
    {
        foreach ($items as $dataItem) {
            $item = new Item();
            $item->setPublicId($this->readOffset($dataItem, 'id'));
            $item->setTitle($this->readOffset($dataItem, 'title'));
            $item->setLastModified(new \DateTime($this->readOffset($dataItem, 'date_published')));
            $contentHtml = $this->readOffset($dataItem, 'content_html');
            $item->setDescription($this->readOffset($dataItem, 'content_text', $contentHtml));
            $item->setLink($this->readOffset($dataItem, 'url'));
            $this->readAuthor($item, $dataItem);
            $this->readMedias($item, $dataItem);
            $feed->add($item);
        }

        return $this;
    }

    /**
     * @param array $data
     * @param string $offsetName
     * @param string|null $default
     * @return null|string
     */
    public function readOffset(array $data, string $offsetName, string $default = null): ?string
    {
        if (array_key_exists($offsetName, $data)) {
            return $data[$offsetName];
        }

        return $default;
    }

    protected function readMedias(ItemInterface $item, array $data): void
    {
        if (array_key_exists('attachments', $data)) {
            foreach ($data['attachments'] as $attachment) {
                $media = new Item\Media();
                $media
                    ->setType($attachment['mime_type'])
                    ->setUrl($attachment['url'])
                    ->setLength($attachment['size_in_bytes'] ?? null)
                    ->setTitle($attachment['title'] ?? null);
                $item->addMedia($media);
            }
        }
    }

    protected function readAuthor(NodeInterface $node, array $data): void
    {
        if (array_key_exists('author', $data)) {
            $author = $this->extractAuthor($data['author']);
            $node->setAuthor($author);
        }
        if (array_key_exists('authors', $data) && is_array($data['authors'])) {
            $author = $this->extractAuthor(reset($data['authors']));
            $node->setAuthor($author);
        }
    }

    protected function extractAuthor(array $data): Author
    {
        $author = new Author();
        $author->setName($this->readOffset($data, 'name'));
        $author->setUri($this->readOffset($data, 'url'));
        $author->setEmail($this->readOffset($data, 'email'));

        return $author;
    }
}
