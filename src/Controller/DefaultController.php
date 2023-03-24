<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class DefaultController extends AbstractController
{
	   #[Route('/', name: 'app_home')]
    public function homepage(Request $request)
    {        
        //Varibale Global para la API 
        $Url = 'https://api.stackexchange.com/2.3/questions?order=desc&sort=activity&site=stackoverflow';  

        //Creación del Formulario
        $form = $this->createFormBuilder()
            ->add('tagged', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'form-control'
                ]

            ])
            ->add('todate', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('fromdate', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('send', SubmitType::class,[
                'label' => 'Enviar',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        //Validación del Froms
        if ($form->isSubmitted() && $form->isValid()) {

            $form->get('tagged')->getData() ? $Url = $Url.'&tagged='.($form->get('tagged')->getData()) : null ;
            $form->get('todate')->getData() ? $Url = $Url.'&todate='.(strtotime($form->get('todate')->getData())) : null ;
            $form->get('fromdate')->getData() ? $Url = $Url = $Url.'&fromdate='.(strtotime($form->get('fromdate')->getData())) : null ;

            $questions = $this->getCurl($Url);

            return $this->render('base.html.twig',[
                'questions' => $questions['items'],
                'form' => $form->createView()

            ]);
        }

        $questions = $this->getCurl($Url);

        return $this->render('base.html.twig',[
            'questions' => $questions['items'],
            'form' => $form->createView()

        ]);
    }


    //Petición get a la API
    public function getCurl($Url){

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $Url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Cookie: prov=97f6aaef-2296-4311-9ae4-3671e86f8505'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $questions  = json_decode($response, true);

        return $questions;

    }

}