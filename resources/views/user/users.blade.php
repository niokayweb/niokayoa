@extends('layouts.app')
@section('content')
   <!-- main content start-->
    <div class="main-content" >
        <!-- page heading start-->
        <div class="page-heading">
            <h3>
                首页
            </h3>
            <ul class="breadcrumb">
                <li>
                    <a href="#">首页</a>
                </li>
                <li>
                    <a href="#">管理员列表</a>
                </li>
                <li class="active"></li>
            </ul>
        </div>
        <!-- page heading end-->

        <!--body wrapper start-->
        <div class="wrapper">
        <div  class="center row pre-scrollable" style="max-height:780px;">
        <div class="col-sm-12">
        <section class="panel">
        <header class="panel-heading">
            <span class="tools pull-right">
                <a href="javascript:;" class="fa fa-chevron-down"></a>
                <a href="javascript:;" class="fa fa-times"></a>
             </span>
        </header>
        <div class="panel-body">
        <div class="adv-table">
        <table  class="display table table-bordered table-striped" id="dynamic-table">
        <thead>
        <tr>
            <td>ID</td>
            <td>打卡编号</td>
            <td>姓名</td>
            <td>邮箱</td>
            <td>状态</td>
            <td>角色</td>
            <td>创建时间</td>
            <td>操作</td>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $key => $user)
            <tr class="users" data-id="{{$user->id}}" data-name="{{$user->name}}" data-email="{{$user->email}}" data-roleid="{{$user->role_id}}" data-status="{{$user->status}}" data-iUid="{{$user->iUid}}" >
                <td>{{$user->id}}</td>
                <td>{{$user->iUid}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>@if ($user->status == 1) 正常 @else 离职 @endif</td>
                <td>@if ($user->email == 'admin@5fun.com') <span style="color:green">超级管理员</span> @elseif($user->role_id == 0) 普通成员 @else <span style="color:green">{{isset($roles[$user->role_id]) ? $roles[$user->role_id] : '未知角色'}}</span> @endif</td>
                <td>{{$user->created_at}}</td>
                <td>
                    @if ($user->email != 'admin@5fun.com')
                        <a href="javascript:" class="user_edit" style="color:green">编辑&nbsp;&nbsp;&nbsp;</a>
                        <a href="javascript:" class="user_status" style="color:black">@if($user->status == 0) 恢复 @else 设为离职 @endif &nbsp;&nbsp;&nbsp;</a>
                        <a href="javascript:" class="user_del" style="color:black">删除</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
        </table>
            {!! $users->appends(['name'=>isset($_GET['name']) ? $_GET['name'] : '','role_id'=>isset($_GET['role_id']) ? $_GET['role_id'] : '',])->links() !!}
        </div>
        </div>
        </section>
        </div>
        </div>
        </div>
        <!--body wrapper end-->
        <!-- 模态框（Modal） -->
        <div class="modal fade" id="editList" tabindex="-1" role="dialog" aria-labelledby="editList" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel"></h4>
                    </div>
                    <div class="modal-body" id="modelShow">

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>
    <!-- main content end-->
</section>
@endsection
@section('js')
    <script>
        $('#user_add').click(function(){
            var roleSelect = '<select class="roleSelect" name="role_id">';
            roleSelect += '<option value="0">'+"普通成员"+'</option>';
            @foreach($roles as $k => $role)
                roleSelect += '<option value="'+{{$k}}+'">'+"{{$role}}"+'</option>';
            @endforeach
                roleSelect += '</select>';
            var html = '<form action="add" method="post" id="addRole">';
            html += '<div class="input-group">请输入邮箱：<input type="text" id="email" name="email" class="form-control" value=""></div>'+'<br />';
            html += '<div class="input-group">请输入编号：<input type="text" id="iUid" name="iUid" class="form-control" value=""></div>'+'<br />';
            html += '<div class="input-group">请输入姓名：<input type="text" id="name" name="name" class="form-control" value=""></div>'+'<br />';
            html += '<div class="input-group">请输入密码：<input type="password" id="password" name="password" class="form-control" value=""></div>'+'<br />';
            html += '<div class="input-group">企业微信uid：<input type="text" id="wx_uid" name="wx_uid" class="form-control"  value=""></div>'+'<br />';
            html += '<div class="input-group">请选择角色 : '+roleSelect;
            html += '</form>';
            showMessage(html,false,true,function(){
                ajaxSubmit($('#addRole'));
            })
        })

        $('.user_edit').click(function(){
            var iUid = $(this).parents('.users').data('iuid');
            var name = $(this).parents('.users').data('name');
            var email = $(this).parents('.users').data('email');
            var role_id = $(this).parents('.users').data('roleid');
            var password = $(this).parents('.users').data('password');
            var sid = $(this).parents('.users').data('id');
            var roleSelect = '<select class="roleSelect" name="role_id">';
            roleSelect += '<option value="0">'+"普通成员"+'</option>';
            @foreach($roles as $k => $role)
                roleSelect += '<option value="'+{{$k}}+'" '+({{$k}}==role_id?'selected':'')+'>'+"{{$role}}"+'</option>';
            @endforeach
                roleSelect += '</select>';
            var shtml = '<form  id="editRole">';
            <!-- CSRF Token -->
            shtml +='<meta name="csrf-token" content="{{ csrf_token() }}">';
            shtml += '<div style="margin-bottom:2px;">请输入邮箱：<input type="text" id="email" name="email" value="'+email+'"></div>'+'<br />';
            shtml += '<div style="margin-bottom:2px;">请输入编号：<input type="text" id="iUid" name="iUid" value="'+iUid+'"></div>'+'<br />';
            shtml += '<div style="margin-bottom:2px;">请输入姓名：<input type="text" id="name" name="name" value="'+name+'"></div>'+'<br />';
            shtml += '<div style="margin-bottom:2px;">请输入密码：<input type="password" id="password" name="password" value="'+password+'"></div>'+'<br />';
            shtml += '请选择角色 : '+roleSelect;
            shtml +='<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">关闭</button><button type="button" onclick="dopost(\'edit\','+sid+');" class="btn btn-primary">提交</button></div></form>';

            $('#modelShow').html(shtml);
            $('#myModalLabel').html('管理员修改');
            $('#editList').modal('show');
        });

        function dopost(str,sid){
            if(str=='edit'){
                var email=$('#email').val();
                var iUid=$('#iUid').val();
                var name=$('#name').val();
                var password=$('#password').val();
                var role_id=$('.roleSelect').val();
                $.ajax({
                    type: 'POST',
                    url: '/admin/user/edit',
                    data: {'id':sid,'email': email,'iUid':iUid,'name':name,'password':password,'role_id':role_id},
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data){
                        console.log(data);
                    },
                    error: function(xhr, type){
                        alert('Ajax error!')
                    }
                });
            }
        }

        // $('#editBtn').click(function(){
        //     alert("111");
        //     ajaxSubmit($('#editRole'));
        // });


        $('.user_del').click(function(){
            var id = $(this).parents('.users').data('id');
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

        $('.user_status').click(function(){
            var id = $(this).parents('.users').data('id');
            var status = $(this).parents('.users').data('status');
            var msg = status == 1 ? '确定要设为离职?' : '确定要恢复?';
            showMessage(msg,false,true,function(){
                ajaxData('set_status/'+id,{},function(res){
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