<div>
    <?php
   




    ?>
     <h3>Podaj nowe hasło</h3>
        <?php
       if($params['before']==='WebsiteLogged')
       {
        ?>
            <form method="POST" action="index.php?action=chgPasswd">
                <ul>  
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
                        <input type="submit" value="Wyślij" />
                    </li>
                </ul>
            </form>
        



        <?php
       }
       else if($params['before']==='WebsiteNotLogged')
        {
            
            ?>
           
                <form method="POST" action="index.php?action=chgPasswd">
                    <ul>  
                        <li>
                            <label>Email <span class="required">*</span></label>
                            <input type="text" name="login" value="<?php if(isset($params['before']['login'])){echo  $params['before']['login']; } ?>" required></label>
                            <div class="errorMsg"><?php 
                            if(isset($params['error']['login']))
                            {   
                                    echo 'Niepoprawny adres email'; 
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
            
        
        <?php
       }else
       {?>
        <form method="POST" action="index.php?action=chgPasswd&chgCode=<?=$params['code'] ?>">
                <ul>  
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
                        <input type="submit" value="Wyślij" />
                    </li>
                </ul>
            </form>




       <?php
       }
       ?>  
    
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
