<table>
	<thead>
		<tr>
			<% if ShowWeekLeft %>
				<th class="weekLeftTitle">
					$WeekLeftTitle
				</th>
			<% end_if %>
			<% control Days %>
				<th class="dayTitle $DayTitleClass">
					$DayTitle
				</th>
			<% end_control %>
			<% if ShowWeekRight %>
				<th class="weekRightTitle">
					$WeekRightTitle
				</th>
			<% end_if %>
		</tr>
	</thead>
	<tbody>
		<% control Weeks %>
			<tr class="$WeekClass">
				<% if ShowWeekLeft %>
					<td class="weekLeft">
						<p><% if WeekLink %><a href="$WeekLink">$WeekLeft</a><% else %>$WeekLeft<% end_if %></p>
					</td>
				<% end_if %>
				<% control Days %>
					<td class="<% if IsToday %>today<% else_if IsPast %>past<% else %>coming<% end_if %> <% if CurrentMonth %>currentmonth<% else_if PrevMonth %>prevmonth<% if PrevYear %> prevyear<% end_if %><% else %>nextmonth<% if NextYear %> nextyear<% end_if %><% end_if %> $DayClass">
						<p><% if Link %><a href="$Link">$Day</a><% else %>$Day<% end_if %></p>
					</td>
				<% end_control %>
				<% if ShowWeekRight %>
					<td class="weekRight">
						<p><% if WeekLink %><a href="$WeekLink">$WeekRight</a><% else %>$WeekRight<% end_if %></p>
					</td>
				<% end_if %>
			</tr>
		<% end_control %>
	</tbody>
</table>