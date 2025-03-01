<?php

namespace App\Application\CQRS\Trait;

use http\Exception\RuntimeException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

trait HandleMultiplyTrait
{
    private MessageBusInterface $messageBus;

    /**
     *
     * This trait provides utility methods for handling messages with the Symfony Messenger component.
     *
     * The `handle` method dispatches a message using the `MessageBusInterface` and expects exactly one handler to process the message.
     * It retrieves the results of handling using the `HandledStamp` instances associated with the message envelope.
     *
     * The `getResultByHandlerName` method allows retrieving a specific handler's result by its name, with support for matching handler names that include namespaces.
     * 
     * @throws LogicException If the message bus is not initialized, or if the message is not handled as expected.
     * @throws ExceptionInterface
     */
    private function handle(object $message): mixed
    {
        if (!isset($this->messageBus)) {
            throw new LogicException(\sprintf('You must provide a "%s" instance in the "%s::$messageBus" property, but that property has not been initialized yet.', MessageBusInterface::class, static::class));
        }

        $envelope = $this->messageBus->dispatch($message);
        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        if (!$handledStamps) {
            throw new LogicException(\sprintf('Message of type "%s" was handled zero times. Exactly one handler is expected when using "%s::%s()".', get_debug_type($envelope->getMessage()), static::class, __FUNCTION__));
        }
        

        $results = [];
        foreach ($handledStamps as $handledStamp) {
            $results[$handledStamp->getHandlerName()] = $handledStamp->getResult();
        }

        return $results;
    }


    /**
     * Retrieves the result associated with a specific handler name from the provided results array.
     *
     * This method allows retrieving the result of a handler either by its exact name or by matching
     * a namespace-prefixed handler name that starts with the given handlerName followed by a double colon (::).
     *
     * If the handler is not found in the results, it throws an Exception.
     *
     * @param array $results The array of handler results where the key is the handler name and the value is the result.
     * @param string $handlerName The name of the handler to retrieve the result for.
     *
     * @throws RuntimeException If the handler name is not found in the results.
     *
     * @return mixed The result corresponding to the matched handler name.
     */
    private function getResultByHandlerName(array $results, string $handlerName): mixed
    {
        if (key_exists($handlerName,$results)) {
            return $results[$handlerName];
        }

        foreach ($results as $key => $value) {
            if (str_starts_with($key, $handlerName . '::')) {
                return $value;
            }
        }

        throw new RuntimeException(sprintf('Handler with name "%s" was not found in the results.', $handlerName));
    }


    /**
     * Combines the functionality of handling a message and retrieving the result by a specific handler name.
     *
     * This method dispatches the given message using the `handle` method, retrieves all the handler results,
     * and then fetches the result for the specified handler by using the `getResultByHandlerName` method.
     *
     * @param object $message The message object to be dispatched and handled.
     * @param string $handlerName The name of the handler whose result is to be retrieved.
     *
     * @throws LogicException If the message bus is not initialized, or if the message is not handled as expected.
     * @throws RuntimeException If the handler name is not found in the results.
     * @throws ExceptionInterface
     *
     * @return mixed The result from the specified handler.
     */
    private function handleAndGetResultByHandlerName(object $message, string $handlerName): mixed
    {
        $results = $this->handle($message);
        $result = $this->getResultByHandlerName($results, $handlerName);
        return $result;
    }
}