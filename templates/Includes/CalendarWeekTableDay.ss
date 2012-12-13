<td class="<% if IsToday %>today<% else_if IsPast %>past<% else %>coming<% end_if %> <% if CurrentMonth %>currentmonth<% else_if PrevMonth %>prevmonth<% if PrevYear %> prevyear<% end_if %><% else %>nextmonth<% if NextYear %> nextyear<% end_if %><% end_if %> $DayClass">
	<p><% if Link %><a href="$Link">$Day</a><% else %>$Day<% end_if %></p>
</td>