<?php
/**
 * Created by PhpStorm.
 * User: Стас
 * Date: 17.09.2018
 * Time: 23:34
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $mail;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $password_hash;

    public function validateEmail($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function setUser($mail, $password, $name){
        $this->mail = $mail;
        $this->password_hash = password_hash($password, PASSWORD_DEFAULT);
        $this->name = $name;
    }

    public function verifyPassword($password){
        return password_verify($password, $this->password_hash);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }



}