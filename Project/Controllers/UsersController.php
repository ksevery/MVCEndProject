<?php
namespace Controllers;

use EndF\BaseController;
use Models\BindingModels\RegisterUserBindingModel;
use Models\BindingModels\UserLoginBindingModel;

class UsersController extends BaseController
{
    /**
     * @PUT
     * @Route("users/{id:int}")
     * @param $productId
     */
    public function sell($productId)
    {
        echo 'sold';
    }

    /**
     * @Authorize
     */
    public function profile()
    {
        echo 'My Profile';
    }

    /**
     * @POST
     * @BINDING UserLoginBindingModel $login
     * @param UserLoginBindingModel $login
     * @throws \Exception
     */
    public function login(UserLoginBindingModel $login)
    {
        var_dump($login);
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
        $this->session->_login = $id;
        $this->session->_username = $login->getUsername();
        $this->session->escapedUsername = $username;
        $this->redirect('/');
    }

    /**
     * @POST
     */
    public function logout()
    {
        $this->session->destroySession();
        $this->redirect('/');
    }

    /**
     * @POST
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

        $this->db->prepare('INSERT INTO users(username, password, money) values(?, ?, ?)', array($username, $password, $this->config->cart['initialCash']));
        $this->db->execute();

        $loginModel = new UserLoginBindingModel();
        $loginModel->setUsername($username);
        $loginModel->setAfterRegisterPass($password);

        $this->login($loginModel);
    }
}