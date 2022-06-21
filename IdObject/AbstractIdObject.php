<?php
namespace PhpMemoryCache\IdObject;

abstract class AbstractIdObject
{
    /**
     * @var string
     */
    private const INVALID_ID_EXCEPTION_MESSAGE = 'Only positive integer ids are allowed.';

    /**
     * @var int
     */
    protected int $id;

    /**
     * Class constructor
     *
     * @param int $id
     */
    public function __construct($id)
    {
        $this->validateId($id);
        $this->id = (int) $id;
    }


    public function getId(): int
    {
        return $this->id;
    }


    protected function validateId($id)
    {
        $id = filter_var(
            $id,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if (!$id) {
            throw new \Exception($this, static::INVALID_ID_EXCEPTION_MESSAGE);
        }
    }
}