@extends('layouts.app')
@section('content')
        <div class="content">
            <div class="main">
               <div class="query_wrap">
                   <h4 class="query_title">权限管理</h4>
                   <div class="topbar">
                       所属系统: <input type="text" id="system_name" placeholder="" value="{{isset($_GET['system_name']) ? $_GET['system_name'] : ''}}">
                       <!-- 所属模块: <input type="text" id="parent_id" placeholder="" value="{{isset($_GET['parent_id']) ? $_GET['parent_id'] : ''}}"> -->
                       <button id="search">搜索</button>
                       <button id="authority_add" class="pull-right">添加权限</button>
                   </div>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>名称</td>
                                <td>所属系统</td>
                                <td>所属模块</td>
                                <td>权限</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody id="table_content">
                            @foreach($authorities as $key => $authority)
                                <tr  class="authority" data-id="{{$authority->id}}" data-sid="{{$authority->system_id}}" data-name="{{$authority->name}}" data-authority="{{$authority->authority}}" data-pid="{{$authority->parent_id}}">
                                    <td>{{$authority->id}}</td>
                                    <td>{{$authority->name}}</td>
                                    <td>{{$systems[$authority->system_id]}}</td>
                                    <td>@if($authority->parent_id ==0) 顶级模块 @else {{$modules[$authority->parent_id]}} @endif</td>
                                    <td>{{$authority->authority}}</td>
                                    <td>
                                    <a href="javascript:" class="authority_edit" style="color:green">编辑&nbsp;&nbsp;&nbsp;</a>
                                    <a href="javascript:" class="authority_del" style="color:black">删除</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! $authorities->appends(['system_name'=>isset($_GET['system_name']) ? $_GET['system_name'] : ''])->links() !!}
               </div>
            </div>
        </div>
@endsection
@section('js')
<script type="text/javascript">

$('#search').click(function(){
    var system_name = $('#system_name').val();
    // var parent_id = $('#parent_id').val();
    location.href = '/authority/index?system_name='+system_name;
})

$('#authority_add').click(function(){
    var systemSelect = '<select class="systemSelect" name="system_id">';
    @foreach($systems as $k => $system)
    systemSelect += '<option value="'+{{$k}}+'">'+"{{$system}}"+'</option>';
    @endforeach
    systemSelect += '</select>';
    var moduleSelect = '<select class="moduleSelect" name="parent_id">';
    moduleSelect += '<option value="0">顶级模块</option>';
    @foreach($firstModules as $k => $module)
    moduleSelect += '<option value="'+{{$k}}+'">'+"{{$module}}"+'</option>';
    @endforeach
    moduleSelect += '</select>';
    var html = '<form action="add" method="post" id="addAuthority">';
    html += '请选择系统：'+systemSelect+'<br />';
    html += '请选择模块：'+moduleSelect+'<br />';
    html += '请输入名称：<input type="text" id="name" name="name" value="">'+'<br />';
    html += '请输入权限：<input type="text" id="authority" name="authority" value="">'+'<br />';
    html += '</form>';
    showMessage(html,false,true,function(){
        ajaxSubmit($('#addAuthority'));
    })
    $('.systemSelect').change(function(){
        $('.moduleSelect').html(""); 
        var id = $(this).val();
        $("<option value='0'>顶级模块</option>").appendTo($('.moduleSelect'));
        ajaxData('modules/'+id,{},function(res){
            for(var i = 0;i < res.data.ids.length; i++ ){ 
                $("<option value =' " + res.data.ids[i] + " '> "+ res.data.name[res.data.ids[i]] +"</option>").appendTo($('.moduleSelect')); 
            } 
        })
    })
})

$('.authority_edit').click(function(){
    var pid = $(this).parents('.authority').data('pid');
    var id = $(this).parents('.authority').data('id');
    var name = $(this).parents('.authority').data('name');
    var system_id = $(this).parents('.authority').data('sid');
    var authority = $(this).parents('.authority').data('authority');
    var systemSelect = '<select class="systemSelect" name="system_id">';
    @foreach($systems as $k => $system)
      systemSelect += '<option value="'+{{$k}}+'" '+({{$k}}==system_id?'selected':'')+'>'+"{{$system}}"+'</option>';
    @endforeach
    systemSelect += '</select>';
    var moduleSelect = '<select class="moduleSelect" name="parent_id">';
    moduleSelect += '</select>';
    var html = '<form action="edit/'+id+'" method="post" id="editRole">';
    html += '请选择系统：'+systemSelect+'<br />';
    html += '请选择模块：'+moduleSelect+'<br />';
    html += '请输入名称：<input type="text" id="name" name="name" value="'+name+'">'+'<br />';
    html += '请输入权限：<input type="text" id="authority" name="authority" value="'+authority+'">'+'<br />';
    html += '</form>';
    showMessage(html,false,true,function(){
        ajaxSubmit($('#editRole'));
    })
    $('.moduleSelect').html(""); 
    var mid = $('.systemSelect').val();
    $("<option value='0'>顶级模块</option>").appendTo($('.moduleSelect'));
    ajaxData('modules/'+mid,{},function(res){
        for(var i = 0;i < res.data.ids.length; i++ ){ 
            if(res.data.ids[i] != id){
                $("<option value =' " + res.data.ids[i] + "'" + (pid==res.data.ids[i]?'selected':'') + " '> "+ res.data.name[res.data.ids[i]] +"</option>").appendTo($('.moduleSelect')); 
            }
        } 
    })
    $('.systemSelect').change(function(){
        $('.moduleSelect').html(""); 
        var modid = $(this).val();
        $("<option value='0'>顶级模块</option>").appendTo($('.moduleSelect'));
        ajaxData('modules/'+modid,{},function(res){
            for(var i = 0;i < res.data.ids.length; i++ ){ 
                if(res.data.ids[i] != id){
                  $("<option value =' " + res.data.ids[i] + " '> "+ res.data.name[res.data.ids[i]] +"</option>").appendTo($('.moduleSelect')); 
                }
            } 
        })
    })
})


$('.authority_del').click(function(){
    var id = $(this).parents('.authority').data('id');
    showMessage('确定要删除权限？',false,true,function(){
        ajaxData('del/'+id,{},function(res){
            if(res.code == 0){
                showMessage(res.msg,false,false,function(){
                    window.location.reload();
                })
            }else{
                showMessage(res.msg)
            }
            
        })
    })
})


</script>
@endsection