<div id="$ID" class="calendar $NameClass $ContainerClass">
	<% control Calendars %>
		<div class="$InnerClass">
			<table>
				<thead>
					<tr>
						<th class="timeTitle">
							$TimeTitle
						</th>
						<% control Days %>
							<th class="dayTitle $DayTitleClass">
								$DayTitle
							</th>
						<% end_control %>
					</tr>
				</thead>
				<tbody>
					<% control Periods %>
						<tr class="$TimeClass">
							<td class="time">
								<p>$Time</p>
							</td>
							<% control Days %>
								<td class="<% if IsTodayNow %>todayNow<% else_if IsToday %>today <% if IsPast %>past<% else %>coming<% end_if %><% else_if IsPast %>past<% else %>coming<% end_if %> $DayClass">
									<p>$Content</p>
								</td>
							<% end_control %>
						</tr>
					<% end_control %>
				</tbody>
			</table>
		</div>
	<% end_control %>
</div>