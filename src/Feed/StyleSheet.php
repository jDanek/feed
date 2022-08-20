<?php declare(strict_types=1);

namespace Danek\FeedIo\Feed;

class StyleSheet
{
    /**
     * @var string
     */
    private $href;

    /**
     * @var string
     */
    private $type;

    const DEFAULT_TYPE = 'text/xsl';

    /**
     * StyleSheet constructor.
     * @param string $href
     * @param string $type
     */
    public function __construct(string $href, string $type = self::DEFAULT_TYPE)
    {
        $this->href = $href;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
