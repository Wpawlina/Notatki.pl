<div>
    <h3>Edycja danych</h3>
    <div class="user">
        <?php
        $user=$params['user'];
        ?>
    <form method="POST" action="index.php?action=editUser">
        <ul>
            <li>
                <label>Imie</label>
                <input type="text" name="firstName" value="<?php echo $user['first_name'] ?>">
            </li>
            <li>
                <label>Nazwisko</label>
                <input type="text" name="lastName" value="<?php echo $user['last_name'] ?>">
            </li>
            <li>
                <input type="submit" value="Wyślij"/>
            </li>
        </ul>
    </div>



</div>