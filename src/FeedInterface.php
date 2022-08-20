<?php declare(strict_types=1);

namespace Danek\FeedIo;

use Danek\FeedIo\Feed\NodeInterface;
use Danek\FeedIo\Feed\ItemInterface;
use Danek\FeedIo\Feed\StyleSheet;


/**
 * Interface FeedInterface
 * Represents the top node of a news feed
 * @package FeedIo
 */
interface FeedInterface extends \Iterator, \Countable, NodeInterface
{

    /**
     * This method MUST return the feed's full URL
     * @return string
     */
    public function getUrl(): ?string;

    /**
     * @param string|null $url
     * @return FeedInterface
     */
    public function setUrl(string $url = null): FeedInterface;

    /**
     * @return string $language
     */
    public function getLanguage(): ?string;

    /**
     * @param string|null $language
     * @return FeedInterface
     */
    public function setLanguage(string $language = null): FeedInterface;

    /**
     * @return string
     */
    public function getLogo(): ?string;

    /**
     * @param string|null $logo
     * @return FeedInterface
     */
    public function setLogo(string $logo = null): FeedInterface;

    /**
     * Atom : feed.entry <feed><entry>
     * Rss  : rss.channel.item <rss><channel><item>
     * @param ItemInterface $item
     * @return FeedInterface
     */
    public function add(ItemInterface $item): FeedInterface;

    /**
     * @return ItemInterface
     */
    public function newItem(): ItemInterface;

    public function addNS(string $ns, string $dtd): FeedInterface;

    public function setStyleSheet(StyleSheet $styleSheet): FeedInterface;

    public function getStyleSheet(): ?StyleSheet;
}
