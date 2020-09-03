<?php

namespace TPG\LightCrudBundle\DataLoader\Filter;


use InvalidArgumentException;

final class ConditionsGroup
{
    public const AND = 'AND';
    public const OR = 'OR';
    /**
     * @var string
     */
    private $type;
    /**
     * @var Condition[]
     */
    private $conditions;

    /**
     * @var self[]
     */
    private $groups;

    public function __construct(string $type = self:: AND)
    {
        if (!in_array(strtoupper($type), [self:: AND, self:: OR], true)) {
            throw new InvalidArgumentException('Incorrect conditions group type');
        }
        $this->type = $type;
    }

    public static function createFromArray(array $group): self
    {
        if (isset($group['type'])) {
            $conditionGroup = new ConditionsGroup($group['type']);
        } else {
            $conditionGroup = new ConditionsGroup();
        }
        if (!isset($group['conditions']) || count($group['conditions']) === 0) {
            throw new InvalidArgumentException('Incorrect array condition group struct');
        }
        foreach ($group['conditions'] as $condition) {
            if (!is_array($condition)) {
                continue;
            }
            if (isset($condition['type'], $condition['conditions'])) {
                $conditionGroup->addGroup(static::createFromArray($condition));
            }
            if (isset($condition['property'], $condition['operator'], $condition['value'])) {
                $conditionGroup->addCondition(Condition::createFromArray($condition));
            }
        }
        return $conditionGroup;
    }

    public function addGroup(self $group): void
    {
        $this->groups[] = $group;
    }

    public function addCondition(Condition $condition): void
    {
        $this->conditions[] = $condition;
    }

    public function isOr(): bool
    {
        return !$this->isAnd();
    }

    public function isAnd(): bool
    {
        return $this->type === self:: AND;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getGroups(): array
    {
        return $this->groups ?? [];
    }
}