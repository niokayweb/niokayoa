@extends('layouts.app')
<link rel="StyleSheet" href="{{ asset('oa/dtree/dtree.css') }}" type="text/css" />
<style>
    .dtree{ width: 100%;}
</style>
<body>
@section('content')
<div class="content">
    <div class="main">
        <div class="dtree" id="dtree_div">
        </div>
        <div class="selectorResult">
            选择结果：
            <ul id="ulSelected">
                <!-- <li id="user_2_1" uid="1" name="姓名" mytype="2">
                    <div class="selectedUser" onmouseover="showRemove(this)" onmouseout="hideRemove(this)" onmousemove="setRemove(this,event)" onclick="doRemove(this,event);" style="cursor: pointer;">
                        姓名
                    </div>
                </li> -->
            </ul>
              <input type='button' name='bTest' class="save_btn" value='保存' onclick='save();'>
        </div>   
        <div>
          
        </div>
        </div>
</div>
@endsection
@section('js')
<script type="text/javascript" src="{{ asset('oa/dtree/dtree.js') }}"></script>
<script type="text/javascript">
    d = new dTree('d', true);   //参数一: 树名称。参数二：单选多选 true多选  false单选  默认单选 
    // dTree实例属性以此为：  节点ID，父类ID，chechbox的名称，chechbox的值，chechbox的显示名称，
    // chechbox是否被选中--默认是不选，chechbox是否可用：默认是可用，节点链接：默认是虚链接              
    d.add(0, -1, '权限管理');
    @foreach($systems as $k => $system)
        d.add({{ $system->id.'0000' }}, 0, 'authority', 0, "{{$system->name}}", true, false);
        @foreach($authorities as $k => $authority)
            @if($authority->system_id == $system->id && $authority->parent_id == 0)
                d.add({{ $authority->id }}, {{$system->id.'0000'}}, 'authority', {{$authority->id}}, "{{$authority->name}}", false, @if(isset($roleAuthorities[$authority->id])) true @else false @endif);
            @elseif($authority->parent_id != 0)
                d.add({{ $authority->id }}, {{$authority->parent_id}}, 'authority', {{$authority->id}}, "{{$authority->name}}", false, @if(isset($roleAuthorities[$authority->id])) true @else false @endif);
            @endif
        @endforeach
    @endforeach
    document.getElementById('dtree_div').innerHTML = d;
    // document.write(d);
    d.openAll();

function save() {
    var count = 0;
    var obj = document.all.authority;
    var ids = [];
    for (i = 0; i < obj.length; i++) {
        if (obj[i].checked) {
            ids.push(obj[i].value);
            count++;
        }
    }
    ajaxData('../edit_authorities/'+{{$id}},{
        ids: ids
    },function(res){
        showMessage(res.msg);
    })
}
//搜索节点并展开节点
function nodeSearching() {
    var dosearch = $.trim($("#dosearch_text").val());//获取要查询的文字
    var dtree_div = $("#dtree_div").find(".dtree_node").show().filter(":contains('" + dosearch + "')");//获取所有包含文本的节点
    $.each(dtree_div, function (index, element) {
        var s = $(element).attr("node_id");
        d.openTo(s);//根据id打开节点
    });
}

//#region 页面执行入口
$(document).ready(function () {
    //#region 浏览器检测相关方法
    window["MzBrowser"] = {}; (function () {
        if (MzBrowser.platform) return;
        var ua = window.navigator.userAgent;
        MzBrowser.platform = window.navigator.platform;
        MzBrowser.firefox = ua.indexOf("Firefox") > 0;
        MzBrowser.opera = typeof (window.opera) == "object";
        MzBrowser.ie = !MzBrowser.opera && ua.indexOf("MSIE") > 0;
        MzBrowser.mozilla = window.navigator.product == "Gecko";
        MzBrowser.netscape = window.navigator.vendor == "Netscape";
        MzBrowser.safari = ua.indexOf("Safari") > -1;
        if (MzBrowser.firefox) var re = /Firefox(\s|\/)(\d+(\.\d+)?)/;
        else if (MzBrowser.ie) var re = /MSIE( )(\d+(\.\d+)?)/;
        else if (MzBrowser.opera) var re = /Opera(\s|\/)(\d+(\.\d+)?)/;
        else if (MzBrowser.netscape) var re = /Netscape(\s|\/)(\d+(\.\d+)?)/;
        else if (MzBrowser.safari) var re = /Version(\/)(\d+(\.\d+)?)/;
        else if (MzBrowser.mozilla) var re = /rv(\:)(\d+(\.\d+)?)/;
        if ("undefined" != typeof (re) && re.test(ua))
            MzBrowser.version = parseFloat(RegExp.$2);
    })();
});
//显示删除
function showRemove(obj) {
    $(obj).addClass("remove");
}
//隐藏删除
function hideRemove(obj) {
    $(obj).removeClass("remove");
}
//鼠标移动到删除图标，显示手（pointer）
function setRemove(obj, event) {
    var width = $(obj).width();
    var left = $(obj).position().left;
    var e = event || window.event;
    var x = IsIE(GetVersion()) ? e.x : e.pageX;
    if (x > left + width - 9) {
        $(obj).css("cursor", "pointer")
    } else {
        $(obj).css("cursor", "default")
    }
}
function GetVersion() { return MzBrowser.version; }
function GetName() {
    var name = "undefined";
    if (MzBrowser.ie) { name = "ie"; }
    else if (MzBrowser.firefox) { name = "firefox"; }
    else if (MzBrowser.safari) { name = "safari"; }
    return name;
}
function IsIE(versionValue) {
    if (versionValue == 11) {
        return IsIE11();
    }
    var name = GetName();
    var version = GetVersion();
    return name == 'ie' && parseInt(version) == versionValue;
}   
</script>
@endsection
</body>
</html>



