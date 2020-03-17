# Wbaiyy/composer-cleaner
删除通过composer安装私有包中
[.gitattributes](https://git-scm.com/book/zh/v1/%E8%87%AA%E5%AE%9A%E4%B9%89-Git-Git%E5%B1%9E%E6%80%A7)
设置的`/path/to/file export-ignore`文件

## 运行环境要求
* php >= 5.6

## 安装
你只需要在**composer.json**中增加以下代码：
```js
    {
        "repositories": [
           {
               "type": "composer",
               "url": "http://www.composer-satis.com.master.test50.egomsl.com"
           }
        ],
        "require": {
            "Wbaiyy/composer-cleaner": "^1.0"
        },
        "config": {
            "secure-http" : false
        }
    }
```

## 配置
除了默认删除**.gitattributes**中配置的**export-ignore**文件外，
你还可以在**composer.json**中配置删除vendor中的其它文件

```js
{
    "extra": {
        "Wbaiyy/composer-cleaner": [
            "facebook/aws/tests",
            "package-name/path"
        ]
    }
}
```
> 上面配置中，将额外删除**vendor/aws/tests**及**vendor/package-name/path**
