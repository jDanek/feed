<?php declare(strict_types=1);

namespace Danek\FeedIo;

use Danek\FeedIo\Adapter\NotFoundException;
use Danek\FeedIo\Rule\OptionalField;

class RuleSet
{
    /**
     * @var \ArrayIterator
     */
    protected $rules;

    /**
     * @var array
     */
    protected $aliases = [];

    /**
     * @var RuleAbstract
     */
    protected $default;

    /**
     * @param RuleAbstract|null $default default rule
     */
    public function __construct(RuleAbstract $default = null)
    {
        $this->rules = new \ArrayIterator(array());
        $this->default = is_null($default) ? new OptionalField() : $default;
    }

    /**
     * @return RuleAbstract
     */
    public function getDefault(): RuleAbstract
    {
        return $this->default;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules->getArrayCopy();
    }

    /**
     * @param RuleAbstract $rule
     * @param array $aliases
     * @return RuleSet
     */
    public function add(RuleAbstract $rule, array $aliases = []): RuleSet
    {
        $this->rules->offsetSet(strtolower($rule->getNodeName()), $rule);
        $this->addAliases($rule->getNodeName(), $aliases);

        return $this;
    }

    /**
     * @param string $name
     * @param array $aliases
     * @return RuleSet
     */
    public function addAliases(string $name, array $aliases): RuleSet
    {
        foreach ($aliases as $alias) {
            $this->aliases[strtolower($alias)] = strtolower($name);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return RuleAbstract
     * @throws NotFoundException
     */
    public function get(string $name): RuleAbstract
    {
        $name = $this->getNameForAlias(strtolower($name));
        if ($this->rules->offsetExists($name)) {
            return $this->rules->offsetGet($name);
        }

        return $this->default;
    }

    /**
     * @param string $alias
     * @return string
     */
    public function getNameForAlias(string $alias): string
    {
        if (array_key_exists($alias, $this->aliases)) {
            return $this->aliases[$alias];
        }

        return $alias;
    }
}
