<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final class TodayFilter extends AbstractFilter
{
    private Security $security;
    protected ?\Symfony\Component\Serializer\NameConverter\NameConverterInterface $nameConverter;

    public function __construct(Security $security, $nameConverter = null)
    {
        $this->security = $security;
        $this->nameConverter = $nameConverter;
    }

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        // Restrict access to veterinarians
        if (!$this->security->isGranted('ROLE_VETERINARIAN')) {
            return;
        }

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
                'description' => 'Filter consultations for today (only for veterinarians)',
            ],
        ];
    }
}