<?php

$data = readRequestVar('DATA', []);
if (count($data) > 0) {
    $sql = "SELECT * 
            FROM utilisateur
            WHERE utilisateur.utilisateurLogin LIKE :utilisateurLogin";
    $res = DB::query($sql, [':utilisateurLogin' => $data['utilisateurLogin']], true);
    if (DB::num_rows($res) > 0) {
        $row = $res->fetchObject();

        if (password_verify($data['utilisateurPassword'], $row->utilisateurPassword)) {
            // Enregistrement en session
            foreach ($row as $key => $value) {
                $_SESSION[getSessionName()][$key] = $value;
            }
            $_SESSION[getSessionName()]['utilisateurLibelle'] = $_SESSION[getSessionName()]['utilisateurPrenom'].' '.$_SESSION[getSessionName()]['utilisateurNom'];

            redirect('index.php');
            exit();
        } else {
            redirect('index.php?errorLogin=2');
            exit();
        }
    }

    redirect('index.php?errorLogin=1');
    exit();
}

?>
<style>
    html, body {
        background-color: #ffffff;
    }
</style>
<div class="ml-5 mr-5 auth">
    <div class="text-center mt-5 mb-5">
        <img src="./img/logo.png" alt="Logo" width="220" class="mb-5">
    </div>

    <div class="mb-5">
        <h2>Connexion</h2>
    </div>

    <form name="authForm" id="authForm" action="index.php" method="post">
        <div class="form-group">
            <label for="utilisateurLogin">Identifiant</label>
            <input type="text" class="form-control" name="DATA[utilisateurLogin]" id="utilisateurLogin" placeholder="Votre identifiant">
        </div>
        <div class="form-group">
            <label for="utilisateurPassword">Mot de passe</label>
            <input type="password" class="form-control" name="DATA[utilisateurPassword]" id="utilisateurPassword" placeholder="Votre mot de passe">
        </div>
        <div class="mt-5">
            <button type="submit" class="btn btn-projet font-weight-bold w-100">
                Connexion
            </button>
        </div>
    </form>
</div>