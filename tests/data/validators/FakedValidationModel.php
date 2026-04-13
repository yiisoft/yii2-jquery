<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\tests\data\validators;

use yii\base\Model;

use function array_keys;
use function func_get_args;

/**
 * Stub model for testing.
 *
 * @property mixed $attrA
 * @property mixed $attrA_repeat
 * @property mixed $attr_trim
 * @property mixed $attr_one
 * @property mixed $attr_image
 * @property mixed $attr_images
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
final class FakedValidationModel extends Model
{
    public $safe_attr;
    public $val_attr_a;
    public $val_attr_b;
    public $val_attr_c;
    public $val_attr_d;
    private array $attr = [];

    public function __get($name): mixed
    {
        if (preg_match('/^attr(?:[A-Z]|_)/', $name) === 1) {
            return $this->attr[$name] ?? null;
        }

        return parent::__get($name);
    }

    public function __set($name, $value): void
    {
        if (preg_match('/^attr(?:[A-Z]|_)/', $name) === 1) {
            $this->attr[$name] = $value;

            return;
        }

        parent::__set($name, $value);
    }

    /**
     * @return list<string>
     */
    public function attributes(): array
    {
        /** @var list<string> */
        return array_keys($this->attr);
    }

    public function clientInlineVal($attribute, $params, $validator, $current, $view = null): array
    {
        return func_get_args();
    }

    public static function createWithAttributes(array $attributes = []): self
    {
        $model = new self();

        foreach ($attributes as $attribute => $value) {
            match ($attribute) {
                'attrA' => $model->attrA = $value,
                'attrA_repeat' => $model->attrA_repeat = $value,
                'attr_trim' => $model->attr_trim = $value,
                'attr_one' => $model->attr_one = $value,
                'attr_image' => $model->attr_image = $value,
                'attr_images' => $model->attr_images = $value,
                default => null,
            };
        }

        return $model;
    }

    public function getAttributeLabel($attr): string
    {
        return $attr;
    }

    public function inlineVal($attribute, $params, $validator, $current): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            [
                ['val_attr_a', 'val_attr_b'],
                'required',
                'on' => 'reqTest',
            ],
            [
                'val_attr_c',
                'integer',
            ],
            [
                'attr_images',
                'file',
                'maxFiles' => 3,
                'extensions' => ['png'],
                'on' => 'validateMultipleFiles',
                'checkExtensionByMimeType' => false,
            ],
            [
                'attr_image',
                'file',
                'extensions' => ['png'],
                'on' => 'validateFile',
                'checkExtensionByMimeType' => false,
            ],
            [
                '!safe_attr',
                'integer',
            ],
        ];
    }
}
