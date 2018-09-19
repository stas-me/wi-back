<?php
/**
 * Created by PhpStorm.
 * User: Стас
 * Date: 18.09.2018
 * Time: 13:33
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TokenRepository")
 * @ORM\Table(name="token")
 */
class Token
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @ORM\Column(type="string")
     */
    private $token;

    /**
     * @ORM\Column(type="integer")
     */
    private $initiationDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $lifeLength;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAlive;

    private function generateRandomString($length = 50) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function generateToken($userId, $lifeLength = 3600){ // 3600 seconds == 1 hour
        $this->userId = $userId;
        $this->lifeLength = $lifeLength;
        $this->token = $this->generateRandomString();
        $this->isAlive = true;
        $this->initiationDate = time();
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return mixed
     */
    public function getLifeLength()
    {
        return $this->lifeLength;
    }

    /**
     * @return mixed
     */
    public function getInitiationDate()
    {
        return $this->initiationDate;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $isAlive
     */
    public function setIsAlive($isAlive)
    {
        $this->isAlive = $isAlive;
    }





}