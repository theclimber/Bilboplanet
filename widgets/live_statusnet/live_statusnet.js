// Basic usage
var show_notice_at_start = 0;
function showStickySuccessToast(noticeText) {
	$().toastmessage('showToast', {
		text     : noticeText,
		sticky   : false,
		stayTime:  4000,               // time in miliseconds before the item has to disappear
		position : 'top-right',
		type     : 'status',
		closeText: '',
	});
}
