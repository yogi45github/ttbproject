<?php
/**
 * Created by YQService.
 * User: YQService
 * Date: 17.08.17
 * Time: 16:03
 */

namespace yqservice\controller;

use yqservice\Config;
use yqservice\yqserviceIntegration\YqserviceAftermarket;
use yqservice\yqserviceIntegration\YqserviceOriginalCatalog;
use yqservice\yqserviceIntegration\Language;
use yqservice\modules\Input;
use Twig_Autoloader;
use Twig_Environment;
use Twig_Filter_Function;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

/**
 * @property bool user
 * @property bool amUser
 * @property YqserviceOriginalCatalog request
 * @property bool dev
 */
class Controller
{
    /**
     * @var string
     */
    public $theme;
    /**
     * @var boolean
     */
    public $user;
    /**
     * @var boolean
     */
    public $amUser = false;
    /**
     * @var bool
     */
    public $error;
    /**
     * @var string
     */
    public $message;
    /**
     * @var array
     */
    private $errorTrace;
    /**
     * @var array
     */
    private $responseData;

    public function __construct()
    {
        $this->input = new Input();
        $this->data  = $this->getData();
        $this->theme = Config::$theme;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @param array $requests
     *
     * @param array $params
     *
     * @param string $login
     * @param string $pass
     *
     * @return array
     */
    public function getData($requests = [], $params = [], $login = '', $pass = '')
    {
        $c   = isset($params['c']) ? $params['c'] : '';
        $ssd = isset($params['ssd']) ? $params['ssd'] : '';

        $request = new YqserviceOriginalCatalog($c, $ssd, Config::$catalog_data);
        if (Config::$useLoginAuthorizationMethod) {
            $request->setUserAuthorizationMethod($login, $pass);
        }

        foreach ($requests as $requestItem => $paramsArr) {
            call_user_func_array([$request, $requestItem], $paramsArr);
        }

        $this->user = false;

        if ($data = $request->query()) {
            $this->user = true;
        }


        if ($request->error && (strpos($request->error, 'E_ACCESSDENIED') !== false)) {

            unset($request);
            $request = new YqserviceOriginalCatalog($c, $ssd, Config::$catalog_data);
            if (Config::$useLoginAuthorizationMethod) {
                $request->setUserAuthorizationMethod(Config::$defaultUserLogin, Config::$defaultUserKey);
            }

            foreach ($requests as $requestItem => $paramsArr) {
                call_user_func_array([$request, $requestItem], $paramsArr);
            }

            if ($data = $request->query()) {
                $this->user = false;
            }
        }

        $this->request = $request;

        if ($request->error && empty($params['ignore_error']) && strpos($request->error, 'E_STANDARD_PART_SEARCH') === false) {
            $this->error      = true;
            $this->message    = $request->error;
            $this->errorTrace = $request->errorTrace;
            $this->renderError();
        }

        $this->responseData = $request->responseData;

        $this->request = $request;

        return $data;
    }

    /**
     * @param int $code
     */
    private function renderError($code = 500)
    {
        $this->dev          = Config::$dev;
        $productionRevision = false;

        if (empty($this->dev)) {
            $productionRevision = json_decode(file_get_contents(YQSERVICE_DIR . '/revision.json'));
        }

        $viewVars = (array)$this;

        $this->renderHead([
            'user'               => $this->user,
            'amUser'             => $this->amUser,
            'dev'                => !empty($this->dev),
            'showToGuest'        => Config::$showToGuest,
            'useEnvParams'       => Config::$useEnvParams,
            'showGroupsToGuest'  => Config::$showGroupsToGuest,
            'showOemsToGuest'    => Config::$showOemsToGuest,
            'username'           => isset($_SESSION['username']) ? $_SESSION['username'] : '',
            'am_username'        => isset($_SESSION['am_username']) ? $_SESSION['am_username'] : '',
            'productionRevision' => $productionRevision ?: ''
        ]);

        $this->showRequest(true);

        $this->loadTwig('standardErrors/tmpl', $code . '.twig', $viewVars);
        $this->renderFooter();
        die();
    }

    public function renderHead($vars = [])
    {
        $input  = new Input();
        $format = $input->getString('format');
        $raw    = $format && $format === 'raw' ? true : false;

        if (!$raw) {
            $rootDir = YQSERVICE_DIR;

            $layoutsLoader = new Twig_Loader_Filesystem($rootDir . '/layouts/');
            $layouts       = new Twig_Environment($layoutsLoader, [
                'cache'       => false,
                'auto_reload' => true,
            ]);

            $language = new Language();
            $layouts->addFilter('t', new Twig_Filter_Function([$language, 't']));
            $createUrlFunc = new Twig_SimpleFunction('createUrl', [$language, 'createUrl']);
            $layouts->addFunction($createUrlFunc);
            $currentLocale = $language->getLocalization();
            $input         = new Input();

            $version = [];
            if (realpath(__DIR__ . '/revision.json')) {
                $version = json_decode(file_get_contents(realpath(__DIR__ . '/revision.json')));
            }
            $task = $this->input->getString('task');

            echo $layouts->render('head.twig', [
                'languages'         => $language->getLocalizationsList(),
                'current'           => $currentLocale ?: Config::$catalog_data,
                'availablePages'    => Config::$toolbarPages,
                'theme'             => Config::$theme ?: 'yqservice',
                'task'              => $input->getString('task', ''),
                'version'           => $version,
                'additional'        => $vars,
                'isAftermarket'     => $task === 'aftermarket',
                'applicability'     => Config::$showApplicability,
                'showFindPlate'     => isset(Config::$showFindPlate) ? Config::$showFindPlate : false,
                'plateCountryCodes' => isset(Config::$plateCountryCodes) ? Config::$plateCountryCodes : [],
                'oemExample'        => !empty(Config::$oemExample) ? Config::$oemExample : '0913128000',
                'vinExample'        => Config::$defaultVin,
                'input'             => $this->input->getArray(),
                'ft'                => $this->input->getString('ft')
            ]);
        }
    }

    public function showRequest($requestOnly = false)
    {
        if (Config::$showRequest) {
            $this->loadTwig('tmpl', 'request.twig', ['this' => $this, 'response' => $this->responseData, 'requestOnly' => $requestOnly]);
        }
    }

    public function loadTwig($tpl = '', $view = '', $vars = [])
    {
        if ($tpl === '') {
            $tpl = 'tmpl';
        }

        $rootDir = YQSERVICE_DIR;
        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem($rootDir . '/template/' . $tpl . '/');
        $twig   = new Twig_Environment($loader, [
            'cache'       => false,
            'auto_reload' => true,
        ]);

        $language = new language();

        $createUrlFunc = new Twig_SimpleFunction('createUrl', [$language, 'createUrl']);
        $twig->addFunction($createUrlFunc);

        $twig->addFilter('dump', new Twig_Filter_Function('var_dump'));
        $twig->addFilter('t', new Twig_Filter_Function([$language, 't']));
        $twig->addFilter('noSpaces', new Twig_Filter_Function([$language, 'noSpaces']));
        $twig->addFilter('printr', new Twig_Filter_Function('print_r'));
        $twig->addFilter('xml2array', new Twig_Filter_Function([$this, 'xml2array']));

        echo $twig->render($view, $vars);

        return $twig;
    }

    public function renderFooter()
    {
        $input  = new Input();
        $format = $input->getString('format');
        $raw    = $format && $format === 'raw' ? true : false;
        $task   = $this->input->getString('task');

        if (!$raw && in_array($task, Config::$toolbarPages)) {
            $rootDir = YQSERVICE_DIR;

            $layoutsLoader = new Twig_Loader_Filesystem($rootDir . '/layouts/');
            $layouts       = new Twig_Environment($layoutsLoader, [
                'cache'       => false,
                'auto_reload' => true,
            ]);

            echo $layouts->render('footer.twig', []);
        }
    }

    public function Display($tpl = 'catalogs/tmpl', $view = 'view.twig')
    {
        $this->dev          = Config::$dev;
        $productionRevision = false;

        if (!$this->dev) {
            $productionRevision = json_decode(file_get_contents(YQSERVICE_DIR . '/revision.json'));
        }

        if ($tpl === 'aftermarket') {
            if (isset($_SESSION['logged_in_am']) && $_SESSION['logged_in_am'] === true) {
                $this->amUser = true;
            }
        } else {
            if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
                $this->user = true;
            } else {
                if (!Config::$showToGuest) {
                    $this->renderAuthPage($productionRevision);
                }
            }
        }


        $this->renderHead([
            'user'               => $this->user,
            'amUser'             => $this->amUser,
            'dev'                => $this->dev,
            'showToGuest'        => Config::$showToGuest,
            'useEnvParams'       => Config::$useEnvParams,
            'showGroupsToGuest'  => Config::$showGroupsToGuest,
            'showOemsToGuest'    => Config::$showOemsToGuest,
            'username'           => isset($_SESSION['username']) ? $_SESSION['username'] : '',
            'am_username'        => isset($_SESSION['am_username']) ? $_SESSION['am_username'] : '',
            'productionRevision' => $productionRevision ?: ''
        ]);

        $auth = $this->input->getString('auth', '');

        $language = new Language();

        if ($auth === 'true') {
            if ($tpl === 'aftermarket') {
                $username = isset($_SESSION['am_username']) ? $_SESSION['am_username'] : '';
            } else {
                $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
            }

            $message = str_replace('%name%', $username, $language->t('AUTHORIZED'));
            $this->showMessage($message, 'success');
        } elseif ($auth === 'false') {
            $message = $language->t('UNAUTHORIZED');
            $this->showMessage($message, 'warning');
        }

        $error = $this->input->getString('error', false);

        if ($error) {
            $this->showMessage($error, 'warning');
        }


        if (!isset($this->pathway)) {
            $this->pathway = null;
        }

        if (!isset($this->error)) {
            $this->error = null;
        }

        if ($this->error) {

            if (Config::$useEnvParams && strpos($this->message, 'E_ACCESSDENIED') !== false) {
                $this->message = 'E_ACCESSDENIED';
            }

            $this->loadTwig('error/tmpl', 'default.twig', ['message' => $this->message, 'more' => $this->errorTrace]);
        }

        if ($this->pathway) {
            $this->renderPathway($this->pathway);
        }

        $format = $this->input->getString('format');

        if ($format !== 'raw') {
            $task          = $this->input->getString('task');
            $this->toolbar = in_array($task, Config::$toolbarPages);
            $this->showRequest();
        }

        $this->loadTwig($tpl . '/tmpl', $view . '.twig', (array)$this);
        $this->renderFooter();
    }

