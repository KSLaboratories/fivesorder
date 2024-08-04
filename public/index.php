<?php

require_once('../config.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once '../inc/head.php' ?>
    <title><?= $kpf_config["seo"]["title_short"] ?></title>
    <link rel="stylesheet" href="src/css/style.css">

    <!-- src -->
    <link rel="stylesheet" href="./node_modules/boxicons/css/boxicons.min.css">
</head>

<body>

    <nav>
        <div class="logo">
            <img src="src/img/logo.png" alt="logo">
        </div>
        <hr>
        <ul>
            <li class="active"><i class='bx bx-book-alt'></i></li>
            <li><i class='bx bx-donate-heart'></i></li>
        </ul>
    </nav>

    <main>
        <header>
            <ul>
                <li>v<?= $kpf_config["framework"]["framework_version"] ?> <i class='bx bxl-github'></i></li>
            </ul>
        </header>

        <div class="container">
            <div class="container__title">
                <h1>Welcome to <span><?= $kpf_config['framework']['title'] ?></span> 👋</h1>
                <p>Follow the steps below to get you started.</p>
            </div>

            <div class="container__card">
                <div class="subtitle">
                    <h2>Lets get started !</h2>
                    <h2><span id="clickedCard">?</span>/<span id="totalCard">?</span></h2>
                </div>
                <div class="bar">
                    <div id="statusBar"></div>
                </div>

                <!-- cards -->
                <div class="objectif">

                    <div class="objectif__card" id="congratulations">
                        <div class="icon">
                            <i class='bx bx-happy-beaming'></i>
                        </div>
                        <div class="name">
                            <h3>Congratulations</h3>
                            <p>Congratulations, KerogsPHP framework has been installed.</p>
                        </div>
                    </div>

                    <div class="objectif__card" id="structure-file">
                        <div class="icon">
                            <i class='bx bx-sitemap'></i>
                        </div>
                        <div class="name">
                            <h3>structure of files</h3>
                            <p>Discover the framework's file tree.</p>
                        </div>
                    </div>

                    <div class="objectif__card" id="framework-basics">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <div class="name">
                            <h3>Framework basics</h3>
                            <p>Learn the basics of the framework and how to use it.</p>
                        </div>
                    </div>

                    <div class="objectif__card" id="npm-and-composer">
                        <div class="icon">
                        <i class='bx bxs-component' ></i>
                        </div>
                        <div class="name">
                            <h3>Using NPM and Composer.</h3>
                            <p>Learn how to add new NPM packages and Composer dependencies with this short tutorial.</p>
                        </div>
                    </div>

                    <div class="objectif__card" id="sass">
                        <div class="icon">
                            <i class='bx bxl-sass'></i>
                        </div>
                        <div class="name">
                            <h3>How to use SASS/SCSS</h3>
                            <p>Learn how to use SASS and SCSS with the framework </p>
                        </div>
                    </div>

                    <div class="objectif__card" id="typescript">
                        <div class="icon">
                            <i class='bx bxl-typescript'></i>
                        </div>
                        <div class="name">
                            <h3>How to use TypeScript</h3>
                            <p>Learn how to use TypeScript with the framework.</p>
                        </div>
                    </div>

                    <!-- cards end -->

                </div>

            </div>
        </div>

        <?php require_once 'src/html/popup.htm' ?>

    </main>

    <?php require_once '../inc/script.php' ?>
</body>

</html>