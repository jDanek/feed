<?php declare(strict_types=1);

namespace Danek\FeedIo;

use Danek\FeedIo\Rule\DateTimeBuilder;

abstract class DateRuleAbstract extends RuleAbstract
{
    /**
     * @var DateTimeBuilder
     */
    protected $dateTimeBuilder = null;

    /**
     * @var string
     */
    protected $defaultFormat = \DateTime::RSS;

    /**
     * @param DateTimeBuilder $dateTimeBuilder
     * @return DateRuleAbstract
     */
    public function setDateTimeBuilder(DateTimeBuilder $dateTimeBuilder): DateRuleAbstract
    {
        $this->dateTimeBuilder = $dateTimeBuilder;

        return $this;
    }

    /**
     * @return DateTimeBuilder
     */
    public function getDateTimeBuilder(): DateTimeBuilder
    {
        if (is_null($this->dateTimeBuilder)) {
            throw new \UnexpectedValueException('date time builder should not be null');
        }

        return $this->dateTimeBuilder;
    }

    /**
     * @return string
     */
    public function getDefaultFormat(): string
    {
        return $this->defaultFormat;
    }

    /**
     * @param string $defaultFormat
     */
    public function setDefaultFormat(string $defaultFormat): void
    {
        $this->defaultFormat = $defaultFormat;
    }
}
