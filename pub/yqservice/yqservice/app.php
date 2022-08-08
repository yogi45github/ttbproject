<?php

namespace yqservice;

use yqservice\controller\Controller;
use yqservice\yqserviceIntegration\Language;

class app
{

    public static function start()
    {
        $route = self::parse($_SERVER['REQUEST_URI']);

        if (isset($route['task']) && $route['task'] !== '') {

            $namespace      = 'yqservice\controller\\';
            $task           = ucfirst($route['task']);
            $controllerName = $namespace . $task;

            $controller = new $controllerName();
            $controller->Display();
        } else {
            if (Config::$showWelcomePage) {
                $controller = new Controller();
                $controller->renderHead();
                $controller->loadTwig('tmpl', 'index.twig');
                $controller->renderFooter();
            } else {
                $language   = new Language();
                $controller = new Controller();
                $controller->redirect($language->createUrl('catalogs'));
            }
        }
    }

    public static function parse(&$segments)
    {

        $url = parse_url($segments);
        if (isset($url['query'])) {
            $query = $url['query'];
        }

        if (!empty($query)) {
            $values = explode('&', $query);
            foreach ($values as $key => $value) {
                if ($value === '') {
                    unset($values[$key]);
                }
                $parameter = explode('=', $value);

                if (!empty($parameter)) {
                    $parameters[$parameter[0]] = isset($parameter[1]) ? $parameter[1] : '';
                }


            }
            reset($values);

            $params = [];
            foreach ($values as $value) {
                $key = explode('=', $value);

                if (isset($key[0]) && isset($key[1])) {

                    $params[$key[0]] = $key[1];

                }

            }

            return [
                'task'   => isset($parameters['task']) ? $parameters['task'] : '',
                'params' => $params
            ];
        }

        return [];
    }

}
