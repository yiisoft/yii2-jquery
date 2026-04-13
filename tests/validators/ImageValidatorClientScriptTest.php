<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\tests\validators;

use PHPUnit\Framework\Attributes\Group;
use Yii;
use yii\jquery\tests\data\validators\FakedValidationModel;
use yii\jquery\tests\TestCase;
use yii\validators\ImageValidator;

/**
 * Unit tests for {@see ImageValidatorClientScript} jQuery client-side validation script.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
#[Group('jquery')]
#[Group('validators')]
final class ImageValidatorClientScriptTest extends TestCase
{
    public function testClientValidateAttribute(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(
            [
                'class' => ImageValidator::class,
                'maxHeight' => 600,
                'maxWidth' => 800,
                'minHeight' => 50,
                'minWidth' => 100,
            ],
        );

        $modelValidator->attrA = 'test-image.jpg';

        self::assertSame(
            <<<JS
            yii.validation.image(attribute, messages, {"message":"File upload failed.","skipOnEmpty":true,"mimeTypes":[],"wrongMimeType":"Only files with these MIME types are allowed: .","extensions":[],"wrongExtension":"Only files with these extensions are allowed: .","maxFiles":1,"tooMany":"You can upload at most 1 file.","notImage":"The file \u0022{file}\u0022 is not an image.","minWidth":100,"underWidth":"The image \u0022{file}\u0022 is too small. The width cannot be smaller than 100 pixels.","maxWidth":800,"overWidth":"The image \u0022{file}\u0022 is too large. The width cannot be larger than 800 pixels.","minHeight":50,"underHeight":"The image \u0022{file}\u0022 is too small. The height cannot be smaller than 50 pixels.","maxHeight":600,"overHeight":"The image \u0022{file}\u0022 is too large. The height cannot be larger than 600 pixels."}, deferred);
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );
        self::assertSame(
            [
                'message' => 'File upload failed.',
                'skipOnEmpty' => true,
                'mimeTypes' => [],
                'wrongMimeType' => 'Only files with these MIME types are allowed: .',
                'extensions' => [],
                'wrongExtension' => 'Only files with these extensions are allowed: .',
                'maxFiles' => 1,
                'tooMany' => 'You can upload at most 1 file.',
                'notImage' => 'The file "{file}" is not an image.',
                'minWidth' => 100,
                'underWidth' => 'The image "{file}" is too small. The width cannot be smaller than 100 pixels.',
                'maxWidth' => 800,
                'overWidth' => 'The image "{file}" is too large. The width cannot be larger than 800 pixels.',
                'minHeight' => 50,
                'underHeight' => 'The image "{file}" is too small. The height cannot be smaller than 50 pixels.',
                'maxHeight' => 600,
                'overHeight' => 'The image "{file}" is too large. The height cannot be larger than 600 pixels.',
            ],
            $validator->getClientOptions($modelValidator, 'attrA'),
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('someIncorrectValue', $errorMessage);

        self::assertSame(
            'Please upload a file.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }
}
