<html>
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <?= $this->getLayoutData('meta') ?>
</head>
<body>
<div id="wrapper">
    <?= $this->getLayoutData('header'); ?>
    <div class="container siteContainer">
        <?= $this->getLayoutData('body'); ?>
    </div>
    <?= $this->getLayoutData('footer'); ?>
    <?php
    ?>
</div>
</body>
</html>