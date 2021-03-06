<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\HttpKernel;
use Sonata\BlockBundle\Block\BlockServiceInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\HttpKernel\Kernel;

class ActionBlockService extends BaseBlockService implements BlockServiceInterface
{
    protected $renderer;

    /**
     * @param string $name
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentHandler|\Symfony\Bundle\FrameworkBundle\HttpKernel $renderer
     */
    public function __construct($name, EngineInterface $templating, $renderer)
    {
        parent::__construct($name, $templating);
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        throw new \RuntimeException('Not used at the moment, editing using a frontend or backend UI could be changed here');
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        throw new \RuntimeException('Not used at the moment, validation for editing using a frontend or backend UI could be changed here');
    }

    /**
     * @param BlockInterface $block
     * @param null|Response  $response
     *
     * @return Response
     */
    public function execute(BlockInterface $block, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }

        if (!$block->getActionName()) {
            throw new \RuntimeException(sprintf(
                'ActionBlock with id "%s" does not have an action name defined, implement a default or persist it in the document.',
                $block->getId()
            ));
        }

        if ($block->getEnabled()) {
            $response = $this->render($block->getActionName(), array('block' => $block));
        }

        return $response;
    }

    /**
     * @param $controller
     * @param $attributes
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function render($controller, $attributes)
    {
        if (!class_exists('Symfony\Component\HttpKernel\Fragment\FragmentHandler')) {
            // TODO: Symfony 2.1 compatibility
            $response = $this->renderer->render($controller, array('attributes' => $attributes));
        } else {
            $response = $this->renderer->render(new ControllerReference($controller, $attributes));
        }

        return new Response($response);
    }
}
