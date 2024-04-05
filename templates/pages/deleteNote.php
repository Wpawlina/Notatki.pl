<div class="show">   
    <?php $note=$params['note'];?>
    <?php if($note):?>
    <ul>
        
        <li>Tytuł:&nbsp;<?=$note['title'] ?></li>
        <li><?=$note['description'] ?></li>
        <li>Zapisano:&nbsp;<?=$note['created']?></li>
    </ul>
    <form method="POST" action="index.php?action=deleteNote">
        <input name="id" type="hidden" value="<?=$note['note_id'] ?>"/>
        <button type="submit">Usuń</button>
    </form>
     <?php else:?>
        <div>
             Brak notatki do wyświetlenia
        </div>
    <?php endif;?>
    <a href="index.php"><button>Powrót to listy notatek</button></a>
</div>