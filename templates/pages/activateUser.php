<div>
    <?php
    $result=$params['result'];




    ?>
    <div class="user">
       <?php
       if($result==='Positive')
       {
        echo "<h2>Aktywowano konto</h2>";
       }
       else
       {
        echo "<h2>Aktywowacja nie powiodła się upewnij sie żę otrzymałeś poprawny link</h2>";
       }

       ?>   
    </div>
</div>