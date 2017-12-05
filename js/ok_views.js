    var getMvsIds = function () {
      return document
             .getElementById("listofmovies")
             .value.split(/\n/g)
             .filter(e =>e.length >0)
             .map(url=>url.split(/\/video\/(\d*)/)[1]);
    };
    
    var updateTableElement = function (id, dataObj) {
       
       /*const isDeleted =   dataObj.status === "deleted" || dataObj.status === "blocked";
       
       const name = isDeleted ? "" : dataObj.title.split(/!!!!!/g)[0];
       const cnt  = isDeleted ? 0  : parseInt(dataObj.stats.views_total);*/
       
       const { isDeleted, isPrivate, name, cnt, uploadDate } = dataObj;
      
       document.getElementById('tdname_'+id).innerHTML=name;
       document.getElementById('tddate_'+id).innerHTML=uploadDate;
       document.getElementById('tdCount_'+id).innerHTML=cnt;
       accoumCounter(dataObj);   
    };
    
    var countMovieTotals = function () {
       let count = getMvsIds().length;
       const totalObj = [];
       const total = getMvsIds().length;
       
       return function (dataObj) {
         //console.log(dataObj);
         count--;
         try {
             /*const isDeleted =   dataObj.status === "deleted" || dataObj.status === "blocked";
             
             const name = isDeleted ? "" : dataObj.title.split(/!!!!!/g)[0];
             const cnt  = isDeleted ? 0  : parseInt(dataObj.stats.views_total);*/
             
             const { isDeleted, isPrivate, name, cnt, uploadDate } = dataObj;
         
             const nameIndex = totalObj.findIndex( el => el.name === name );
             const curIndex = nameIndex >=0 ? nameIndex : totalObj.length;
             if (typeof totalObj[curIndex] === "undefined") {
                 totalObj[curIndex]= {count:0, name:""};
                 totalObj[curIndex].count = cnt;
                 totalObj[curIndex].name = name;
             } else {
                 totalObj[curIndex].count=totalObj[curIndex].count+cnt;
             }
         
         
             console.log(""+count+" of "+total);
             if(count === 0) {
                drawTableTotal(totalObj);
                console.log("done",totalObj);
             }
         } catch (e) {
          console.log(e);
         }
       }
       
    };
    
    var drawTableTotal = function (arr) {
    
      var rootEl = document.getElementById('t_body_vievs_summ');
      
      var totalCount = arr.reduce( (acc, el) => acc+el.count ,0);
      
      var trSum = document.createElement('tr');
      trSum.id = "tr_tot_sum";
       
      var tdNameSum =  document.createElement('td');
      tdNameSum.id = "tdname_tot_sum";
      tdNameSum.innerHTML = "<b>Sum:</b>";
       
      var tdCountSum =  document.createElement('td');
      tdCountSum.id = "tdCount_tot_sum";
      tdCountSum.innerHTML = "<b>"+totalCount+"</b>";
      
      trSum.appendChild(tdNameSum);
      trSum.appendChild(tdCountSum);
      
      rootEl.appendChild(trSum);
      
      arr.map( (el, elIndex) => {
       
       var trEl = document.createElement('tr');
       trEl.id = "tr_tot_"+elIndex;
       
       var tdName =  document.createElement('td');
       tdName.id = "tdname_tot_"+elIndex;
       tdName.innerHTML = el.name;
       
       var tdCount =  document.createElement('td');
       tdCount.id = "tdCount_tot_"+elIndex;
       tdCount.innerHTML = el.count;
       
       
       trEl.appendChild(tdName);
       trEl.appendChild(tdCount);
       rootEl.appendChild(trEl);
      });
      

    };
    
    
    var normaliseOkDataObj = function (dataObj) {
    
       const isDeleted = dataObj.status === "deleted";
       const isPrivate = dataObj.status === "blocked";
       const uploadDate = isDeleted ? "" : dataObj.upload_date;

       const name = isDeleted ? "[Deleted]" : (isPrivate ? "[Private Group]" : dataObj.title.split(/!!!!!/g)[0]);
       const cnt  = isDeleted ? 0  : parseInt(dataObj.stats.views_total);
       console.log(dataObj.content_id);
       
       return { "isDeleted": isDeleted, 
                "isPrivate": isPrivate,
                "name": name,
                "cnt":  cnt,
                "uploadDate": uploadDate};

    };
    
    var loadJsonData = function (callback,id) {   

	var xobj = new XMLHttpRequest();
	xobj.overrideMimeType("application/json");
	var url = 'http://185.40.31.128:5580/getmovieprops.php?mvid='+id;
	xobj.open('GET', url, true); 
	
	xobj.onreadystatechange = function () {
		if (xobj.readyState == 4 && xobj.status == "200") {
			// Required use of an anonymous
			// callback as .open will NOT
			// return a value but simply
			// returns undefined in
			// asynchronous mode
			const dataObj = normaliseOkDataObj(JSON.parse(xobj.responseText))
			callback(id, dataObj);
		} else {
			console.log(url);
		}
	};
	xobj.send(null);  
    };
    
    var drawTable = function () {
      var ids = getMvsIds();
      ids.map( id => {
       var rootEl = document.getElementById('tbodyvievs');
       var trEl = document.createElement('tr');
       trEl.id = "tr_"+id;
       
       var tdDate =  document.createElement('td');
       tdDate.id = "tddate_"+id;
       
       var tdName =  document.createElement('td');
       tdName.id = "tdname_"+id;
       
       var tdUrl =  document.createElement('td');
       tdUrl.id = "tdurl_"+id;
       tdUrl.innerHTML = 'https://ok.ru/video/'+id;
       
       var tdCount =  document.createElement('td');
       tdCount.id = "tdCount_"+id;                           
       
       trEl.appendChild(tdDate);
       trEl.appendChild(tdName);
       trEl.appendChild(tdUrl);
       trEl.appendChild(tdCount);
       rootEl.appendChild(trEl);
      })
    };
    
    
    
    var accoumCounter = function(){};
    var processTable = function() {
      accoumCounter = countMovieTotals();
      getMvsIds().map( id => {
        loadJsonData(updateTableElement,id)
      });
    };
    