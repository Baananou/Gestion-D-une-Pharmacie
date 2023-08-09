<?php

namespace App\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Ordonnance;
use App\Entity\Medicament;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class OrdonnanceController extends AbstractController
{
    #[Route('/ordonnance', name: 'app_ordonnance')]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ordonnance = new Ordonnance();
        $ordonnance->setNomPatient('yessine');
        $ordonnance->setCreatedAt(new \DateTime());
        $ordonnance->setTotalPrice('2000');
        $medicament1 = new medicament();
        $medicament1->setNomMed('doliprane');
        $medicament1->setPrice(20);
        $medicament1->setCategorie('paracetamol');
        $medicament2 = new medicament();
        $medicament2->setNomMed('adol');
        $medicament2->setPrice(15);
        $medicament2->setCategorie('paracetamolA');
        $ordonnance->addMedicament($medicament1);
        $ordonnance->addMedicament($medicament2);
        $entityManager->persist($medicament1);
        $entityManager->persist($medicament2);
        $entityManager->persist($ordonnance);
        $entityManager->flush();
        return $this->render('ordonnance/index.html.twig', [
            'id' => $ordonnance->getId(),
        ]);
    }
    #[Route('/ordonnance/{id}', name: 'app_ordonnance_show')]
    public function show($id)
    {
        $ordonnance = $this->getDoctrine()
            ->getRepository(Ordonnance::class)
            ->find($id);
        if (!$ordonnance) {
            throw $this->createNotFoundException(
                'No ordonnance found for id ' . $id
            );
        }
        $em=$this->getDoctrine()->getManager();
        $medicament=$em->getRepository(Medicament::class);
        $listMedicaments=$em
            ->getRepository(Medicament::class)
            ->findBy(['Ordonnance'=>$ordonnance]);

        return $this->render('ordonnance/show.html.twig',array(

            'ordonnance' => $ordonnance,
            'medicament'=>$medicament,
            'listMedicaments'=>$listMedicaments

        ));
    }
    #[Route('/ajouter', name: 'Ajouter')]
    public function ajouter(Request $request)
    {
        $medicament = new Medicament();
        $fb = $this->createFormBuilder($medicament)
            ->add('nomMed',TextType::class)
            ->add('Price', TextType::class)
            ->add('Categorie',TextType::class)
            ->add('ordonnance',EntityType::class,[
                'class'=>Ordonnance::class,
                'choice_label'=>'nomPatient',
            ])
            ->add('Valider',submitType::class);
            $form = $fb->getForm();
            $form->handleRequest($request);
        if ($form->isSubmitted()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($medicament);
        $em->flush();
        return $this->redirectToRoute('Ajouter');
        }


            return $this->render('ordonnance/ajouter.html.twig',
            ['f' => $form->createView()]);
    }
    #[Route('/add', name: 'Ajout_ord')]
    public function ajouter2( Request $request)
    {
        $ordonnance = new Ordonnance();
        $form = $this->createdForm("App\Form\OrdonnanceType",$job);

        $form->handleRequest($request);
     if ($form->isSubmitted()) {
         $em = $this->getDoctrine()->getManager();
         $em->persist($ordonnance);
         $em->flush();
         $session = new Session();
         $session->getFlashBag()->add('notice','medicament ajoute avec succes');
         return $this->redirectToRoute(('home'));
         return $this->redirectToRoute('Ajouter');
     }
        return $this->render('ordonnance/ajouter.html.twig',
        ['f'=> $form->createView()]);
    }


    #[Route('home/', name: 'home')]

    public function home()
{
    $em = $this->getDoctrine()->getManager();
    $repo = $em->getRepository(Medicament::class);
    $lesMedicaments = $repo->findAll();
    return $this->render('ordonnance/home.html.twig',
    ['lesMedicaments' => $lesMedicaments]);
}
}