    protected function renderAuthPage($productionRevision = false, $isAm = false)
    {
        http_response_code(401);
        $task = $this->input->getString('task');
        $this->renderHead([
            'user'               => $this->user,
            'amUser'             => $this->amUser,
            'dev'                => !empty($this->dev) ? $this->dev : false,
            'username'           => isset($_SESSION['username']) ? $_SESSION['username'] : '',
            'productionRevision' => $productionRevision ?: '',
            'useEnvParams'       => Config::$useEnvParams,
            'authPage'           => true,
        ]);
        $this->loadTwig('error/tmpl', 'unauthorized.twig', ['type' => 'unauthorized', 'isAftermarket' => ($isAm || $task === 'aftermarket')]);
        $this->renderFooter();
        die();
    }

    public function showMessage($message, $type = 'default')
    {
        $language = new Language();

        $this->loadTwig('tmpl', 'message.twig', ['message' => $language->t($message), 'type' => $type]);
    }

    public function renderPathway($pathway)
    {
        $input  = new Input();
        $format = $input->getString('format');
        $raw    = $format && $format === 'raw' ? true : false;

        if (!$raw) {
            $rootDir       = YQSERVICE_DIR;
            $language      = new language();
            $layoutsLoader = new Twig_Loader_Filesystem($rootDir . '/layouts/');
            $layouts       = new Twig_Environment($layoutsLoader, [
                'cache'       => false,
                'auto_reload' => true,
            ]);

            $function = new Twig_SimpleFunction('createUrl', [$language, 'createUrl']);
            $layouts->addFunction($function);

            $layouts->addFilter('dump', new Twig_Filter_Function('var_dump'));
            $layouts->addFilter('t', new Twig_Filter_Function([$language, 't']));
            $layouts->addFilter('noSpaces', new Twig_Filter_Function([$language, 'noSpaces']));
            $layouts->addFilter('printr', new Twig_Filter_Function('print_r'));
            $currentLink = getenv('REQUEST_URI');

            $vars = [
                'pathway' => $pathway,
                'current' => $currentLink
            ];

            echo $layouts->render('pathway.twig', $vars);
        }
    }

