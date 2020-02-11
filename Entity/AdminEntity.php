<?php


namespace Entity;

class AdminEntity
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
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    public function __set($property, $value){
        if('id' === $property) {
            $this->id = (int) $value;
        }
        if('firstName' === $property) {
            $this->firstName = (string) $value;
        }
        if('Name' === $property) {
            $this->name = (string) $value;
        }
        if('email' === $property) {
            $this->email = (string) $value;
        }
        if('password' === $property) {
            $this->password = (string) $value;
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
    public function getName(): string
    {
        return $this->name;
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



}