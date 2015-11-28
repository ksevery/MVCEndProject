<div>
    <p>You are finally home. It is safe here.</p>
    <?php if(!\EndF\Application::getInstance()->getHttpContext()->getSession()->hasSessionKey('token')) :?>
    <div id="login" class="row">
        <?php
            \EndF\FormViewHelper::init()
                ->initForm('../users/login', ['class' => 'formGroup'], 'post')
                ->initLabel()->setValue("Username")->setAttribute('for', 'username')->create()
                ->initTextBox()->setName('username')->setAttribute('id', 'username')->setAttribute('class', 'form-control input-md')->create()
                ->initLabel()->setValue("Password")->setAttribute('for', 'password')->create()
                ->initPasswordBox()->setName('password')->setAttribute('id', 'password')->setAttribute('class', 'form-control input-md')->create()
                ->initSubmit()->setAttribute('value', 'Login')->setAttribute('class', 'btn btn-primary btn-lg col-sm-4 col-sm-offset-4')->create()
                ->render();
        ?>
    </div>
    <div class="row">
        <h1 class="text-center">Register</h1>
        <?php
        \EndF\FormViewHelper::init()
            ->initForm('../users/register', ['class' => 'form-group'], 'post')
            ->initLabel()->setValue("Username")->setAttribute('for', 'username')->create()
            ->initTextBox()->setName('username')->setAttribute('id', 'username')->setAttribute('class', 'form-control input-md')->create()
            ->initLabel()->setValue("Password")->setAttribute('for', 'password')->create()
            ->initPasswordBox()->setName('password')->setAttribute('id', 'password')->setAttribute('class', 'form-control input-md')->create()
            ->initLabel()->setValue("Confirm Password")->setAttribute('for', 'confPassword')->create()
            ->initPasswordBox()->setName('confirm')->setAttribute('id', 'confPassword')->setAttribute('class', 'form-control input-md')->create()
            ->initSubmit()->setAttribute('value', 'Register')->setAttribute('class', 'btn btn-primary btn-lg col-sm-4 col-sm-offset-4')->create()
            ->render(true);
        ?>
    </div>
    <?php else : ?>
    <div>
        <h1>Hello, <?= \EndF\Common::dump(\EndF\Application::getInstance()->getHttpContext()->getUserData()) ?></h1>
        <?php
            \EndF\FormViewHelper::init()
        ->initForm('../users/logout', ['class' => 'formGroup'], 'post')
        ->initSubmit()->setAttribute('value', 'Logout')->setAttribute('class', 'btn btn-primary btn-lg col-sm-4 col-sm-offset-4')->create()
        ->render();
        ?>
    </div>
    <?php endif; ?>
</div>