    function returnRequest($request, $requestItem)
    {
        return $request->$requestItem();
    }

    public function getBackUrl()
    {
        $envBackUrl = getenv('UUE_BACK_URL');
        if ($envBackUrl && Config::$useEnvParams) {
            return base64_decode($envBackUrl);
        }

        if (!Config::$useEnvParams) {
            return Config::$SiteDomain;
        } else {
            return false;
        }
    }

    public function getLinkTarget()
    {
        if (!Config::$useEnvParams) {
            return Config::$linkTarget;
        }

        $envTarget = getenv('BACKURL_NEW_WINDOW');

        return boolval($envTarget) ? '_blank' : Config::$linkTarget;
    }

    public function xml2array(\SimpleXMLElement $xmlObject, $out = [])
    {
        foreach ((array)$xmlObject as $index => $node)
            $out[$index] = (is_object($node)) ? $this->xml2array($node) : $node;

        return $out;
    }

    public function logoutFromAftermarket()
    {
        unset($_SESSION['logged_in_am']);
        unset($_SESSION['am_username']);
        unset($_SESSION['am_key']);

        $data = $this->input->formData();

        if (!$_SESSION['logged_in_am']) {
            $this->redirect($data['user']['backurl']);
        }
    }

    protected function loginByAftermarket($user, $redirectUrl = null)
    {
        $request = [
            'appendFindOEM' => [
                'oem' => 'C110'
            ]
        ];

        $login = trim($user['login']);
        $key   = $user['password'];

        $response = $this->getAftermarketData($request, [], $login, $key);

        if ($this->amUser) {
            $_SESSION['logged_in_am'] = true;
            $_SESSION['am_username']  = $login;
            $_SESSION['am_key']       = $key;

            if (!$redirectUrl) {
                $this->redirect($user['backurl'] . '&auth=true');
            }

            $this->redirect($redirectUrl);
        } else {
            unset($_SESSION['logged_in_am']);
            unset($_SESSION['am_username']);
            unset($_SESSION['am_key']);

            if ($response->error) {
                $language = new Language();
                $this->redirect($language->createUrl('catalogs'), $response->message, true);
                die();
            }

            $this->redirect($user['backurl'] . '&auth=false');
        }
    }

