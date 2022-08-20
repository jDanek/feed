<?php declare(strict_types=1);

namespace Danek\FeedIo\Standard;

use Danek\FeedIo\Reader\Document;
use Danek\FeedIo\Rule\Atom\Author;
use Danek\FeedIo\Rule\Atom\Content;
use Danek\FeedIo\Rule\Atom\LinkNode;
use Danek\FeedIo\Rule\Atom\Logo;
use Danek\FeedIo\Rule\Atom\Summary;
use Danek\FeedIo\Rule\Description;
use Danek\FeedIo\Rule\Language;
use Danek\FeedIo\Rule\Media;
use Danek\FeedIo\Rule\PublicId;
use Danek\FeedIo\Rule\Atom\Category;
use Danek\FeedIo\RuleSet;

class Atom extends XmlAbstract
{
    /**
     * Atom document must have a <feed> root node
     */
    const ROOT_NODE_TAGNAME = 'feed';

    const ITEM_NODE = 'entry';

    const DATETIME_FORMAT = \DateTime::ATOM;

    /**
     * Formats the document according to the standard's specification
     * @param \DOMDocument $document
     * @return \DOMDocument
     */
    public function format(\DOMDocument $document): \DOMDocument
    {
        $element = $document->createElement('feed');
        $element->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        $document->appendChild($element);

        return $document;
    }

    /**
     * Tells if the parser can handle the feed or not
     * @param Document $document
     * @return mixed
     */
    public function canHandle(Document $document): bool
    {
        if (!isset($document->getDOMDocument()->documentElement->tagName)) {
            return false;
        }
        return self::ROOT_NODE_TAGNAME === $document->getDOMDocument()->documentElement->tagName;
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
     * Builds and returns a rule set to parse the root node
     * @return RuleSet
     */
    public function buildFeedRuleSet(): RuleSet
    {
        $ruleSet = $this->buildBaseRuleSet();
        $ruleSet
            ->add(new Logo())
            ->add(new Description());

        return $ruleSet;
    }

    /**
     * Builds and returns a rule set to parse an item
     * @return RuleSet
     */
    public function buildItemRuleSet(): RuleSet
    {
        $ruleSet = $this->buildBaseRuleSet();
        $ruleSet
            ->add(new Content())
            ->add(new Summary())
            ->add(new Media(), ['media:group'])
            ->add(new Media(), ['media:content']);

        return $ruleSet;
    }

    /**
     * @return RuleSet
     */
    protected function buildBaseRuleSet(): RuleSet
    {
        $ruleSet = parent::buildBaseRuleSet();
        $ruleSet
            ->add(new Category())
            ->add(new Author())
            ->add(new LinkNode())
            ->add(new PublicId('id'))
            ->add(new Language('lang'))
            ->add($this->getModifiedSinceRule('updated'), ['published']);

        return $ruleSet;
    }
}
