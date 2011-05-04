<div id="$ID" class="calendar $NameClass $ContainerClass">
	<% control Calendars %>
		<div class="$InnerClass $ExtraInnerClass <% if IsNow %>now<% else_if IsPast %>past<% else %>coming<% end_if %>">
			<% control Months %>
				<div class="$InnerClass $MonthClass <% if IsNow %>now<% else_if IsPast %>past<% else %>coming<% end_if %>">
					<p class="monthTitle"><% if Link %><a href="$Link">$MonthTitle</a><% else %><span>$MonthTitle</span><% end_if %></p>
					<% include CalendarWeekTable %>
				</div>
			<% end_control %>
		</div>
	<% end_control %>
</div>