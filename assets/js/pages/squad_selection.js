simkit.app.controller('squadSelection', function($scope){	
	$scope.playerSelect = function(e){
		var cb = jQuery(e.target);
		var tr = cb.closest('.tr');
		var table  = cb.closest('.table-mockup');
		if(cb.is(':checked'))
		{
			tr.addClass('selected');
		}
		else
		{
			tr.removeClass('selected');
		}
		var cnt = table.find('.tr.selected').length;
		table.siblings('.selection_number').html(cnt+ ' selected');
		if(cnt == 11)
		{
			table.find(':checkbox').not(':checked').attr('disabled', 'disabled');
		}
		else
		{
			table.find(':checkbox').not(':checked').removeAttr('disabled');
		}
	};
});