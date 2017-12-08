<?php

namespace AppBundle\Mailer;

use AppBundle\Entity\MailTemplate;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;

class Mailer
{
    /**
     * List of emails which will be sent even if email
     * notifications are disabled by the user
     *
     * @var array
     */
    static public $importantMails = [
        'new_user',
        'remind_password',
        'new_user'
    ];

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $twig;

    /**
     * @var string
     */
    private $sender;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param \Swift_Mailer $mailer
     * @param EngineInterface $twig
     * @param EntityManager $em
     * @param string $sender
     */
    public function __construct(\Swift_Mailer $mailer, EngineInterface $twig, EntityManager $em, $sender)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->em = $em;
        $this->sender = $sender;
    }

    /**
     * @param ContactInterface $contact
     * @param string $alias
     * @param array $data
     */
    public function user(ContactInterface $contact, $alias, $data = [])
    {
        if (!$contact->receiveNotifications() && !in_array($alias, self::$importantMails)) {
            return;
        }

        /** @var MailTemplate $template */
        $template = $this->em->getRepository(MailTemplate::class)->findOneBy(['alias'=>$alias]);

        if (!$template) {
            throw new \InvalidArgumentException(sprintf("Template %s does not exist", $alias));
        }

        $this->send([$contact->getEmail() => $contact->getFullName()], $template, ['user' => $contact] + $data);
    }

    /**
     * @param array $users
     * @param $alias
     * @param array $data
     */
    public function users(array $users, $alias, $data = [])
    {
        foreach ($users as $user) {
            $this->user($user, $alias, $data);
        }
    }

    /**
     * @param MailTemplate $template
     * @param array $data
     * @return string
     */
    protected function render(MailTemplate $template, array $data)
    {
        return $this->twig->render("AppBundle:Mail:template.html.twig", compact('template') + $data);
    }

    /**
     * @param string|array $to
     * @param MailTemplate $template
     * @param array $data
     */
    private function send($to, MailTemplate $template, array $data = [])
    {
        $body = $this->render($template, $data);

        $message = new \Swift_Message($template->getSubject(), $body, "text/html", "utf-8");
        $message->setFrom($this->sender);
        $message->setTo($to);

        $this->mailer->send($message);
    }
}
