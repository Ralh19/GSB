<?php

/**
 * Vue Connexion
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 * @link      https://getbootstrap.com/docs/3.3/ Documentation Bootstrap v3
 */
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Function</title>
        <link rel="icon" type="image/x-icon" href="favicon.png">
        <style>
            html {
                width: 100%;
                min-width: 18.75rem;
                height: 100%;
                background-color: #121212;
                margin: 0;
                padding: 0;
            }
            body {
                display: grid;
                width: 100%;
                min-width: 18.75rem;
                min-height: calc(100vh);
                grid-template-columns: 100%;
                align-items: center;
                justify-self: center;
                background-color: #222222;
                padding: 0;
                margin: auto;
            }
            .content {
                grid-column: 1 / 2;
                text-align: center;
                padding: 0 1.5rem;
                margin: auto;
            }
            .pdfData {
                text-align: right;
            }
            h1 {
                display: block;
                font-family: 'Times New Roman', Times, serif;
                font-size: 2.5rem;
                font-style: normal;
                font-weight: 900;
                color: #FFFFFF;
                margin: auto;
            }
            hr {
                margin-bottom: 2rem;
            }
            .content label {
                font-family: 'Times New Roman', Times, serif;
                font-size: 1.25rem;
                font-style: normal;
                font-weight: 700;
                color: #FFFFFF;
                padding-right: 0.25rem;
            }
            .content input {
                font-family: 'Times New Roman', Times, serif;
                font-size: 1.25rem;
                font-style: normal;
                font-weight: 700;
                color: #FFFFFF;
                background-color: #222222;
                padding: 0.5rem;
                margin-bottom: 2rem;
                border: 0.25rem solid #969696;
                border-radius: 0.5rem;
            }
            button {
                display: block;
                font-size: 1.5rem;
                color: #FFFFFF;
                background-color: darkgreen;
                padding: 0.375rem 1.25rem;
                margin: auto;
                border: 0.125rem solid #e2e2e2;
                border-radius: 1rem;
                cursor: pointer;
            }
            button:hover {
                background-color: green;
            }
            @media(max-width: 28.75rem) {
                p {
                    margin-right: 0;
                }
                button {
                    display: block;
                    margin: auto;
                    margin-top: 1rem;
                }
            }
        </style>
    </head>
    <body>
        <form class="content" action="create.php" method="post" target="_blank">
            <h1>FPDF Tutorial</h1>
            <hr>
            <div class="pdfData">
                <label for="pdfName">Name:</label>
                <input type="text" name="pdfName" value="Fred">
                <br>
                <label for="pdfAddress">Address:</label>
                <input type="text" name="pdfAddress" value="1001 Sky Ave">
                <br>
                <label for="pdfAddress2">Address2:</label>
                <input type="text" name="pdfAddress2" value="Apt G">
                <br>
                <label for="pdfCity">City:</label>
                <input type="text" name="pdfCity" value="Chicago">
                <br>
                <label for="pdfState">State:</label>
                <input type="text" name="pdfState" value="Illinois">
                <br>
                <label for="pdfZip">Zip:</label>
                <input type="text" name="pdfZip" value="12345">
                <br>
                <label for="pdfCountry">Country:</label>
                <input type="text" name="pdfCountry" value="United States">
                <br>
                <label for="pdfPhone">Phone:</label>
                <input type="phone" name="pdfPhone" value="123-456-7890">
                <br>
                <label for="pdfEmail">Email:</label>
                <input type="email" name="pdfEmail" value="fred@my-mail.com">
            </div>
            <button type="submit">Submit</button>
        </form>
    </body>
</html>




<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Identification utilisateur</h3>
            </div>
            <div class="panel-body">
                <form role="form" method="post" 
                      action="index.php?uc=connexion&action=valideConnexion">
                    <fieldset>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-user"></i>
                                </span>
                                <input class="form-control" placeholder="Login"
                                       name="login" type="text" maxlength="45">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-lock"></i>
                                </span>
                                <input class="form-control"
                                       placeholder="Mot de passe" name="mdp"
                                       type="password" maxlength="45">
                            </div>
                        </div>
                        <input class="btn btn-lg btn-success btn-block"
                               type="submit" value="Se connecter">
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
