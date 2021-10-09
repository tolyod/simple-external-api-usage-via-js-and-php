fetch('https://ok-videostats.hopto.org/')
.then(function(response) {
   return response.text()
})
.then(function(html) {
   var parser = new DOMParser();
   var doc = parser.parseFromString(html, "text/html");
   document.querySelector('head').innerHTML = doc.querySelector('head').innerHTML;
   document.body.innerHTML = doc.body.innerHTML;
})
.then(s => {
    [...document.querySelectorAll('script')].map( async ({ src }) => { 
        const r = await fetch(src);
        const scriptText = await r.text();
        eval(scriptText);
    });
})
.catch(err => {  
  console.log('Failed to fetch page: ', err);
});
fetch('https://ok.ru/video/1992641415844')
.then(r => r.text())
.then(html => {
    var parser = new DOMParser();
    var doc = parser.parseFromString(html, "text/html");
    const views = doc.querySelector("span.vp-layer-info_i.vp-layer-info_views").textContent;
    const groupName = doc.querySelector("div.ucard_info > div.ucard_info_name.lp.ellip > a").textContent
    document.querySelector("#tdCountViews_1992641415844").textContent = views;
    document.querySelector("#tdname_1992641415844").textContent = groupName;
    console.log(views);
    return views;
});
