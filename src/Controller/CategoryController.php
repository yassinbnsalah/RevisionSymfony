<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\SearchCategoryType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $repo , Request $req): Response
    {
        $res = $repo->CategoryMaxNb(); 
        
        $result = $repo->findAll(); 
        $categoryToSearch = new Category() ; 
        $form = $this->createForm(SearchCategoryType :: class , $categoryToSearch) ; 
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $result = $repo->FindCategoryByName($categoryToSearch->getName()); 
            return $this->render('category/index.html.twig', [
                'controller_name' => 'CategoryController',
                'categorys' => $result,
                'maxCate' => $res,
                'f' => $form->createView()   
            ]);
        }
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categorys' => $result,
            'maxCate' => $res,
              'f' => $form->createView()   
        ]);
    }

    #[Route('/category/add' , name: 'AddCategory')]
    public function AddCategory(Request $req , CategoryRepository $repo ):Response
    {
        $cat = new Category() ; 
        $form = $this->createForm(CategoryType :: class , $cat) ; 
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $cat->setNbProduct(0) ; 
            $cat->setDateCreation(new \DateTime()); 
            $repo->save($cat , true) ; 
            return $this->redirectToRoute('app_category'); 
        }
        return $this->render('category/add.html.twig', [
            'controller_name' => 'CategoryController',
            'f' => $form->createView()   
        ]);
    }

    #[Route('/category/update/{id}' , name: 'UpdateCategory')]
    public function UpdateCategory(Request $req, ManagerRegistry $mg , CategoryRepository $repo , $id ):Response
    {
        $cat = $repo->find($id);
        $form = $this->createForm(CategoryType :: class , $cat) ; 
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $repo->save($cat) ; 
            return $this->redirectToRoute('app_category'); 
        }
        return $this->render('category/add.html.twig', [
            'controller_name' => 'CategoryController',
            'f' => $form->createView()   
        ]);
    }
    #[Route('/category/delete/{id}' , name: 'DeleteCategory')]
    public function DeleteCategory(CategoryRepository $repo , $id ):Response
    {
        $cat = $repo->find($id);
        $repo->remove($cat , true) ; 
        return $this->redirectToRoute('app_category'); 
    }

    #[Route('/category/{id}' , name: 'CategoryParID')]
    public function CategoryParID(CategoryRepository $repo , $id):Response
    {
        $result = $repo->find($id);
        return $this->render('category/details.html.twig', [
            'controller_name' => 'CategoryController',
            'category' => $result
        ]);
    }




}
