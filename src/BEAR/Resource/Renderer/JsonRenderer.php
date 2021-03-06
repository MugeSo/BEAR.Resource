<?php
/**
 * This file is part of the BEAR.Resource package
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace BEAR\Resource\Renderer;

use BEAR\Resource\RenderInterface;
use BEAR\Resource\RequestInterface;
use BEAR\Resource\ResourceObject;

class JsonRenderer implements RenderInterface
{
    /**
     * {@inheritdoc}
     */
    public function render(ResourceObject $ro)
    {
        // evaluate all request in body.
        if (is_array($ro->body) || $ro->body instanceof \Traversable) {
            array_walk_recursive(
                $ro->body,
                function (&$element) {
                    if ($element instanceof RequestInterface) {
                        /** @var $element callable */
                        $element = $element();
                    }
                }
            );
        }
        $ro->view = @json_encode($ro->body, JSON_PRETTY_PRINT);

        return $ro->view;
    }
}
