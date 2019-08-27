<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\Article;

use Symfony\Component\HttpFoundation\Response;

// Allow to use annotations
use Symfony\Component\Routing\Annotation\Route;

// Enable to specify methods
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

// Use twig
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Use forms
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="article_list")
     */
    public function index()
    {
        // Articles to pass to template
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

        // Return twig template and send data
        return $this->render('articles/index.html.twig', array('articles' => $articles));
    }

    /**
     * @Route("/article/new", name="new_article", methods={"GET", "POST"})
     */
    public function new(Request $request)
    {
        // Create article
        $article = new Article();

        // Create form and pass in article
        $form = $this->createFormBuilder($article)->add('title', TextType::class, array('attr' => array('class' => 'form-control')))->add('body', TextareaType::class, array('required' => 'false', 'attr' => array('class' => 'form-control')))->add('save', SubmitType::class, array('label' => 'Create', 'attr' => array('class' => 'btn btn-primary mt-3')))->getForm();

        // Handle form submit
        $form->handleRequest($request);

        // Check if request has been submitted
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $entityManagere = $this->getDoctrine()->getManager();

            $entityManagere->persist($article);

            $entityManagere->flush($article);

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/article/edit/{id}", name="edit_article", methods={"GET", "POST"})
     */
    public function edit(Request $request, $id)
    {
        // Find article with ID
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        // Create form and pass in article
        $form = $this->createFormBuilder($article)->add('title', TextType::class, array('attr' => array('class' => 'form-control')))->add('body', TextareaType::class, array('required' => 'false', 'attr' => array('class' => 'form-control')))->add('save', SubmitType::class, array('label' => 'Update', 'attr' => array('class' => 'btn btn-primary mt-3')))->getForm();

        // Handle form submit
        $form->handleRequest($request);

        // Check if request has been submitted
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagere = $this->getDoctrine()->getManager();
            $entityManagere->flush($article);

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/article/delete/{id}")
     */
    public function delete(Article $article)
    {
        // Create entity manager
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($article);

        $entityManager->flush();

        return $this->redirectToRoute('article_list');
    }

    /**
     * @Route("/article/{id}", name="article_show", requirements={"id"="\d+"})
     */
    public function show($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        return $this->render('articles/show.html.twig', array('article' => $article));
    }
//    /**
//     * @Route("/article/save")
//     */
//    public function save() {
//        $entityManager = $this->getDoctrine()->getManager();
//
//        $article = new Article();
//
//        $article->setTitle('Article One');
//        $article->setBody('This is the first article');
//
//        $entityManager->persist($article);
//
//        $entityManager->flush();
//
//        return new Response('Saved an article with the id of'.$article->getId());
//    }

}