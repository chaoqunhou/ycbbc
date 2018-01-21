//----------------------------------------------------------------------------------------------------------------------
api 验参层

验证规则:

'index','create'    直接通过
'read','edit','delete','update'   ->> Common.id

其他 && 被外部直接访问 ->> 一律验入参
跳过验证示例：'aaAction'  =>  [''],

验证位置:

app\validate\:controller

$scene = [
//      ':action'   =>  [xxxxxxxxxxxxxxxx,
		'read'      =>  ['id','goods_id'],
];

//----------------------------------------------------------------------------------------------------------------------
路由
    理解  RESTful API
    参考  TP5资源路由文档

    //(blog : 模块名)
    标识	    请求类型	    生成路由规则	    对应操作方法（默认）
    index	GET	        blog	        index
    create	GET	        blog/create	    create
    save	POST	    blog	        save
    read	GET	        blog/:id	    read
    edit	GET	        blog/:id/edit	edit        停用      post    blog/editAction
    update	PUT	        blog/:id	    update      停用      post    blog/update/:id
    delete	DELETE	    blog/:id	    delete      停用      post    blog/delete/:id

    方便与app连调 ，  ↓↓↓↓======================
    update	PUT	    blog/:id	        update
    delete	DELETE	blog/:id	        delete
    改为             ↓↓↓↓----------------------
    update	POST	blog/update/:id	    update
    delete	POST	blog/delete/:id	    delete
    ==========================================
    edit	GET	        blog/:id/edit	edit
                    ↓↓↓↓----------------------
    edit	POST	        blog/editAction	edit
    ==========================================

    一般情况接口定义请符合RESTful规则
    -------如有特殊情况 ↓↓↓↓----------
    请求类型     生成路由规则	            对应操作方法
    POST        blog/[action]Action     blog/[action]Action

//----------------------------------------------------------------------------------------------------------------------
