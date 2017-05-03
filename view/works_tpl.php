<h4>Задания / работа</h4>


<ul id="worksnames" class="nav nav-tabs">
  <?= $names ?>
</ul>

<div id="work" class="tab-content">
     
  <?= $tabs ?>
 
</div>

<!-- Он-лайн проверка ссылок Dr.Web -->
<script language="JavaScript">
  function resultURL() 
  {
    var left = (screen.width - 640)/2;
    var top =  (screen.height - 400)/2;
    window.open( "", "scan", "width=640"+
                 ",height=400,left="+left+
                 ",top="+top+",scrollbars=no,resizable=yes");
    document.getElementById( "drwebscanformURL" ).target = "scan";
    return true;
  } 
</script>
<form id="drwebscanformURL" action="http://online.us.drweb.com/result/" onSubmit="return resultURL()" method="post">
<div style="width: 225px; height: 105px; background: url(/images/drweb/fon_white.gif)">
	<div style="padding: 50px 10px 0 10px;">
		<div ><input type="text" name="url" value="http://" class=find style="width:100%; border: #9ac461 2px solid;"></div>
		<div style="padding-top: 5px; text-align: right"><input type="image" src="/images/drweb/button.gif" width="121" height="21"></div>
	</div>
</div>
</form>