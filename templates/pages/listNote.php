         <div class="list">
            <section>
                  <div class="message">
                     <?php
                     #wyswietlanie wiadomosci i błedów uzytkownikowi
                     if(!empty($params['before']))
                        {
                           switch($params['before']){
                              case 'created':
                               echo 'Notatka została utworzona !!!';
                              break;
                              case 'edited':
                                 echo 'Notatka została zaktualizowana';
                              break;
                              case 'deleted':
                                 echo 'Notatka została usunieta';
                              break;
                              case 'loggedin':
                                 echo ' Zalogowano użytkownika ';
                              break;
                              case 'userEdited':
                                 echo 'Zmieniono dane użytkownika';
                              break;
                              case 'ChangedPassword':
                                 echo 'Hasło zostało zmienione';
                              break;
                           }

                        }
                        if(!empty($params['error'])) 
                        {
                           switch($params['error']){
                              case 'NotFound':
                              echo "Notatka nie została znaleziona";
                              break;
                              case 'missingNoteId':
                                 echo "Niepoprawne identyfikator notatki";
                              break;
                              case 'missingUser':
                                 echo "Najpierw musisz się zalogować";
                              break;
                           }


                        }
                     ?>
                  </div>
                  <?php 
                           
                           $sort=$params['sort'] ?? [];
                           $by=$sort['by'] ?? 'title';
                           $order=$sort['order'] ?? 'desc';
                           $page=$params['page']??[];
                           $size=$page['size']?? 10;
                           $currentPage=$page['number']?? 1;
                           $pages=$page['pages']??1;
                           $title = $params['where']['title']?? null;
                           $date = $params['where']['date']?? null;
                          
                        
                  ?>
                  <div>
                     <form class="settings-form" action="index.php" method="GET">
                     <div>
                           <label>Wyszukaj:<input type="text" name="title" value="<?= $title?>"/></label>
                        </div>
                        <div>
                           <label>Dzień: <input type="date" name="date" value="<?= $date?>"/></label>
                        </div>
                        
                        <div>Sortuj po:</div>
                        <div>
                           <label>Tytule: <input name="sortby" type="radio" value="title" <?php echo $by==='title'?  'checked' : '' ?>/> </label>
                           <label>Dacie: <input name="sortby" type="radio" value="created"  <?php echo $by==='created'?  'checked' : '' ?> /> </label>

                        </div>

                        <div>Kierunek sortowania:</div>
                        <div>
                           <label>Rosonąco:<input name="sortorder" type="radio" value="asc"  <?php echo $order==='asc'?  'checked' : '' ?>  /> </label>
                           <label>Malejąco: <input name="sortorder" type="radio" value="desc"   <?php echo $order==='desc'?  'checked' : '' ?> /> </label>
                        </div>
                        <div>
                           <div>Rozmiar paczki</div> 
                           <label>1 <input name="pageSize" type="radio"  value="1" <?php echo $size===1 ? 'checked': '' ?> /></label> 
                           <label>5 <input name="pageSize" type="radio"  value="5" <?php echo $size===5 ? 'checked': '' ?> /></label> 
                           <label>10 <input name="pageSize" type="radio"  value="10" <?php echo $size===10 ? 'checked': '' ?> /></label> 
                           <label>25 <input name="pageSize" type="radio"  value="25" <?php echo $size===25 ? 'checked': '' ?> /></label> 
                        </div>
                        <input  type="submit" value="Wyślij"/>


                     </form>
                  </div>
                  <div class="tbl-header">
                     <table cellpadding="0"   cellspacing="0" border="0">
                        <thead>
                           <tr>
                           
                              <th>Tytuł</th>
                              <th>Data</th>
                              <th>Opcje</th>
                           </tr>
                        </thead>
                     </table>
                  </div>
                  <div class="tbl-content">
                        <table cellpadding="0" cellspacing="0" border="0">
                           <tbody>
                              <?php foreach($params['notes']?? [] as $note):?>
                                 <tr>
                                    <td><?=$note['title'] ?></td>
                                    <td><?=$note['created'] ?></td>
                                    <td>
                                       <a href="index.php?action=showNote&id=<?=(int) $note['note_id']?>"><button>Szczegóły</button></a>
                                       <a href="index.php?action=deleteNote&id=<?=(int) $note['note_id']?>"><button>Usuń</button></a>
                                    </td>
                                 </tr>
                              <?php endforeach;?>
                           </tbody>
                           </table>
                  </div>
                  <?php if($pages!==0):?>
                  <?php $paginationUrl="&pageSize=$size&sortby=$by&sortorder=$order&title=$title&date=$date";?>
                  <ul class="pagination">
                     <?php if($currentPage!==1):?>
                     <li>
                           <a href="index.php?page=<?php echo $currentPage - 1 . $paginationUrl ?>">
                              <button ><<</button>
                           </a>
                     </li>
                     <?php endif?>
                     <?php for($i=1;$i<=$pages;$i++):?>
                        
                        <li>
                           <a href="index.php?page=<?= $i.$paginationUrl?>">
                              <button ><?=$i?></button>
                           </a>
                        </li>
                     <?php endfor;?>
                     <?php if($currentPage!==$pages):?>
                     <li>
                           <a href="index.php?page=<?= $currentPage+1 . $paginationUrl?>">
                              <button >>></button>
                           </a>
                     </li>
                     <?php endif?>
                     <?php endif?>
                  </ul>
            </section>
        </div>
