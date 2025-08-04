<div class="overlay"
    _="on click remove .phoneActive from .navigation then remove .phoneActive from .overlay then remove .phoneActive from #buttonNavigation then remove .commandsActive from #buttonCommands then remove .commandsActive from .command">
</div>

<?php

// file name
$filename = basename($_SERVER['PHP_SELF']);

?>
<nav class="navigation">
    <div class="logo navBtnAct">
        <a <?= $filename == "index.php" ? "class='active'" : "" ?> href="/">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-blocks-icon lucide-blocks">
                <path
                    d="M10 22V7a1 1 0 0 0-1-1H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5a1 1 0 0 0-1-1H2" />
                <rect x="14" y="2" width="8" height="8" rx="1" />
            </svg>
        </a>
    </div>

    <div class="navBtn navBtnAct">
        <a <?= $filename == "index.php" ? "class='active'" : "" ?> href="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-house-icon lucide-house">
                <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" />
                <path
                    d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
            </svg>
        </a>
        <?php if ($_SESSION['account_type'] == "ca") { ?>
            <a <?= $filename == "caisse.php" ? "class='active'" : "" ?> href="/caisse">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-layout-grid-icon lucide-layout-grid">
                <rect width="7" height="7" x="3" y="3" rx="1" />
                <rect width="7" height="7" x="14" y="3" rx="1" />
                <rect width="7" height="7" x="14" y="14" rx="1" />
                <rect width="7" height="7" x="3" y="14" rx="1" />
            </svg>
        </a>
        <?php } ?>
        <?php if ($_SESSION['account_type'] == "cu") { ?>
            <a <?= $filename == "cuisine.php" ? "class='active'" : "" ?> href="/cuisine">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-chef-hat-icon lucide-chef-hat">
                    <path
                        d="M17 21a1 1 0 0 0 1-1v-5.35c0-.457.316-.844.727-1.041a4 4 0 0 0-2.134-7.589 5 5 0 0 0-9.186 0 4 4 0 0 0-2.134 7.588c.411.198.727.585.727 1.041V20a1 1 0 0 0 1 1Z" />
                    <path d="M6 17h12" />
                </svg>
            </a>
        <?php } ?>
        <a <?= $filename == "commandes.php" ? "class='active'" : "" ?> href="/commandes">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-layout-list-icon lucide-layout-list">
                <rect width="7" height="7" x="3" y="3" rx="1" />
                <rect width="7" height="7" x="3" y="14" rx="1" />
                <path d="M14 4h7" />
                <path d="M14 9h7" />
                <path d="M14 15h7" />
                <path d="M14 20h7" />
            </svg>
        </a>
        <!-- <a <?= $filename == "package.php" ? "class='active'" : "" ?> href="/package">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-package-icon lucide-package">
                <path
                    d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z" />
                <path d="M12 22V12" />
                <polyline points="3.29 7 12 12 20.71 7" />
                <path d="m7.5 4.27 9 5.15" />
            </svg>
        </a> -->
        <?php if ($_SESSION['account_type'] == "sa") { ?>
            <a <?= $filename == "super_admin.php" ? "class='active'" : "" ?> href="/super_admin">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-shield-user-icon lucide-shield-user">
                    <path
                        d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                    <path d="M6.376 18.91a6 6 0 0 1 11.249.003" />
                    <circle cx="12" cy="11" r="4" />
                </svg>
            </a>
        <?php } ?>
    </div>

    <div class="bottom navBtnAct">
        <a href="/logout">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-log-out-icon lucide-log-out">
                <path d="m16 17 5-5-5-5" />
                <path d="M21 12H9" />
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            </svg>
        </a>
    </div>
</nav>