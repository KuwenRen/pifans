define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'chat/groupchat/index' + location.search,
                    add_url: 'chat/groupchat/add',
                    edit_url: 'chat/groupchat/edit',
                    del_url: 'chat/groupchat/del',
                    multi_url: 'chat/groupchat/multi',
                    import_url: 'chat/groupchat/import',
                    table: 'group_chat',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {
                            field: 'group_type_id', title: __('Group_type_id'),
                            operate: 'LIKE',
                            searchList: Config.typeList,
                            formatter: Table.api.formatter.label
                        },
                        {field: 'group_name', title: __('Group_name'), operate: 'LIKE'},
                        // {field: 'group_master_user_id', title: __('Group_master_user_id')},
                        {
                            field: 'show_image',
                            title: __('Show_image'),
                            operate: false,
                            events: Table.api.events.image,
                            formatter: Table.api.formatter.image
                        },
                        {
                            field: 'is_enable_switch',
                            title: __('Is_enable_switch'),
                            operate: false,
                            table: table,
                            formatter: Table.api.formatter.toggle
                        },
                        {field: 'createtime', title: __('Createtime'), operate: false},
                        {field: 'updatetime', title: __('Updatetime'), operate: false},
                        {
                            field: 'status',
                            title: __('Status'),
                            searchList: {"1": __('Status 1'), "0": __('Status 0')},
                            formatter: Table.api.formatter.status
                        },
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'chat/groupchat/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'chat/groupchat/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'chat/groupchat/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});