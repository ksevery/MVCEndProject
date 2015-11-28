<?php
namespace Controllers;

use EndF\Common;
use EndF\HttpContext\UserData;
use Models\BindingModels\RegisterUserBindingModel;
use Models\BindingModels\UserLoginBindingModel;

class UsersController extends ProjectBaseController
{
    /**
     * @EndF\DefaultAnnotations\Authorize
     */
    public function profile()
    {
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('body', 'profile');
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.home');
    }

    /**
     * @EndF\DefaultAnnotations\POST
     * @BINDING UserLoginBindingModel $login
     * @param UserLoginBindingModel $login
     * @throws \Exception
     */
    public function login(UserLoginBindingModel $login)
    {
        $this->db->prepare("SELECT userId, username
                                FROM users
                                WHERE username = ? AND password = ?",
            array($login->getUsername(), $login->getPassword()));
        $response = $this->db->execute()->fetchRowAssoc();
        if (!$response) {
            throw new \Exception('No user matching provided username and password!', 400);
        }

        $id = $response['userId'];
        $username = $response['username'];
        $this->httpContext->getSession()->escapedUsername = $username;
        $this->httpContext->getSession()->token = base64_encode(openssl_random_pseudo_bytes(64));
        $userData = new UserData();
        $userData->username = $login->getUsername();
        $userData->id = $id;
        $userData->role = $this->db->getUserRole($id);
        $this->httpContext->setUserData($userData);
        //Common::dump($userData->role);
        $this->redirect('/');
    }

    /**
     * @EndF\DefaultAnnotations\POST
     */
    public function logout()
    {
        $this->httpContext->getSession()->destroySession();
        $this->httpContext->getCookies()->removeCookie('_token');
        $this->redirect('/');
    }

    /**
     * @EndF\DefaultAnnotations\POST
     * @BINDING RegisterUserBindingModel $user
     * @param RegisterUserBindingModel $user
     * @throws \Exception
     */
    public function register(RegisterUserBindingModel $user)
    {
        $username = $user->getUsername();
        $password = $user->getPassword();
        $confirm = $user->getConfirm();

        $this->db->prepare('SELECT * FROM users WHERE username = ?');
        $this->db->execute(array($username));

        if($this->db->affectedRows() > 0){
            throw new \Exception('Username already exists!', 422);
        }

        if($password != $confirm){
            throw new \Exception('Password confirmation does not match!', 400);
        }

        $this->db->prepare('INSERT INTO users(username, password, roleId) values(?, ?, ?)', array($username, $password, 1));
        $this->db->execute();

        $loginModel = new UserLoginBindingModel();
        $loginModel->setUsername($username);
        $loginModel->setAfterRegisterPass($password);

        $this->login($loginModel);
    }
}