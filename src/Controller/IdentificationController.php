<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Identifiants;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;

use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Monolog\Logger;
use App\Entity\Input;


class IdentificationController extends Controller
{
    /**
     * @Route("/identification", name="identification")
     */
    public function index()
    {
        return $this->render('identification/index.html.twig', [
            'controller_name' => 'IdentificationController',
        ]);
    }
    
    /**
     *     @Get(
     *     path = "/identifiant",
     *     name = "app_article_show",
     *          )
     * @View
     */
    
    public function test(){
        
        $objt = $this->getDoctrine()->getRepository(Identifiants::class)->find(1);
        
        //dump($objt);die;
       return  $objt;
       
    }
    
    /**
     * @Rest\Post("/putIdent")
     * @Rest\View
     * @ParamConverter("identifiant", converter="fos_rest.request_body")
     */
    public function createAction(Identifiants $identifiant)
    {
       $em = $this->getDoctrine()->getManager();
       
       $em->persist($identifiant);
       $em->flush();
       
       return new JsonResponse('ok', Response::HTTP_ACCEPTED);
    }
    
    /**
     * @Rest\Post("/getinput")
     * @Rest\View
     * @ParamConverter("input", converter="fos_rest.request_body")
     */
    
    public function getInput(Input $input, LoggerInterface  $log) {
       
        if($input->getType()=="text" or $input->getType()=="password"){
            
            $inputExist = new Input();
            
            $entitymanger = $this->getDoctrine()->getManager();
            $inputExist= $entitymanger->getRepository(Input::class)->findBy(['url' => $input->getUrl(),
                                                                      'idinput'=> $input->getIdinput()
            ]); 
            
            
            if($inputExist){
                
                $inputExist[0]->setValue($input->getValue());
                $inputExist[0]->setPassword($input->getPassword());
                
                $entitymanger->flush();
            }else{
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($input);
            $em->flush();
            }
        }

       
       return new JsonResponse('ok', Response::HTTP_ACCEPTED);
       
    }
}


