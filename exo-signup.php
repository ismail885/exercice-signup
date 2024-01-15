<?php 

    // PAge de Signup

    // On vérifie que le form a été soumis dans un premier temps
    if (isset($_POST['submit'])) {

        // On vérifie que les champs soient bien remplis
        if (!empty($_POST['email']) && !empty($_POST['pseudo']) && !empty($_POST['password']) && !empty($_POST['password-confirm'])) {

            $email = $_POST['email'];
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $password = $_POST['password'];
            $confirm = $_POST['password-confirm'];

            $regex = '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$ %^&*-]).{8,}$/';

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "L'email n'est pas au bon format";
            } elseif ($password != $confirm) {
                $error = "Les mots de passe doivent etre identiques";
            } elseif (!preg_match($regex, $password)) {
                $error = "Le mot de passe doit contenir au moins 12 caractères, minuscule, majuscule, chiffre et caractère spécial";
            } 

            // Hasher le mot de passe (avat de l'enregistrer en BDD) avec password_hash

            $hash = password_hash($password, PASSWORD_DEFAULT);

            // 1) Connexion à la BDD 

            $connexion = new mysqli("localhost", "root", "", "information");

            // 2) Vérifier que personne n'a le meme email en BDD
            

            $email = $_POST['email'];
            $checkemail_query = "SELECT * FROM users WHERE Email = '$email'";
            $result = $connexion->query($checkemail_query);

            if ($result->num_rows > 0) {
              echo "Cet email est déjà enregistré.";
            } else {
              // 3) Insérer le nouvel utilisateur en BDD
              $email = $_POST['email'];
              $pseudo = $_POST['pseudo'];
              $password = $hash;

              $insertquery = "INSERT INTO users (Pseudo, Email, mdp) VALUES ('$pseudo', '$email', '$password')";
              if ($connexion->query($insertquery) === TRUE) {
                $inserted_id = $connexion->insert_id;
              } else {
                echo "Erreur lors de l'insertion en base de données";
              }
            }
            // 4) Le rediriger vers la page de login avec header(Location: ...) 
            header("Location: login_formulaire.php");
                exit();

            // 5) N'oubliez pas dr créer la table users en BDD 

        } else {
            $error = "Veuillez remplir tous les champs !";
        }
        $connexion->close();
    }
?> 


    <!-- Formulaire de signup ici  -->
    <form class="space-y-6" method="POST">

      <!-- Label et input email       -->
      <div>
        <label for="email">Email address</label><br>
          <input id="email" name="email" type="text" autocomplete="email">
      </div>

      <!-- Label et input pour le pseudo -->
      <div>
        <label for="pseudo">Pseudo</label><br>
          <input id="pseudo" name="pseudo" type="text" autocomplete="pseudo" >
      </div>

        <!-- Mot de passe et confirmation -->
        <div>
          <label for="password" >Password</label><br>
        </div>

        <div class="mt-2">
          <input id="password" name="password" type="password" autocomplete="current-password" >
        </div>

        <label for="password-confirm">Password Confirmation</label><br>
        <div>
          <input id="password-confirm" name="password-confirm" type="password" autocomplete="current-password" >
        </div>
      </div>

      <?php if (isset($error)) : ?>

        <p><?= $error ?></p>

      <?php endif ?>

      <!-- Bouton de soumission -->
      <div>
        <input type="submit" name="submit" value="Signup">
      </div>

    </form>


  </div>
</div>