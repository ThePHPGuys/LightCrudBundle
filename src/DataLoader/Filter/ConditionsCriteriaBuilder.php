<?php

namespace TPG\LightCrudBundle\DataLoader\Filter;


use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\ExpressionBuilder;
use Exception;

class ConditionsCriteriaBuilder
{

    public function build(ConditionsGroup $group): Criteria
    {
        $criteria = Criteria::create();
        $criteria->where($this->resolveGroup($group));
        return $criteria;
    }

    private function resolveGroup(ConditionsGroup $group): Expression
    {
        $expressions = [];
        foreach ($group->getConditions() as $condition) {
            $expressions[] = $this->resolveCondition($condition);
        }

        foreach ($group->getGroups() as $subgroup) {
            $expressions[] = $this->resolveGroup($subgroup);
        }

        return $group->isAnd() ? Criteria::expr()->andX(...$expressions) : Criteria::expr()->orX(...$expressions);
    }

    private function resolveCondition(Condition $condition): Comparison
    {
        $property = $condition->property();
        $value = $condition->value();

        /**
         * @var $expr ExpressionBuilder
         */
        $expr = Criteria::expr();
        switch ($condition->operator()) {
            case '=':
                return $expr->eq($property, $value);
            case '>':
                return $expr->gt($property, $value);
            case '>=':
                return $expr->gte($property, $value);
            case '<':
                return $expr->lt($property, $value);
            case '<=':
                return $expr->lte($property, $value);
            case '<>':
                return $expr->neq($property, $value);
            case 'startsWith':
                return $expr->startsWith($property, $value);
            case 'endsWith':
                return $expr->endsWith($property, $value);
            case 'contains':
                return $expr->contains($property, $value);
            case 'in':
                return $expr->in($property, (array)$value);
            case 'notIn':
                return $expr->notIn($property, (array)$value);
            case 'notEmpty':
                return $expr->neq($property, null);
            default:
                throw new Exception(sprintf('Unimplemented %s operator', $condition->operator()));
        }
    }
}