define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'pinetwork/pinetwork_list/index' + location.search,
                    add_url: 'pinetwork/pinetwork_list/add',
                    edit_url: 'pinetwork/pinetwork_list/edit',
                    del_url: 'pinetwork/pinetwork_list/del',
                    multi_url: 'pinetwork/pinetwork_list/multi',
                    import_url: 'pinetwork/pinetwork_list/import',
                    table: 'pinetwork_list',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {
                            field: 'type_id', title: __('所属类型'), operate: 'LIKE',
                            searchList: Config.typeList,
                            formatter: Table.api.formatter.label
                        },
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'title1', title: __('标题1'), operate: 'LIKE'},
                        {
                            field: 'show_image',
                            title: __('Show_image'),
                            operate: false,
                            events: Table.api.events.image,
                            formatter: Table.api.formatter.image
                        },
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {
                            field: 'create_time',
                            title: __('Createtime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            autocomplete: false
                        },
                        {
                            field: 'update_time',
                            title: __('Updatetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            autocomplete: false
                        },
                        {
                            field: 'status_switch',
                            title: __('状态(打开/关闭)'),
                            table: table,
                            searchList:{0:"关闭",1:"打开"},
                            formatter: Table.api.formatter.toggle
                        },
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
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
