<table>
	<thead>
		<tr>
			<% if ShowWeekLeft %>
				<th class="weekLeftTitle">
					$WeekLeftTitle
				</th>
			<% end_if %>
			<% loop Days %>
				<th class="dayTitle $DayTitleClass">
					$DayTitle
				</th>
			<% end_loop %>
			<% if ShowWeekRight %>
				<th class="weekRightTitle">
					$WeekRightTitle
				</th>
			<% end_if %>
		</tr>
	</thead>
	<tbody>
		<% loop Weeks %>
			<tr class="$WeekClass">
				<% if ShowWeekLeft %>
					<td class="weekLeft">
						<p><% if WeekLink %><a href="$WeekLink">$WeekLeft</a><% else %>$WeekLeft<% end_if %></p>
					</td>
				<% end_if %>
				<% loop Days %>
					<td class="<% if IsToday %>today<% else_if IsPast %>past<% else %>coming<% end_if %> <% if CurrentMonth %>currentmonth<% else_if PrevMonth %>prevmonth<% if PrevYear %> prevyear<% end_if %><% else %>nextmonth<% if NextYear %> nextyear<% end_if %><% end_if %> $DayClass">
						<p><% if Link %><a href="$Link">$Day</a><% else %>$Day<% end_if %></p>
					</td>
				<% end_loop %>
				<% if ShowWeekRight %>
					<td class="weekRight">
						<p><% if WeekLink %><a href="$WeekLink">$WeekRight</a><% else %>$WeekRight<% end_if %></p>
					</td>
				<% end_if %>
			</tr>
		<% end_loop %>
	</tbody>
</table>