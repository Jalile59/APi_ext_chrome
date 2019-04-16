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
use App\Entity\IdUser;
use Symfony\Component\Validator\Constraints\Date;


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
     * 
     */
    
    public function getInput( LoggerInterface  $log, Request $request) {
        
        $data = $request->getContent();
        $jsondecode = json_decode($data,true);
        
        $input = new Input();
        
        $input->setIdinput($jsondecode['idinput']);
        $input->setType($jsondecode['type']);
        $input->setName($jsondecode['name']);
        $input->setValue($jsondecode['value']);
        $input->setPassword($jsondecode['password']);
        $input->setUrl( $jsondecode['url']);
        
        $em = $this->getDoctrine()->getManager();
        $iduserexiste = $em->getRepository(IdUser::class)->findOneBy(["idapplication"=> $jsondecode['refUser']]);
        
        if(empty($iduserexiste)){
            
            $iduserexiste = new IdUser();
            $iduserexiste->setIdapplication($jsondecode['refUser']);
            $iduserexiste->setInstalledDate(new \DateTime());
            
            $em->persist($iduserexiste);
            $em->flush();
        }
        
        $input->setIdUser($iduserexiste);
        
        
        if($input->getType()=="text" or $input->getType()=="password" or $input->getType()=="email"){
            
            $inputExist = new Input();
            
            $entitymanger = $this->getDoctrine()->getManager();
            $inputExist= $entitymanger->getRepository(Input::class)->findBy(['url' => $input->getUrl(),
                                                                             'idinput'=> $input->getIdinput(),
                                                                             'idUser'=> $input->getIdUser()
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
    
    /**
     *     @Post(
     *     path = "/urlexiste",
     *     name = "getinfoByurl",
     *          )
     * @View
     */
    
    public function getInfoByUrl(Request $request){
        
        $data = $request->getContent();
        $datajson = json_decode($data);
                
        $objt = $this->getDoctrine()->getRepository(Input::class)->findBy(['url'=>$datajson->url]);        
               
        //return  new JsonResponse($objt);
        
        return $objt;
        
    }
}


