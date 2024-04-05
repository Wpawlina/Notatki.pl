
<div>
    <h3>Zarejestruj się</h3>
    <div class="user">
    <div class="message">
    <?php
    
        if(!empty($params['error'])) 
        {
            switch($params['error']){
            case 'NotFound':
              echo "Błędne dane logowania";
            break;
            case 'missingUser':
                echo "Najpierw musisz się zalogowąć";
            break;
        }


        }
    ?>
    </div>
            <form method="POST" action="index.php?action=createUser">
                <ul>
                        <li>
                            <label>Email <span class="required">*</span></label>
                            <input type="text" name="login" value="<?php if(isset($params['before']['login'])){echo  $params['before']['login']; } ?>" required></label>
                            <div class="errorMsg"><?php 
                            if(isset($params['error']['login']))
                            {   
                                if($params['error']['login']===1)
                                {
                                    echo 'Niepoprawny adres email';
                                }
                                if($params['error']['login']===2)
                                {
                                    echo 'Konto o takim emailu już istnieje';
                                }
                               
                                
                            }
                            ?></div>
                        </li>
                        <li>
                            <label>Imie</label>
                            <input type="text" name="firstName" value="<?php if(isset($params['before']['firstName'])){echo  $params['before']['firstName']; } ?>"></label>
                            <div class="errorMsg"><?php 
                            if(isset($params['error']['firstName']))
                            {   
                                echo 'Imie może mieć maksymalnie 30 znaków';
                            }
                            ?></div>
                        </li>
                        <li>
                            <label>Nazwisko</label>
                            <input type="text" name="lastName" value="<?php if(isset($params['before']['lastName'])){echo  $params['before']['lastName']; } ?>" ></label>
                            <div class="errorMsg"><?php 
                            if(isset($params['error']['lastName']))
                            {   
                                echo 'Nazwisko może mieć maksymalnie 30 znaków';
                            }
                            ?></div>
                        </li>
                        <li>
                            <label>Hasło  <span class="required">*</span></label>
                            <input type="password" name="password" required>
                            <div class="errorMsg"><?php 
                            if(isset($params['error']['password']))
                            {   
                                echo 'Hasło musi mieć minimum 8 znaków w tym jedną dużą litere, jedną cyfrę oraz jeden znak specjalny';
                            }
                            ?></div>  
                        </li>
                        <li>
                            <label>Powtórz Hasło  <span class="required">*</span> </label>
                            <input type="password" name="password2" required>
                            <div class="errorMsg"><?php 
                            if(isset($params['error']['password2']))
                            {   
                                echo 'Hasła muszą być takie same';
                            }
                            ?></div>  
                         
                        </li>
                        <li>
                            <label> <input type="checkbox" name="terms" value="accept" <?php if(isset($params['before']['terms'])){echo  'checked'; } ?> > Akceptuje <a href="index.php?action=terms">regulamin</a>  <span class="required">*</span></label>
                            <div class="errorMsg"><?php 
                            if(isset($params['error']['terms']))
                            {   
                                echo 'Zaakceptuj regulamin';
                            }
                            ?></div> 
                        </li>
                        <li>
                        <input type="hidden" name="recaptcha-response" id="recaptcha-response">
                        <div class="errorMsg">
                            <?php
                            if(isset($params['error']['recaptcha']))
                            {   
                                echo 'Jesteś robotem!!!';
                            }
                            ?></div>
                        </li>
                        <li>
                            <input type="submit" value="Wyślij" />
                        </li>
                    </ul>
            </form>
            Masz już konto? | <a href="index.php?action=searchUser">Zaloguj się!</a>
    </div>
</div>

<script>

    var siteKey = '6LcQ9DwoAAAAABAyIdD8FRvKjlip5fRZMNQU01II';

    grecaptcha.ready(function() {
        grecaptcha.execute(siteKey, { action: 'submit_form' }).then(function(token) {
            var recaptchaResponse = document.getElementById('recaptcha-response');
            recaptchaResponse.value = token;
        });
    });
</script>