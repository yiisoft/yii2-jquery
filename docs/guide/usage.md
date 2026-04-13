Basic usage and examples
========================

Once [`\yii\jquery\Bootstrap`](configuration.md) is registered, every supported validator, grid component, and widget
is automatically wired with its jQuery-backed `$clientScript`. For most applications, no further action is required.
`ActiveForm`, `GridView`, `Captcha`, and client-side validation all behave as before.

This chapter shows how to *override* a single jQuery implementation with a custom one, using the strategy-pattern entry 
point exposed by the framework.


Overriding a validator client script
------------------------------------

The `clientScript` configuration key can be set directly on an individual rule. The example below swaps the default
`RequiredValidatorClientScript` for a custom implementation, leaving every other rule untouched:

```php
use yii\base\Model;

final class LoginForm extends Model
{
    public string $email = '';
    public string $password = '';

    public function rules(): array
    {
        return [
            [
                ['email', 'password'],
                'required',
                'clientScript' => [
                    'class' => \app\validators\MyCustomRequiredClientScript::class,
                ],
            ],
            ['email', 'email'],
        ];
    }
}
```

Your custom class should implement the same client-script contract as
`\yii\jquery\validators\RequiredValidatorClientScript`. Use the shipped class as a reference implementation.


Overriding a widget client script
---------------------------------

The same mechanism works for widgets. Pass a `clientScript` configuration when creating the widget:

```php
use yii\widgets\ActiveForm;

$form = ActiveForm::begin(
    [
        'id' => 'login-form',
        'clientScript' => [
            'class' => \app\widgets\MyCustomActiveFormClientScript::class,
        ],
    ],
);

// ... form fields ...

ActiveForm::end();
```

The custom class should follow the same contract as `\yii\jquery\widgets\ActiveFormClientScript`.


Disabling jQuery support for a single component
-----------------------------------------------

Because the mappings are registered as DI container *defaults*, you can pass an entirely different `clientScript`
implementation; for example one that emits Vanilla JS, HTMX, or Alpine helpers instead of jQuery; whenever a specific 
form or grid should not use jQuery.


Next steps
----------

* [Configuration](configuration.md) — full list of mapped components.
* [Testing](testing.md) — validate your custom client scripts locally.
