<?php
/**
 * Created by PhpStorm.
 * User: Стас
 * Date: 17.09.2018
 * Time: 23:10
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use \AppBundle\Entity\User;
use \AppBundle\Entity\Token;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
/*use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;*/

class UserController extends Controller
{
    private $err = '';
    private $json = array();

    /**
     * @Route("/user/register")
     * @Method({"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $em = $this->getDoctrine()->getManager();


        $body = $request->getContent();
        $data = json_decode($body, true);

        if( !isset($data['email'])      || strlen(trim($data['email'])) == 0 ||
            !isset($data['password1'])  || strlen($data['password1'])   == 0 ||
            !isset($data['password2'])  || strlen($data['password2'])   == 0 ||
            !isset($data['name'])       || strlen(trim($data['name']))  == 0 ){
                $this->err .= 'All fields are required!';
        }
        else if(!$user->validateEmail($data['email'])){
            $this->err .= 'Please enter a valid E-Mail!';
        }
        else if( $em->getRepository('AppBundle:User')->isTaken($data['email']) ){
            $this->err .= 'A user with this E-Mail has already been registered!';
        }
        else if($data['password1'] != $data['password2']){
            $this->err .= 'Passwords don\'t match!';
        }






        if(!$this->err){
            $user->setUser($data['email'], $data['password1'], $data['name']);
            $em->persist($user);
            $em->flush();

            $token = new Token();
            $token->generateToken($user->getId());
            $em->persist($token);
            $em->flush();

            $this->json['token'] = $token->getToken();
            $this->json['lifeLength'] = $token->getLifeLength();
            $resp = new JsonResponse($this->json);
        }else{
            $this->json['errorText'] = $this->err;
            $resp = new JsonResponse($this->json);
            $resp->setStatusCode(400);
        }

        $resp->headers->set('Access-Control-Allow-Origin', 'http://wi-front.com' );

        return $resp;
    }

    /**
     * @Route("/user/login")
     * @Method({"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function loginAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $body = $request->getContent();
        $data = json_decode($body, true);

        if( !isset($data['email'])      || strlen(trim($data['email'])) == 0 ||
            !isset($data['password'])   || strlen($data['password'])    == 0 ){
            $this->err .= 'All fields are required!';
        }



        if(!$this->err){
            $user = $em->getRepository('AppBundle:User')->getUserByMail($data['email']);
            if(!$user) { $this->err .= 'This user does not exist!'; }
        }

        if(!$this->err){
            $user = $em->getRepository('AppBundle:User')->getUserByMail($data['email']);
            if(!$user->verifyPassword($data['password'])) { $this->err .= 'Wrong password!'; }
        }



        if(!$this->err){

            $token = new Token();
            $token->generateToken($user->getId());
            $em->persist($token);
            $em->flush();

            $this->json['token'] = $token->getToken();
            $this->json['lifeLength'] = $token->getLifeLength();
            $resp = new JsonResponse($this->json);
        }else{
            $this->json['errorText'] = $this->err;
            $resp = new JsonResponse($this->json);
            $resp->setStatusCode(400);
        }

        $resp->headers->set('Access-Control-Allow-Origin', 'http://wi-front.com' );

        return $resp;
    }

    /**
     * @Route("/user/dashboard")
     * @Method({"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function dashboardAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $body = $request->getContent();
        $data = json_decode($body, true);

        if( !isset($data['token']) || strlen(trim($data['token'])) == 0 ){
            $this->err .= 'Please log in first!';
        }

        if( !$this->err ){
            $token = $em->getRepository('AppBundle:Token')->checkToken($data['token'], $this->err);

        }

        if( !$this->err ){
            $user = $em->getRepository('AppBundle:User')->getUser($token->getUserId());

        }


        if(!$this->err){

            //dump($user);die;
            $this->json['name'] = $user->getName();
            $resp = new JsonResponse($this->json);
        }else{
            $this->json['errorText'] = $this->err;
            $resp = new JsonResponse($this->json);
            $resp->setStatusCode(400);
        }

        $resp->headers->set('Access-Control-Allow-Origin', 'http://wi-front.com' );

        return $resp;
    }

    /**
     * @Route("/user/logout")
     * @Method({"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function logoutAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $body = $request->getContent();
        $data = json_decode($body, true);

        if( !isset($data['token']) || strlen(trim($data['token'])) == 0 ){
            $this->err .= 'Please log in first!';
        }

        if( !$this->err ){
            $token = $em->getRepository('AppBundle:Token')->checkToken($data['token'], $this->err);
        }

        if( !$this->err ){
            $token->setIsAlive(false);
            $em->persist($token);
            $em->flush();
        }


        if(!$this->err){
            $resp = new JsonResponse($this->json);
        }else{
            $this->json['errorText'] = $this->err;
            $resp = new JsonResponse($this->json);
            $resp->setStatusCode(400);
        }

        $resp->headers->set('Access-Control-Allow-Origin', 'http://wi-front.com' );

        return $resp;
    }
}