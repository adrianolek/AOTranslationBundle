<?php

namespace AO\TranslationBundle\Controller;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AO\TranslationBundle\Form as Form;

/**
 * ProfilerController
 * @author Adrian Olek <adrianolek@gmail.com>
 *
 */
class ProfilerController extends ContainerAware
{
    /**
     * @Route("/_profiler/{token}/translations")
     */
    public function translationsAction($token, Request $request)
    {
        $profiler = $this->container->get('profiler');
        $locales = $this->container->getParameter('ao_translation.locales');
        
        $collector = $profiler->loadProfile($token)->getCollector('translation');
        
        $t_form = new Form\TranslationsType(
            $this->container->get('ao_translation.entity_manager'),
            $collector->getMessages(), array_keys($locales)
        );
        
        $form = $this->container->get('form.factory')->create($t_form);
        $messages = $t_form->getMessages();

        if ($request->isMethod('post')) {
            $form->bind($request);
            $t_form->save($form->getData());
            $this->container->get('session')->getFlashBag()
                    ->add('notice', 'Your translations have been saved!');
            return new RedirectResponse(
                    $this->container->get('router')
                            ->generate('_profiler',
                                    array('panel' => 'translation',
                                            'token' => $token)));
        }

        return $this->container->get('templating')
                ->renderResponse(
                        'AOTranslationBundle:Profiler:translations.html.twig',
                        array('collector' => $collector,
                                'form' => $form->createView(), 'messages' => $messages,
                                'token' => $token, 'locales' => $locales));
    }

    /**
     * @Route("/_profiler/{token}/translations/reset_action")
     */
    public function resetActionCacheAction($token, Request $request)
    {
        $profiler = $this->container->get('profiler');
        $collector = $profiler->loadProfile($token)->getCollector('translation');
        
        $key = $collector->getCacheKey();
        
        $this->container->get('ao_translation.entity_manager')
            ->getRepository('AOTranslationBundle:Cache')->resetActionCache($key);
        
        return new JsonResponse(array('status' => 'OK'));
    }

    /**
     * @Route("/_profiler/{token}/translations/reset")
     */
    public function resetCacheAction($token, Request $request)
    {
        $this->container->get('ao_translation.entity_manager')
            ->getRepository('AOTranslationBundle:Cache')->resetCache();

        return new JsonResponse(array('status' => 'OK'));
    }
}
