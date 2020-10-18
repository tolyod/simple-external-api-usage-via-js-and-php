var getMvsIds = function () {
  return document
    .getElementById("listofmovies")
    .value.split(/\s|\n/g)
    .filter(e =>e.length >0)
    .map(url=>url.split(/\/video\/(\d*)/)[1]);
};

var updateTableElement = function (id, dataObj) {
  const { isDeleted, isPrivate, groupName, name, thumb, cntViews, cntLikes, cntComments, uploadDate } = dataObj;
  const trElem = document.getElementById('tr_'+id);
  var cntStyleColor = '#c3e6cb';
  // trElem.dataset.vars = JSON.stringify(dataObj);
  Object.entries(dataObj).map(([k, v]) => trElem.dataset[k] = v);
  if (isDeleted) {
    trElem.className = 'table-danger';
  }
  if (isPrivate) {
    trElem.className = 'table-secondary';
  }
  if (!isPrivate && !isDeleted) {
    trElem.className = 'table-success';
  }
  if (cntViews > 300 && cntViews < 1000) {
    cntStyleColor = '#ffeeba';
  }
  if (cntViews <= 300) {
    cntStyleColor = '#f5c6cb';
  }
  document.getElementById('tdname_'+id).innerHTML=name;
  document.getElementById('tdgrpname_'+id).innerHTML=groupName;
  document.getElementById('tdthumb_'+id).innerHTML=thumb;
  document.getElementById('tddate_'+id).innerHTML=uploadDate;
  document.getElementById('tdurl_'+id).innerHTML= `<a href="https://ok.ru/video/${id}" target="_blank" title="Video ${name}">${id}</a>`;
  document.getElementById('tdCountViews_'+id).innerHTML=cntViews;
  document.getElementById('tdCountViews_'+id).style.backgroundColor = cntStyleColor;
  document.getElementById('tdCountLikes_'+id).innerHTML=cntLikes;
  document.getElementById('tdCountComments_'+id).innerHTML=cntComments;
  accoumCounter(dataObj);
};

var countMovieTotals = function () {
  let count = getMvsIds().length;
  const totalObj = [];
  // const total = getMvsIds().length;

  return function (dataObj) {
    //console.log(dataObj);
    count--;
    try {
      const { name, cntViews, cntLikes, cntComments } = dataObj;
      const nameIndex = totalObj.findIndex( el => el.name === name );
      const curIndex = nameIndex >=0 ? nameIndex : totalObj.length;
      if (typeof totalObj[curIndex] === "undefined") {
        totalObj[curIndex]= {countViews:0, countComments:0, countLikes:0, name:""};
        totalObj[curIndex].countViews = cntViews;
        totalObj[curIndex].countComments = cntComments;
        totalObj[curIndex].countLikes = cntLikes;
        totalObj[curIndex].name = name;
      } else {
        totalObj[curIndex].countViews += cntViews;
        totalObj[curIndex].countComments += cntComments;
        totalObj[curIndex].countLikes += cntLikes;
      }
      if(count === 0) {
        drawTableTotal(totalObj);
      }
    } catch (e) {
      // console.log(e);
    }
  }
};

var drawTableTotal = function (arr) {
  var rootEl = document.getElementById('t_body_views_summ');
  var totalCountViews = arr.reduce( (acc, el) => acc+el.countViews ,0);
  var totalCountComments = arr.reduce( (acc, el) => acc+el.countComments ,0);
  var totalCountLikes = arr.reduce( (acc, el) => acc+el.countLikes ,0);
  var trSum = document.createElement('tr');
  trSum.id = "tr_tot_sum";

  var tdNameSum =  document.createElement('td');
  tdNameSum.id = "tdname_tot_sum";
  tdNameSum.innerHTML = "<b>Sum:</b>";

  var tdCountSumComments =  document.createElement('td');
  tdCountSumComments.id = "tdCount_tot_sum_comments";
  tdCountSumComments.innerHTML = "<b>"+totalCountComments+"</b>";

  var tdCountSumLikes =  document.createElement('td');
  tdCountSumLikes.id = "tdCount_tot_sum_likes";
  tdCountSumLikes.innerHTML = "<b>"+totalCountLikes+"</b>";

  var tdCountSumViews =  document.createElement('td');
  tdCountSumViews.id = "tdCount_tot_sum_likes";
  tdCountSumViews.innerHTML = "<b>"+totalCountViews+"</b>";


  trSum.appendChild(tdNameSum);
  trSum.appendChild(tdCountSumComments);
  trSum.appendChild(tdCountSumLikes);
  trSum.appendChild(tdCountSumViews);

  rootEl.appendChild(trSum);
  arr.map( (el, elIndex) => {
    var trEl = document.createElement('tr');
    trEl.id = "tr_tot_"+elIndex;

    var tdName =  document.createElement('td');
    tdName.id = "tdname_tot_"+elIndex;
    tdName.innerHTML = el.name;

    var tdCountComments =  document.createElement('td');
    tdCountComments.id = "tdCountComments_tot_"+elIndex;
    tdCountComments.innerHTML = el.countComments;

    var tdCountLikes =  document.createElement('td');
    tdCountLikes.id = "tdCountLikes_tot_"+elIndex;
    tdCountLikes.innerHTML = el.countLikes;

    var tdCountViews =  document.createElement('td');
    tdCountViews.id = "tdCountViews_tot_"+elIndex;
    tdCountViews.innerHTML = el.countViews;

    trEl.appendChild(tdName);
    trEl.appendChild(tdCountComments);
    trEl.appendChild(tdCountLikes);
    trEl.appendChild(tdCountViews);
    rootEl.appendChild(trEl);
  });
};

