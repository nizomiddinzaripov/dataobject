<?php

namespace Programm011\Dataobject;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class DataObject
{
    /**
     * @param array $parameters
     *
     * @return static
     */
    public static function createFromArray(?iterable $parameters = []): ?self
    {
        if (is_null($parameters)) {
            return null;
        }
        $instance = new static;

        try {
            $class = new \ReflectionClass(static::class);

            $fields = [];

            foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
                if ($reflectionProperty->isStatic()) {
                    continue;
                }

                $field = $reflectionProperty->getName();

                $fields[$field] = $reflectionProperty;
            }

            foreach ($fields as $field => $validator) {
                $typeName = $validator->getType()->getName();
                if (!$validator->getType()->isBuiltin()) {
                    $dataObject = new $typeName;
                    if ($dataObject instanceof DataObject) {
                        if (is_array($parameters[$field])) {
                            $value = $dataObject::createFromArray($parameters[$field]);
                        } else {
                            $value = $dataObject::createFromModel($parameters[$field]);
                        }
                    }
                } else {
                    $value = ($parameters[$field] ?? $parameters[Str::snake($field)] ?? $validator->getDefaultValue() ?? $instance->{$field} ?? null);
                }

                $instance->{$field} = $value;

                unset($parameters[$field]);
            }
        } catch (\Exception $exception) {
        }

        return $instance;
    }

    /**
     * @param Model $model
     *
     * @return static
     */
    public static function createFromModel(Model $model): static
    {
        return static::createFromArray($model->toArray());
    }

    /**
     * @param Collection $collection
     *
     * @return Collection
     */
    public static function collection(Collection $collection): Collection
    {
        return $collection->transform(function ($img) {
            return static::createFromModel($img);
        });
    }

    /**
     * @param bool $trim_nulls
     *
     * @return array
     */
    public function all(bool $trim_nulls = false): array
    {
        $data = [];

        try {
            $class = new \ReflectionClass(static::class);

            $properties = $class->getProperties(\ReflectionProperty::IS_PUBLIC);

            foreach ($properties as $reflectionProperty) {
                if ($reflectionProperty->isStatic()) {
                    continue;
                }

                $value = $reflectionProperty->getValue($this);

                if ($trim_nulls === true) {
                    if (!is_null($value)) {
                        $data[$reflectionProperty->getName()] = $value;
                    }
                } else {
                    $data[$reflectionProperty->getName()] = $value;
                }
            }
        } catch (\Exception $exception) {
        }

        return $data;
    }
}
