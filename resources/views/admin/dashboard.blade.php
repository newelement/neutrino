@extends('neutrino::admin.template.header-footer')
@section('title', 'Dashboard | ')
@section('content')
<div class="container dashboard">
	<div class="content full">

		<div class="dashboard-header">
			@if (isset($google_analytics_client_id) && !empty($google_analytics_client_id))
                <div id="embed-api-auth-container"></div>
            @else
                <p style="margin:0; color:#333; font-size: 13px; padding: 0 12px 12px 12px; text-align:center;">
					@lang('neutrino::dashboard.setup_ga_api')
                    <a href="https://console.developers.google.com" target="_blank">https://console.developers.google.com</a>
                </p>
            @endif
		</div>

		<div class="dashboard-cols small">

			<div class="dashboard-col">
				<div class="dashboard-card">
					<div class="d-card-body small">
						<div class="small-icon">
							<i class="fal fa-users"></i>
						</div>
						<div class="small-stat">
							<div id="active-users-container"></div>
						</div>
					</div>
					<div class="d-card-footer">
						Refreshes every 3 seconds
					</div>
				</div>
			</div>

			<div class="dashboard-col">
				<div class="dashboard-card">
					<div class="d-card-body small">
						<div class="small-icon">
							<i class="fal fa-user-friends"></i>
						</div>
						<div class="small-stat">
							New vs. Returning Users
							<strong><span class="new-users">0</span> / <span class="returning-users">0</span></strong>
						</div>
					</div>
					<div class="d-card-footer">
						Last 7 days
					</div>
				</div>
			</div>

			<div class="dashboard-col">
				<div class="dashboard-card">
					<div class="d-card-body small">
						<div class="small-icon">
							<i class="fal fa-comment-alt"></i>
						</div>
						<div class="small-stat">
							<div class="open-comments">
								Moderate Comments
								<a href="/admin/moderate-comments"><strong>{{ _getCommentCount() }}</strong></a>
							</div>
						</div>
					</div>
					<div class="d-card-footer">
						Comment moderation is <strong>{{ getSetting('moderate_comments')? 'on' : 'off' }}</strong>.
					</div>
				</div>
			</div>

		</div>

        @if( shoppeExists() )
        <div class="dashboard-cols small">
            <div class="dashboard-col">
                <div class="dashboard-card">
                    <div class="d-card-body small">
                        <div class="small-icon">
                            <i class="fal fa-dollar-sign"></i>
                        </div>
                        <div class="small-stat">
                            <div class="open-comments">
                                Sales Today
                                <strong>${{ formatCurrency( $sales_today ) }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="d-card-footer">
                        Yesterday: <strong>${{ formatCurrency( $sales_yesterday ) }}</strong>
                    </div>
                </div>
            </div>
            <div class="dashboard-col">
                <div class="dashboard-card">
                    <div class="d-card-body small">
                        <div class="small-icon">
                            <i class="fal fa-box-alt"></i>
                        </div>
                        <div class="small-stat">
                            <div class="open-comments">
                                New Orders
                                <a href="/admin/orders"><strong>{{ $orderCount }}</strong></a>
                            </div>
                        </div>
                    </div>
                    <div class="d-card-footer">
                        Orders needing processing.
                    </div>
                </div>
            </div>
            <div class="dashboard-col">
                <div class="dashboard-card">
                    <div class="d-card-body small">
                        <div class="small-icon">
                            <i class="fal fa-shopping-cart"></i>
                        </div>
                        <div class="small-stat">
                            <div class="open-comments">
                                Active Carts
                                <strong>{{ $active_carts->count() }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="d-card-footer">
                        Past 2 weeks.
                    </div>
                </div>
            </div>
        </div>
        @endif

		<div class="dashboard-cols">

			@include('neutrino::admin.partials.quick-block')

			<div class="dashboard-col">
				<div class="dashboard-card">
					<div class="d-card-header">
						<h3>Traffic <small>Last Week vs This Week</small></h3>
					</div>
					<div class="d-card-body">
						<div class="Chartjs">
							<figure class="Chartjs-figure" id="chart-1-container"></figure>
							<ol class="Chartjs-legend" id="legend-1-container"></ol>
						</div>
					</div>
				</div>
			</div>

			<div class="dashboard-col">
				<div class="dashboard-card">
					<div class="d-card-header">
						<h3>Traffic <small>Last Year vs This Year</small></h3>
					</div>
					<div class="d-card-body">
						<div class="Chartjs">
							<figure class="Chartjs-figure" id="chart-2-container"></figure>
                            <ol class="Chartjs-legend" id="legend-2-container"></ol>
						</div>
					</div>
				</div>
			</div>

			<div class="dashboard-col">
				<div class="dashboard-card">
					<div class="d-card-header">
						<h3>Top Browsers</h3>
					</div>
					<div class="d-card-body">
						<div class="Chartjs">
							<figure class="Chartjs-figure" id="chart-3-container"></figure>
                            <ol class="Chartjs-legend" id="legend-3-container"></ol>
						</div>
					</div>
				</div>
			</div>

			<div class="dashboard-col">
				<div class="dashboard-card">
					<div class="d-card-header">
						<h3>Top Countries</h3>
					</div>
					<div class="d-card-body">
						<div class="Chartjs">
							<figure class="Chartjs-figure" id="chart-4-container"></figure>
                            <ol class="Chartjs-legend" id="legend-4-container"></ol>
						</div>
					</div>
				</div>
			</div>

			<div class="dashboard-col">
				<div class="dashboard-card">
					<div class="d-card-header">
						<h3>Traffic Sources <small>Top 10 past 7 days</small></h3>
					</div>
					<div class="d-card-body">
						<table cellpadding="0" cellspacing="0" class="top-ten-sources">
							<thead>
								<tr>
									<th style="text-align: left;">Source</th>
									<th>Sessions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>

					</div>
				</div>
			</div>

		</div>
	</div>
</div>

@if(isset($google_analytics_client_id) && !empty($google_analytics_client_id))
	<script>
	var ids = 'ga:{{ env('GOOGLE_ANALYTICS_VIEW_ID') }}';
	var google_analytics_client_id = '{{ $google_analytics_client_id }}';

    (function (w, d, s, g, js, fs) {
        g = w.gapi || (w.gapi = {});
        g.analytics = {
            q: [], ready: function (f) {
                this.q.push(f);
            }
        };
        js = d.createElement(s);
        fs = d.getElementsByTagName(s)[0];
        js.src = 'https://apis.google.com/js/platform.js';
        fs.parentNode.insertBefore(js, fs);
        js.onload = function () {
            g.load('analytics');
        };
    }(window, document, 'script'));
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.1.1/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="/vendor/newelement/neutrino/js/analytics.js"></script>
@endif


@endsection
