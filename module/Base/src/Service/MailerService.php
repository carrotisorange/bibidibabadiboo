<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Model\ViewModel;
Use Zend\Mime;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

class MailerService extends BaseService
{
    const TEMPLATE_PATH = 'paginator';
    /**
     * @var Array
     */
    private $config;
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    public function __construct(
        Array $config,
        Logger $logger,
        PhpRenderer $renderer,
        ViewModel $view,
        Message $mail,
        TransportInterface $transport)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->renderer = $renderer;
        $this->view = $view;
        $this->mail = $mail;
        $this->transport = $transport;
    }
    /**
     * @param object $mailConfig Expected properties: templatePath, template, to, subject
     * @param array $variables
     */
    public function sendHtmlMail($mailConfig, array $variables)
    {
        $this->sendMail($mailConfig, $variables, true);
    }
    /**
     * @param object $mailConfig Expected properties: templatePath, template, to, subject
     * @param array $variables
     */
    public function sendTextMail($mailConfig, array $variables)
    {
        $this->sendMail($mailConfig, $variables, false);
    }
    /**
     * @param object $mailConfig Expected properties: templatePath, template, to, subject
     * @param array $variables
     * @param boolean $isHtmlMail Flag that controls mail's body type
     */
    protected function sendMail($mailConfig, array $variables, $isHtmlMail = true)
    {
        if (empty($mailConfig)) {
            $this->log("Mailer.sendMail: empty mailConfig", Logger::ERR);
            return;
        }
            
        if (!empty($mailConfig->templatePath)) {
            $this->view->setTemplate($mailConfig->templatePath);
            foreach ($variables as $key => $value) {
                $this->view->setVariable($key, $value);
            }
            $content = $this->renderer->render($this->view);
        } else {
            $content = $mailConfig->body;
        }
        
        $to = preg_split("/[\s,;]+/", $mailConfig->to);
        $to = $to[0];
        $subject = $mailConfig->subject;
        $this->mail->setTo($to);
        //newly added as from is needed to send mail
        $from = $mailConfig->from;
        $this->mail->setFrom($from);
        $this->mail->setSubject($subject);
        if (!empty($mailConfig->bcc)) $this->mail->setBcc($mailConfig->bcc);
        if (!empty($mailConfig->cc)) $this->mail->setCc($mailConfig->cc);
        
        $mailContent = new Mime\Part($content);
        $mailContent->charset = 'utf-8';
        $mimeMessage = new Mime\Message();
        
        if ($isHtmlMail) {
            $mailContent->type = Mime\Mime::TYPE_HTML;
        } else {
            $mailContent->type = Mime\Mime::TYPE_TEXT;     
        }
        
        if (!empty($variables['attachmentFile']) 
                && !empty($variables['attachmentFileName'])) {
            $fileContent = fopen($variables['attachmentFile'], 'r');
            $attachment = new Mime\Part($fileContent);
            $attachment->type = 'text/csv';
            $attachment->filename = $variables['attachmentFileName'];
            $attachment->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
            // Setting the encoding is recommended for binary data
            $attachment->encoding = Mime\Mime::ENCODING_BASE64;
            // then add them to a MIME message
            $mimeMessage->setParts([$mailContent, $attachment]);
        } else {
            $mimeMessage->setParts([$mailContent]);
        }
        
        $this->mail->setBody($mimeMessage);
        $this->mail->setEncoding('UTF-8');
        
        $this->transport->send($this->mail);
		
        if (!empty($subject) && !empty($to)) {
            $recipient = is_array($to) ? implode(', ', $to) : $to;
            $this->log("Mail '{$subject}' sent to [" . $recipient . ']');
        }
    }
    /**
     * Log messages pertaining to jobs
     *
     * @param string $message - Message to log
     * @param int $level - See Logger constants
     */
    protected function log($message, $level = Logger::INFO)
    {
        $this->logger->log($level, $message);
    }
}
