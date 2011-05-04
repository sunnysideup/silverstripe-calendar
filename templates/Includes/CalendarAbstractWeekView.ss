<div id="$ID" class="calendar $NameClass $ContainerClass">
	<% control Calendars %>
		<div class="$InnerClass $ExtraInnerClass <% if IsNow %>now<% else_if IsPast %>past <% if IsNowYear %>nowYear<% else_if IsPastYear %>pastYear<% end_if %><% else %>coming <% if IsNowYear %>nowYear<% else_if IsPastYear %><% else %>comingYear<% end_if %><% end_if %>">
			<% include CalendarWeekTable %>
		</div>
	<% end_control %>
</div>