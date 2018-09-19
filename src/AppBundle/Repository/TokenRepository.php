<?php
/**
 * Created by PhpStorm.
 * User: Стас
 * Date: 18.09.2018
 * Time: 20:27
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class TokenRepository extends EntityRepository
{
    /**
     * @param $token
     * @param $err
     * @return mixed
     */
    public function checkToken($token, &$err){
        $selected = $this->createQueryBuilder('token')
                ->where('token.token = :token')
                ->andWhere('token.isAlive = true')
                ->setParameter('token', $token)
                ->getQuery()
                ->getResult();

        if(!isset($selected[0])){
            $err .= 'Please log in first!';
        }else{
            $selected = $selected[0];
            if( intval($selected->getInitiationDate()) + intval($selected->getLifeLength()) < time() ){
                $err .= 'Your authorization token has expired. Please log in first!';
            }
        }
        return $selected;
    }
}