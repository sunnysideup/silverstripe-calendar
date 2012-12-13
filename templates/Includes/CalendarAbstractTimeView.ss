<div id="$ID" class="calendar $NameClass $ContainerClass">
	<% loop Calendars %>
		<div class="$InnerClass">
			<table>
				<thead>
					<tr>
						<th class="timeTitle">
							$TimeTitle
						</th>
						<% loop Days %>
							<th class="dayTitle $DayTitleClass">
								$DayTitle
							</th>
						<% end_loop %>
					</tr>
				</thead>
				<tbody>
					<% loop Periods %>
						<tr class="$TimeClass">
							<td class="time">
								<p>$Time</p>
							</td>
							<% loop Days %>
								<td class="<% if IsTodayNow %>todayNow<% else_if IsToday %>today <% if IsPast %>past<% else %>coming<% end_if %><% else_if IsPast %>past<% else %>coming<% end_if %> $DayClass">
									<p>$Content</p>
								</td>
							<% end_loop %>
						</tr>
					<% end_loop %>
				</tbody>
			</table>
		</div>
	<% end_loop %>
</div>