<?php
$urlScheme = 'http';
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
  $urlScheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
}
$urlPrefix = $urlScheme . "://" . $_SERVER['HTTP_HOST'] . "/";
if($_SERVER['HTTP_HOST'] !== 'ok-videostats.hopto.org') {
  header('Location: https://ok-videostats.hopto.org', 301);
  exit;
}
?>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="<?=$urlPrefix?>js/jquery-3.5.1.slim.min.js"></script>
    <script src="<?=$urlPrefix?>js/bootstrap.min.js"></script>
    <script src="<?=$urlPrefix?>js/tablesort.min.js"></script>
    <script src='<?=$urlPrefix?>js/sorts/tablesort.number.min.js'></script>
    <script src='<?=$urlPrefix?>js/sorts/tablesort.date.min.js'></script>
    <style type="text/css">
    <?=file_get_contents('css/bootstrap.min.css');?>
    <?=file_get_contents('css/tablesort.css');?>
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
      background-color: inherit;
      border: inherit;
      right: 10px;
      font-size: 24px;
    }
    .copy-btn:focus {
      background-color:grey;
      /* filter:invert(100%); */
    }
    .copy-btn-sum {
      position: absolute;
      right: 10px;
      font-size: 24px;
      background-color: inherit;
      border: inherit;
    }
    .copy-btn-sum:focus {
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
        <!--button class="copy-btn" id="btn-copy">📋</button-->
        <button class="copy-btn" id="btn-copy"><svg class="" viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M5.75 1a.75.75 0 00-.75.75v3c0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75v-3a.75.75 0 00-.75-.75h-4.5zm.75 3V2.5h3V4h-3zm-2.874-.467a.75.75 0 00-.752-1.298A1.75 1.75 0 002 3.75v9.5c0 .966.784 1.75 1.75 1.75h8.5A1.75 1.75 0 0014 13.25v-9.5a1.75 1.75 0 00-.874-1.515.75.75 0 10-.752 1.298.25.25 0 01.126.217v9.5a.25.25 0 01-.25.25h-8.5a.25.25 0 01-.25-.25v-9.5a.25.25 0 01.126-.217z"></path></svg></button>
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
                <th style="min-width: 90px;">Shares</th>
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
          <div>
            <!--button class="copy-btn-sum" id="btn-copy-sum">📋</button-->
            <button class="copy-btn-sum" id="btn-copy-sum">
              <svg class="" viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.75 1a.75.75 0 00-.75.75v3c0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75v-3a.75.75 0 00-.75-.75h-4.5zm.75 3V2.5h3V4h-3zm-2.874-.467a.75.75 0 00-.752-1.298A1.75 1.75 0 002 3.75v9.5c0 .966.784 1.75 1.75 1.75h8.5A1.75 1.75 0 0014 13.25v-9.5a1.75 1.75 0 00-.874-1.515.75.75 0 10-.752 1.298.25.25 0 01.126.217v9.5a.25.25 0 01-.25.25h-8.5a.25.25 0 01-.25-.25v-9.5a.25.25 0 01.126-.217z">
                </path>
              </svg>
            </button>
          </div>
         <table class="table table-hover" id="movies-summary">
            <thead>
              <tr>
                <th>name total</th>
                <th>Comments</th>
                <th>Shares</th>
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
    <script src="<?=$urlPrefix?>js/ok_views.js"></script>
  </body>
</html>
