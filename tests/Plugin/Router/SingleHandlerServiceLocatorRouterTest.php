<?php
/**
 * This file is part of the prooph/service-bus.
 * (c) 2014-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\ServiceBus\Plugin\Router;

use Interop\Container\ContainerInterface;
use Prooph\Common\Event\DefaultActionEvent;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Router\SingleHandlerServiceLocatorRouter;
use ProophTest\ServiceBus\Mock\MessageHandler;
use ProophTest\ServiceBus\TestCase;

class SingleHandlerServiceLocatorRouterTest extends TestCase
{
    /**
     * @test
     */
    public function it_routes()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('message')->willReturn(true)->shouldBeCalled();
        $container->get('message')->willReturn(new MessageHandler())->shouldBeCalled();

        $commandBus = new CommandBus();

        $actionEvent = new DefaultActionEvent(
            MessageBus::EVENT_DISPATCH,
            $commandBus,
            [
                MessageBus::EVENT_PARAM_MESSAGE_NAME => 'message',
            ]
        );

        $router = new SingleHandlerServiceLocatorRouter($container->reveal());
        $commandBus->utilize($router);

        $router->onRouteMessage($actionEvent);

        $this->assertInstanceOf(MessageHandler::class, $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE_HANDLER));
    }
}
