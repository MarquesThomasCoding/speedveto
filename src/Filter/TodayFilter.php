<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

final class TodayFilter extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($property !== 'consultationDate' || $value !== 'today') {
            return;
        }

        $today = new \DateTime();
        $today->setTime(0, 0);
        $tomorrow = clone $today;
        $tomorrow->modify('+1 day');

        $parameterName = $queryNameGenerator->generateParameterName('today');
        $tomorrowParameterName = $queryNameGenerator->generateParameterName('tomorrow');

        $queryBuilder
            ->andWhere(sprintf('o.consultationDate >= :%s', $parameterName))
            ->andWhere(sprintf('o.consultationDate < :%s', $tomorrowParameterName))
            ->setParameter($parameterName, $today)
            ->setParameter($tomorrowParameterName, $tomorrow);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'consultationDate[today]' => [
                'property' => 'consultationDate',
                'type' => 'string',
                'required' => false,
                'description' => 'Filter consultations for today',
            ],
        ];
    }
} 