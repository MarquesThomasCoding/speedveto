<?php 

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Entity\Consultation;
use Symfony\Bundle\SecurityBundle\Security;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ConsultationVeterinarianAttributionProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private Security $security
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof Consultation) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        $user = $this->security->getUser();

        if($this->security->isGranted('ROLE_VETERINARIAN')) {
            if ($data->getVeterinarian() === null) {
                $data->setVeterinarian($user);
                return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            }
            
            throw new BadRequestException('This consultation is already assigned to a veterinarian.');
        }

        throw new BadRequestException('You are not authorized to manage appointments.');
    }
}
