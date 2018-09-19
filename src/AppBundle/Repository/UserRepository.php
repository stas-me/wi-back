<?php
/**
 * Created by PhpStorm.
 * User: Стас
 * Date: 18.09.2018
 * Time: 13:22
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param $email
     * @return mixed
     */
    public function isTaken($email){
        try{
            return $this->createQueryBuilder('user')
                ->where('user.mail = :email')
                ->setParameter('email', $email)
                ->getQuery()
                ->getOneOrNullResult();
        }
        catch (\Doctrine\ORM\NonUniqueResultException $e){
            return true;
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getUser($id){
        $user = $this->createQueryBuilder('user')
            ->where('user.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
        return $user[0];
    }

    /**
     * @param $mail
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUserByMail($mail){
        $user = $this->createQueryBuilder('user')
            ->where('user.mail = :mail')
            ->setParameter('mail', $mail)
            ->getQuery()
            ->getOneOrNullResult();
        return $user;
    }
}