var normaliseOkDataObj = function (dataObj) {
  const isDeleted = dataObj.status === "deleted";
  const isPrivate = dataObj.status === "blocked";
  const noStats = !dataObj.stats;
  const noComments = noStats ? true : !dataObj.stats.comments;
  const hasTitle = dataObj.title.length;
  const uploadDate = noStats ? "" : dataObj.upload_date.split(/T/).join(" / ");
  const name = hasTitle
    ? dataObj.title.split(/!!!!!/g)[0]
    : (
        isPrivate
        ? '[Private Group]'
        : (
            (isDeleted && noStats)
            ? '[Deleted]'
            : '[Deleted Private]'
          )
    );
  const groupName =
    noStats
    ? "[Deleted]"
    : `<a href="https://ok.ru/search?st.mode=Groups&st.grmode=Groups&st.posted=set&st.query=${dataObj.login}">${dataObj.login}</a>`;

  const cntViews  = noStats ? 0  : parseInt(dataObj.stats.views_total);
  const cntLikes  = noStats ? 0  : parseInt(dataObj.stats.likes);
  const cntComments  = (noStats || noComments) ? 0 : parseInt(dataObj.stats.comments);
  const thumb = (isDeleted || isPrivate) ? "" : `<img src=${dataObj.thumbnail} class="thumb"/>`;
  // console.log(dataObj.content_id);
  return {
    "isDeleted": isDeleted,
    "isPrivate": isPrivate,
    "name": name,
    "thumb" : thumb,
    "groupName": groupName,
    "cntViews":  cntViews,
    "cntLikes" : cntLikes,
    "cntComments" : cntComments,
    "uploadDate": uploadDate
  };
};

var loadJsonData = function (callback,id) {
  var xobj = new XMLHttpRequest();
  xobj.overrideMimeType("application/json");
  var url = window.location.origin + '/getmovieprops.php?mvid='+id;
  xobj.open('GET', url, true);
  xobj.onreadystatechange = function () {
    if (xobj.readyState == 4 && xobj.status == "200") {
      const dataObj = normaliseOkDataObj(JSON.parse(xobj.responseText))
      callback(id, dataObj);
    } else {
      // console.log(url);
    }
  };
  xobj.send(null);
};

var drawTable = function () {
  var ids = getMvsIds();
  ids.map( id => {
    var rootEl = document.getElementById('tbodyviews');
    var trEl = document.createElement('tr');
    trEl.id = "tr_"+id;

    var tdDate =  document.createElement('td');
    tdDate.id = "tddate_"+id;
    tdDate.className="small";

    var tdGrpName =  document.createElement('td');
    tdGrpName.className = "group_name";
    tdGrpName.id = "tdgrpname_"+id;

    var tdThumb =  document.createElement('td');
    tdThumb.id = "tdthumb_"+id;

    var tdName =  document.createElement('td');
    tdName.id = "tdname_"+id;
    tdName.className = "movie_name";

    var tdUrl =  document.createElement('td');
    tdUrl.id = "tdurl_"+id;
    tdUrl.className = "movie_url";
    tdUrl.innerHTML = 'https://ok.ru/video/'+id;

    var tdCountViews =  document.createElement('td');
    tdCountViews.id = "tdCountViews_" + id;

    var tdCountComments =  document.createElement('td');
    tdCountComments.id = "tdCountComments_"+id;

    var tdCountLikes =  document.createElement('td');
    tdCountLikes.id = "tdCountLikes_"+id;

    trEl.appendChild(tdDate);
    trEl.appendChild(tdGrpName);
    trEl.appendChild(tdThumb);
    trEl.appendChild(tdName);
    trEl.appendChild(tdUrl);
    trEl.appendChild(tdCountComments);
    trEl.appendChild(tdCountLikes);
    trEl.appendChild(tdCountViews);
    rootEl.appendChild(trEl);
  })
};

var accoumCounter = function(){};

var processTable = function() {
  accoumCounter = countMovieTotals();
  getMvsIds().map( id => {
    loadJsonData(updateTableElement, id)
  });
  new Tablesort(document.getElementById('movies-table-id'), {
    descending: true
  });
};

function selectElementContents(el) {
  var body = document.body, range, sel;
  if (document.createRange && window.getSelection) {
    range = document.createRange();
    sel = window.getSelection();
    sel.removeAllRanges();
    try {
      range.selectNodeContents(el);
      sel.addRange(range);
    } catch (e) {
      range.selectNode(el);
      sel.addRange(range);
    }
    document.execCommand("copy");
    sel.removeAllRanges();
  } else if (body.createTextRange) {
    range = body.createTextRange();
    range.moveToElementText(el);
    range.select();
    range.execCommand("Copy");
  }
  return false;
}

document.getElementById('btn-process').addEventListener('click', processTable);
document.getElementById('listofmovies').addEventListener('change', drawTable);
document.getElementById('btn-copy').addEventListener('click', () => selectElementContents(document.getElementById('movies-table-id')));
document.getElementById('btn-reload').addEventListener('click', () => location.reload());
