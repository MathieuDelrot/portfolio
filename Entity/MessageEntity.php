<?php


namespace Entity;


class MessageEntity
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
    private $lastName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $message;


    public function __construct(array $datas)
    {
        $this->hydrate($datas);
    }

    public function hydrate(array $datas)
    {
        foreach ($datas as $key => $value)
        {
            $this->__set($key, $value);
        }
    }

    public function __set($property, $value){

        if('id' === $property) {
            $this->id = (int) $value;
        }
        if('firstName' === $property) {
            $this->firstName = (string) $value;
        }
        if('lastName' === $property) {
            $this->lastName = (string) $value;
        }
        if('email' === $property) {
            $this->email = (string) $value;
        }
        if('message' === $property) {
            $this->message = (string) $value;
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
    public function getLastName(): string
    {
        return $this->lastName;
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
    public function getMessage(): string
    {
        return $this->message;
    }



}