<?php declare(strict_types=1);

namespace Danek\FeedIo\Reader;

class FixerSet
{
    protected $fixers = [];

    /**
     * @param FixerAbstract
     * @return FixerSet
     */
    public function add(FixerAbstract $fixer): FixerSet
    {
        $this->fixers[] = $fixer;

        return $this;
    }

    /**
     * @param Result $result
     * @return FixerSet
     */
    public function correct(Result $result): FixerSet
    {
        foreach ($this->fixers as $fixer) {
            $fixer->correct($result);
        }

        return $this;
    }
}
