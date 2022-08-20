<?php declare(strict_types=1);

namespace Danek\FeedIo\Standard;

use Danek\FeedIo\Reader\Document;
use Danek\FeedIo\Rule\Author;
use Danek\FeedIo\Rule\Description;
use Danek\FeedIo\Rule\Image;
use Danek\FeedIo\Rule\Language;
use Danek\FeedIo\Rule\Link;
use Danek\FeedIo\Rule\PublicId;
use Danek\FeedIo\Rule\Media;
use Danek\FeedIo\Rule\Category;
use Danek\FeedIo\Rule\Logo;
use Danek\FeedIo\RuleSet;

class Rss extends XmlAbstract
{

    /**
     * Format version
     */
    const VERSION = '2.0';

    /**
     * RSS document must have a <rss> root node
     */
    const ROOT_NODE_TAGNAME = 'rss';

    /**
     * <channel> node contains feed's metadata
     */
    const CHANNEL_NODE_TAGNAME = 'channel';

    /**
     * publication date
     */
    const DATE_NODE_TAGNAME = 'pubDate';

    protected $mandatoryFields = ['channel'];

    /**
     * Formats the document according to the standard's specification
     * @param \DOMDocument $document
     * @return \DOMDocument
     */
    public function format(\DOMDocument $document): \DOMDocument
    {
        $rss = $document->createElement(static::ROOT_NODE_TAGNAME);
        $rss->setAttribute('version', static::VERSION);

        $channel = $document->createElement(static::CHANNEL_NODE_TAGNAME);
        $rss->appendChild($channel);
        $document->appendChild($rss);

        return $document;
    }

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
        return static::ROOT_NODE_TAGNAME === $document->getDOMDocument()->documentElement->tagName;
    }

    /**
     * @param \DOMDocument $document
     * @return \DOMElement
     */
    public function getMainElement(\DOMDocument $document): \DOMElement
    {
        return $document->documentElement->getElementsByTagName(static::CHANNEL_NODE_TAGNAME)->item(0);
    }

    /**
     * @return RuleSet
     */
    public function buildFeedRuleSet(): RuleSet
    {
        $ruleSet = $this->buildBaseRuleSet();
        $ruleSet->add(new Language());

        return $ruleSet;
    }

    /**
     * @return RuleSet
     */
    public function buildItemRuleSet(): RuleSet
    {
        $ruleSet = $this->buildBaseRuleSet();
        $ruleSet
            ->add(new Author(), ['dc:creator'])
            ->add(new PublicId())
            ->add(new Image())
            ->add(new Media(), ['media:thumbnail'])
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
            ->add(new Link())
            ->add(new Description())
            ->add(new Category())
            ->add(new Logo())
            ->add($this->getModifiedSinceRule(static::DATE_NODE_TAGNAME), ['dc:date', 'lastBuildDate', 'lastPubDate']);

        return $ruleSet;
    }
}
