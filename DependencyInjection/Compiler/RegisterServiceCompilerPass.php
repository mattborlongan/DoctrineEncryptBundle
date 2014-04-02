<?php

namespace TDM\DoctrineEncryptBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Description of RegisterServiceCompilerPass
 *
 * @author wpigott
 */
class RegisterServiceCompilerPass implements CompilerPassInterface {

    public function process(ContainerBuilder $container) {

        if ($container->hasParameter('tdm_doctrine_encrypt.encryptor_service')) {
            // Load some parameters
            $secretKey = $container->getParameter('tdm_doctrine_encrypt.secret_key');
            $encryptorServiceId = $container->getParameter('tdm_doctrine_encrypt.encryptor_service');

            // Get the definitions
            $encryptorDefinition = $this->getDefinition($container, $encryptorServiceId);

            $this->adjustDefinition($encryptorServiceId, $secretKey, $encryptorDefinition, $this->getDefinition($container, 'tdm_doctrine_encrypt.subscriber.encrypt'));
            $this->adjustDefinition($encryptorServiceId, $secretKey, $encryptorDefinition, $this->getDefinition($container, 'tdm_doctrine_encrypt.subscriber.decrypt'));
        }
    }

    private function adjustDefinition($encryptorServiceId, $secretKey, $encryptorDefinition, $subscriberDefinition) {
        $encryptorDefinition->setArguments(array($secretKey));
        $subscriberDefinition->replaceArgument(2, '');
        $subscriberDefinition->addArgument(new Reference($encryptorServiceId));
    }

    /**
     * 
     * @param ContainerBuilder $container
     * @param string $id
     * @return Definition
     * @throws \RuntimeException
     */
    private function getDefinition(ContainerBuilder $container, $id) {
        try {
            return $container->findDefinition($id);
        } catch (InvalidArgumentException $e) {
            throw new \RuntimeException('Unable to locate service (' . $id . ').', NULL, $e);
        }
    }

}

?>
