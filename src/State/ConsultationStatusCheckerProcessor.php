<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Entity\Consultation;
use Symfony\Bundle\SecurityBundle\Security;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ConsultationStatusCheckerProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private Security $security
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Vérifiez si l'entité est une Consultation
        if (!$data instanceof Consultation) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        $user = $this->security->getUser();

        if ($this->security->isGranted('ROLE_ASSISTANT')) {
            // Les assistants peuvent modifier uniquement si le statut actuel n'est pas "done"
            $originalData = $context['previous_data'] ?? null;

            if ($originalData instanceof Consultation) {
                if ($originalData->getStatus()->value === 'done') {
                    throw new BadRequestException('Assistants are not allowed to edit consultations with status "done".');
                }
            }

            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        if ($this->security->isGranted('ROLE_VETERINARIAN')) {
            $originalData = $context['previous_data'] ?? null;

            if ($originalData instanceof Consultation) {
                // On autorise toujours la modification si la consultation n'a pas de vétérinaire
                if ($originalData->getVeterinarian() === null) {
                    return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
                }
                
                // Sinon on vérifie si seul le statut est modifié
                if ($originalData->getStatus() !== $data->getStatus()) {
                    return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
                }

                throw new BadRequestException('Veterinarians can only modify the status or take unassigned consultations.');
            }

            throw new BadRequestException('Original data not found for comparison.');
        }

        // Si aucune condition n'est remplie, lancez une exception
        throw new Exception('Not allowed to edit this consultation.');
    }
}
