# yii2-attach-file
This extension for yii2 allows you to attach different uploaded files to models.
**The extension is not done! It does not work!**

## Configuration

To use this extension, simply add the following code in your application configuration:

```php
return [
    //.....
    'modules' => [
        //.....
        'attach_file' => [
            'class' => rezident\attachfile\AttachFileModule::class,
            'originalsPath' => '@app/files/originals',
            'viewsPath' => '@app/files/views',
            'webPath' => '@app/files/views'
        ]
    ]
];
```

## Usage

### Adding behavior to the model

To adding behavior to the model you have to 

```php
public function behaviors()
{
    return [
        'specified' => [
            'class' => rezident\attachfile\behaviors\AttachFileBehavior::class,
            'modelKey' => 'specified' // If it is not specified, will be used the short name of the model class
        ]
    ];
}
            
```

