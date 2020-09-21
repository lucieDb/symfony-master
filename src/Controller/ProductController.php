<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


//@todo : stocker le chemin de l'image dans la bdd
//faire la vérification 

class ProductController extends AbstractController
{
    /**
     * @Route("/product/create", name="product_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            //on récupère l'image pour l'upload
            $image = $form->get('image')->getData();
            $fileName = uniqid().'.'.$image->guessExtension();

            //on déplace l'image uploadé vers un dossier de notre projet
            $image->move($this->getParameter('kernel.project_dir').'/public/uploads',
            $fileName);

            $product->setImage($fileName);

            //@todo : ajouter une propriété image dans la classe product
            //$product->setImage($name);

            $entityManager->persist($product); // on persiste l'objet
            $entityManager->flush(); // on exécute la requête (INSERT...)
        }
        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
            'edit' => false,
        ]);
    }

    /**
     * @Route("/product",name="product_index")
     */
    public function index(ProductRepository $repository)
    {
        $products = $repository->findAll();

        // dump($products);

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_show")
     */
    public function show($id, ProductRepository $repository)
    {
        
        $product = $repository->find($id);

        if(!$product){
            throw $this->createNotFoundException();
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/product/edit/{id}", name="product_edit")
     */
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // on exécute la requête (INSERT...)
        }
        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
            'edit' => true,
        ]);
    }

    /**
     * @Route("/product/remove/{id}", name="product_remove")
     */
    public function remove(Product $product, EntityManagerInterface $entityManager, Request $request)
    {
        $token = $request->request->get('csrf_token');
        
        //Ici, on se protège d'une faille CSRF
        if($this->isCsrfTokenValid('delete-'.$product->getId(), $token)){
            $entityManager->remove($product);
            $entityManager->flush(); // on exécute la requête (INSERT...)
        }

        return $this->redirectToRoute('product_index');
    }
}
