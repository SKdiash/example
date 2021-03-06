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
    
    if(isset($_POST["btn_delete_user_admin"]))//zmacknuto tlacitko smazat uzivatele
    {
        if(isset($_POST["usr"])){

            // Pokud nekdo zadal mezery na zacatku a konce - smazeme
            $usr = trim($_POST["usr"]);

            // Test zda nemame prazdne pole
            if(!empty($usr)){
                // Pro bezpecnost prevadive do html formatu
                $usr = htmlspecialchars($usr, ENT_QUOTES);
            }else{
                // Pokud se nastala chyba - ukladame to do promenne
                $_SESSION["error_messages"] .= "<p class='mesage_error'>Nastala chyba při mazání účastníka.</p>";

                // Vraceme uzivateli na hlavni stranku
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."/administration.php");

                exit();
            }

        }else{
            // Pokud se nastala chyba - ukladame to do promenne
            $_SESSION["error_messages"] .= "<p class='mesage_error'>Nastala chyba při mazání účastníka.</p>";

            // Vraceme uzivateli na hlavni stranku
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."/administration.php");

            exit();
        }
        //vyper uzivatele pro smazani
        $select =  $mysqli->query("SELECT * FROM users WHERE email = '".$usr."'");
        $row_mem = $select->fetch_assoc();

        if($row_mem['firm'] == 0){//jednotlivec
            //kurzy ve kterych je registrovan
            $select_course = $mysqli->query("SELECT id_l_course FROM member_of_course WHERE id_member = '".$row_mem['id']."'");

           for($i = 1; $i <= ($select_course->num_rows); $i++){
                $row_course= $select_course->fetch_assoc(); //snizit pocet ucastniku v konkretnim kurzu
                $log_course = $mysqli->query("UPDATE `listed_course` SET number_logged=number_logged-1 WHERE id = '".$row_course['id_l_course']."'");
            }
        
            $delete_mem = $mysqli->query("DELETE FROM member_of_course WHERE id_member = '".$row_mem['id']."'");//smazat z tabulky ucastniku u konk. kurzu
        }
        if($row_mem['firm'] == 1) {//firma
             //smazat objednavky
            $delete_order = $mysqli->query("DELETE FROM `order` WHERE id_firm = '".$row_mem['id']."'");
        }
        //smazani uzivatele
        $delete = $mysqli->query("DELETE FROM `users` WHERE email = '".$usr."'");
        if(!$delete){
            // Pokud se nastala chyba - ukladame to do promenne
            $_SESSION["error_messages"] .= "<p class='mesage_error' >Nastala chyba při mazání účastníka.</p>";

            // Vraceme uzivateli na hlavni stranku
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."/administration.php");

            exit();
        }else{

            $_SESSION["success_messages"] = "<p class='success_message'>Mazání účastníka proběhlo úspěšně.</p>";

            // Vraceme uzivateli na hlavni stranku
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."/administration.php");
        }
         $delete->close();

            // Zavirame database
            $mysqli->close();
    }
    else{
        exit("<p><strong>Error!</strong> Nacházíte se na špatné stránce. Vráťe se na <a href=".$address_site.">hlavní stránku</a>.</p>");
    }
?>