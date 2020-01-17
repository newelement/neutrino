// Active Users JS
!function(t){function i(s){if(e[s])return e[s].exports;var n=e[s]={exports:{},id:s,loaded:!1};return t[s].call(n.exports,n,n.exports,i),n.loaded=!0,n.exports}var e={};return i.m=t,i.c=e,i.p="",i(0)}([function(t,i){"use strict";gapi.analytics.ready(function(){gapi.analytics.createComponent("ActiveUsers",{initialize:function(){this.activeUsers=0,gapi.analytics.auth.once("signOut",this.handleSignOut_.bind(this))},execute:function(){this.polling_&&this.stop(),this.render_(),gapi.analytics.auth.isAuthorized()?this.pollActiveUsers_():gapi.analytics.auth.once("signIn",this.pollActiveUsers_.bind(this))},stop:function(){clearTimeout(this.timeout_),this.polling_=!1,this.emit("stop",{activeUsers:this.activeUsers})},render_:function(){var t=this.get();this.container="string"==typeof t.container?document.getElementById(t.container):t.container,this.container.innerHTML=t.template||this.template,this.container.querySelector("strong").innerHTML=this.activeUsers},pollActiveUsers_:function(){var t=this.get(),i=1e3*(t.pollingInterval||5);if(isNaN(i)||5e3>i)throw new Error("Frequency must be 5 seconds or more.");this.polling_=!0,gapi.client.analytics.data.realtime.get({ids:t.ids,metrics:"rt:activeUsers"}).then(function(t){var e=t.result,s=e.totalResults?+e.rows[0][0]:0,n=this.activeUsers;this.emit("success",{activeUsers:this.activeUsers}),s!=n&&(this.activeUsers=s,this.onChange_(s-n)),1==this.polling_&&(this.timeout_=setTimeout(this.pollActiveUsers_.bind(this),i))}.bind(this))},onChange_:function(t){var i=this.container.querySelector("strong");i&&(i.innerHTML=this.activeUsers),this.emit("change",{activeUsers:this.activeUsers,delta:t}),t>0?this.emit("increase",{activeUsers:this.activeUsers,delta:t}):this.emit("decrease",{activeUsers:this.activeUsers,delta:t})},handleSignOut_:function(){this.stop(),gapi.analytics.auth.once("signIn",this.handleSignIn_.bind(this))},handleSignIn_:function(){this.pollActiveUsers_(),gapi.analytics.auth.once("signOut",this.handleSignOut_.bind(this))},template:'<div class="ActiveUsers">Active Users <strong class="ActiveUsers-value"></strong></div>'})})}]);

