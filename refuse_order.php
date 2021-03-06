<?php
    session_start();
?>    

<!-- Specialni block na chyby a zpravy -->
<div class="block_for_messages">
    <?php
      
        // pokud jsou nejake chyby - vypise se jake
        if(isset($_SESSION["error_messages"]) && !empty($_SESSION["error_messages"])){
            echo $_SESSION["error_messages"];

            // Smazani chyb pri aktualizace stranky
            unset($_SESSION["error_messages"]);
        }
        // pokud jsou nejake zpravy - vypise se jake
        if(isset($_SESSION["success_messages"]) && !empty($_SESSION["success_messages"])){
            echo $_SESSION["success_messages"];

            // Smazani zprav pri aktualizace stranky
            unset($_SESSION["success_messages"]);
        }
    ?>
</div>

<?php
    require_once("dbconnect.php");

    // Deklarujeme promennou na zpravy chyb
    $_SESSION["error_messages"] = '';

    // Deklarujeme promennou na zpravy
    $_SESSION["success_messages"] = '';
    
    if(isset($_POST["btn_submit_refuse_order"]))//tlacitko odmitnout
    {
         if(isset($_POST["refuse"])){

                // Pokud nekdo zadal mezery na zacatku a konce - smazeme
                $refuse = trim($_POST["refuse"]);

                // Test zda nemame prazdne pole
                if(!empty($refuse)){
                    // Pro bezpecnost prevadive do html formatu
                    $refuse = htmlspecialchars($refuse, ENT_QUOTES);
                }else{
                    // Pokud se nastala chyba - ukladame to do promenne
                    $_SESSION["error_messages"] .= "<p class='mesage_error'>Nastala chyba při odmítnutí objednávky.</p>";

                    // Vraceme uzivateli na hlavni stranku
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ".$address_site."/take_order.php");

                    exit();
                }
            }else{
                // Pokud se nastala chyba - ukladame to do promenne
                $_SESSION["error_messages"] .= "<p class='mesage_error'>Nastala chyba při odmítnutí objednávky.</p>";

                // Vraceme uzivateli na hlavni stranku
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."/take_order.php");

                exit();
            }
            //zmena na odmitnuto v databazi
        $refuse_update = $mysqli->query("UPDATE `order` SET accept=2 WHERE id = '".$refuse."'");

        if(!$refuse_update){
            // Pokud se nastala chyba - ukladame to do promenne
            $_SESSION["error_messages"] .= "<p class='mesage_error' >Nastala chyba při odmítnutí objednávky.</p>";

            // Vraceme uzivateli na hlavni stranku
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."/take_order.php");

            exit();
        }else{

            $_SESSION["success_messages"] = "<p class='success_message'>Odmítnutí objednávky proběhlo úspěšně.</p>";

            // Vraceme uzivateli na hlavni stranku
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."/take_order.php");
        }
         $refuse_update->close();

            // Zavirame database
            $mysqli->close();
    }
    else{
        exit("<p><strong>Error!</strong> Nacházíte se na špatné stránce. Vráťe se na <a href=".$address_site.">hlavní stránku</a>.</p>");
    }


?>