<?php
$pageContent = <<<EOF
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>      
    <script src="js/ok_views.js"></script>
  </head>
  <body>
    <div class="container-fluid">
      <center>
        <h1 class="bg-primary">OK views counter</h1>
      </center>
      <div class="row">
        <div class="col-md-4">
          <form name="test" method="post" action="#">
            <p><textarea name="comment" onchange="drawTable();" cols="60" rows="30" id="listofmovies"></textarea></p>
            <p><input type="button"  onclick="processTable();" value="Отправить">
               <input type="reset" onclick="location.reload();" value="Очистить"></p>
          </form>
        </div>
        <div class="col-md-4">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>date</th>
                <th>name</th>
                <th>url</th>
                <th>count</th>
              </tr>
            </thead>
            <tbody id="tbodyvievs">        
            </tbody>
          </table>
        </div>
        <div class="col-md-4">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>name total</th>
                <th>count total</th>
              </tr>
            </thead>
            <tbody id="t_body_vievs_summ">        
            </tbody>
          </table>
        </div>
      </div>
    </div> 
  </body>
</html>
EOF;

echo $pageContent;
?>