    public function getAftermarketData($requests = [], $params = [], $login = '', $pass = '')
    {
        $request = new YqserviceAftermarket(Config::$catalog_data);

        if ($this->input->getString('task') === 'aftermarket') {
            $login = $this->getAuthAmLogin();
            $pass  = $this->getAuthAmKey();
        }

        if (!$this->isAuthorise()) {
            $login = Config::$defaultUserLogin;
            $pass  = Config::$defaultUserKey;
        }

        if (Config::$useLoginAuthorizationMethod) {
            $request->setUserAuthorizationMethod($login, $pass);
        }

        foreach ($requests as $requestItem => $paramsArr) {
            call_user_func_array([$request, $requestItem], $paramsArr);
        }

        $this->amUser = false;
        $data         = $request->query();

        if (!empty($data->oems)) {
            $this->amUser = true;
        }


        if ($request->error && (strpos($request->error, 'E_ACCESSDENIED') !== false)) {
            $this->amUser = false;
        }

        if ($request->error && empty($params['ignore_error'])) {
            $this->error   = true;
            $this->message = 'AFTERMARKET:' . $request->error;

            $err          = new \stdClass();
            $err->error   = true;
            $err->message = $request->error;

            return $err;
        }
        $this->responseData = $request->data;

        $this->request = $request;

        return $data;
    }

    protected function getAuthAmLogin()
    {
        return !empty($_SESSION['am_username']) ? $_SESSION['am_username'] : false;
    }

    protected function getAuthAmKey()
    {
        return !empty($_SESSION['am_key']) ? $_SESSION['am_key'] : false;
    }

    protected function isAuthorise()
    {
        return !empty($_SESSION['logged']) ? $_SESSION['logged'] : false;
    }

    public function redirect($link, $error = null, $hideFromUrl = false)
    {
        header("Location: " . $link . ($error ? '&error=' . $error : '') . '&' . ($hideFromUrl ? 'hideErrorFromUrl=1' : ''));
        exit();
    }

    protected function isAuthoriseInAm()
    {
        return !empty($_SESSION['logged_in_am']) ? $_SESSION['logged_in_am'] : false;
    }
}