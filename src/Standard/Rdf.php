<?php declare(strict_types=1);

namespace Danek\FeedIo\Standard;

use Danek\FeedIo\Reader\Document;
use Danek\FeedIo\RuleSet;
use Danek\FeedIo\Rule\Structure;

class Rdf extends Rss
{

    /**
     * Format version
     */
    const VERSION = '1.0';

    /**
     * RDF document must have a <rdf> root node
     */
    const ROOT_NODE_TAGNAME = 'rdf';

    /**
     * publication date
     */
    const DATE_NODE_TAGNAME = 'dc:date';

    /**
     * Tells if the parser can handle the feed or not
     * @param Document $document
     * @return boolean
     */
    public function canHandle(Document $document): bool
    {
        if (!isset($document->getDOMDocument()->documentElement->tagName)) {
            return false;
        }
        return false !== strpos($document->getDOMDocument()->documentElement->tagName, static::ROOT_NODE_TAGNAME);
    }

    /**
     * @param \DOMDocument $document
     * @return \DOMElement
     */
    public function getMainElement(\DOMDocument $document): \DOMElement
    {
        return $document->documentElement;
    }

    /**
     * @return RuleSet
     */
    public function buildFeedRuleSet(): RuleSet
    {
        $ruleSet = new RuleSet();
        $ruleSet->add(new Structure(static::CHANNEL_NODE_TAGNAME, $this->buildItemRuleSet()));

        return $ruleSet;
    }
}
