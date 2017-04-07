<h4>Биржа рефералов</h4>
   
<div class="ref-page">
    
    <p>Вы можете выставить своего реферала на биржу или приобрести нового.</p>
    
    <p>
       <a href="#myModal" id="refstock" class="btn btn-success" data-toggle="modal" role="button">Выставить на биржу</a>
    </p>
    
 <div class="row">
 
<div class="col-md-12">
 <table id="listrefstock" class="table table-hover ref_list">
     
        <?= $refstock ?>

 </table>
 </div>
  
 </div>
  
  
    

</div>


<div id="myModal" class="modal fade">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <!-- Заголовок модального окна -->
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Список ваших рефералов</h4>
      </div>
      <!-- Основное содержимое модального окна -->
      <div class="modal-body">

         <table class="table table-hover ref_list">
     
            <?= $refTable ?>

         </table>        
        
      </div>
      <!-- Футер модального окна -->
      <div class="modal-footer">
<!--        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>-->
        <button type="button" id="addrefstock" data-dismiss="modal" class="btn btn-primary">Выбрать</button>
      </div>
    </div>
  </div>
</div>