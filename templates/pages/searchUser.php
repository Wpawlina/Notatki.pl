<div>
    <h3>Zaloguj się</h3>
    <div class="user">
    <div class="message">
    <?php
     #wyswietlanie wiadomosci i błedów uzytkownikowi
        if(!empty($params['error'])) 
        {
            switch($params['error'])
            {
                case 'NotFound':
                echo "Nie znaleziono użytkownika";
                break;
                case 'WrongCredentials':
                echo "Błędne dane logowania";
                break;
                case 'NotActivated':
                echo "Najpierw aktywuj konto";
                break;
                case 'missingUser':
                    echo "Najpierw musisz się zalogowąć";
                break;
                
            }
        }
        if(!empty($params['before'])) 
        {
            switch($params['before'])
            {
                case 'userCreated':
                    echo "Zarejestrowano użytkownika";
                break;
                case 'loggedout':
                    echo 'Wylogowano użytkownika';
                break;
                case 'deletedUser':
                    echo 'Usunięto użytkownika';
                break;
                case 'emailSent':
                    echo 'Wysłano link do zmiany hasła na emaila przypisanego do konta';
                break;
                case 'ChangedPassword':
                    echo 'Hasło zostało zmienione';
                 break;
               
            }
        }
    ?>
    </div>
            <form method="POST" action="index.php?action=searchUser">
                <ul>
                        <li>
                        <label>Email</label>
                        <input type="text" name="login" required></label>
                        </li>
                        <li>
                            <label>Hasło</label>
                            <input type="password" name="password" required>
                            
                        </li>
                        <li>
                            <input type="submit" value="Wyślij" />
                        </li>
                    </ul>
            </form>
            Zapomniałeś hasła? | <a href="index.php?action=chgPasswd">Zresetuj teraz!</a></br>
            Nie masz jeszcze konta? | <a href="index.php?action=createUser">Załóż teraz!</a>
    </div>
</div>