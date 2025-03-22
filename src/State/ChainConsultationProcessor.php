<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Psr\Log\LoggerInterface;

class ChainConsultationProcessor implements ProcessorInterface
{
    public function __construct(
        private ConsultationStatusCheckerProcessor $statusProcessor,
        private ConsultationVeterinarianAttributionProcessor $veterinarianProcessor,
        private LoggerInterface $logger
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        try {
            $this->logger->info('Début du ChainConsultationProcessor');
            
            // Log des données entrantes
            $this->logger->info('Données entrantes:', [
                'data' => $data,
                'operation' => $operation->getName(),
                'context' => $context
            ]);

            // Exécute le status checker
            $this->logger->info('Exécution du StatusCheckerProcessor');
            $data = $this->statusProcessor->process($data, $operation, $uriVariables, $context);
            
            // Exécute le veterinarian attribution
            $this->logger->info('Exécution du VeterinarianAttributionProcessor');
            $result = $this->veterinarianProcessor->process($data, $operation, $uriVariables, $context);
            
            $this->logger->info('ChainConsultationProcessor terminé avec succès');
            return $result;
            
        } catch (\Exception $e) {
            $this->logger->error('Erreur dans ChainConsultationProcessor: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 