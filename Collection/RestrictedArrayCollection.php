<?php
namespace PhpMemoryCache\Collection;

/**
 * RestrictedArrayCollection adds various restrictions on changing the contents:
 *      - allows to enforce type of object elements allowed to be added to the collection
 *      - allows to lock down the collection to become immutable
 *
 * This class can be extended to impose some other restrictions
 */

class RestrictedArrayCollection extends ArrayCollection
{
    /**
     * @var string|null
     */
    const ELEMENT_TYPE = null;

    /**
     * Immutable flag; can be only set to true once
     *
     * @var bool
     */
    private $isImmutable = false;

    /**
     * Overloaded constructor to check for values in the collection
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct();

        // we need to add elements one by one for the checks to work properly
        foreach ($elements as $key => $element) {
            $this->set($key, $element);
        }
    }

    /**
     * Sets this collection to be immutable once and for all
     */
    final public function setImmutable()
    {
        $this->isImmutable = true;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value)
    {
        $this->checkRestrictionsBeforeChange();

        $this->checkElementRestrictions($value);

        parent::set($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function add($value)
    {
        $this->checkRestrictionsBeforeChange();

        $this->checkElementRestrictions($value);

        return parent::add($value);
    }

    /**
     * {@inheritDoc}
     */
    public function append(CollectionInterface $values)
    {
        $this->checkRestrictionsBeforeChange();

        foreach ($values as $value) {
            $this->checkElementRestrictions($value);
            $this->add($value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        $this->checkRestrictionsBeforeChange();

        parent::remove($key);
    }

    /**
     * {@inheritDoc}
     */
    public function removeElement($element)
    {
        $this->checkRestrictionsBeforeChange();

        parent::removeElement($element);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->checkRestrictionsBeforeChange();

        parent::clear();
    }

    /**
     * Performs checks on restrictions before changing the value of this collection
     *
     * @throws \BadMethodCallException
     */
    protected function checkRestrictionsBeforeChange()
    {
        if ($this->isImmutable) {
            throw new \BadMethodCallException('This collection is immutable.');
        }
    }

    /**
     * Runs checks on elements restrictions
     *
     * @param mixed $element
     *
     * @throws \InvalidArgumentException
     */
    protected function checkElementRestrictions($element)
    {
        if (null !== ($elementType = static::ELEMENT_TYPE) && !$element instanceof $elementType) {
            throw new \InvalidArgumentException('Expected element type is "' . $elementType . '".');
        }
    }
}