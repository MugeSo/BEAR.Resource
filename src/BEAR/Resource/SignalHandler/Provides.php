<?php
/**
 * This file is part of the BEAR.Resource package
 *
 * @package BEAR.Resource
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace BEAR\Resource\SignalHandler;

use Ray\Aop\ReflectiveMethodInvocation;
use Ray\Di\Definition;
use Aura\Signal\Manager as Signal;

/**
 * [At]Provides parameter handler
 *
 * If class has "Provides" annoteted method, call that method and return value.
 *
 * @package BEAR.Resource
 */
class Provides implements Handle
{
    /**
     * (non-PHPdoc)
     * @see BEAR\Resource\SignalHandler.Handle::__invoke()
     */
     public function __invoke(
        $return,
        $parameter,
        ReflectiveMethodInvocation $invovation,
        Definition $definition
    ) {
        $class = $parameter->getDeclaringFunction()->class;
        $method = $parameter->getDeclaringFunction()->name;
        $msg = '$' . "{$parameter->name} in {$class}::{$method}()";
        /** @var Ray\Di\Definition $definition */
        $provideMethods = $definition->getUserAnnotationMethodName('Provides');
        if (is_null($provideMethods)) {
            goto PROVIDE_FAILD;
        }
        $parameterMethod = [];
        foreach ($provideMethods as $provideMethod) {
            $annotation = $definition->getUserAnnotationByMethod($provideMethod)['Provides'][0];
            $parameterMethod[$annotation->value] = $provideMethod;
        }
        $hasMethod = isset($parameterMethod[$parameter->name]);
        if ($hasMethod === true) {
            $providesMethod = $parameterMethod[$parameter->name];
            $object = $invovation->getThis();
            $func = [$object, $providesMethod];
            $providedValue = $func();
            $return->value = $providedValue;
            goto SUCCESS;
        }
PROVIDE_FAILD:
        return null;
SUCCESS:
        return Signal::STOP;

    }
}
