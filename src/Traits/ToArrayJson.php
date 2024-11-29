<?php
/**
 * @author XJ.
 * @Date   2023/8/28 0028
 */

namespace Fatbit\FormRequestParam\Traits;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * @implements Arrayable
 * @implements Jsonable
 */
trait ToArrayJson
{
    protected array $objectVars = [];

    /**
     * 转换成数组
     * Created by XJ.
     * Date: 2021/11/12
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [];
        foreach ($this->getObjectVars() as $var) {
            $data[$var] = $this->{$var};
        }

        return $data;
    }

    /**
     * 转换成json
     * Created by XJ.
     * Date: 2021/11/12
     *
     * @return array|object|string|null
     */
    public function toJson($options = 256)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Created by XJ.
     * Date: 2022/1/7
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->toJson();
    }

    /**
     * 获取对象属性
     *
     * @author XJ.
     * @Date   2024/11/29 星期五
     * @return array|string[]
     */
    public function getObjectVars()
    {
        if (!empty($this->objectVars)) {
            return $this->objectVars;
        }
        $reflectionClass = new \ReflectionClass(static::class);
        $properties      = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);

        return $this->objectVars = array_map(fn($v) => $v->name, $properties);
    }
}