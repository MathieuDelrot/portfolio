<?php


namespace App\Entity;

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


    public function __construct(array $datas = null)
    {
        if(!empty($datas)){
            $this->hydrate($datas);
        }
    }

    public function hydrate(array $datas = null)
    {
        foreach ($datas as $key => $value)
        {
            $method = 'set'.ucfirst($key);

            if (method_exists($this, $method))
            {
                $this->$method($value);
            }
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

    /**
     * @param int $id
     * @return MessageEntity
     */
    public function setId(int $id): MessageEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $firstName
     * @return MessageEntity
     */
    public function setFirstName(string $firstName): MessageEntity
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @param string $lastName
     * @return MessageEntity
     */
    public function setLastName(string $lastName): MessageEntity
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @param string $email
     * @return MessageEntity
     */
    public function setEmail(string $email): MessageEntity
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $message
     * @return MessageEntity
     */
    public function setMessage(string $message): MessageEntity
    {
        $this->message = $message;
        return $this;
    }


}