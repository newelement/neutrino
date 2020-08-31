!function(t){var e={};function n(i){if(e[i])return e[i].exports;var s=e[i]={i:i,l:!1,exports:{}};return t[i].call(s.exports,s,s.exports,n),s.l=!0,s.exports}n.m=t,n.c=e,n.d=function(t,e,i){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:i})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var i=Object.create(null);if(n.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var s in t)n.d(i,s,function(e){return t[e]}.bind(null,s));return i},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="/",n(n.s=229)}({229:function(t,e,n){t.exports=n(230)},230:function(t,e){!function(t){function e(i){if(n[i])return n[i].exports;var s=n[i]={exports:{},id:i,loaded:!1};return t[i].call(s.exports,s,s.exports,e),s.loaded=!0,s.exports}var n={};e.m=t,e.c=n,e.p="",e(0)}([function(t,e){"use strict";gapi.analytics.ready((function(){gapi.analytics.createComponent("ActiveUsers",{initialize:function(){this.activeUsers=0,gapi.analytics.auth.once("signOut",this.handleSignOut_.bind(this))},execute:function(){this.polling_&&this.stop(),this.render_(),gapi.analytics.auth.isAuthorized()?this.pollActiveUsers_():gapi.analytics.auth.once("signIn",this.pollActiveUsers_.bind(this))},stop:function(){clearTimeout(this.timeout_),this.polling_=!1,this.emit("stop",{activeUsers:this.activeUsers})},render_:function(){var t=this.get();this.container="string"==typeof t.container?document.getElementById(t.container):t.container,this.container.innerHTML=t.template||this.template,this.container.querySelector("strong").innerHTML=this.activeUsers},pollActiveUsers_:function(){var t=this.get(),e=1e3*(t.pollingInterval||5);if(isNaN(e)||5e3>e)throw new Error("Frequency must be 5 seconds or more.");this.polling_=!0,gapi.client.analytics.data.realtime.get({ids:t.ids,metrics:"rt:activeUsers"}).then(function(t){var n=t.result,i=n.totalResults?+n.rows[0][0]:0,s=this.activeUsers;this.emit("success",{activeUsers:this.activeUsers}),i!=s&&(this.activeUsers=i,this.onChange_(i-s)),1==this.polling_&&(this.timeout_=setTimeout(this.pollActiveUsers_.bind(this),e))}.bind(this))},onChange_:function(t){var e=this.container.querySelector("strong");e&&(e.innerHTML=this.activeUsers),this.emit("change",{activeUsers:this.activeUsers,delta:t}),t>0?this.emit("increase",{activeUsers:this.activeUsers,delta:t}):this.emit("decrease",{activeUsers:this.activeUsers,delta:t})},handleSignOut_:function(){this.stop(),gapi.analytics.auth.once("signIn",this.handleSignIn_.bind(this))},handleSignIn_:function(){this.pollActiveUsers_(),gapi.analytics.auth.once("signOut",this.handleSignOut_.bind(this))},template:'<div class="ActiveUsers">Active Users <strong class="ActiveUsers-value"></strong></div>'})}))}]),gapi.analytics.ready((function(){gapi.analytics.auth.authorize({container:"embed-api-auth-container",clientid:google_analytics_client_id});var t=new gapi.analytics.ext.ActiveUsers({container:"active-users-container",pollingInterval:5});t.once("success",(function(){var t;this.container.firstChild;document.getElementById("embed-api-auth-container").style.display="none",this.on("change",(function(){var e=this.container.firstChild;clearTimeout(t),t=setTimeout((function(){e.className=e.className.replace(/ is-(increasing|decreasing)/g,"")}),3e3)}))}));var e,n,i,s={ids:ids};function a(t){return new Promise((function(e,n){new gapi.analytics.report.Data({query:t}).once("success",(function(t){e(t)})).once("error",(function(t){n(t)})).execute()}))}function r(t){var e=document.getElementById(t),n=document.createElement("canvas"),i=n.getContext("2d");return e.innerHTML="",n.width="100%",n.height=e.offsetHeight,e.appendChild(n),i}function o(t,e){document.getElementById(t).innerHTML=e.map((function(t){return'<li><i style="background:'+(t.color||t.fillColor)+'"></i>'+t.label+"</li>"})).join("")}t.set(s).execute(),e=moment(),n=a({ids:ids,dimensions:"ga:date,ga:nthDay",metrics:"ga:users","start-date":moment(e).subtract(1,"day").day(0).format("YYYY-MM-DD"),"end-date":moment(e).format("YYYY-MM-DD")}),i=a({ids:ids,dimensions:"ga:date,ga:nthDay",metrics:"ga:users","start-date":moment(e).subtract(1,"day").day(0).subtract(1,"week").format("YYYY-MM-DD"),"end-date":moment(e).subtract(1,"day").day(6).subtract(1,"week").format("YYYY-MM-DD")}),Promise.all([n,i]).then((function(t){var e=t[0].rows.map((function(t){return+t[2]})),n=t[1].rows.map((function(t){return+t[2]})),i=t[1].rows.map((function(t){return+t[0]})),s={labels:i=i.map((function(t){return moment(t,"YYYYMMDD").format("ddd")})),datasets:[{label:"Last Week",fillColor:"rgba(200,200,200,0.5)",pointColor:"rgba(150,150,150,1)",pointStrokeColor:"#fff",data:n},{label:"This Week",fillColor:"rgba(100,100,100,0.5)",pointColor:"rgba(65,65,65,1)",pointStrokeColor:"#fff",data:e}]};new Chart(r("chart-1-container")).Line(s),o("legend-1-container",s.datasets)})),function(){var t=moment(),e=a({ids:ids,dimensions:"ga:month,ga:nthMonth",metrics:"ga:users","start-date":moment(t).date(1).month(0).format("YYYY-MM-DD"),"end-date":moment(t).format("YYYY-MM-DD")}),n=a({ids:ids,dimensions:"ga:month,ga:nthMonth",metrics:"ga:users","start-date":moment(t).subtract(1,"year").date(1).month(0).format("YYYY-MM-DD"),"end-date":moment(t).date(1).month(0).subtract(1,"day").format("YYYY-MM-DD")});Promise.all([e,n]).then((function(t){for(var e=t[0].rows.map((function(t){return+t[2]})),n=t[1].rows.map((function(t){return+t[2]})),i=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],s=0,a=i.length;s<a;s++)void 0===e[s]&&(e[s]=null),void 0===n[s]&&(n[s]=null);var c={labels:i,datasets:[{label:"Last Year",fillColor:"rgba(50,50,50,0.5)",data:n},{label:"This Year",fillColor:"rgba(130,130,130,0.5)",data:e}]};new Chart(r("chart-2-container")).Bar(c),o("legend-2-container",c.datasets)})).catch((function(t){console.error(t.stack)}))}(),a({ids:ids,dimensions:"ga:browser",metrics:"ga:pageviews",sort:"-ga:pageviews","max-results":5}).then((function(t){var e=[],n=["#333333","#555555","#777777","#999999","#cccccc"];void 0!==t.rows&&(t.rows.forEach((function(t,i){e.push({value:+t[1],color:n[i],label:t[0]})})),new Chart(r("chart-3-container")).Doughnut(e),o("legend-3-container",e))})),a({ids:ids,dimensions:"ga:country",metrics:"ga:sessions",sort:"-ga:sessions","max-results":5}).then((function(t){var e=[],n=["#333333","#555555","#777777","#999999","#cccccc"];void 0!==t.rows&&(t.rows.forEach((function(t,i){e.push({label:t[0],value:+t[1],color:n[i]})})),new Chart(r("chart-4-container")).Doughnut(e),o("legend-4-container",e))})),a({ids:ids,dimensions:"ga:userType",metrics:"ga:users"}).then((function(t){var e=document.querySelector(".new-users"),n=document.querySelector(".returning-users");void 0!==t.rows&&(e.innerHTML=t.rows[0][1],t.rows[1]&&(n.innerHTML=t.rows[1][1]))})),a({ids:ids,dimensions:"ga:sourceMedium",metrics:"ga:sessions",sort:"-ga:sessions","max-results":10}).then((function(t){var e="";void 0!==t.rows&&(t.rows.forEach((function(t){e+='<tr><td lass="medium-source">'+t[0]+'</td><td class="medium-sesh">'+t[1]+"</td></tr>"})),document.querySelector(".top-ten-sources tbody").innerHTML=e)})),Chart.defaults.global.animationSteps=60,Chart.defaults.global.animationEasing="easeInOutQuart",Chart.defaults.global.responsive=!0,Chart.defaults.global.maintainAspectRatio=!1,window.dispatchEvent(new Event("resize"))}))}});
//# sourceMappingURL=analytics.js.map