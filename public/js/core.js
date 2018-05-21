function ajaxSubmit(form, callback, errorHandle){
	var data = form.serialize();
	$.ajax({
		url:AppUrl(form.attr('action')),
		dataType:'json',
		type:form.attr('method')?form.attr('method'):'post',
		data:data,
		success:function(res, textStatus){
			console.log(res);
			if(res.code == 0){
				if(callback != undefined && typeof(callback) == 'function'){
					callback(res);
				} else if(res.url != '' && res.url != undefined) {
					window.location.href = res.url;
				} else {
					window.location.reload();
				}
			} else {
				if(errorHandle != undefined && typeof(errorHandle) == 'function'){
					errorHandle(res);
				} else {
					showMessage(res.msg);
				}
			}
		}
	});
	return false;
}

function ajaxData(url, data, okCb, errorHandle, options){
    var _options = {'url':AppUrl(url),'data':data, 'type':'post', 'dataType':'json', 'async':true};
	if(typeof options != 'object'){
		options = {};
	}
    for(var i in options){
        if(_options.hasOwnProperty(i)){
            _options[i] = options[i];
        }
    }
    _options['success'] = function(res, textStatus){
		if(res.code == 0){
			if(okCb != undefined && typeof(okCb) == 'function'){
				okCb(res);
			} else if(res.url != '' && res.url != undefined) {
				window.location.href = res.url;
			} else {
				window.location.reload();
			}
		} else {
			if(errorHandle != undefined && typeof(errorHandle) == 'function'){
				errorHandle(res);
			} else {
				showMessage(res.msg);
			}
		}
	};
	$.ajax(_options);
	return false;
}

function showMessage(msg, autoClose, isCancel, okCb, cancelCb){
	var mThis = $('#openBox');
	var isShow = mThis.data('isShow');
	if(isShow)return;
	mThis.show();
	mThis.data('isShow', true);
	var s = typeof autoClose == 'number'?autoClose:3;
	if(autoClose){
		var st = setInterval(function(){
			s--;
			if(s > 0){
				//$('#openBox_txt').html(msg+'<br />'+s+'秒后自动关闭');
				$('#openBox_auto').html(s+'秒后自动关闭');
			} else {
				hideMessage(okCb);
			}
		}, 1000);
	}
	$('#openBox_txt').html(msg);
	if(autoClose)
		$('#openBox_auto').html(s+'秒后自动关闭');
	else
		$('#openBox_auto').html('');
	if(isCancel){
		$('#openBox_cancel').show();
	} else {
		$('#openBox_cancel').hide();
	}
	function hideMessage(cb){
		if(autoClose && st)
			clearInterval(st);
		mThis.hide();
		mThis.data('isShow', false);
		if(cb != undefined && typeof cb == 'function'){
			cb();
		}
	}
	$('#openBox_ok').click(function(){
		hideMessage(okCb);
		$('#openBox_cancel').unbind('click');
		$(this).unbind('click');
		return false;
	});
	$('#openBox_cancel').click(function(){
		hideMessage(cancelCb);
		$('#openBox_ok').unbind('click');
		$(this).unbind('click');
		return false;
	});

	$(document).keydown(function(event){
		$('.inputThis').removeClass('inputThis');
		var box = $('.merchant_popbox');
		var inp = $('input:focus').addClass('inputThis');
		if(box.css("display")=='block'){
			if (event.keyCode == 13){
				box.fadeOut(200,function(){
					inp.focus();
				})
				$('input').blur();
				$('#openBox_ok').click();
			}
		}
	})
}

// $.fn.setForm = function(jsonValue) {
// 	var obj=this;
// 	obj[0].reset();
// 	$.each(jsonValue, function (name, val) {
// 		var htmlType = $("[name='"+name+"']").attr("type");
// 		if(htmlType == "text" || htmlType == "textarea" || htmlType == "select-one" || htmlType == "hidden" || htmlType == "button"){
//             $("[name='"+name+"']").val(val);
//         }else if(htmlType == "radio"){
//         	$("input[type=radio][name='"+name+"']").attr("checked",false);
//             $("input[type=radio][name='"+name+"'][value='"+val+"']").attr("checked",true);
//         }else if(htmlType == "checkbox"){
//         	$("input[type=checkbox][name='"+name+"']").prop("checked","");
//             $("input[type=checkbox][name='"+name+"'][value='"+val+"']").prop("checked","checked");
//         }
// 	});
// }

function AppUrl(url)
{
	if (url.match(/index\.php\?r=/) == null) {
		url = url.replace(/\&/g, '-');
		url = url.replace(/\=/g, '-');
		return url;
	}
	if (typeof(BASEURL) == 'undefined') {
		BASEURL = '';
	}
	var urlArray = url.split(/\&/);
	var host = route = query = '';
	var urlArrayLength = urlArray.length;
	for(i = 0; i < urlArrayLength; i++)
	{
		if (i == 0) {
			var headArray = urlArray[i].split(/index\.php\?r=/);
			host = headArray[0];
			if (host.match(/^http/) == null) {
				host = BASEURL;
			}
			var routeArr = headArray[1].split(/\//);
			for(j = 0; j < routeArr.length; j++)
			{
				if (routeArr[j] == '') {
					routeArr[j] = 'index';
				}
				route = route + routeArr[j] + '-';
			}
		} else {
			query = query + urlArray[i].replace(/\=/, '-') + '-';
		}
	}
	route = route.slice(0, route.length - 1);
	query = query.slice(0, query.length - 1);
	var appUrl = host + route;
	if (query != '') {
		appUrl = appUrl + '-' + query;
	}

	return appUrl;
}