gapi.analytics.ready(function () {

	gapi.analytics.auth.authorize({
		container: 'embed-api-auth-container',
		clientid: google_analytics_client_id
	});

	var activeUsers = new gapi.analytics.ext.ActiveUsers({
		container: 'active-users-container',
		pollingInterval: 5
	});

	activeUsers.once('success', function () {
		var element = this.container.firstChild;
		var timeout;
		document.getElementById('embed-api-auth-container').style.display = 'none';
		this.on('change', function () {
			var element = this.container.firstChild;
			clearTimeout(timeout);
			timeout = setTimeout(function () {
				element.className = element.className.replace(/ is-(increasing|decreasing)/g, '');
			}, 3000);
		});
	});

	var data = { ids: ids };
	activeUsers.set(data).execute();
	renderWeekOverWeekChart();
	renderYearOverYearChart();
	renderTopBrowsersChart();
	renderTopCountriesChart();
	newVsReturning();
	gaReferral();

	function renderWeekOverWeekChart() {
		var now = moment();
		var thisWeek = query({
			'ids': ids,
			'dimensions': 'ga:date,ga:nthDay',
			'metrics': 'ga:users',
			'start-date': moment(now).subtract(1, 'day').day(0).format('YYYY-MM-DD'),
			'end-date': moment(now).format('YYYY-MM-DD')
		});
		var lastWeek = query({
			'ids': ids,
			'dimensions': 'ga:date,ga:nthDay',
			'metrics': 'ga:users',
			'start-date': moment(now).subtract(1, 'day').day(0).subtract(1, 'week')
					.format('YYYY-MM-DD'),
			'end-date': moment(now).subtract(1, 'day').day(6).subtract(1, 'week')
					.format('YYYY-MM-DD')
		});
		Promise.all([thisWeek, lastWeek]).then(function (results) {
			var data1 = results[0].rows.map(function (row) {
				return +row[2];
			});
			var data2 = results[1].rows.map(function (row) {
				return +row[2];
			});
			var labels = results[1].rows.map(function (row) {
				return +row[0];
			});
			labels = labels.map(function (label) {
				return moment(label, 'YYYYMMDD').format('ddd');
			});
			var data = {
				labels: labels,
				datasets: [
					{
						label: 'Last Week',
						fillColor: 'rgba(200,200,200,0.5)',
						pointColor: 'rgba(150,150,150,1)',
						pointStrokeColor: '#fff',
						data: data2
					},
					{
						label: 'This Week',
						fillColor: 'rgba(100,100,100,0.5)',
						pointColor: 'rgba(65,65,65,1)',
						pointStrokeColor: '#fff',
						data: data1
					}
				]
			};
			new Chart(makeCanvas('chart-1-container')).Line(data);
			generateLegend('legend-1-container', data.datasets);
		});
	}

	function renderYearOverYearChart() {
		var now = moment();
		var thisYear = query({
			'ids': ids,
			'dimensions': 'ga:month,ga:nthMonth',
			'metrics': 'ga:users',
			'start-date': moment(now).date(1).month(0).format('YYYY-MM-DD'),
			'end-date': moment(now).format('YYYY-MM-DD')
		});
		var lastYear = query({
			'ids': ids,
			'dimensions': 'ga:month,ga:nthMonth',
			'metrics': 'ga:users',
			'start-date': moment(now).subtract(1, 'year').date(1).month(0)
					.format('YYYY-MM-DD'),
			'end-date': moment(now).date(1).month(0).subtract(1, 'day')
					.format('YYYY-MM-DD')
		});
		Promise.all([thisYear, lastYear]).then(function (results) {
			var data1 = results[0].rows.map(function (row) {
				return +row[2];
			});
			var data2 = results[1].rows.map(function (row) {
				return +row[2];
			});
			var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
				'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
			for (var i = 0, len = labels.length; i < len; i++) {
				if (data1[i] === undefined) data1[i] = null;
				if (data2[i] === undefined) data2[i] = null;
			}
			var data = {
				labels: labels,
				datasets: [
					{
						label: 'Last Year',
						fillColor: 'rgba(50,50,50,0.5)',
						data: data2
					},
					{
						label: 'This Year',
						fillColor: 'rgba(130,130,130,0.5)',
						data: data1
					}
				]
			};
			new Chart(makeCanvas('chart-2-container')).Bar(data);
			generateLegend('legend-2-container', data.datasets);
		})
		.catch(function (err) {
			console.error(err.stack);
		});
	}

	function renderTopBrowsersChart() {
		query({
			'ids': ids,
			'dimensions': 'ga:browser',
			'metrics': 'ga:pageviews',
			'sort': '-ga:pageviews',
			'max-results': 5
		})
		.then(function (response) {
			var data = [];
			var colors = ['#333333', '#555555', '#777777', '#999999', '#cccccc'];
            if( typeof response.rows !== 'undefined' ){
    			response.rows.forEach(function (row, i) {
    				data.push({value: +row[1], color: colors[i], label: row[0]});
    			});
    			new Chart(makeCanvas('chart-3-container')).Doughnut(data);
    				generateLegend('legend-3-container', data);
            }
		});
	}

	function renderTopCountriesChart() {
		query({
			'ids': ids,
			'dimensions': 'ga:country',
			'metrics': 'ga:sessions',
			'sort': '-ga:sessions',
			'max-results': 5
		})
		.then(function (response) {
			var data = [];
			var colors = ['#333333', '#555555', '#777777', '#999999', '#cccccc'];
            if( typeof response.rows !== 'undefined' ){
			    response.rows.forEach(function (row, i) {
    				data.push({
    					label: row[0],
    					value: +row[1],
    					color: colors[i]
    				});
    			});
    			new Chart(makeCanvas('chart-4-container')).Doughnut(data);
    			generateLegend('legend-4-container', data);
            }
		});
	}

	function newVsReturning() {
		query({
			'ids': ids,
			'dimensions': 'ga:userType',
			'metrics': 'ga:users'
		})
		.then(function (response) {
			let $newUsers = document.querySelector('.new-users');
			let $returningUsers = document.querySelector('.returning-users');
            if( typeof response.rows !== 'undefined'){
    			$newUsers.innerHTML = response.rows[0][1];
                if(  response.rows[1]){
    			    $returningUsers.innerHTML = response.rows[1][1];
                }
            }
		});
	}

	function gaReferral() {
		query({
			'ids': ids,
			'dimensions': 'ga:sourceMedium',
			'metrics': 'ga:sessions',
			'sort': '-ga:sessions',
			'max-results': 10
		})
		.then(function (response) {
			let li = '';
            if( typeof response.rows !== 'undefined' ){
    			response.rows.forEach(function(v){
    				li += '<tr><td lass="medium-source">'+v[0]+'</td><td class="medium-sesh">'+v[1]+'</td></tr>';
    			});
    			let $tableBody = document.querySelector('.top-ten-sources tbody');
    			$tableBody.innerHTML = li;
            }
		});
	}

	function query(params) {
		return new Promise(function (resolve, reject) {
			var data = new gapi.analytics.report.Data({query: params});
			data.once('success', function (response) {
				resolve(response);
			})
			.once('error', function (response) {
				reject(response);
			})
			.execute();
		});
	}

	function makeCanvas(id) {
		var container = document.getElementById(id);
		var canvas = document.createElement('canvas');
		var ctx = canvas.getContext('2d');
		container.innerHTML = '';
		canvas.width = '100%' //container.offsetWidth;
		canvas.height = container.offsetHeight;
		container.appendChild(canvas);
		return ctx;
	}

	function generateLegend(id, items) {
		var legend = document.getElementById(id);
		legend.innerHTML = items.map(function (item) {
			var color = item.color || item.fillColor;
			var label = item.label;
			return '<li><i style="background:' + color + '"></i>' + label + '</li>';
		}).join('');
	}

	Chart.defaults.global.animationSteps = 60;
	Chart.defaults.global.animationEasing = 'easeInOutQuart';
	Chart.defaults.global.responsive = true;
	Chart.defaults.global.maintainAspectRatio = false;
	window.dispatchEvent(new Event('resize'));
});
