<div>
    <?php
    $user=$params['user'];




    ?>
    <div class="user">
        <h3>Witaj <?=$user['first_name'].' '.$user['last_name'] ?></h3>
       <div class="user">login: <?=$user['email'] ?></div>
       <div class="user"> <a href="index.php?action=logout"><button>Wyloguj się</button></a> <a href="index.php?action=editUser"><button>Edytuj dane o sobie</button><a> <a href="index.php?action=chgPasswd"><button>Zmień hasło</button></a> <a href="index.php?action=deleteUser"><button>Zamknij konto</button></a></div>


            
            
    </div>
</div>