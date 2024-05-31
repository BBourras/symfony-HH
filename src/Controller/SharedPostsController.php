<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\SharePostFormType;
use App\Repository\PostRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class SharedPostsController extends AbstractController
{
    public function __construct(private readonly PostRepository $postRepository) {}

    /**
     * @throws NonUniqueResultException
     * @throws TransportExceptionInterface
     */
    #[Route('
        /posts/{slug}/share',
        name:'app_posts_share',
        requirements: [
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET', 'POST']
    )]
    public function create(Request $request, Post $post, MailerInterface $mailer): Response
    {
        // Si on a la bonne url, ON CREE LE FORMULAIRE DE PARTAGE
        $form = $this->createForm(SharePostFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $subject = sprintf(" %s vous invite à lire l'article '%s' ", $data['sender_name'], $post->getTitle()) ;

            $email = (new TemplatedEmail())
                ->  from(new Address (
                    $this->getParameter ('app.contact_email') ,
                    $this->getParameter ('app.name')
                ) )
                ->to($data['recipient_email'])
                ->subject($subject)
                ->htmlTemplate('emails/shared_posts/create.html.twig')
                ->context([
                    'post' => $post,
                    'sender_name' => $data['sender_name'],
                    'sender_comments' => $data['sender_comments'],
                ])
            ;

            $mailer->send($email);
            $this->addFlash('success', "L'article a été partagé avec ". $data['recipient_email']);
            return $this->redirectToRoute('app_home');
        }
        return $this->render("shared_posts\create.html.twig", compact('form', 'post'));
    }
}