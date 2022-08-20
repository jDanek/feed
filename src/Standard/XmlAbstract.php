<?php declare(strict_types=1);

namespace Danek\FeedIo\Standard;

use Danek\FeedIo\Formatter\XmlFormatter;
use Danek\FeedIo\FormatterInterface;
use Danek\FeedIo\StandardAbstract;
use Danek\FeedIo\RuleSet;
use Danek\FeedIo\Rule\ModifiedSince;
use Danek\FeedIo\Rule\Title;

abstract class XmlAbstract extends StandardAbstract
{

    /**
     * Name of the node containing all the feed's items
     */
    const ITEM_NODE = 'item';

    /**
     * This is for XML Standards
     */
    const SYNTAX_FORMAT = 'Xml';

    /**
     * RuleSet used to parse the feed's main node
     * @var RuleSet
     */
    protected $feedRuleSet;

    /**
     * @var RuleSet
     */
    protected $itemRuleSet;

    /**
     * Formats the document according to the standard's specification
     * @param \DOMDocument $document
     * @return \DOMDocument
     */
    abstract public function format(\DOMDocument $document): \DOMDocument;

    /**
     * @param \DOMDocument $document
     * @return \DOMElement
     */
    abstract public function getMainElement(\DOMDocument $document): \DOMElement;

    /**
     * Builds and returns a rule set to parse the root node
     * @return RuleSet
     */
    abstract public function buildFeedRuleSet(): RuleSet;

    /**
     * Builds and returns a rule set to parse an item
     * @return RuleSet
     */
    abstract public function buildItemRuleSet(): RuleSet;

    /**
     * @return string
     */
    public function getItemNodeName(): string
    {
        return static::ITEM_NODE;
    }

    /**
     * @return FormatterInterface
     */
    public function getFormatter(): FormatterInterface
    {
        return new XmlFormatter($this);
    }

    /**
     * Returns the RuleSet used to parse the feed's main node
     * @return RuleSet
     */
    public function getFeedRuleSet(): RuleSet
    {
        if (is_null($this->feedRuleSet)) {
            $this->feedRuleSet = $this->buildFeedRuleSet();
        }

        return $this->feedRuleSet;
    }

    /**
     * @return RuleSet
     */
    public function getItemRuleSet(): RuleSet
    {
        if (is_null($this->itemRuleSet)) {
            $this->itemRuleSet = $this->buildItemRuleSet();
        }

        return $this->itemRuleSet;
    }

    /**
     * @param string $tagName
     * @return ModifiedSince
     */
    public function getModifiedSinceRule(string $tagName): ModifiedSince
    {
        $rule = new ModifiedSince($tagName);
        $rule->setDefaultFormat($this->getDefaultDateFormat());
        $rule->setDateTimeBuilder($this->dateTimeBuilder);

        return $rule;
    }

    /**
     * @return RuleSet
     */
    protected function buildBaseRuleSet(): RuleSet
    {
        $ruleSet = $ruleSet = new RuleSet();
        $ruleSet->add(new Title());

        return $ruleSet;
    }
}
