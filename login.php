<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FO | Login</title>
    <link rel="stylesheet" href="src/css/login.css">
</head>

<body>

    <div class="ccenter">
        <h1>Sélectionner un compte</h1>

        <div class="ccenter__inside">
            <div class="box" style="background-image:url();">
                <div class="title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-user-icon lucide-shield-user">
                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                        <path d="M6.376 18.91a6 6 0 0 1 11.249.003" />
                        <circle cx="12" cy="11" r="4" />
                    </svg>
                    <h2>Super Admin</h2>
                </div>
                <form action="action/login_form.php" method="post">
                    <input type="hidden" name="account_type" value="sa">
                    <input type="password" name="password" id="" required maxlength="15">
                    <button type="submit">Connexion</button>
                </form>
                <p class="desc">Modération du site, gestion des repas, prix, ect...</p>
            </div>
            <div class="box">
                <div class="title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chef-hat-icon lucide-chef-hat">
                        <path d="M17 21a1 1 0 0 0 1-1v-5.35c0-.457.316-.844.727-1.041a4 4 0 0 0-2.134-7.589 5 5 0 0 0-9.186 0 4 4 0 0 0-2.134 7.588c.411.198.727.585.727 1.041V20a1 1 0 0 0 1 1Z" />
                        <path d="M6 17h12" />
                    </svg>
                    <h2>Cuisinier</h2>
                </div>
                <form action="action/login_form.php" method="post">
                    <input type="hidden" name="account_type" value="cu">
                    <input type="password" name="password" id="" required maxlength="15">
                    <button type="submit">Connexion</button>
                </form>
                <p class="desc">Prépare les commandes, indique le status d'un repas ect...</p>
            </div>
            <div class="box">
                <div class="title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-receipt-euro-icon lucide-receipt-euro">
                        <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z" />
                        <path d="M8 12h5" />
                        <path d="M16 9.5a4 4 0 1 0 0 5.2" />
                    </svg>
                    <h2>Caissier</h2>
                </div>
                <form action="action/login_form.php" method="post">
                    <input type="hidden" name="account_type" value="ca">
                    <input type="password" name="password" id="" required maxlength="15">
                    <button type="submit">Connexion</button>
                </form>
                <p class="desc">Ajoute des commandes, indique si donner ou pas, ect.</p>
            </div>
        </div>
    </div>

</body>

</html>