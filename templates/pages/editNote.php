<div>
            <h3>Edycja notatki</h3>
            
            <div>
                <?php if(!empty($params['note'])):?>
                <?php  $note=$params['note'];?>
                <form class="note-form" action="index.php?action=editNote" method="post">
                    <ul>
                        <li>
                            <input name="id" type="hidden"  value="<?= $note['note_id']?>"/>
                            <label>Tytuł <span class="required">*</span></label>
                            <input type="text" name="title" class="field-long " value="<?= $note['title']?>" />
                        </li>
                        <li>
                            <label>Treść</label>
                            <textarea name="description" id="field5" class="field-long field-textarea"><?= $note['description']?></textarea>
                        </li>
                        <li>
                            <input type="submit" value="Wyślij" />
                        </li>
                    </ul>
                </form>
                <?php else: ?>
                    <div>
                        Brak danych do wyświetlenia
                        <a href="index.php"><button>Powrót to listy notatek</button></a>
                    </div>
                <?php endif; ?>
            </div>

           
            


        </div>