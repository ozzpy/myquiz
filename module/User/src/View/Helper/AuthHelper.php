<?php

declare(strict_types=1);

namespace User\View\Helper;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Helper\AbstractHelper;
use User\Plugin\AuthPlugin;

class AuthHelper extends AbstractHelper
{
    protected $authPlugin;

    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        if (null === $this->authPlugin) {
            $this->setAuthPlugin($container->get(AuthPlugin::class));
        }

        return $this->authPlugin;
    }

    public function getAuthPlugin()
    {
        if (null === $this->authPlugin) {
            $this->setAuthPlugin(new AuthPlugin());
        }

        return $this->authPlugin;
    }

    public function setAuthPlugin($authPlugin)
    {
        if (!$authPlugin instanceof AuthPlugin) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s expects a %s instance; received %s',
                    __METHOD__,
                    AuthPlugin::class,
                    (is_object($authPlugin) ? get_class(
                        $authPlugin
                    ) : gettype($authPlugin))
                )
            );
        }

        $this->authPlugin = $authPlugin;
        return $this;
    }
}
