<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Tag;
use App\Form\CommentFormType;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class PostsController extends AbstractController
{
    public function __construct(private readonly PostRepository $postRepository) { }

    #[Route('/', name: 'app_home', methods: ['GET'])]    // 2 Routes pour 1 fonction
    #[Route(
        '/tags/{tagSlug}',
        name: 'app_posts_by_tag',
        requirements: ['tagSlug' => Requirement::ASCII_SLUG,],
        methods: ['GET']
    )]
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        TagRepository $tagRepository,
        ?string $tagSlug
    ): Response {
        $tag = null;
        // s'il ya un StagSlug, on filtre les posts par tag
        if ($tagSlug) {
            $tag = $tagRepository->findOneBySlug($tagSlug);
        }
        // s'il n'y a pas un StagSlug, on récupère tous les posts
        $query = $this->postRepository->createAllPublishedOrderedByNewestQuery($tag);
        $page = $request->query->getInt('page', 1);
        $pagination = $paginator->paginate(
            $query,
            $page,
            Post::NUM_ITEMS_PER_PAGE,   // depuis constante ds Post
            [
                PaginatorInterface::PAGE_OUT_OF_RANGE => 'fix'
            ]
        );
        return $this->render("posts\index.html.twig", compact('pagination', 'tag'));
    }


    /**
     * @throws NonUniqueResultException
     */
    #[Route(
        '/posts/{slug}',
        name: 'app_posts_show',
        requirements: ['slug' => Requirement::ASCII_SLUG,],
        methods: ['GET', 'POST']
    )]
    public function show(Request $request, Post $post, PostRepository $postRepository, EntityManagerInterface $em): Response
    {
        //  LES ARTICLES AVEC DES TAGS SIMILAIRES au post affiché
        // dd($postRepository->findSimilar($post, maxResults :4));
        $similarPosts = $postRepository->findSimilar($post);

        // On importe un "CRITERIA" DE FILTRAGE pour les commentaires actifs.
        $comments = $post->getActiveComments();

        // Le FORMULAIRE pour créer des commentaires
        $commentForm = $this->createForm(CommentFormType::class);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment = $commentForm->getData();  // = objet de l'entité Comment
            $comment->setPost($post);   // on récupère l'article grâce à la méthode setPost de Comment

            $em->persist($comment);
            $em->flush();
            $this->addFlash('success', "Le commentaire a été publié ");
            return $this->redirectToRoute('app_posts_show', ['slug' => $post->getSlug()]);
        }

        return $this->render("posts\show.html.twig", compact('post', 'comments', 'commentForm', 'similarPosts'));
    }
}
