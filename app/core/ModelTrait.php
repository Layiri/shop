<?php


namespace App\core;
use Respect\Validation\Validator;


/**
 * Model
 *
 * Provides virtually protected properties as a workaround to avoid conflicts
 * between PDO's reflection injection and magic methods.
 */
trait ModelTrait
{
    /**
     * Properties
     *
     * Classes should never access this array directly.
     *
     * @var mixed[]
     */
    protected $hiddenProperties = [];

    /**
     * {@inheritdoc}
     *
     * @param string $name The name of the property
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->hasProperty($name);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name The name of the property
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getPropertyValue($name);
    }

    /**
     * {@inheritdoc}
     *
     * This method can use aliases.
     *
     * @param string $name The name of the property
     * @param mixed $value The value of the property
     */
    public function __set($name, $value)
    {
        $this->setPropertyValue($name, $value);
    }

    /**
     * Get the list of virtual properties
     *
     * @abstract
     *
     * @return string[] Defined properties
     */
    abstract protected function getProperties();

    /**
     * Get the list of virtual properties aliases
     *
     * Aliases are usefull to bind database columns names to class properties.
     *
     * @return string[] An array of `alias => property`
     */
    protected function getPropertiesAliases(): array
    {
        return [];
    }

    /**
     * Get the list of properties validators
     *
     * ```
     * return [
     *     'id'       => Validator::intType()->positive(),
     *     'password' => Validator::alnum()->notEmpty(),
     * ]
     * ```
     *
     * @see ModelTrait::setPropertyValue()
     *
     * @return Validator[]
     */
    protected function getPropertiesValidators()
    {
        return [];
    }

    /**
     * Get the value of a given property
     *
     * @param string $name The name of the property
     *
     * @return mixed
     */
    protected function getPropertyValue($name)
    {
        if (isset($this->hiddenProperties[$name])) {
            return $this->hiddenProperties[$name];
        }
    }

    /**
     * Check if a property is defined
     *
     * @param string $name The name of the property
     *
     * @return bool
     */
    protected function hasProperty($name)
    {
        return in_array($name, $this->getProperties());
    }

    /**
     * Check if a property has a validator
     *
     * @param string $name The name of the property
     *
     * @return bool
     */
    protected function hasPropertyValidator($name)
    {
        $validators = $this->getPropertiesValidators();

        if (!empty($validators[$name])) {
            $validator = $validators[$name];

            return is_object($validator) && $validator instanceof Validator;
        }

        return false;
    }

    /**
     * Get the real name of an aliased property
     *
     * @param string $name Alias to resolve
     *
     * @return string
     */
    protected function resolvePropertyName($name)
    {
        if ($this->hasProperty($name)) {
            return $name;
        }

        $aliases = $this->getPropertiesAliases();

        if (isset($aliases[$name]) && $this->hasProperty($aliases[$name])) {
            return $aliases[$name];
        }
    }

    /**
     * Set the value of a property
     *
     * This method can use aliases.
     *
     * @param string $name The name of the property
     * @param mixed $value The value of the property
     */
    protected function setPropertyValue($name, $value)
    {
        $name = $this->resolvePropertyName($name);

        if ($name && $this->validatePropertyValue($name, $value)) {
            $this->hiddenProperties[$name] = $value;
        }
    }

    /**
     * Validate the value of a property
     *
     * @param string $name The name of the property
     * @param mixed $value The value of the property
     *
     * @return bool Is the value valid?
     */
    protected function validatePropertyValue($name, $value)
    {
        if (!$this->hasProperty($name)) {
            return false;
        }

        if (!$this->hasPropertyValidator($name)) {
            return true;
        }

        return $this->getPropertiesValidators()[$name]->validate($value);
    }
}
