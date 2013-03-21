<table>
	<thead>
		<tr>
			<% if ShowWeekLeft %>
				<th class="weekLeftTitle">
					$WeekLeftTitle
				</th>
			<% end_if %>
			<% loop Days %>
				<th class="dayTitle $DayTitleClass" scope="col">
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
					<% include CalendarWeekTableDay %>
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
