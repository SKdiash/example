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
    
    if(isset($_POST["btn_submit_logout_course"]))
    {
        if(isset($_POST["course_log"])){//id konk. kurzu

            // Pokud nekdo zadal mezery na zacatku a konce - smazeme
            $log = trim($_POST["course_log"]);

            // Test zda nemame prazdne pole
            if(!empty($log)){
                // Pro bezpecnost prevadive do html formatu
                $log = htmlspecialchars($log, ENT_QUOTES);
            }else{
                // Pokud se nastala chyba - ukladame to do promenne
                $_SESSION["error_messages"] .= "<p class='mesage_error'>Nezadán kurz, ze kterého se chcete odhlásit. Zkuste to ještě jednou, prosím.</p>";

                // Vraceme uzivateli na hlavni stranku
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."/course_on.php");

                exit();
            }

        }else{
            // Pokud se nastala chyba - ukladame to do promenne
            $_SESSION["error_messages"] .= "<p class='mesage_error'>Nezadán kurz.</p>";

            // Vraceme uzivateli na hlavni stranku
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."/course_on.php");

            exit();
        }

        if(isset($_POST["member_log"])){//clen u kurzu

            // Pokud nekdo zadal mezery na zacatku a konce - smazeme
            $member = trim($_POST["member_log"]);

            // Test zda nemame prazdne pole
            if(!empty($member)){
                // Pro bezpecnost prevadive do html formatu
                $member = htmlspecialchars($member, ENT_QUOTES);
            }else{
                // Pokud se nastala chyba - ukladame to do promenne
                $_SESSION["error_messages"] .= "<p class='mesage_error'>Váš id není zadán. Zkuste to ještě jednou, prosím.</p>";

                // Vraceme uzivateli na hlavni stranku
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."/course_on.php");

                exit();
            }

        }else{
            // Pokud se nastala chyba - ukladame to do promenne
            $_SESSION["error_messages"] .= "<p class='mesage_error'>Váš id není zadán.</p>";

            // Vraceme uzivateli na hlavni stranku
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."/course_on.php");

            exit();
        }

        //smazani uzivatele z konkretniho kurzu
        $delete_mem = $mysqli->query("DELETE FROM member_of_course WHERE id_l_course ='".$log."' AND id_member = '".$member."'");

        if(!$delete_mem){
            // Pokud se nastala chyba - ukladame to do promenne
            $_SESSION["error_messages"] .= "<p class='mesage_error' >Nastala chyba při odhlášení z kurzu. Zkuste to ještě jednou, prosím.</p>";

            // Vraceme uzivateli na hlavni stranku
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."/course_on.php");

            exit();
        }else{

            $_SESSION["success_messages"] = "<p class='success_message'>logout complete!!!</p>";
            $log_course = $mysqli->query("UPDATE `listed_course` SET number_logged=number_logged-1 WHERE id = '".$log."'");
            if(!$log_course){
            // Pokud se nastala chyba - ukladame to do promenne
                $_SESSION["error_messages"] .= "<p class='mesage_error' >Nastala chyba při aktualizaci kurzu.</p>";

                // Vraceme uzivateli na hlavni stranku
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."/course_on.php");
                exit();
            }else{

                $_SESSION["success_messages"] = "<p class='success_message'>Aktualizace proběhla úspěšně.</p>";
                // Vraceme uzivateli na hlavni stranku
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."/course_on.php");
            }
            // Vraceme uzivateli na hlavni stranku
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."/course_on.php");
        }
        $delete_mem->close();
        $log_course->close();
        // Zavirame database
        $mysqli->close();
    }
    else{
         exit("<p><strong>Error!</strong> Nacházíte se na špatné stránce. Vráťe se na <a href=".$address_site.">hlavní stránku</a>.</p>");
    }

?>