##Vision Ajax Infinite Scroll Extension
This extension reduces product load time by automatically loads products as users scroll down the website without reloading the whole page.

##Support: 
version - 2.3.x

##How to install Extension

1. Download the archive file.
2. Unzip the file
3. Create a folder [Magento_Root]/app/code/Vision/AjaxInfiniteScroll
4. Drop/move the unzipped files

#Enable Extension:
- php bin/magento module:enable Vision_AjaxInfiniteScroll
- php bin/magento setup:upgrade
- php bin/magento cache:clean
- php bin/magento setup:static-content:deploy -f
- php bin/magento cache:flush

#Disable Extension:
- php bin/magento module:disable Vision_AjaxInfiniteScroll
- php bin/magento setup:upgrade
- php bin/magento cache:clean
- php bin/magento setup:static-content:deploy -f
- php bin/magento cache:flush
