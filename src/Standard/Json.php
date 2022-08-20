<?php declare(strict_types=1);

namespace Danek\FeedIo\Standard;

use Danek\FeedIo\Formatter\JsonFormatter;
use Danek\FeedIo\FormatterInterface;
use Danek\FeedIo\Reader\Document;
use Danek\FeedIo\StandardAbstract;

class Json extends StandardAbstract
{
    const SYNTAX_FORMAT = 'Json';

    protected $mandatoryFields = ['version', 'title', 'items'];

    /**
     * @param Document $document
     * @return bool
     */
    public function canHandle(Document $document): bool
    {
        return $document->isJson();
    }

    /**
     * @return FormatterInterface
     */
    public function getFormatter(): FormatterInterface
    {
        return new JsonFormatter();
    }
}
