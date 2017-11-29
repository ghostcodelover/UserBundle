<?php

/******************************************************************************
 *   This file is part of the EventsCoreBundle package.                       *
 *                                                                            *
 *   (c) Events <http://events.cd/>                                           *
 *                                                                            *
 *   For the full copyright and license information, please view the LICENSE  *
 *   file that was distributed with this source code.                         *
 ******************************************************************************/

namespace ZND\USM\UserBundle\Mailer;

use ZND\USM\UserBundle\Entity\UserInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Muekendi emmanuel <mukendiemmanuel@events.cd>
 * @DI\Service("znd_usm_user.user_mailer")
 */
class Mailer implements MailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var string
     *
     */
    private $template;

    /**
     * @var string
     *
     */
    private $admin;

    /**
     * Mailer constructor.
     *
     * @param \Swift_Mailer                                             $mailer
     * @param UrlGeneratorInterface                                     $router
     * @param EngineInterface                                           $templating
     *
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     *
     * @internal param $template
     *
     * @internal param array $parameters
     *
     * @internal param \Symfony\Component\DependencyInjection\ContainerInterface $container
     *
     * @internal param $template
     * @internal param $sender
     * @DI\InjectParams({
     *     "mailer" = @DI\Inject("mailer"),
     *    "router"  = @DI\Inject("router"),
     *    "templating" = @DI\Inject("templating"),
     *     "container" = @DI\Inject("service_container")
     *     })
     */
    public function __construct($mailer, UrlGeneratorInterface  $router, EngineInterface $templating, ContainerInterface $container)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->admin= $container->getParameter('znd_usm_user.admin.name');
        $this->template= $container->getParameter('znd_usm_user.user.templates.confirm_user');
    }

    /**
     * {@inheritdoc}
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $url = 'http://10.42.0.135#user/confirm/'.$user->getConfirmationToken();
        $rendered = $this->templating->render($this->template, [
            'user' => $user,
            'confirmationUrl' => $url,
        ]);
        $this->sendEmailMessage($rendered, $this->admin, (string) $user->getEmail());
    }

    /**
     * {@inheritdoc}
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['resetting.template'];
        $url = $this->router->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'confirmationUrl' => $url,
        ));
        $this->sendEmailMessage($rendered, $this->sender, (string) $user->getEmail());
    }

    /**
     * @param string $renderedTemplate
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail)
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = array_shift($renderedLines);
        $body = implode("\n", $renderedLines);

        $message = \Swift_Message::newInstance();
        $message->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body);
        $this->mailer->send($message);
    }
}
