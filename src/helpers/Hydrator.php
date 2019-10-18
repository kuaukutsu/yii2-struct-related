<?php
namespace kuaukutsu\struct\related\helpers;

use kuaukutsu\struct\related\Related;
use ReflectionClass;
use ReflectionException;
use Yii;

/**
 * Class Hydrator
 * @package kuaukutsu\struct\related\helpers
 *
 * Example
 *
 * $data = [];
 *
 * $dtoHydrator = new \kuaukutsu\struct\related\helpers\Hydrator([
 *  'resourceUri' => 'resourceUri',
 *  'owner' => 'owner/0/_owner',
 *  'items' => 'items',
 * ]);
 *
 * $item = $dtoHydrator->hydrate($data, RelatedDTO::class);
 *
 */
class Hydrator
{
    /**
     * Mapping
     * @var array
     */
    private $map;

    /**
     * Hydrator constructor.
     * @param array $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @param array $data
     * @param string $className
     * @return null|object
     */
    public function hydrate(array $data, string $className): ?object
    {
        try {
            $reflection = $this->getReflectionClass($className);
            $object = $reflection->newInstanceWithoutConstructor();
            foreach ($this->map as $dataKey => $propertyName) {
                if ($reflection->hasProperty($dataKey)) {
                    $property = $reflection->getProperty($dataKey);
                    $property->setAccessible(true);
                    $property->setValue($object, self::getValueByPath($data, $propertyName));
                }
            }

            return $object;

        } catch (ReflectionException $exception) {
            Yii::error($exception->getMessage(), Related::class);
        }

        return null;
    }

    /**
     * Local cache of reflection class instances
     * @var array
     */
    private $reflectionClassMap = [];

    /**
     * @param string $className
     * @return ReflectionClass
     * @throws ReflectionException
     */
    protected function getReflectionClass(string $className): ReflectionClass
    {
        if (!isset($this->reflectionClassMap[$className])) {
            $this->reflectionClassMap[$className] = new ReflectionClass($className);
        }

        return $this->reflectionClassMap[$className];
    }

    /**
     * Example: getValueByPath(Data[], 'key/subkey')
     *
     * @param array $array
     * @param string $path
     * @param mixed|null $default
     * @return mixed
     */
    protected static function getValueByPath(array $array, $path, $default = null)
    {
        $key = trim($path, '/');
        $keyArr = explode('/', $key);

        if (count($keyArr) > 1) {
            $search = $array;
            foreach ($keyArr as $name) {
                if (!isset($search[$name])) {
                    $search = $default;
                    break;
                }

                $search = $search[$name];
            }

            return $search;
        }

        return $array[$key] ?? $default;
    }
}