<div id="header">
    <header>
        <nav class="navbar navbar-default">
            <ul class="nav navbar-nav">
                <li><a href="/">Home</a></li>
                <?php if(\EndF\Application::getInstance()->getHttpContext()->getSession()->hasSessionKey('token')) :?>
                    <li><a href="/users/profile">Profile</a></li>
                <?php endif; ?>
                <li role="separator" class="divider"></li>
            </ul>
        </nav>
        <div class="row">
            <h1>Welcome to Hell's Conferences!</h1>
        </div>
    </header>

</div>