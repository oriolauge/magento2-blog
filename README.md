# Magento 2 Blog module
This module adds options to create a blog inside Magento 2 intance. Also, this blog is inspred by [Mageplaza's blog](https://github.com/mageplaza/magento-2-blog) and [Magefan's blog](https://github.com/magefan/module-blog), but with the diferents that the blog content is based with EAV database model like products.

With this model, we get more featres like more easy to create new attributes, maintant blog content in different languages or create new SEO features like hreflangs.

## Technologies
- Magento 2.4.3 CE
- Stability: Stable Build

## Instalation guide
```
composer require oag/module-blog
php bin/magento module:enable OAG_Blog # If you upload via FTP
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```
## Uninstalation guide
You can execute command ```php bin/magento module:uninstall OAG_Blog``` to uninstall module. WARNING/TODO: We don't finish the uninstall script!

## Key Features
TODO

## Future Features
TODO

## FAQ
- The post list admin panel show an incorrect column order.
To fix this issue, you can remove in ui_bookmark mysql table the rows with namespace oag_blog_post_listing. When this rows are remove it, you can flush cache and the order will be correct
