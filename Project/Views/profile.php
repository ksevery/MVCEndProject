<?php
$userData = \EndF\Application::getInstance()->getHttpContext()->getUserData();
?>

<div class="row">
    <ul class="list-group">
        <li class="list-group-item lead"><span class="label label-primary pull-left">Username: </span><?= $userData->username ?></li>
        <li class="list-group-item lead"><span class="label label-primary pull-left">Your role: </span><?= $userData->role ?></li>
    </ul>
</div>
