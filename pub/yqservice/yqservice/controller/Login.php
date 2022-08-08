<?php
/**
 * Created by YQService.
 * User: YQService
 * Date: 27.08.18
 * Time: 14:04
 */

namespace yqservice\controller;

use yqservice\controller\Controller;


class Login extends Controller
{
    /**
     * @var boolean
     */
    private $isAftermarket;

    public function Display($tpl = 'login', $view = 'view')
    {
        $view = $this->input->getString('view');
        switch ($view) {
            case 'login':
                $this->login();
                break;
            case 'logout':
                $user = $this->input->formData()['user'];
                $url  = parse_url($user['backurl']);
                parse_str($url['query'], $backurlParams);
                $task = $backurlParams['task'];

                if ($task === 'aftermarket') {
                    $this->logoutFromAftermarket();
                    break;
                }

                $this->logout();
                break;
        }

    }

    public function login()
    {
        $user = $this->input->formData()['user'];

        if (!$user) {
            return;
        }

        $login = trim($user['login']);
        $key   = $user['password'];

        $url = parse_url($user['backurl']);
        parse_str($url['query'], $backurlParams);
        $task = $backurlParams['task'];

        $isAm = !empty($this->input->formData()['isAm']);

        if ((!empty($task) && $task === 'aftermarket') || $isAm) {
            $this->isAftermarket = true;
        } else {
            $requests = [
                'appendListCatalogs' => []
            ];

            $this->getData($requests, [], $login, $key);

            if ($this->user) {
                $_SESSION['logged']   = true;
                $_SESSION['username'] = $login;
                $_SESSION['key']      = $key;
                $this->redirect($user['backurl'] . '&auth=true');
            } else {
                unset($_SESSION['logged']);
                unset($_SESSION['username']);
                unset($_SESSION['key']);
                $this->redirect($user['backurl'] . '&auth=false');
            }
        }

        $this->loginByAftermarket($user);
    }

    public function logout()
    {
        if (!empty($_SESSION['logged'])) {
            unset($_SESSION['logged']);
            unset($_SESSION['username']);
            unset($_SESSION['key']);
        }

        if (!empty($_SESSION['logged_in_am'])) {
            unset($_SESSION['am_username']);
            unset($_SESSION['am_key']);
            unset($_SESSION['logged_in_am']);
        }

        $data = $this->input->formData();

        if (empty($_SESSION['logged']) && empty($_SESSION['logged_in_am'])) {
            $this->redirect($data['user']['backurl']);
        }
    }
}