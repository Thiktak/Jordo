<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace Jordo\PrintBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Doctrine\Common\Annotations\Reader;

/**
 * This listener will replace reponse content by pdf document's content if Pdf annotations is found.
 * Also adds pdf format to request object and adds proper headers to response object.
 * 
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class PdfListener
{
    private $templatingEngine;
    
    public function __construct(EngineInterface $templatingEngine)
    {
        $this->templatingEngine = $templatingEngine;
    }
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if( $request->get('_format') == 'pdf' ) {
            //$request->setFormat('pdf', 'application/pdf');
            //echo 'application/pdf<br />';
        }
    }
    
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $format = $request->get('_format');
        
        if($format != 'pdf' || !is_array($controller = $event->getController()) || !$controller)
            return;
        
        //$method = $this->reflectionFactory->createMethod($controller[0], $controller[1]);
        
        /*$annotation = $this->annotationReader->getMethodAnnotation($method, 'Ps\PdfBundle\Annotation\Pdf');
        
        if($annotation)
        {
            $request->attributes->set('_pdf', $annotation);
        }*/ 
    }
    
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        
        $response = $event->getResponse();
               
        $stylesheetContent = null;
        /*if($stylesheet = $annotation->stylesheet)
        {
            $stylesheetContent = $this->templatingEngine->render($stylesheet);
        }*/
        
        $content = $this->getPdfContent($response, $request, $stylesheetContent);                       

        //$headers = (array) $annotation->headers;
        //$headers['content-length'] = strlen($content);
        /*foreach($headers as $key => $value)
        {
            $response->headers->set($key, $value);
        }*/

        $response->setContent($content);
    }
    
    private function getPdfContent(Response $response, Request $request, $stylesheetContent)
    {
        try
        {
            $responseContent = $response->getContent();
            
            if( $request->get('_format') != 'pdf' )
                return $responseContent;
            
            include_once dirname(__FILE__) . '/../Vendors/mPDF/mpdf.php';

            if( preg_match('`<html>(.*)</html>$`sUi', $responseContent) ) {
                $mpdf = new \mPDF();
                $mpdf->WriteHTML($responseContent);
                $mpdf->SetHTMLHeaderByName('header');
                $mpdf->SetHTMLFooterByName('footer');
                $responseContent = $mpdf->Output(); exit(); // */
            }

            return $responseContent;
        }
        catch(\Exception $e)
        {
            $request->setRequestFormat('html');
            $response->headers->set('content-type', 'text/html');
            throw $e;
        }
    }
}