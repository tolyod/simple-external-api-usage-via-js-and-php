<?php
?>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery-3.5.1.slim.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/tablesort.min.js"></script>
    <script src='js/sorts/tablesort.number.min.js'></script>
    <script src='js/sorts/tablesort.date.min.js'></script>
    <link href='css/tablesort.css' rel='stylesheet'>
    <style type="text/css">
    td.small { font-size: 60%; }
    img.thumb { width: 64px; height: 36px; }
    td.group_name { font-size:14px; word-break: break-word; }
    td.movie_name { word-break: break-word;}
    td.movie_url { font-size:14px; }
    .first-header {
        color: #fff;
        background-color: #337ab7;
        margin-top: 10px;
        border-radius: 4px;
     }
    .copy-btn {
         position: absolute;
         right: 10px;
         top: -30px;
         width: 38px;
        font-size: 10px;
    }
    .copy-btn:focus {
      background-color:grey;
    }
    </style>
  </head>
  <body>
    <div class="container-fluid">
      <center>
        <h1 class="first-header">OK views counter</h1>
      </center>
      <div class="row">
        <div class="col-xs-6 col-md-2">
          <form name="test" method="post" action="#">
            <p>
                <textarea
                    style="width:-webkit-fill-available;"
                    name="comment"
                    cols="40"
                    rows="30"
                    id="listofmovies"></textarea>
            </p>
            <p>
                <input
                    id="btn-process"
                    type="button"
                    value="Отправить" />
                <input
                    id="btn-reload"
                    type="reset"
                    value="Очистить">
            </p>
          </form>
        </div>
        <div class="col-xs-6 col-md-6">
        <div class="table-responsive">
        <div>
        <button class="copy-btn" id="btn-copy">Copy</button>
        </div>
          <table id="movies-table-id" class="table">
            <thead>
              <tr>
                <th style="min-width: 70px;">Date</th>
                <th data-sort-method='none' class="no-sort">Group</th>
                <th data-sort-method='none' class="no-sort">Thumb</th>
                <th data-sort-method='none' class="no-sort">Name</th>
                <th data-sort-method='none' class="no-sort">Url</th>
                <th style="min-width: 115px;">Comments</th>
                <th style="min-width: 90px;">Likes</th>
                <th style="min-width: 90px;">Views</th>
              </tr>
            </thead>
            <tbody id="tbodyviews">
            </tbody>
          </table>
          </div>
        </div>
        <div class="col-xs-6 col-md-4">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>name total</th>
                <th>Comments</th>
                <th>Likes</th>
                <th>Views</th>
              </tr>
            </thead>
            <tbody id="t_body_views_summ">
            </tbody>
          </table>
          </div>
        </div>
      </div>
    </div>
    <script src="js/ok_views.js"></script>
  </body>
</html>
