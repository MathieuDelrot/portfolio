<?php


namespace App\Entity;

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

    /**
     * @param int $id
     * @return AdminEntity
     */
    public function setId(int $id): AdminEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $firstName
     * @return AdminEntity
     */
    public function setFirstName(string $firstName): AdminEntity
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @param string $name
     * @return AdminEntity
     */
    public function setName(string $name): AdminEntity
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $email
     * @return AdminEntity
     */
    public function setEmail(string $email): AdminEntity
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $password
     * @return AdminEntity
     */
    public function setPassword(string $password): AdminEntity
    {
        $this->password = $password;
        return $this;
    }



}