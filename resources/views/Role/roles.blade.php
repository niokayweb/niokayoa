@extends('layouts.app')
@section('content')
        <div class="content">
            <div class="main">
               <div class="query_wrap">
                   <h4 class="query_title">角色管理</h4>
                   <div class="topbar">
                       <!-- <input type="text" id="searchByName" placeholder="姓名">
                       <button id="search">搜索</button>
 -->                       <button id="role_add" class="pull-right">添加角色</button>
                   </div>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>角色名</td>
                                <td>角色描述</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody id="table_content">
                            @foreach($roles as $key => $role)
                                <tr class="role" data-id="{{$role->id}}" data-name="{{$role->name}}" data-desc="{{$role->desc}}">
                                    <td>{{$role->id}}</td>
                                    <td>{{$role->name}}</td>
                                    <td>{{$role->desc}}</td>
                                    <td>
                                    <a href="javascript:" class="role_edit" style="color:black">编辑&nbsp;&nbsp;&nbsp;</a>
                                    <a href="{{'authorities/'.$role->id}}" class="auth_edit" style="color:green">设置权限&nbsp;&nbsp;&nbsp;</a>
                                    <a href="javascript:" class="role_del" style="color:black">删除</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! $roles->links() !!}
               </div>
            </div>
        </div>
        
@endsection
@section('js')
<script type="text/javascript">

$('#role_add').click(function(){
    var html = '<form action="add" method="post" id="addRole">';
    html += '请输入名称：<input type="text" id="name" name="name" value="">'+'<br />';
    html += '请输入描述：<input type="text" id="desc" name="desc" value="">'+'<br />';
    html += '</form>';
    showMessage(html,false,true,function(){
        ajaxSubmit($('#addRole'));
    })
})

$('.role_edit').click(function(){
    var name = $(this).parents('.role').data('name');
    var desc = $(this).parents('.role').data('desc');
    var id = $(this).parents('.role').data('id');
    var html = '<form action="edit/'+id+'" method="post" id="editRole">';
    html += '请输入名称：<input type="text" id="name" name="name" value="'+name+'">'+'<br />';
    html += '请输入描述：<input type="text" id="desc" name="desc" value="'+desc+'">'+'<br />';
    html += '</form>';
    showMessage(html,false,true,function(){
        ajaxSubmit($('#editRole'));
    })
})

$('.role_del').click(function(){
    var id = $(this).parents('.role').data('id');
    showMessage('确定要删除角色？',false,true,function(){
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