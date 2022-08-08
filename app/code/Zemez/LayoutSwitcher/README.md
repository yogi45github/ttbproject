Description
===========
Magento2 Layout Switcher implementation.

Install
=======

1. Add repo to composer.json file:
```json
{
    "repositories": [
            {
                "type": "composer",
                "url": "https://repo.magento.com/"
            },
            {
                "type": "vcs",
                "url": "http://products.git.devoffice.com/magento/magento2-layout-switcher.git"
            }
        ]
}
```

2. Add the module to composer:
```bash
composer require zemez/layout-switcher:dev-master
```

3. Enable the module:
```bash
bin/magento module:enable --clear-static-content Zemez_LayoutSwitcher
bin/magento setup:upgrade
```
4. Add index.php:
```bash
$params = $_SERVER;
$params[\Zemez\LayoutSwitcher\Model\StoreResolver\Plugin\Website::PARAM_MODE] = true;
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
// $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('Magento\Framework\App\Http');
$bootstrap->run($app);
```

Configure
=========

Please navigate to the **Stores -> Settings -> Configuration -> Zemez -> Layout Switcher** in order to configure the module.

Uninstall
=========

1. Disable the module:
```bash
bin/magento module:disable --clear-static-content Zemez_LayoutSwitcher
```

2. Remove the module from composer:
```bash
composer remove zemez/layout-switcher
```