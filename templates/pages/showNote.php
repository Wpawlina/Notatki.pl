<div class="show">   
    <?php $note=$params['note'];?>
    <?php if($note):?>
    <ul>
        <li>Tytuł:&nbsp;<?=$note['title'] ?></li>
        <li><?=$note['description'] ?></li>
        <li>Zapisano:&nbsp;<?=$note['created']?></li>
    </ul>
    <a href="index.php?action=editNote&id=<?= $note['note_id']?>"><button>Edytuj notatkę</button></a>
    
     <?php else:?>
        <div>
             Brak notatki do wyświetlenia
        </div>
    <?php endif;?>
    <a href="index.php"><button>Powrót to listy notatek</button></a>
</div>