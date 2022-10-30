<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddProductWithoutCategoryType;
use App\Form\ProductType;
use App\Form\UpdateProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(ProductRepository $repo): Response
    {
        $result = $repo->findAll() ; 

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $result 
        ]);
    }

    #[Route('/product/add' , name:'AddProduct')]
    public function AddProduct(ProductRepository $repo , CategoryRepository $catRepo , Request $req):Response{
        $pr = new Product() ; 
        $form = $this->createForm(ProductType :: class , $pr);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $pr->setDateCreation(new \DateTime()); 
            $cat = $pr->getCategory() ; 
            $cat->setNbProduct($cat->getNbProduct()+1);
            $catRepo->save($cat);
            $repo->save($pr , true) ;
            return $this->redirectToRoute('app_product');
        }
        return $this->render('product/add.html.twig', [
            'controller_name' => 'ProductController',
            'f' => $form->createView() 
        ]);
    }

    #[Route('/product/add/{id}' , name:'AddProductWithCat')]
    public function AddProductWithCat($id ,ProductRepository $repo , CategoryRepository $catRepo , Request $req):Response{
        $pr = new Product() ; 
        $form = $this->createForm(AddProductWithoutCategoryType :: class , $pr);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $category = $catRepo->find($id); 
            $pr->setCategory($category) ; 
            $pr->setDateCreation(new \DateTime()); 
           // $cat = $pr->getCategory() ; 
            $category->setNbProduct($category->getNbProduct()+1);
            $catRepo->save($category);
            $repo->save($pr , true) ;
            return $this->redirectToRoute('app_category');
        }
        return $this->render('product/add.html.twig', [
            'controller_name' => 'ProductController',
            'f' => $form->createView() 
        ]);
    }

    #[Route('/product/update/{id}' , name:'UpdateProduct')]
    public function UpdateProduct(ProductRepository $repo , CategoryRepository $catRepo , Request $req , $id):Response{
        $pr =  $repo->find($id) ; 
        $form = $this->createForm(UpdateProductType :: class , $pr);
        $form->handleRequest($req);


        // $categoryold = $pr->getCategory(); 
        // $categoryold->setNbProduct($categoryold->getNbProduct()-1); 
        // $catRepo->save($categoryold) ;
      
        

        if($form->isSubmitted()){
            
            $categoryNew = $pr->getCategory(); 
            $categoryNew->setNbProduct($categoryNew->getNbProduct()+1);  
            $catRepo->save($categoryNew) ;

            
            $pr->setDateCreation(new \DateTime()); 
            $repo->save($pr , true) ;

            
          

            return $this->redirectToRoute('app_product');
        }
        // $categoryold = $pr->getCategory(); 
        // $categoryold->setNbProduct($categoryold->getNbProduct()+1); 
        // $catRepo->save($categoryold) ;
        return $this->render('product/add.html.twig', [
            'controller_name' => 'ProductController',
            'f' => $form->createView() 
        ]);
    }

    #[Route('/product/delete/{id}' , name:'DeleteProduct')]
    public function DeleteProduct(ProductRepository $repo , CategoryRepository $catRepo  , Request $req , $id):Response{
        $pr =  $repo->find($id) ; 
        $cat = $pr->getCategory() ; 
        $cat->setNbProduct($cat->getNbProduct()-1);
        $catRepo->save($cat);
        
            $repo->remove($pr , true) ;
            return $this->redirectToRoute('app_product');
     
    }
}
