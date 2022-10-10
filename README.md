# Yii2 Common Lib

[![PHP Version Require](http://poser.pugx.org/gunter1020/yii2-common/require/php)](https://packagist.org/packages/gunter1020/yii2-common)
[![Latest Stable Version](http://poser.pugx.org/gunter1020/yii2-common/v)](https://packagist.org/packages/gunter1020/yii2-common)
[![Total Downloads](http://poser.pugx.org/gunter1020/yii2-common/downloads)](https://packagist.org/packages/gunter1020/yii2-common)
[![License](http://poser.pugx.org/gunter1020/yii2-common/license)](https://packagist.org/packages/gunter1020/yii2-common)

## Installation

```bash
composer require gunter1020/yii2-common
```

or include the dependency in the `composer.json` file:

```json
{
    "require": {
        "gunter1020/yii2-common": "^1.0"
       
    }
}
```

## Configuration

### `Optional` Use [EncryptBehavior](src/behaviors/EncryptBehavior.php)

> Setting your config file. `e.g., /config/web.php`

```php
use gunter1020\yii2\common\base\GuSecurity;

$config = [
    'components' => [
        'security' => [
            'class' => GuSecurity::class,
            'encryptKey' => 'YOUR_ENCRYPT_KEY',
        ]
    ]
]
```

> Setting your models file. `e.g., /models/Account.php`

```php
use gunter1020\yii2\common\behaviors\EncryptBehavior;

public function behaviors()
{
    return [
        'encryptAttrs' => [
            'class' => EncryptBehavior::class,
            'attributes' => ['id_number', 'salary_amount'],
        ],
    ];
}
```
