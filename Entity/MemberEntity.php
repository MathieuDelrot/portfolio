<?php

namespace Entity;


class MemberEntity
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $passwordKey;


    private $keyDate;

    /**
     * @var boolean
     */
    private $validate;


    public function __set($property, $value){
        if('id' === $property) {
            $this->id = (int) $value;
        }
        if('firstName' === $property) {
            $this->firstName = (string) $value;
        }
        if('email' === $property) {
            $this->email = (string) $value;
        }
        if('password' === $property) {
            $this->password = (string) $value;
        }
        if('passwordKey' === $property) {
            $this->passwordKey = (string) $value;
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }



    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }


    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }



    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }


    /**
     * @return string
     */
    public function getPasswordKey(): string
    {
        return $this->passwordKey;
    }


    /**
     * @return mixed
     */
    public function getKeyDate()
    {
        return $this->keyDate;
    }

    /**
     * @return bool
     */
    public function isValidate(): bool
    {
        return $this->validate;
    }

}