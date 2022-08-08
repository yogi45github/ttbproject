# YQService

**Requirements:**

* PHP 5.6 +
* php-soap
* php-xml
* php-zip
* php-openssl
* php-mbstring
* php-curl

### How to install?
**Demo:**
> 1. Place the files in a directory accessible to the web server.
> 2. Run "php composer.phar install" in library directory.
> 3. Use index.php like entry point to show demo.

### How to  use lib?
> 1. import YqserviceOriginalCatalog or YqserviceAftermarket classes.
> 2. Use instance of YqserviceOriginalCatalog or YqserviceAftermarket to create request. Add requests by "append" methods.

**Find by VIN example:**
    
    use yqservice\yqserviceIntegration\YqserviceOriginalCatalog;
    
    class ExampleClass {
        
        public function FindByVinExample($vin, $catalogCode) {
    
            $request = new YqserviceOriginalCatalog($catalogCode, '', 'en_US');
            $request->setUserAuthorizationMethod(Config::$defaultUserLogin, Config::$defaultUserKey);
    
            $request->appendFindVehicleByVIN($vin);
    
            $data = $request->query(); /** Now you can see VehicleListObject in $data[0] */
    
            return array_shift($data);
        }   
    }
**Get catalog info example:**

    use yqservice\yqserviceIntegration\YqserviceOriginalCatalog;
        
    class ExampleClass {
            
        public function GetCatalogInfoExample($catalogCode)
        {
            $request = new YqserviceOriginalCatalog($catalogCode, '', 'en_US');
            $request->setUserAuthorizationMethod(Config::$defaultUserLogin, Config::$defaultUserKey);
            
            $request->appendGetCatalogInfo();
            $data = $request->query();
            
            return array_shift($data);
        }
    }
  
**Multiple requests (You can use up to five at a time):**
    
    use yqservice\yqserviceIntegration\YqserviceOriginalCatalog;
    
    class ExampleClass {
        
        public function SomeMultipleRequests($vin, $catalogCode)
        {
            $request = new YqserviceOriginalCatalog($catalogCode, '', 'en_US');
            $request->setUserAuthorizationMethod(Config::$defaultUserLogin, Config::$defaultUserKey);
            
            $request->appendGetCatalogInfo();
            $request->appendFindVehicleByVIN($vin);
            $data = $request->query(); /** Now you can see CatalogObject in $data[0] and VehiclesObject in $data[1] */
    
            return [
                'catalogInfo' => $data[0],
                'vehicles' => $data[1]
            ];
        } 
    }