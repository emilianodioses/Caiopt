<?php

namespace AppBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Mail extends Controller
{  
    public function sendEmail($mailtemplate, $parameters, $attachement)
    {
        $message = \Swift_Message::newInstance();
                    
        $message->setFrom(array('info@opticacontini.com.ar' => 'Optica contini'))
                ->setSubject($parameters['titulo'])
                ->setTo($parameters['cliente'])
                ->setBody($this->renderView($mailtemplate, $parameters),'text/html');

        if(!empty($attachement)){
            $message->attach(\Swift_Attachment::fromPath($attachement.'.pdf')->setFilename('Recibo.pdf'));
        }

        $result = $this->get('mailer')->send($message);
    }    
}
